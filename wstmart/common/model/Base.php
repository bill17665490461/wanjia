<?php
namespace wstmart\common\model;
use think\Model;
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
 * 基础模型器
 */

class Base extends Model {
	/**
	 * 获取空模型
	 */
	public function getEModel($tables){
		$rs =  Db::query('show columns FROM `'.config('database.prefix').$tables."`");
		$obj = [];
		if($rs){
			foreach($rs as $key => $v) {
				$obj[$v['Field']] = $v['Default'];
				if($v['Key'] == 'PRI')$obj[$v['Field']] = 0;
			}
		}
		return $obj;
	}
	/**
     * 检测是否使用手机访问
     * @access public
     * @return bool
     */
    public function isMobile()
    {
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }
    
    //修改详情描述中src地址
    public function changeImgSrc($str){
        $str = htmlspecialchars_decode($str);
        $rule = '/<img src="\/(upload.*?)"/';
        preg_match_all($rule, $str, $images);
        foreach($images[0] as $k=>$v){
            $str = str_replace($v, "<img src=\"__ROOT__/".WSTImg($images[1][$k],3)."\"", $str);
        }
        return $str;
    }
}