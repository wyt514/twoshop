<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/18
 * Time: 19:46
 */

namespace app\common\model;
use think\Model;

class Goods extends Model
{
    protected $table = 'shop_goods';
    protected $autoWriteTimestamp = false;
    public function addGoods($data)
    {
        if ($data){
            $rs = $this->save($data);
            return $rs;
        }else{
            return false;
        }
    }

    public function goodsData()
    {
        $data = $this->order('goodsid desc')->paginate(10);
        return $data;
    }
}