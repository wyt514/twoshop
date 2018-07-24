<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/19
 * Time: 23:00
 */

namespace app\admin\controller;


use think\Log;

class Order extends Base
{
    public function orderlist()
    {
       $order =  db('order');
       $data = $order->where('dataflag=1')->select();
       return view('','data');
    }

    public function fahuo()
    {
        $rs = db('order')->where('orderstatus',0)->paginate(10);
        return view('',compact('rs'));
    }

    public function changefahuo()
    {
        $data = input('post.');
        $data['orderstatus'] = 1;
        $rs = db('order')->where('orderid',$data['orderid'])->update($data);
        if ($rs){
            $this->success('ok');
        }else{
            $this->error('error');
        }
    }

    public function essearch()
    {
        $search = model('Search');
        //Log::record();
        $search->addOne();

    }
}