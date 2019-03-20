<?php 
namespace wstmart\admin\validate;
use think\Validate;
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
 * 店铺验证器
 */
class Agencys extends Validate{
	protected $rule = [
        ['realName'  ,'require|max:100','请输入真实姓名|真实姓名不能超过50个字符'],
        ['telephone'  ,'require|max:40','请输入联系手机|联系手机不能超过20个字符'],
        ['commissionRate'  ,'require','请输入佣金'],
        ['areaId'  ,'require','请选择所在区域'],
        ['agencyAddress'  ,'require','请输入详细地址'],
        ['bankId'  ,'require','请选择结算银行'],
        ['bankAreaId'  ,'require','请选择开户所地区'],
        ['bankNo'  ,'require','请选择银行账号'],
        ['bankUserName'  ,'require|max:100','请输入持卡人名称|持卡人名称长度不能能超过50个字符'],
        ['agencyStatus'  ,'in:-1,1','无效的店铺状态'],
        ['statusDesc'  ,'checkStatusDesc:1','请输入停止原因']
    ];

    protected $scene = [
        'add'   =>  ['realName','telephone','commissionRate','areaId','agencyAddress','bankId','bankAreaId','bankNo','bankUserName'],
        'edit'  =>  ['realName','telephone','commissionRate','areaId','agencyAddress','bankId','bankAreaId','bankNo','bankUserName']
    ];
    
    protected function checkStatusDesc($value){
    	$agencyStatus = Input('post.agencyStatus/d',0);
    	$key = Input('post.statusDesc');
    	return ($agencyStatus==-1 && $key=='')?'请输入停止原因':true;
    }
}