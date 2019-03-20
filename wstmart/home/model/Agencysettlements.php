<?php
namespace wstmart\home\model;
use think\Db;
/**
 * ============================================================================
 *  MKT多用户商城
 * 版权所有 2016-2066 深圳买客多信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.mktsoft.com.cn
 *  
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 结算类
 */
class Agencysettlements extends Base{
    /**
     * 获取区域商结算单列表
     */
    public function pageQueryAgency(){
        $where = [];
        if(input('settlementNo')!='')$where['settlementNo'] = ['like','%'.input('settlementNo').'%'];
        if((int)input('isFinish')>=0)$where['settlementStatus'] = (int)input('isFinish');
        $where['agencyId'] = (int)session('WST_USER.agencyId');
        return Db::name('agencysettlements')->alias('s')->where($where)->order('settlementId', 'desc')
            ->paginate(input('pagesize/d'));
    }
    /**
     *  获取区域商未结算订单列表
     */
    public function agencyUnSettledQuery(){
        $where = [];
        if(input('orderNo')!='')$where['orderNo'] = ['like','%'.input('orderNo').'%'];
        $where['dataFlag'] = 1;
        $where['orderStatus'] = 2;
        $where['agencySettlementId'] = 0;
        $where['agencyId'] = (int)session('WST_USER.agencyId');
        $page =  Db::name('orders')->where($where)->order('orderId', 'desc')
                   ->field('orderId,orderNo,createTime,payType,goodsMoney,deliverMoney,totalMoney,agencyFee,realTotalMoney')
                   ->paginate(input('pagesize/d'))->toArray();
        if(count($page['Rows'])){
            foreach ($page['Rows'] as $key => $v) {
                $page['Rows'][$key]['payTypeNames'] = WSTLangPayType($v['payType']);
            }
        }
        return $page;
    }
    /**
     * 区域商申请结算指定的订单
     */
    public function agencySettlement(){
        $agencyId = (int)session('WST_USER.agencyId');
        $ids = input('ids');
        $where['dataFlag'] = 1;
        $where['orderStatus'] = 2;
        $where['agencySettlementId'] = 0;
        $where['orderId'] = ['in',$ids];
        $where['agencyId'] = $agencyId;
        $orders = Db::name('orders')->where($where)->field('orderId,payType,realTotalMoney,agencyFee')->select();
        if(empty($orders))return WSTReturn('没有需要结算的订单，请刷新后再核对!');
        $settlementMoney = 0;
        $agencyFee = 0;    //区域商要收的佣金
        $ids = [];
        foreach ($orders as $key => $v) {
            $ids[] = $v['orderId'];
            if($v['payType']==1){
                $settlementMoney += $v['realTotalMoney'];
            }
            $agencyFee += abs($v['agencyFee']);
        }
        
        $agencys = Db::name('agencys')->alias('s')->join('banks b','b.bankId=s.bankId','inner')->where('s.agencyId',$agencyId)->field('b.bankName,s.bankAreaId,bankNo,bankUserName')->find();
        if(empty($agencys))WSTReturn('无效的区域商结算账号!');
        Db::startTrans();
        try{
            $areaNames  = model('areas')->getParentNames($agencys['bankAreaId']);
            $data = [];
            $data['settlementType'] = 0;
            $data['agencyId'] = $agencyId;
            // $data['accName'] = $agencys['bankName'];
            // $data['accNo'] = $agencys['bankNo'];
            // $data['accUser'] = $agencys['bankUserName'];
            // $data['areaName'] = implode('',$areaNames);
            $data['settlementMoney'] = $settlementMoney;
            $data['commissionFee'] = $agencyFee;
            $data['settlementStatus'] = 0;
            $data['createTime'] = date('Y-m-d H:i:s');
            $result = $this->save($data);
            if(false !==  $result){
                 $this->settlementNo = $this->settlementId.(fmod($this->settlementId,7));
                 $this->save();
                 Db::name('orders')->where(['orderId'=>['in',$ids]])->update(['agencySettlementId'=>$this->settlementId]);
                 //修改区域商订单情况
                 $prefix = config('database.prefix');
                 $upSql = 'update '.$prefix.'agencys set noSettledOrderNum=noSettledOrderNum-'.count($orders).',agencyMoney=agencyMoney+'.$agencyFee.',noSettledOrderFee=noSettledOrderFee-'.$agencyFee.' where agencyId='.$agencyId;
                 Db::execute($upSql);
                 Db::commit();
                 return WSTReturn('提交结算申请成功，请留意结算信息~',1);
            }
        }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('提交结算申请失败',-1);
    }
    /**
     * 获取区域商已申请结算订单
     */
    public function agencySettledQuery(){
        $where = [];
        if(input('settlementNo')!='')$where['settlementNo'] = ['like','%'.input('settlementNo').'%'];
        if(input('orderNo')!='')$where['orderNo'] = ['like','%'.input('orderNo').'%'];
        if((int)input('isFinish')>=0)$where['settlementStatus'] = (int)input('isFinish');
        $where['dataFlag'] = 1;
        $where['orderStatus'] = 2;
        $where['o.agencyId'] = (int)session('WST_USER.agencyId');
        $page = Db::name('orders')->alias('o')->join('wst_agencysettlements s','o.agencySettlementId=s.settlementId')->where($where)->field('orderId,orderNo,payType,goodsMoney,deliverMoney,totalMoney,o.agencyFee,realTotalMoney,s.settlementTime,s.settlementNo')->order('s.settlementTime desc')->paginate(input('pagesize/d'))->toArray();
        if(count($page['Rows'])){
            foreach ($page['Rows'] as $key => $v) {
                $page['Rows'][$key]['agencyFee'] = abs($v['agencyFee']);
                $page['Rows'][$key]['payTypeNames'] = WSTLangPayType($v['payType']);
            }
        }
        return $page;
    }

    /**
     * 获取结算订单详情
     */
    public function getById(){
        $settlementId = (int)input('id');
        $object =  Db::name('settlements')->alias('st')->where('settlementId',$settlementId)->join('__SHOPS__ s','s.shopId=st.shopId','left')->field('s.shopName,st.*')->find();
        if(!empty($object)){
            $object['list'] = Db::name('orders')->where(['settlementId'=>$settlementId])
                      ->field('orderId,orderNo,payType,goodsMoney,deliverMoney,realTotalMoney,totalMoney,commissionFee,createTime')
                      ->order('payType desc,orderId desc')->select();
        }
        return $object;
    }
}