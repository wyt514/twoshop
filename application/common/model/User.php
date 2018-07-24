<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/16
 * Time: 22:16
 */
namespace app\common\model;
use think\Model;
class User extends Model
{

    protected $table = 'shop_user';
    protected $updateTime = false;
    public function findByUserName($username)
    {
        return $this->where("username=$username")->find();
    }

    public function checkData($data)
    {
        $rs = $this->validate('Register.register')->save($data);
        if(false === $rs){
            // 验证失败 输出错误信息
            dump($this->getError());
        }
    }

    public function userlist()
    {
        $con['flag'] = ['<>',-1];
        //return $this->where($con)->fetchSql(true)->paginate(1);
        return $this->where($con)->paginate(2);
    }

    //违规删除的会员

    public function deleteuserlist()
    {
        //$con['flag'] = ['<>',-1];
        //return $this->where($con)->fetchSql(true)->paginate(1);
        return $this->where('flag=-1')->paginate(2);
    }



}