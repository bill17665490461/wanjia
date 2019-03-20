<?php
namespace wstmart\home\controller;
use wstmart\home\model\Goods;
use wstmart\common\model\GoodsCats;
use wstmart\home\model\Shops as S;
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
 * 门店控制器
 */

class Shops extends Base{
	/**
	 * 商家登录
	 */
	public function login(){
		$USER = session('WST_USER');
		if(!empty($USER) && isset($USER['shopId'])){
			echo 1;die;$this->redirect("shops/index");
		}
		$loginName = cookie("loginName");
		if(!empty($loginName)){
			$this->assign('loginName',cookie("loginName"));
		}else{
			$this->assign('loginName','');
		}
		return $this->fetch('shop_login');
	}
	/**
	 * 商家中心
	 */
	public function index(){
		session('WST_MENID1',null);
		session('WST_MENUID31',null);
		$s = model('shops');
		$data = $s->getShopSummary((int)session('WST_USER.shopId'));
		$this->assign('data',$data);
		return $this->fetch('shops/index');
	}
    /**
     * 店铺街
     */
    public function shopStreet(){
    	$g = new GoodsCats();
    	$goodsCats = $g->listQuery(0);
    	$this->assign('goodscats',$goodsCats);
    	//店铺街列表
    	$s = model('shops');
    	$pagesize = 10;
    	$selectedId = input("get.id/d");
    	$this->assign('selectedId',$selectedId);
    	$list = $s->pageQuery($pagesize);
        //dump($list);die;
        if($this->isMobile()){
            $pages = $s->getPages($pagesize);
            $this->assign('pages',$pages);
        }
    	$this->assign('list',$list);
    	$this->assign('keyword',input('keyword'));
    	$this->assign('keytype',1);
    	return $this->fetch('shop_street');
    }
    /**
     * 店铺详情
     */
    public function home(){
    	$s = model('shops');
    	$shopId = (int)input("param.shopId/d");
        $goodsId = db('goods')->where('shopId',$shopId)->value('goodsId');
        $this->redirect('goods/detail',['id'=>$goodsId]);
    	$data['shop'] = $s->getShopInfo($shopId);        
        //果园介绍
        $shopContent = $s->getContent((int)session('WST_USER.shopId'));
        $shopContent = htmlspecialchars_decode($shopContent);
        $rule = '/<img src="\/(upload.*?)"/';
        preg_match_all($rule, $shopContent, $images);
        foreach($images[0] as $k=>$v){
            $shopContent = str_replace($v, "<img src=\"__ROOT__/".WSTImg($images[1][$k],3)."\"", $shopContent);
        }
        $this->assign('shopContent',$shopContent);

        $ct1 = input("param.ct1/d",0);
        $ct2 = input("param.ct2/d",0);
        $goodsName = input("param.goodsName");
        if(($data['shop']['shopId']==1 || $shopId==0) && $ct1==0 && !isset($goodsName)){
            if(!$this->isMobile())
            $this->redirect('shops/selfShop');
        }
        
    	if(empty($data['shop']))return $this->fetch('error_lost');
    	$data['shopcats'] = $f = model('ShopCats','model')->getShopCats($shopId);
    	$g = model('goods');
        if($this->isMobile()){
            $shopGoods = $g->mobshopGoods($shopId);
            $this->assign('shopGoods',$shopGoods);
        }
    	$data['list'] = $g->shopGoods($shopId);
    	$this->assign('msort',input("param.msort/d",0));//筛选条件
    	$this->assign('mdesc',input("param.mdesc/d",1));//升降序
    	$this->assign('sprice',input("param.sprice"));//价格范围
    	$this->assign('eprice',input("param.eprice"));
    	$this->assign('ct1',$ct1);//一级分类
    	$this->assign('ct2',$ct2);//二级分类
    	$this->assign('goodsName',urldecode($goodsName));//搜索
    	$this->assign('data',$data);
    	return $this->fetch('shop_home');
    }
    
    /**
     * 查看店铺设置
     */
    public function info(){
    	$s = model('shops');
    	$object = $s->getByView((int)session('WST_USER.shopId'));
    	$this->assign('object',$object);
    	return $this->fetch('shops/shops/view');
    }
    /**
    * 自营店铺
    */
    public function selfShop(){
        $s = model('shops');
        $data['shop'] = $s->getShopInfo(1);
        if(empty($data['shop']))return $this->fetch('error_lost');
        $this->assign('selfShop',1);
	    $data['shopcats'] = model('ShopCats')->getShopCats(1);
	    $this->assign('goodsName',urldecode(input("param.goodsName")));//搜索
	    // 店长推荐
	    $data['rec'] = $s->getRecGoods('rec');
	    // 热销商品
	    $data['hot'] = $s->getRecGoods('hot');
	    $this->assign('data',$data);
	    return $this->fetch('self_shop');
    }
    /**
    * 区域商店铺列表
    */
    public function agencyShops(){
        $agencyId = (int)session('WST_USER.agencyId');
        $m = new S();
        $rs = $m->agencyShops($agencyId);
        return $rs;
    }
    /**
    * 果园介绍
    */
    public function shopContent(){
        $s = model('shops');
        $object = $s->getContent((int)session('WST_USER.shopId'));
        $object = htmlspecialchars_decode($object);
        $rule = '/<img src="\/(upload.*?)"/';
        preg_match_all($rule, $object, $images);
        foreach($images[0] as $k=>$v){
            $object = str_replace($v, "<img src=\"__ROOT__/".WSTImg($images[1][$k],3)."\"", $object);
        }
        $this->assign('object',$object);
        return $this->fetch('shops/shops/shopContent');
    }
    /**
    * 保存果园介绍
    */
    public function contentSave(){
        $s = model('shops');
        $rs = $s->contentSave((int)session('WST_USER.shopId'));
        return $rs;
    }

