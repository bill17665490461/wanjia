<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\Agencys as M;
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
 * 区域商控制器
 */
class Agencys extends Base{
    public function index(){
    	return $this->fetch("list");
    }
    public function stopIndex(){
    	return $this->fetch("list_stop");
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
     * 跳去编辑页面
     */
    public function toEdit(){
    	$m = new M();
    	$id = (int)Input("get.id");
    	if($id>0){
    	    $object = $m->getById((int)Input("get.id"));
    	    $data['object']=$object;
    	}else{
    		$object = $m->getEModel('agencys');
    		$object['loginName'] = '';
    		$data['object']=$object;
    	}
    	$data['bankList'] = model('banks')->listQuery();
    	$data['areaList'] = model('areas')->listQuery(0); 
    	return $this->fetch("edit",$data);
    }
    /**
     * 跳去新增页面
     */
    public function toAddByApply(){
    	$apply = model('ShopApplys')->checkOpenShop((int)Input("get.id"));
    	if($apply['shopId']!=''){
    		$this->assign("msg",'对不起，该开店申请已处理！');
    		return $this->fetch("./message");
    	}else{
    		$object = model('ShopApplys')->getEModel('shops');
    		$object['userId'] = (int)$apply['userId'];
    		$object['applyId'] = (int)Input("get.id");
    		$object['loginName'] = '';
    		$object['catshops'] = [];
    		$object['accreds'] = [];
    		$data = [
    		   'object'=>$object,
    		   'goodsCatList'=>model('goodsCats')->listQuery(0),
    		   'accredList'=>model('accreds')->listQuery(0),
    		   'bankList'=>model('banks')->listQuery(),
    		   'areaList'=>model('areas')->listQuery(0)
    		];
	    	return $this->fetch("edit",$data);
    	}
    }
    /**
     * 新增菜单
     */
    public function add(){
    	$m = new M();
    	return $m->add();
    }
    /**
     * 编辑菜单
     */
    public function edit(){
    	$m = new M();
    	return $m->edit();
    }
    /**
     * 删除菜单
     */
    public function del(){
    	$m = new M();
    	return $m->del();
    }
    //区域商充值
    public function recharge(){
        $m =new M();
        return $m->recharge();
    }
    
}
