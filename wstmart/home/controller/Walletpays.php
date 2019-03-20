<?php
namespace wstmart\home\controller;
use wstmart\home\model\Payments as M;
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
 * 钱包支付控制器
 */
class Walletpays extends Base{
	public function Walletpay(){
		$m = new M();
		$userId = (int)session('WST_USER.userId');
		$orderNo = input("orderNo/s");
		$isBatch = (int)input("isBatch/d");
		$payPassword = input('payPassword/s');
		$needPay = $m->getPayOrders(['userId'=>$userId,'orderNo'=>$orderNo,'isBatch'=>$isBatch]);
		$user = db('users')->where('userId',$userId)->find();
		if(!$user['payPwd']){
			return WSTReturn('您还没有设置支付密码，请先设置',-2,url('home/users/security'));
		}else{
			//检测支付密码
			if($user['payPwd'] == md5($payPassword.$user['loginSecret'])){
				//钱包余额是否足够支付
				if($user['userMoney'] < $needPay){
					return WSTReturn('钱包余额不足',-1);
				}
				// 完成支付
				$cutMoney = db('users')->where('userId',$userId)->setDec('userMoney', $needPay);
				if($cutMoney){
					$obj = array();
					$obj["trade_no"] = $orderNo;
					$obj["out_trade_no"] = $orderNo;
					$obj["total_fee"] = $needPay;
					$obj["userId"] = $userId;
					$obj["isBatch"] = $isBatch;
					$obj["payFrom"] = 3;
					$rs = $m->complatePay($obj);
					return $rs;
				}else{
					return WSTReturn('支付失败',-1);
				}
			}else{
				return WSTReturn('支付密码错误',-1);
			}
		}
	}
}