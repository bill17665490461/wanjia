<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\Shops as M;
use think\Db;
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
 * 店铺控制器
 */
class Shops extends Base{
    public function index(){
    	return $this->fetch("list");
    }
    public function stopIndex(){
    	return $this->fetch("list_stop");
    }
    public function listTrial(){
        return $this->fetch("list_trial");
    }
    /**
     * 获取分页
     */
    public function pageQuery(){
    	$m = new M();
    	return $m->pageQuery(1);
    }
    /**
     * 停用店铺列表
     */
    public function pageStopQuery(){
    	$m = new M();
    	return $m->pageQuery(-1);
    }
    /**
     * 待审核店铺列表
     */
    public function pageStopTrial(){
        $m = new M();
        return $m->pageQuery(-2);
    }
    /**
     * 修改待审状态
     */
    public function toAdopt(){
        $id = input('post.id/d');

        $result = Db::table('wst_shops')->where('shopId',$id)->setField('shopStatus',1);

        if(false !== $result){
            $userId = Db::table('wst_shops')->where('shopId',$id)->field('userId')->find();
            
            if ($id = Db::table('wst_users')->where('userId',$userId['userId'])->setField('userType',1)) {
                return WSTReturn("成功通过", 1);
            }

            
        }
        return WSTReturn('通过失败',-1);
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
    	    $object['applyId'] = 0;
    	    $data['object']=$object;
    	}else{
    		$object = $m->getEModel('shops');
    		$object['catshops'] = [];
    		$object['accreds'] = [];
    		$object['applyId'] = 0;
    		$object['loginName'] = '';
    		$data['object']=$object;
    	}

    	$data['goodsCatList'] = model('goodsCats')->listQuery(0);
    	$data['accredList'] = model('accreds')->listQuery(0);
    	$data['bankList'] = model('banks')->listQuery();
    	$data['areaList'] = model('areas')->listQuery(0);  
        $data['agencyList'] = $m->getAgencyList();
        $data['getListCats'] = model('goodsCats')->getListCats($object['CatIds']);
        $data['ListCats'] = explode('_',$object['CatIds']);
    	return $this->fetch("edit",$data);
    }
    /**
     * 查看审核页面
     */
    public function isTrial(){
        $m = new M();
        $id = (int)Input("get.id");
        if($id>0){
            $object = $m->getById((int)Input("get.id"));
            $object['applyId'] = 0;
            $data['object']=$object;
        }else{
            $object = $m->getEModel('shops');
            $object['catshops'] = [];
            $object['accreds'] = [];
            $object['applyId'] = 0;
            $object['loginName'] = '';
            $data['object']=$object;
        }
        $quality = explode(',', $object['quality']);
        $service = explode(',', $object['service']);
        $data['quality'] = $quality;
        $data['service'] = $service;
        //dump($data['service']);die;
        $data['goodsCatList'] = model('goodsCats')->listQuery(0);
        //$arr = model('goodsCats')->getTree();
        halt($data['goodsCatList']);
        $data['accredList'] = model('accreds')->listQuery(0);
        $data['bankList'] = model('banks')->listQuery();
        $data['areaList'] = model('areas')->listQuery(0);
        $data['agencyList'] = $m->getAgencyList();

        return $this->fetch("isTrial",$data);
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
            $m = new M();
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
    		   'areaList'=>model('areas')->listQuery(0),
               'agencyList'=>$m->getAgencyList()
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
    
    /**
     * 检测店铺编号是否存在
     */
    public function checkShopSn(){
    	$m = new M();
    	$isChk = $m->checkShopSn(input('post.shopSn'),input('shopId/d'));
        if(!$isChk){
    		return ['ok'=>'该店铺编号可用'];
    	}else{
    		return ['error'=>'对不起，该店铺编号已存在'];
    	}
    }
    
    /**
     * 自营店铺后台
     */
    public function inself(){
    	$staffId=session("WST_STAFF");
    	if(!empty($staffId)){
    		$id=1;
    		$s = new M();
    		$r = $s->selfLogin($id);
    		if($r['status']==1){
    			header("Location: ".Url('home/shops/index'));
    			exit();
    		}
    	}
    	header("Location: ".Url('home/shops/selfShop'));
    	exit();
    }

    //店铺充值
    public function recharge(){
        $m =new M();
        return $m->recharge();
    }
}