    /**
     * 商家开店
     */
    public function shopCegister(){
        $userId = (int)session('WST_USER.userId');
        if (empty($userId)){
            return WSTReturn('11111');
        }
        return WSTReturn('OK',1);
    }

   /**
     * 商家开店
     */
    public function shopCegisters(){
        
        return  $this->fetch('shopCegister');
    }

    /**
     * AJAX获取子栏目列表
     */
    public function listQuery(){
        $id = (int)$_GET['id'];
        if(empty($id)){
           return false;
        }
        $result = Db::name('goods_cats')->where(['dataFlag'=>1,'parentId'=>$id])->select();
        if(empty($result)) {
            return false;
        }
        return $result;
    }

    public function shopsRegister()
    {
        if (!request()->isPost()) {
            return $this->error("非法访问");
        }
        $userId = (int)session('WST_USER.userId');
        if (empty($userId)){
            return WSTReturn('11111');
        }
        $data['shopName'] = input('post.shopName') ? input('post.shopName') : '';
        $data['shopkeeper'] = input('post.shopkeeper') ? input('post.shopkeeper') : '未填写';
        $data['telephone'] = input('post.telephone');
        $data['shopTel'] = input('post.telephone');
        $data['CatIds'] = input('post.catIds1').'_'.input('post.CatIds2').'_'.input('post.CatIds3');
        
        $catIds1 = input('post.catIds1');

        $data['establishtime'] = input('post.establishtime');
        $data['matureMonth'] = input('post.maturetime');
        $data['PickMonthFrom'] = input('post.PickMonthFrom');
        $data['PickDayFrom'] = input('post.PickDayFrom');
        $data['PickMonthTo'] = input('post.PickMonthTo');
        $data['PickDayTo'] = input('post.PickDayTo');
        //$data['PicktimeTo'] = input('post.Picktime2');
        //$data['Picktime'] = input('post.Picktime1');// . '-' . input('post.Picktime2');
        $data['Packing_Specifications'] = input('post.shopName');
        $data['yield'] = input('post.yield');
        //$data['quality'] = implode(',', $_POST['quality']);
        $quality = $_POST['quality'] ? $_POST['quality'] : 0;
        $data['shopAddress'] = input('post.shopAddress');
        $data['Grade'] = input('post.Grade');
        $data['service'] = implode(',', $_POST['service']);
        $data['userId'] = $userId;
        $data['shopStatus'] = -2;

        $city = explode('-', input('post.city'));
        $areaIds = '';
        if (is_array($city)) {

            for ($i = 0; $i < count($city); $i++) {
                $areaId = Db::query("select areaId from wst_areas where areaName='{$city[$i]}'ORDER BY areaId desc");
                if(!empty($areaId)){
                    $areaIds .= $areaId[0]['areaId'] . '_';
                    $data['areaId'] = $areaId[0]['areaId'];
                    $data['bankAreaId'] = $areaId[0]['areaId'];
                }

            }
        }

        $data['areaIdPath'] = $areaIds;
        $data['bankAreaIdPath'] = $areaIds;

        $result = Db::name('shops')->field('shopSn,shopId')->order('shopId desc')->find();
        if(!empty($result)){
            $shopSn = substr(implode(',',explode('S',$result['shopSn'])),2)+1;
            $data['shopSn'] = 'S00000000'.$shopSn;
        }else{
            $data['shopSn'] = 'S000000001';
        }
        if(Db::name('shops')->insert($data)){

            $shopId = Db::name('shops')->getLastInsID();

            $arr = array('shopId' => $shopId, 'totalUsers' => $userId);

            Db::name('shop_scores')->insert($arr);

            //经营范围
            if($catIds1 != 0){
                Db::name('cat_shops')->insert(['shopId'=>$shopId,'catId'=>$catIds1]);
            }

            //认证类型
            if($quality!=''){
                //dump($quality);
                //$accreds = explode(',',$quality);
                foreach ($quality as $v){
                    if((int)$v>0)Db::name('shop_accreds')->insert(['shopId'=>$shopId,'accredId'=>$v]);
                }
            }
            //水果品种
            if($data['CatIds']!=''){
                $accreds = explode('_',$data['CatIds']);
                foreach ($accreds as $key =>$v){
                    if((int)$v>0){
                        Db::name('shop_accreds')->insert(['shopId'=>$shopId,'accredId'=>$v]);
                    }
                }
            }

            //修改用户状态
            Db::name('users')->where('userId',$shopId)->setField('userType', 1);
            $this->success('注册成功，请登录','login',0);
        }else{
            $this->error('注册失败');
        }
    }
    /**
     * 判断果园名称是否存在
     */
    public function isShopName(){
        $text = $_POST['text'];
        $data = model('shops')->getIsShopName($text);
        return WSTReturn('',$data['shopStatus']);
    }
}
