<?php
namespace wstmart\common\model;
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
 * 资金流水业务处理器
 */
class LogMoneys extends Base{
    /**
    * 获取列表
    */
    public function pageQuery($targetType,$targetId){
        $type = (int)input('post.type',-1);
        $where['targetType'] = (int)$targetType;
        $where['targetId'] = (int)$targetId;
        if(in_array($type,[0,1]))$where['moneyType'] = $type;
        $page = $this->where($where)->order('id desc')->paginate()->toArray();
        foreach ($page['Rows'] as $key => $v){
            $page['Rows'][$key]['dataSrc'] = WSTLangMoneySrc($v['dataSrc']);
        }
        return $page;
    }
    public function pageQueryMob($targetType,$targetId){
        $type = (int)input('post.type',-1);
        $page = (int)input('post.page');
        $where['targetType'] = (int)$targetType;
        $where['targetId'] = (int)$targetId;
        if(in_array($type,[0,1]))$where['moneyType'] = $type;
        $data = $this->where($where)->order('id desc')->page($page,10)->select();
        foreach ($data as $k => $v){
            $data[$k]['dataSrc'] = WSTLangMoneySrc($v['dataSrc']);
        }
        return $data;
    }
}
