<?php
namespace wstmart\home\controller;
use wstmart\common\model\CashDraws as M;
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
 * 提现记录控制器
 */
class Cashdraws extends Base{
    /**
     * 查看用户资金流水
     */
	public function index(){
        $rs = model('Users')->getFieldsById((int)session('WST_USER.userId'),['lockMoney','userMoney','payPwd']);
        $this->assign('object',$rs);
        if($this->isMobile()){
            $userId = (int)session('WST_USER.userId');
            $cards = model('CashConfigs')->pageQueryMob(0,$userId);
            $this->assign('cards',$cards);
        }
		return $this->fetch('users/cashdraws/list');
	}
    /**
     * 获取用户数据
     */
    public function pageQuery(){
        $userId = (int)session('WST_USER.userId');
        $data = model('CashDraws')->pageQuery(1,$userId);
        return WSTReturn("", 1,$data);
    }
    public function pageQueryMob(){
        $userId = (int)session('WST_USER.userId');
        $data = model('CashDraws')->pageQueryMob(1,$userId);
        return $data;
    }

    /**
     * 跳转提现页面
     */
    public function toEdit(){
        $this->assign('accs',model('CashConfigs')->listQuery(0,(int)session('WST_USER.userId')));
        return $this->fetch('users/cashdraws/box_draw');
    }

    /**
     * 会员提现
     */ 
    public function drawMoney(){
        $m = new M();
        return $m->drawMoney();
    }

    /**
     * 店铺提现
     */ 
    public function shopDrawMoney(){
        $m = new M();
        return $m->shopDrawMoney();
    }
    /**
     * 区域商提现
     */ 
    public function agencyDrawMoney(){
        $m = new M();
        return $m->agencyDrawMoney();
    }
}
