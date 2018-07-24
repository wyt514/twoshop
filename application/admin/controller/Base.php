<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/19
 * Time: 7:45
 */

namespace app\admin\controller;


use think\Controller;

class Base extends Controller
{
    public $account;
    public function _initialize() {
        // 判定用户是否登录
        $isLogin = $this->isLogin();
        if(!$isLogin) {
            return $this->redirect(url('login/login'));
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
            $this->account = session('user', '');
        }
        return $this->account;
    }
}