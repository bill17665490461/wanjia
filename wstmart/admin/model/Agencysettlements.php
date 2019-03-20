<?php 
namespace wstmart\admin\model;
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
 * 结算业务处理
 */
class Agencysettlements extends Base{
    /**
	 * 获取结算列表
	 */
	public function pageQuery(){
		$settlementNo = input('settlementNo');
		$realName = input('realName');
		$settlementStatus = (int)input('settlementStatus',-1);
		$where = [];
		if($settlementNo!='')$where['settlementNo'] = ['like','%'.$settlementNo.'%'];
        if($realName!='')$where['realName'] = ['like','%'.$realName.'%']; 
        if($settlementStatus>=0)$where['settlementStatus'] = $settlementStatus;
		return Db::name('agencysettlements')->alias('st')->join('__AGENCYS__ s','s.agencyId=st.agencyId','left')->where($where)->field('s.realName,settlementNo,settlementId,settlementMoney,commissionFee,telephone,settlementStatus,settlementTime,st.createTime')->order('st.settlementId', 'desc')
			->paginate(input('pagesize/d'))->toArray();
	}

	/**
	 * 获取结算订单详情
	 */
	public function getById(){
        $settlementId = (int)input('id');
        $object =  Db::name('agencysettlements')->alias('st')->where('settlementId',$settlementId)->join('__AGENCYS__ s','s.agencyId=st.agencyId','left')->field('s.realName,st.*')->find();
        if(!empty($object)){
        	$object['list'] = Db::name('orders')->where(['agencysettlementId'=>$settlementId])
        	          ->field('orderId,orderNo,payType,goodsMoney,deliverMoney,realTotalMoney,totalMoney,agencyFee,createTime')
        	          ->order('payType desc,orderId desc')->select();
        }
        return $object;
	}
	/**
	 * 处理订单
	 */
	public function handle(){
		$id = (int)input('settlementId');
		$remarks = input('remarks');
		Db::startTrans();
        try{
			$object = $this->get($id);
			$object->settlementStatus = 1;
			$object->settlementTime = date('Y-m-d H:i:s');
			if($remarks!='')$object->remarks = $remarks;
			$rs = $object->save();
			if(false !== $rs){
				$agency = model('Agencys')->get($object->agencyId);
				WSTSendMsg($agency['userId'],"您的结算申请【".$object->settlementNo."】已处理，请留意区域商钱包哦~",['from'=>4,'dataId'=>$id]);
				$agency->agencyMoney = $agency->agencyMoney+$object->commissionFee;
                $agency->paymentMoney = $agency->paymentMoney+(-1*$object->commissionFee);
				$agency->save();
				//增加一条资金变动信息
				$lm = [];
				$lm['targetType'] = 2;
				$lm['targetId'] = $object->agencyId;
				$lm['dataId'] = $id;
				$lm['dataSrc'] = 2;
				$lm['remark'] = '区域商结算订单申请【'.$object->settlementNo.'】收取订单佣金¥'.$object->commissionFee."。".(($object->remarks!='')?"【操作备注】：".$object->remarks:'');
				$lm['moneyType'] = 1;
				$lm['money'] = $object->commissionFee;
				$lm['payType'] = 0;
				$lm['createTime'] = date('Y-m-d H:i:s');
				model('LogMoneys')->save($lm);
				Db::commit();
				return WSTReturn('操作成功!',1);
			}
		}catch (\Exception $e) {
            Db::rollback();
        }
		return WSTReturn('操作失败!',-1);
	}

	/**
	 * 获取订单商品
	 */
	public function pageGoodsQuery(){
        $id = (int)input('id');
        return Db::name('orders')->alias('o')->join('__ORDER_GOODS__ og','o.orderId=og.orderId')->where('o.agencysettlementId',$id)
        ->field('orderNo,og.goodsPrice,og.goodsName,og.goodsSpecNames,og.goodsNum,o.agencyRate')->order('o.payType desc,o.orderId desc')->paginate(input('pagesize/d'))->toArray();
    }

    /**
     * 获取待结算商家
     */
    public function pageAgencyQuery(){
    	$areaIdPath = input('areaIdPath');
    	$realName = input('realName');
    	$where = [];
    	if($realName!='')$where['s.realName'] = ['like','%'.$realName.'%'];
    	if($areaIdPath !='')$where['s.areaIdPath'] = ['like',$areaIdPath."%"];
    	$where['s.dataFlag'] = 1;
    	$where['s.noSettledOrderNum'] = ['>',0];
		return Db::table('__AGENCYS__')->alias('s')->join('__AREAS__ a2','s.areaId=a2.areaId')
		       ->where($where)
		       ->field('agencyId,a2.areaName,realName,telephone,abs(noSettledOrderFee) noSettledOrderFee,noSettledOrderNum')
		       ->order('noSettledOrderFee desc')->paginate(input('pagesize/d'));
	}

