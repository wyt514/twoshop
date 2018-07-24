<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/19
 * Time: 21:52
 */

namespace app\home\controller;


use think\Controller;
use think\Session;

class Cart extends Controller
{
    public function cartdata()
    {
        return view('cart');
    }

    public function addgoods()
    {
       $data =  input('post.');
        /*
         *   'pid' => string '' (length=0)
              'goodsid' => string '19' (length=2)
              'userid' => string '1' (length=1)
              'goodsnum' => string '3' (length=1)
        */
        $res['goodsid'] = $data['goodsid'];
        $res['userid'] = $data['userid'];
        $res['goodsnum'] = $data['goodsnum'];
        $res['create_time'] = date('Y-m-d H:i:s',time());
        $cart = db('cart');
        $rs = $cart->insert($res);
        if ($rs){
        //跳转到购物车列表
            $this->success('加入购物车成功，请提交订单','cart/cartlist');
        }else{
            $this->error('加入购物车失败');
        }
    }

    public function cartlist()
    {
        $cart = db('cart');
        $goods = model('Goods');

        //获取当前登陆用户id
        $user = Session::get('vipuser');

        $rs = $cart->where('userid',$user['userid'])->where('isorder',0)->select();
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

        //


        /*if ($rs!=null){

        }else{

        }*/
       // var_dump($rs);exit();
        /*
         *
         * array (size=1)
  0 =>
    array (size=7)
      'id' => int 1
      'userid' => int 1
      'goodsid' => int 10
      'goodsnum' => int 1
      'create_time' => string '2018-04-21 18:47:09' (length=19)
      'img' => string '/upload\20180419\d324e7cd9b2bec8c59b74ea26d483cc9.PNG' (length=53)
      'shopprice' => string '111.00' (length=6)
         * */
        return view('',compact('rs','sum'));
    }

    public function deletecartitem()
    {
        $id =  input('post.id');
        //var_dump($id);exit();
        $cart = db('cart');
        $rs = $cart->delete($id);
        if ($rs){
            echo json_encode(['msg'=>'删除ok']);
        }else{
            echo json_encode(['msg'=>'删除error']);
        }
    }




}