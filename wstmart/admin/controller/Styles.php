<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\Styles as M;
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
 * 风格配置控制器
 */
class Styles extends Base{
	
    public function index(){
        $m = new M();
        $rs = $m->getCats();
        $this->assign('cats',$rs);
    	return $this->fetch();
    }
    /**
     * 获取风格列表
     */
    public function listQueryBySys(){
        $m = new M();
        $rs = $m->listQuery();
        return WSTReturn('',1,$rs);
    }
    
    /**
     * 保存
     */
    public function changeStyle(){
    	$m = new M();
    	return $m->changeStyle();
    }
}