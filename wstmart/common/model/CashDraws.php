<?php
namespace wstmart\common\model;
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
 * 提现流水业务处理器
 */
class CashDraws extends Base{
     /**
      * 获取列表
      */
      public function pageQuery($targetType,$targetId){
      	  $type = (int)input('post.type',-1);
          $where = [];
          $where['targetType'] = (int)$targetType;
          $where['targetId'] = (int)$targetId;
          if(in_array($type,[0,1]))$where['moneyType'] = $type;
          return $this->where($where)->order('cashId desc')->paginate()->toArray();
      }   
      public function pageQueryMob($targetType,$targetId){
          $type = (int)input('post.type',-1);
          $page = (int)input('post.page');
          $where = [];
          $where['targetType'] = (int)$targetType;
          $where['targetId'] = (int)$targetId;
          if(in_array($type,[0,1]))$where['moneyType'] = $type;
          return $this->where($where)->order('cashId desc')->page($page,10)->select();
      }

      /**
       * 申请提现
       */
      public function drawMoney(){
          $userId = (int)session('WST_USER.userId');
          $money = (float)input('money');
          $accId = (float)input('accId');
          $payPwd = input('payPwd');
          if($money<=0)return WSTReturn('提取金额必须大于0');
          if($payPwd=='')return WSTReturn('支付密码不能为空');
          //加载提现账号信息
          $acc = Db::name('cash_configs')->alias('cc')
                   ->join('__BANKS__ b','cc.accTargetId=b.bankId')->where(['cc.dataFlag'=>1,'id'=>$accId])
                   ->field('b.bankName,cc.*')->find();
          if(empty($acc))return WSTReturn('提现账号不存在');
          $areas = model('areas')->getParentNames($acc['accAreaId']);
          //加载用户
          $user = model('users')->get($userId);
          $payPwd = md5($payPwd.$user->loginSecret);
          if($payPwd!=$user->payPwd)return WSTReturn('支付密码错误');
          if($money>$user->userMoney)return WSTReturn('提取金额不能大于用户余额');
          //减去要提取的金额
          $user->userMoney = $user->userMoney-$money;
          $user->lockMoney = $user->lockMoney+$money;
          Db::startTrans();
          try{
             $result = $user->save();
             if(false !==$result){
                //创建提现记录
                $data = [];
                $data['targetType'] = 1;
                $data['targetId'] = $userId;
                $data['money'] = $money;
                $data['accType'] = 3;
                $data['accTargetName'] = $acc['bankName'];
                $data['accAreaName'] = implode('',$areas);
                $data['accNo'] = $acc['accNo'];
                $data['accUser'] = $acc['accUser'];
                $data['cashSatus'] = 0;
                $data['cashConfigId'] = $accId;
                $data['createTime'] = date('Y-m-d H:i:s');
                $data['cashNo'] = '';
                $this->save($data);
                $this->cashNo = $this->cashId.(fmod($this->cashId,7));
                $this->save();
                Db::commit();
                return WSTReturn('提现申请成功，请留意系统信息',1);
             }
          }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('提现申请失败',-1);
          }
      }

    //店铺提现
    public function shopDrawMoney(){
        $shopId = (int)session('WST_USER.shopId');
        $money = (float)input('money');
        if($money<=0)return WSTReturn('提取金额必须大于0');
        //加载店铺
        $shop = model('shops')->get($shopId);
        if($money>$shop->shopMoney)return WSTReturn('提取金额不能大于用户余额');
        //加载银行信息
        $bankName = db('banks')->where('bankId',$shop['bankId'])->value('bankName');
        $areas = model('areas')->getParentNames($shop['bankAreaId']);
        //减去要提取的金额
        $shop->shopMoney = $shop->shopMoney-$money;
        $shop->lockMoney = $shop->lockMoney+$money;
        Db::startTrans();
        try{
            $result = $shop->save();
            if(false !==$result){
                //创建提现记录
                $data = [];
                $data['targetType'] = 2;
                $data['targetId'] = $shopId;
                $data['money'] = $money;
                $data['accType'] = 3;
                $data['accTargetName'] = $bankName;
                $data['accAreaName'] = implode('',$areas);
                $data['accNo'] = $shop['bankNo'];
                $data['accUser'] = $shop['bankUserName'];
                $data['cashSatus'] = 0;
                $data['createTime'] = date('Y-m-d H:i:s');
                $data['cashNo'] = '';
                $this->save($data);
                $this->cashNo = $this->cashId.(fmod($this->cashId,7));
                $this->save();
                Db::commit();
                return WSTReturn('提现申请成功，请留意系统信息',1);
            }
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('提现申请失败',-1);
        }
    }
    //区域商提现
    public function agencyDrawMoney(){
        $agencyId = (int)session('WST_USER.agencyId');
        $money = (float)input('money');
        if($money<=0)return WSTReturn('提取金额必须大于0');
        //加载区域商信息
        $agency = model('agencys')->get($agencyId);
        if($money>$agency->agencyMoney)return WSTReturn('提取金额不能大于用户余额');
        //加载银行信息
        $bankName = db('banks')->where('bankId',$agency['bankId'])->value('bankName');
        $areas = model('areas')->getParentNames($agency['bankAreaId']);
        //减去要提取的金额
        $agency->agencyMoney = $agency->agencyMoney-$money;
        $agency->lockMoney = $agency->lockMoney+$money;
        Db::startTrans();
        try{
            $result = $agency->save();
            if(false !==$result){
                //创建提现记录
                $data = [];
                $data['targetType'] = 3;
                $data['targetId'] = $agencyId;
                $data['money'] = $money;
                $data['accType'] = 3;
                $data['accTargetName'] = $bankName;
                $data['accAreaName'] = implode('',$areas);
                $data['accNo'] = $agency['bankNo'];
                $data['accUser'] = $agency['bankUserName'];
                $data['cashSatus'] = 0;
                $data['createTime'] = date('Y-m-d H:i:s');
                $data['cashNo'] = '';
                $this->save($data);
                $this->cashNo = $this->cashId.(fmod($this->cashId,7));
                $this->save();
                Db::commit();
                return WSTReturn('提现申请成功，请留意系统信息',1);
            }
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('提现申请失败',-1);
        }
    }
}
