<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/21
 * Time: 9:43
 */

namespace app\home\controller;


use app\admin\controller\Base;
use think\Controller;
use wuliu\WlDemo;

class Wuliu
{
    public function index()
    {
        //在发货的时候选择合作的快递，select的值为快递简称---顺丰（sf）
        /*
         *
         *
         * */

        header('Content-type:text/html;charset=utf-8');
        $params = array(
                'key' => '745622be223c49224a86a703e01876d3', //您申请的快递appkey
                'com' => 'zto', //快递公司编码，可以通过$exp->getComs()获取支持的公司列表
                'no'  => '489073393109' //快递编号
            );
            $exp = new WlDemo($params['key']); //初始化类

            $result = $exp->query($params['com'],$params['no']); //执行查询

            if($result['error_code'] == 0){//查询成功
                $list = $result['result']['list'];
                return view('',compact('list'));
            }else{
                echo "获取失败，原因：".$result['reason'];
            }

    }
}