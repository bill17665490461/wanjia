<?php
namespace wstmart\home\controller;
use wstmart\home\model\Agencysettlements as M;
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
 * 结算控制器
 */
class Agencysettlements extends Base{

    public function agencyIndex(){
        return $this->fetch('agencys/settlements/agencyList');
    }
    /**
     * 获取区域商结算单
     */
    public function pageQueryAgency(){
        $m = new M();
        $rs = $m->pageQueryAgency();
        return WSTReturn('',1,$rs);
    }
    /**
     * 获取区域商待结算订单
     */
    public function agencyUnSettledQuery(){
        $m = new M();
        $rs = $m->agencyUnSettledQuery();
        return WSTReturn('',1,$rs);
    }
    /**
     * 区域商申请结算订单
     */
    public function agencySettlement(){
        $m = new M();
        return $m->agencySettlement();
   } 
    /**
    * 获取区域商已结算订单
    */
   public function agencySettledQuery(){
       $m = new M();
       $rs = $m->agencySettledQuery();
       return WSTReturn('',1,$rs);
   }
   /**
    * 查看结算详情
    */
   public function view(){
       $m = new M();
       $rs = $m->getById();
       $this->assign('object',$rs);
       return $this->fetch('agencys/settlements/view');
   }
}
