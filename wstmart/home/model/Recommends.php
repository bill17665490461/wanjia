<?php
namespace wstmart\home\model;
use think\Db;

class Recommends extends Base{
	//首页获取推荐商铺
	function getRecShops(){
		$rs = $this->alias('r')->join('__SHOPS__ s','r.dataId=s.shopId','inner')
		           ->where(['dataSrc'=>1,'s.dataFlag'=>1,'shopStatus'=>1])
		           ->limit(5)
		           ->field('s.shopId,s.shopName,s.shopContent,s.areaIdPath,s.shopImg')->order('dataSort asc')
		           ->select();
		$areaIds = [];
		foreach ($rs as $key =>$v){
			$tmp = explode('_',$v['areaIdPath']);
    		$areaIds[] = $tmp[1];
    		$rs[$key]['areaId'] = $tmp[1];
		}
        $areaMap = [];
        $areas = Db::name('areas')->alias('a')->join('__AREAS__ a1','a1.areaId=a.parentId','left')
			           ->where('a.areaId','in',$areaIds)->field('a.areaId,a.areaName areaName2,a1.areaName areaName1')->select();
	    foreach ($areas as $v){
			$areaMap[$v['areaId']] = $v;
		}
		foreach ($rs as $key =>$v){
			$rs[$key]['areas'] = ['areaName1'=>$areaMap[$v['areaId']]['areaName1'],'areaName2'=>$areaMap[$v['areaId']]['areaName2']];
		}
		return $rs;  
	}
}