<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\ShopApplys as M;
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
 * 店铺申请控制器
 */
class Shopapplys extends Base{
    public function index(){
    	return $this->fetch("list");
    }
    
    /**
     * 获取分页
     */
    public function pageQuery(){
    	$m = new M();
    	return $m->pageQuery();
    }
    /**
     * 获取菜单
     */
    public function get(){        
    	$m = new M();
    	return $m->get((int)Input("post.id"));
    }
    /**
     * 跳去处理页面
     */
    public function toHandle(){
    	$m = new M();
    	$rs = $m->getById((int)Input("get.id"));
    	$this->assign("object",$rs);
    	return $this->fetch("edit");
    }
    /**
     * 编辑菜单
     */
    public function handle(){
    	$m = new M();
    	return $m->handle();
    }
    /**
     * 删除菜单
     */
    public function del(){
    	$m = new M();
    	return $m->del();
    }
}