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
 * 基础控制器
 */
use think\Controller;
use think\Db;
class Base extends Controller {
	public function __construct(){
		parent::__construct();
		$this->assign("v",WSTConf('CONF.wstVersion')."_".WSTConf('CONF.wstPCStyleId'));
        $userId = (int)session('WST_USER.userId');
        $msg_num = Db::name('messages')->where(['receiveUserId'=>$userId,'msgStatus'=>0])->count();
        $this->assign('msg_num',$msg_num);
		$data = array();
		$data['TabareaId'] = (int)Input('areaId');
		
		$aModel = model('home/areas');
        // 获取地区
        $data['Tabarea1'] = $data['Tabarea2'] = $data['Tabarea3'] = $aModel->listQuery(); // 省级
        // 如果有筛选地区 获取上级地区信息
	
        if($data['TabareaId']!==0){
            $areaIds = $aModel->getParentIs($data['TabareaId']);
		
            $selectArea = [];
            $areaName = '';
            foreach($areaIds as $k=>$v){
                $a = $aModel->getById($v);
                $areaName .=$a['areaName'];
                $selectArea[] = $a;
            }
            // 地区完整名称
            $selectArea['areaName'] = $areaName;
            // 当前选择的地区
            $data['TabareaInfo'] = $selectArea;

            $data['area2'] = $aModel->listQuery($areaIds[2]); // 广东的下级
 
            $data['area3'] = $aModel->listQuery($areaIds[1]); // 广州的下级
		 $this->assign('TabareaInfo',$data['TabareaInfo']);
        }
		 $this->assign('Tabarea3',$data['Tabarea3']);
		 $this->assign('Tabarea2',$data['Tabarea2']);
		 $this->assign('Tabarea1',$data['Tabarea1']);
		 //dump($data['Tabarea1']);
		 $this->assign('TabareaId',$data['TabareaId']);
		 
		
		
	}

	protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
    	$style = WSTConf('CONF.wsthomeStyle')?WSTConf('CONF.wsthomeStyle'):'default';
    	if($this->isMobile()){
			$style = 'mobile';
			$replace['__STYLE__'] = str_replace('/index.php','',\think\Request::instance()->root()).'/wstmart/home/view/mobile';
		}else{
			$replace['__STYLE__'] = str_replace('/index.php','',\think\Request::instance()->root()).'/wstmart/home/view/'.WSTConf('CONF.wsthomeStyle');
		}
        return $this->view->fetch($style."/".$template, $vars, $replace, $config);
    }

	/**
	 * 上传图片
	 */
	public function uploadPic(){
		return WSTUploadPic(0);
	}
    /**
     * 手机端压缩上传图片
     */
    public function mobUploadPic(){
        return mobUploadPic();
    }
	/**
    * 编辑器上传文件
    */
    public function editorUpload(){
           return WSTEditUpload(0);
    }
	
	/**
	 * 获取验证码
	 */
	public function getVerify(){
		WSTVerify();
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


     /**
     * 手机端获取地址
     */
    public function getAddress($parentId = 0)
    {
        //获取资源
        $array['province'] = Db::name('areas')->where('parentId', 0)->field('areaId,areaName,parentId')->select();
        $arr = array();$str = '';$strs ='';$falst = '';$s = '';
        if (!empty($array['province'])) {
            //获取一级地址
           foreach ($array['province'] as $k => $v) {

               $falst .=$v['areaId'].',';
           }
           $falst = rtrim($falst, ',');
           $array['city'] = Db::query("select areaId,areaName,parentId from wst_areas where  parentId in($falst)");

            foreach ($array['city'] as $k => $v) {

                $s .=$v['areaId'].',';
            }
            $s = rtrim($s, ',');
            $array['county'] = Db::query("select areaId,areaName,parentId from wst_areas where  parentId in($s)");

        }

        return $array;

    }

}