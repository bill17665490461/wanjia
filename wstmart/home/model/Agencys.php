<?php
namespace wstmart\home\model;
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
 * 区域代理商类
 */
use think\Db;
class Agencys extends Base{
	/**
     * 首页获取区域商信息
     */
	public function getById($agencyId){
		$data = $this->alias('ag')->join('__USERS__ u','ag.userId = u.userId')->where(['agencyId'=>$agencyId,'ag.dataFlag'=>1])->field('ag.noSettledOrderNum,ag.noSettledOrderFee,ag.realName,u.lastTime,u.userPhoto,agencyAddress,ag.telephone,ag.commissionRate')->find();
		$shopNum = model('shops')->where('agencyId='.$agencyId.' and dataFlag=1')->count();
		$data['shopNum']=$shopNum;
		return $data;
	}

    /**
     * 获取区域商指定字段
     */
    public function getFieldsById($agencyId,$fields){
        return $this->where(['agencyId'=>$agencyId,'dataFlag'=>1])->field($fields)->find();
    }
}
