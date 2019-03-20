<?php
namespace wstmart\home\controller;
use wstmart\home\model\Agencys as A;
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
 * 门店控制器
 */

class Agencys extends Base{
	/**
	 * 区域商登录
	 */
	public function login(){
		$USER = session('WST_USER');
		if(!empty($USER) && isset($USER['agencyId']) && !isset($USER['shopId'])){
			$this->redirect("agencys/index");
		}
		$loginName = cookie("loginName");
		if(!empty($loginName)){
			$this->assign('loginName',cookie("loginName"));
		}else{
			$this->assign('loginName','');
		}
		return $this->fetch('agency_login');
	}
	/**
	 * 区域商中心
	 */
	public function index(){
		session('WST_MENID2',null);
		session('WST_MENUID32',null);
		$m = new A();
		$agency = $m->getById((int)session('WST_USER.agencyId'));
		$this->assign('agency',$agency);
		return $this->fetch('agencys/index');
	}

	//区域商店铺列表
	public function shopList(){
		// echo (int)session('WST_USER.agencyId');
		return $this->fetch('agencys/shopList');
	}
}
