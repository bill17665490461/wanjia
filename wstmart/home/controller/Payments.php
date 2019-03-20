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
 */
class Payments extends Base{
	public function paySuc($id){
		$order = db('orders')->where('orderId',$id)->find();
		$m = new M();
		$obj = array();
		$obj["trade_no"] = $order['orderNo'];
		$obj["out_trade_no"] = $order['orderNo'];
		$obj["total_fee"] = $order['realTotalMoney'];
		$obj["userId"] = (int)session('WST_USER.userId');
		$obj["isBatch"] = 2;
		$obj["payFrom"] = 1;
		$rs = $m->complatePay($obj);
		dump($rs);exit;
	}
}
