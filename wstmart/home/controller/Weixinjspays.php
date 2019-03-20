<?php
namespace wstmart\home\controller;
use wstmart\home\model\Payments as M;
use think\Loader;
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
 * 微信支付控制器
 */
class Weixinjspays extends Base{
	
	 // 公众号支付
    public function wxjspay()
    {
    	Loader::import('weixinpay.WxPayApi');
    	Loader::import('weixinpay.JsApiPay');
    	Loader::import('weixinpay.WxPayDataBase');
		$userId = (int)session('WST_USER.userId');
		//$data = model('Orders')->checkOrderPay();
		$flag = true;
		//if($data["status"]==1){
			$orderNo = input('orderNo');
			
			if(strpos($orderNo,"=")){
				$da=explode("=",$orderNo);
				$obj["orderNo"] = $da[1];
			}else{
				$obj["orderNo"] = $orderNo;
			}
		
			$obj["userId"] = $userId;
			
			$obj["isBatch"] = 0;
			
			$m = new M();
			$needPay = $m->getPayOrders($obj);
			
			
			//if($needPay>0){
				$flag = false;
				
				$input = new \WxPayUnifiedOrder();//②、统一下单
				$tools = new \JsApiPay();
				$openId = $tools->GetOpenid();
				//print_r($da[1]);die;
				$input->SetBody("万家果园");
				$input->SetAttach("order");
				
				$input->SetOut_trade_no($obj["orderNo"]."_".time());
				$input->SetTotal_fee(1);
				
				$input->SetTime_start(date("YmdHis"));
				$input->SetTime_expire(date("YmdHis", time() + 600));
				$input->SetGoods_tag("万家果园");
			
				$input->SetNotify_url(url("home/weixinpays/wxNotify","",true,true));
				
				$input->SetTrade_type("JSAPI");
				
				
				$input->SetOpenid($openId);
				
				$orders = \WxPayApi::unifiedOrder($input);
				//print_R($orders);die;
				//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
				$jsApiParameters = $tools->GetJsApiParameters($orders);
				$editAddress = $tools->GetEditAddressParameters();//获取共享收货地址js函数参数
				$this->assign("jsApiParameters", $jsApiParameters);
				$this->assign("editAddress", $editAddress);
				$this->assign("orderNO", $da[1]);
				$this->assign("needPay", $needPay);
				
		        return $this->fetch('wxjspay');
		 //  }
			// if ($flag) {
				// return $this->fetch('order_pay_step3');
			// }

    }
	
	

}
