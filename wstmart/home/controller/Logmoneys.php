<?php
namespace wstmart\home\controller;
use wstmart\common\model\LogMoneys as M;
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
 * 资金流水控制器
 */
class Logmoneys extends Base{
    /**
     * 查看用户资金流水
     */
	public function usermoneys(){
		$rs = model('Users')->getFieldsById((int)session('WST_USER.userId'),['lockMoney','userMoney']);
		$this->assign('object',$rs);
		return $this->fetch('users/logmoneys/list');
	}
    /**
     * 查看商家资金流水
     */
    public function shopmoneys(){
        $rs = model('Shops')->getFieldsById((int)session('WST_USER.shopId'),['lockMoney','shopMoney','noSettledOrderFee','paymentMoney','bankUserName','bankNo']);
        $this->assign('object',$rs);
        return $this->fetch('shops/logmoneys/list');
    }
    /**
     * 查看区域商资金流水
     */
    public function agencymoneys(){
        $rs = model('Agencys')->getFieldsById((int)session('WST_USER.agencyId'),['lockMoney','agencyMoney','noSettledOrderFee','paymentMoney','realName','bankUserName','bankNo']);
        $this->assign('object',$rs);
        return $this->fetch('agencys/logmoneys/list');
    }
    /**
     * 获取用户数据
     */
    public function pageUserQuery(){
        $userId = (int)session('WST_USER.userId');
        $data = model('logMoneys')->pageQuery(0,$userId);
        return WSTReturn("", 1,$data);
    }
    public function pageUserQueryMob(){
        $userId = (int)session('WST_USER.userId');
        $data = model('logMoneys')->pageQueryMob(0,$userId);
        return $data;
    }
    /**
     * 获取商家数据
     */
    public function pageShopQuery(){
        $shopId = (int)session('WST_USER.shopId');
        $data = model('logMoneys')->pageQuery(1,$shopId);
        return WSTReturn("", 1,$data);
    }
    /**
     * 获取区域商数据
     */
    public function pageAgencyQuery(){
        $agencyId = (int)session('WST_USER.agencyId');
        $data = model('logMoneys')->pageQuery(2,$agencyId);
        return WSTReturn("", 1,$data);
    }
}