   /**
    * 获取商家未结算的订单
    */
   public function pageAgencyOrderQuery(){
   	     $orderNo = input('orderNo');
   	     $payType = (int)input('payType',-1);
         $where = [];
         $where['agencySettlementId'] = 0;
         $where['orderStatus'] = 2;
         $where['agencyId'] = (int)input('id');
         $where['dataFlag'] = 1;
         if($orderNo!='')$where['orderNo'] = ['like','%'.$orderNo.'%'];
         if(in_array($payType,[0,1]))$where['payType'] = $payType;
   	     $page = Db::name('orders')->where($where)
        	          ->field('orderId,orderNo,payType,goodsMoney,deliverMoney,realTotalMoney,totalMoney,agencyFee,createTime')
        	          ->order('payType desc,orderId desc')->paginate(input('pagesize/d'))->toArray();
        if(count($page['Rows'])>0){
        	foreach ($page['Rows'] as $key => $v) {
        		$page['Rows'][$key]['payTypeName'] = WSTLangPayType($v['payType']);
        	}
        }
        return $page;
   }

   /**
    * 生成结算单
    */
	public function generateSettleByAgency(){
		$agencyId = (int)input('id');
		$where = [];
		$where['agencyId'] = $agencyId;
		$where['dataFlag'] = 1;
		$where['orderStatus'] = 2;
		$where['agencySettlementId'] = 0;
		$orders = Db::name('orders')->where($where)->field('orderId,payType,realTotalMoney,agencyFee')->select();
    	if(empty($orders))return WSTReturn('没有需要结算的订单，请刷新后再核对!');
    	$settlementMoney = 0;
        $agencyFee = 0;    //区域商的佣金
        $ids = [];
    	foreach ($orders as $key => $v) {
            $ids[] = $v['orderId'];
    		if($v['payType']==1){
                $settlementMoney += $v['realTotalMoney'];
            }
            $agencyFee += abs($v['agencyFee']);
    	}
    	
    	$agencys = Db::name('agencys')->alias('s')->join('banks b','b.bankId=s.bankId','inner')->where('s.agencyId',$agencyId)->field('b.bankName,s.bankAreaId,bankNo,bankUserName,userId')->find();
    	if(empty($agencys))WSTReturn('无效的区域商结算账号!');
    	Db::startTrans();
		try{
            $areaNames  = model('areas')->getParentNames($agencys['bankAreaId']);
            $data = [];
            $data['settlementType'] = 0;
            $data['agencyId'] = $agencyId;
            $data['accName'] = $agencys['bankName'];
            $data['accNo'] = $agencys['bankNo'];
            $data['accUser'] = $agencys['bankUserName'];
            $data['areaName'] = implode('',$areaNames);
            $data['settlementMoney'] = $settlementMoney;
            $data['commissionFee'] = $agencyFee;
            $data['settlementStatus'] = 1;
            $data['settlementTime'] = date('Y-m-d H:i:s');
            $data['createTime'] = date('Y-m-d H:i:s');
            $result = $this->save($data);
            if(false !==  $result){
            	$this->settlementNo = $this->settlementId.(fmod($this->settlementId,7));
            	$this->save();
            	//修改区域商订单情况
                Db::name('orders')->where(['orderId'=>['in',$ids]])->update(['agencySettlementId'=>$this->settlementId]);
                //区域商佣金
                $prefix = config('database.prefix');
                $upSql = 'update '.$prefix.'agencys set noSettledOrderNum=0,noSettledOrderFee=0 where agencyId='.$agencyId;
                Db::execute($upSql);
                //发消息
				WSTSendMsg($agencys['userId'],"您有新的结算单【".$this->settlementNo."】生成，请留意结算信息~",['from'=>4,'dataId'=>$this->settlementId]);
				//增加一条资金变动信息
				$lm = [];
				$lm['targetType'] = 2;
				$lm['targetId'] = $agencyId;
				$lm['dataId'] = $this->settlementId;
				$lm['dataSrc'] = 2;
				$lm['remark'] = '区域商结算订单申请【'.$this->settlementNo.'】收取订单佣金¥'.$agencyFee."。";
				$lm['moneyType'] = 1;
				$lm['money'] = $agencyFee;
				$lm['payType'] = 0;
				$lm['createTime'] = date('Y-m-d H:i:s');
				model('LogMoneys')->save($lm);
				Db::commit();
            	return WSTReturn('生成结算单成功',1);
            }
		}catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('生成结算单失败',-1);
    }
}