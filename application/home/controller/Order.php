<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/21
 * Time: 19:42
 */

namespace app\home\controller;


use think\Session;

class Order extends Base
{

    public function addOrder()
    {
        $totalmoney = input('post.totalmoney');
        $ordernum='';
        for ($i=0;$i<9;$i++) {
            $ordernum.=mt_rand(1, 9);
        }
        $cart = db('cart');
        $goods = model('Goods');

        //获取当前登陆用户id
        $user = Session::get('vipuser');
        $rs = $cart->where('userid',$user['userid'])->where('ispay',0)->select();
        $len = count($rs);

        //商品图片,价格
        $sum=0;
        for ($i=0;$i<$len;$i++){
            $res = $goods->where('goodsid',$rs[$i]['goodsid'])->find();
            //var_dump($res);exit();
            $rs[$i]['img'] = $res['goodsimg'];
            $rs[$i]['shopprice'] = $res['shopprice'];
            $rs[$i]['goodsname'] = $res['goodsname'];
            $sum+=$res['shopprice']*$rs[$i]['goodsnum'];
        }
        return view('order',compact('totalmoney','ordernum','rs'));
    }
    public function showorder()
    {
        $user = Session::get('vipuser');
        $cart = db('cart');
        $goods = model('Goods');

        //获取当前登陆用户id
        $user = Session::get('vipuser');
        $rs = $cart->where('userid',$user['userid'])->where('ispay',0)->select();
        $len = count($rs);

        //商品图片,价格
        $sum=0;
        for ($i=0;$i<$len;$i++){
            $res = $goods->where('goodsid',$rs[$i]['goodsid'])->find();
            //var_dump($res);exit();
            $rs[$i]['img'] = $res['goodsimg'];
            $rs[$i]['shopprice'] = $res['shopprice'];
            $rs[$i]['goodsname'] = $res['goodsname'];
            $rs[$i]['goodsid'] = $res['goodsid'];
            $sum+=$res['shopprice']*$rs[$i]['goodsnum'];
        }
        $orderlist = db('order')->where('userid',$user['userid'])->select();
        $appraises = db('appraises');
        $lenth=count($orderlist);
       for ($i=0;$i<$lenth;$i++){
            if ($appraises->where('orderid',$orderlist[$i]['orderid'])->find()){
                $orderlist[$i]['isappraises']=1;
            }
        }

        //var_dump($orderlist);exit();
        //查看是否有评价
        /*db('appraises')->where('orderid',)*/
        //var_dump($orderlist);exit();
        return view('myorderlist',compact('rs','orderlist'));
    }

    public function insertorderdata()
    {

        //需要的字段
        /*
         * 订单号，用户id，商品总金额，运费，订单总金额，未支付状态，收货人，收货地址，收货手机，积分，创建时间
         * */
        $user = Session::get('vipuser');
        $data = input('post.');
        $list = array();
        //var_dump($data);exit();
        //$gid = count($data['goodsid']);
        $list['goodsid'] = implode(',',$data['goodsid']);
        $list['ordernum'] = $data['ordernum'];
        $list['orderstatus'] = -2;
        $list['goodsmoney'] = $data['totalmoney'];
        $list['delivermoney'] = 0;
        $list['totalmoney'] = $data['totalmoney'];
        $list['ispay'] = 0;
        $list['username'] = $data['username'];
        $list['useraddress'] = $data['useraddress'];
        $list['userphone'] = $data['userphone'];
        $list['orderscore'] = 0;
        $list['isappraise'] = 0;
        $list['ordernum'] = $data['ordernum'];
        $list['dataflag'] = 1;
        $list['userid'] = $user['userid'];
        $list['create_time'] = date('Y-m-d H:i:s',time());
        //var_dump($list);exit();
        $rs = db('order')->insert($list);
        if ($rs){
            $this->success('请及时付款','home/index/index');
        }else{
            $this->error('网络错误');
        }


    }

    //确认收货
    public function comfirmorder()
    {
        $user = Session::get('vipuser');
        $data['orderstatus'] = 2;
        db('order')->where('userid',$user['userid'])->update($data);
        $this->success('你以确认收货，请对商品做出评价','product/pingjia');
    }
}