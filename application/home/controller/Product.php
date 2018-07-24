<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/19
 * Time: 7:45
 */

namespace app\home\controller;


use think\Controller;
use think\Session;

class Product extends Controller
{
    public function specificproduct($goodsid)
    {
        $category = model('Category');
        $goods = model('Goods');
        /*
         * 获取所有的分类信息子孙树
         * */
        $con['flag']=1;
        $con['parentid']=0;
        $cats = $category->where($con)->limit(0,10)->select();
        $gooddata = $goods->where('goodsid',$goodsid)->find();

        //获取到catid，查询相关信息

        return view('',compact('cats','gooddata'));
     }

    public function pingjia()
    {
        $category = model('Category');
        $featured = db('featured');
        /*
         * 获取所有的分类信息子孙树
         * */
        $con['flag']=1;
        $con['parentid']=0;

        //一级分类前十条
        $cats = $category->where($con)->limit(0,10)->select();
        $ordernum = input('param.ordernum');

        //db('order')->where()
        return view('',compact('ordernum','cats'));
     }

    public function addpingjia()
    {

        $data['goodsscore'] = input('post.check');
        //$data['wl'] = input('post.wuliu');
        //$data['ms'] = input('post.ms');
       // $data['fh'] = input('post.fh');
        $data['desc'] = input('post.desc');
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $ordernum = input('post.ordernum');
        $user = Session::get('vipuser');
        $data['userid'] = $user['userid'];

        $order = db('order')->where('ordernum',$ordernum)->find();
        $data['orderid'] =$order['orderid'];
        $goodsid =  explode(',',$order['goodsid']);
        $appriase = db('appraises');
        $len = count($goodsid);
        for ($i=0;$i<$len;$i++){
            $data['goodsid'] = $goodsid[$i];
            $appriase->insert($data);
        }
        $this->success('评价成功');
       //$appriase->insertAll()
        //var_dump($data);exit();
     }

    public function showprodulist()
    {
        return view();
     }

    public function search()
    {
        $data = input('post.search');
        $rs = db('goods')->where('goodsname','like','%'.$data.'%')->select();
        //var_dump($rs);exit();
        return view('showprodulist',compact('rs'));
     }
}