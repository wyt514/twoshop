<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/18
 * Time: 23:16
 */

namespace app\admin\controller;


use think\Controller;
use think\Session;

class Login extends Controller
{
    public function login()
    {
        return view();
    }

    public function checkLogin()
    {

        $username = input('post.username','','htmlspecialchars');
        $password = input('post.password','','htmlspecialchars');
        $rs = db('roles')->where('rolename',$username)->find();
        if ($rs){
            if ($rs['rolepass'] === md5($password.'lanco')){
                //存储session

                session('user',$rs);
               // $rs = Session::get('user');
                //var_dump($rs);exit();
                $this->redirect('index/index');
            }else{
                $this->error('密码错误');
            }
        }else{
            $this->error('用户名错误');
        }
    }

    public function getSession()
    {
       $rs = Session::get('user');
        var_dump($rs);
    }

    public function logout()
    {
        //session
        Session::clear();
        $this->redirect('login/login');
    }
}