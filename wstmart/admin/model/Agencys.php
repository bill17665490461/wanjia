<?php
namespace wstmart\admin\model;
use think\Db;
use think\Loader;
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
 * 地区业务处理
 */
class Agencys extends Base{
	/**
	 * 分页
	 */
	public function pageQuery(){
		return Db::table('__AGENCYS__')->alias('s')->join('__AREAS__ a2','s.areaId=a2.areaId','left')
		       ->where(['s.dataFlag'=>1])
		       ->field('agencyId,realName,a2.areaName,telephone,agencyAddress,agencyStatus,commissionRate,agencyMoney')
		       ->order('agencyId desc')->paginate(input('pagesize/d'));
	}
	/**
	 * 获取区域商信息
	 */
	public function getById($id){
		$shop = $this->get(['dataFlag'=>1,'agencyId'=>$id])->toArray();
		return $shop;
	}
	/**
	 * 新增
	 */
	public function add(){
		$userId = 0;
		//如果是游客的话就要检测一下账号是否存在
		if($userId==0){
			$user = [];
			$user['loginName'] = Input('post.loginName');
			$user['loginPwd'] = Input('post.loginPwd');
			$ck = WSTCheckLoginKey($user['loginName']);
			if($ck['status']!=1)return $ck;
			if($user['loginPwd']=='')$user['loginPwd'] = '88888888';
			$user["loginSecret"] = rand(1000,9999);
	    	$user['loginPwd'] = md5($user['loginPwd'].$user['loginSecret']);
	    	$user["userType"] = 2;
	    	$user['createTime'] = date('Y-m-d H:i:s');
		}
    	$validate = Loader::validate('agencys');
        if(!$validate->check(Input('post.')))return WSTReturn($validate->getError());

        Db::startTrans();
        try{
        	//如果是游客的话就先新增会员资料
        	if($userId==0){
	            model('users')->save($user);
	            $userId = model('users')->userId;
        	}else{
        		model('users')->where('userId',$userId)->update(['userType'=>2]);
        	}
	        $data = Input('post.');
	        $data['createTime'] = date('Y-m-d H:i:s');
	        //获取地区
	        $areaIds = model('Areas')->getParentIs($data['areaId']);
		    if(!empty($areaIds))$data['areaIdPath'] = implode('_',$areaIds)."_";
		    $areaIds = model('Areas')->getParentIs($data['bankAreaId']);
		    if(!empty($areaIds))$data['bankAreaIdPath'] = implode('_',$areaIds)."_";		    
	        WSTUnset($data,'agencyId,dataFlag');
	        $data['userId'] = $userId;
	        $agencyId = 0;
	        if($userId>0){
	        	$this->allowField(true)->save($data);
	        	$agencyId = $this->agencyId;
	        	//启用上传图片
				// WSTUseImages(1, $agencyId, $data['shopImg']);
	        }
	        Db::commit();
	        return WSTReturn("新增成功", 1);
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('新增失败',-1);
        }
	}
	/**
	 * 编辑
	 */
	public function edit(){
		$agencyId = input('post.agencyId/d',0);
		$validate = Loader::validate('Agencys');
        if(!$validate->check(Input('post.')))return WSTReturn($validate->getError());
        Db::startTrans();
        try{
	        $data = input('post.');
	        //获取地区
	        $areaIds = model('Areas')->getParentIs($data['areaId']);
		    if(!empty($areaIds))$data['areaIdPath'] = implode('_',$areaIds)."_";
		    $areaIds = model('Areas')->getParentIs($data['bankAreaId']);
		    if(!empty($areaIds))$data['bankAreaIdPath'] = implode('_',$areaIds)."_";
	        WSTUnset($data,'agencyId,userId,dataFlag,createTime');
	        //启用上传图片
			// WSTUseImages(1, $shopId, $data['shopImg'],'shops','shopImg');
	        $this->allowField(true)->save($data,['agencyId'=>$agencyId,'dataFlag'=>1]);		    
	        
	        Db::commit();
	        return WSTReturn("编辑成功", 1);
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('编辑失败',-1);
        }
	}
	/**
	 * 删除
	 */
    public function del(){
	    $id = input('post.id/d');
		$data = [];
		$data['dataFlag'] = -1;
	    Db::startTrans();
        try{
	        $result = $this->update($data,['agencyId'=>$id]);
	        // WSTUnuseImage('agencys','shopImg',$id);
            if(false !== $result){
            	$userId = $this->where('agencyId='.$id)->value('userId');
            	$res = model('users')->where('userId='.$userId)->update(['userType'=>0]);
            	Db::commit();
        	    return WSTReturn("删除成功", 1);
            }
        }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('删除失败',-1);
	}

	//区域商充值
	public function recharge(){
		$agencyId = (int)input('post.agencyId');
		$money = input('post.money');
		$payType = input('post.payType');
		$rs = db('agencys')->where('agencyId',$agencyId)->setInc('agencyMoney', $money);	
		// $rs = true;	
		//创建充值记录
		if($rs){
			$realName = db('agencys')->where('agencyId',$agencyId)->value('realName');
			$staff = session('WST_STAFF');
			$lm = [];
			$lm['targetType'] = 2;
			$lm['targetId'] = $agencyId;
			$lm['dataSrc'] = 6;
			$lm['remark'] = $staff['roleName'].'【'.$staff['staffName'].'】向区域商【'.$realName.'】充值¥'.$money;
			$lm['moneyType'] = 1;
			$lm['money'] = $money;
			$lm['payType'] = $payType;
			$lm['createTime'] = date('Y-m-d H:i:s');
			model('LogMoneys')->save($lm);
			return WSTReturn('充值成功',1);
		}else{
			return WSTReturn('充值失败',-1);
		}
	}
}