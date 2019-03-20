<?php
namespace wstmart\home\controller;
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
 * 默认控制器
 */
class Index extends Base{
	
    public function index(){
        // $shops = model('Recommends')->getRecShops();
        // foreach ($shops as $key => $v) {
        //     $shopContent = htmlspecialchars_decode($v['shopContent']);
        //     $rule = '/<img src="\/(upload.*?)"/';
        //     preg_match_all($rule, $shopContent, $images);
        //     foreach($images[0] as $k=>$v){
        //         $shopContent = str_replace($v, "<img src=\"__ROOT__/".WSTImg($images[1][$k],3)."\"", $shopContent);
        //     }
        //     $shops[$key]['shopContent']=$shopContent;
        // }
        // $this->assign('recShops',$shops);
        //获取已有入驻果园的省份及果园
        $sheng_shops = model('shops')->getAreaShops();
        $shengNames = $sheng_shops[1];
        $areaShops = $sheng_shops[0];
        $this->assign("shengNames",$shengNames);
        $this->assign("areaShops",$areaShops);
    	$categorys = model('GoodsCats')->getFloors();
    	$this->assign('floors',$categorys);
    	$this->assign('hideCategory',1);
		
		
    	return $this->fetch('index');
    }

   /** 手机端分类
     * @return mixed
     */
    public function classify(){
        
        return $this->fetch('classify');
    }

    /**
     * 手机端区域选择
     */
    public function ergion(){
        $array = $this->getAddress();

        $this->assign('province',$array['province']);
        $this->assign('city',$array['city']);
        $this->assign('county',$array['county']);
        return $this->fetch('ergion');

    }
    /**
     * 保存目录ID
     */
    public function getMenuSession(){
    	$menuId = input("post.menuId");
    	$menuType = session('WST_USER.loginTarget');
    	session('WST_MENUID3'.$menuType,$menuId);
    } 
    /**
     * 获取用户信息
     */
    public function getSysMessages(){
    	$rs = model('Systems')->getSysMessages();
    	return $rs;
    }
    /**
     * 定位菜单以及跳转页面
     */
    public function position(){
    	$menuId = (int)input("post.menuId");
    	$menuType = ((int)input("post.menuType")==1)?1:0;
    	session('WST_MENUID3'.$menuType,$menuId);
    }
}
