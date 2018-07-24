<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/19
 * Time: 7:45
 */

namespace app\home\controller;


use think\Controller;

class Base extends Controller
{
    public $account;
    public function _initialize() {
        // 判定用户是否登录
        $isLogin = $this->isLogin();
        if(!$isLogin) {
             $this->redirect(url('home/login/login'));
        }
    }

    //判定是否登录
    public function isLogin() {
        // 获取sesssion
        $user = $this->getLoginUser();
        if($user) {
            return true;
        }
        return false;

    }

    public function getLoginUser() {
        if(!$this->account) {
            $this->account = session('vipuser', '');
        }
        return $this->account;
    }


}