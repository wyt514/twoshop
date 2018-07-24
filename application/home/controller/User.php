<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/17
 * Time: 11:32
 */

namespace app\home\controller;


use think\Controller;
use think\Request;

class User extends Controller
{
    private $obj = null;

    public function _initialize()
    {
        $this->obj = model('User');
    }

    public function register()
    {
        if (request()->isGet()){
            return view();
        }else{

           /* $dd = input('post.');
            $rs = $this->obj->checkData($dd);
            if (!$rs){
                $this->error();
            }*/
           if (!captcha_check(input('post.verifycode'))){
               $this->error('验证码错误');
           }
            //id,username,pass,sex,email,phone,last_login_time,last_login_ip,flag,rank
        $data = array();
        $data['username'] = filter_input(INPUT_POST,'username',FILTER_SANITIZE_STRING);

        $data['pass'] = md5(filter_input(INPUT_POST,'password',FILTER_SANITIZE_STRING).'lanco');
        $data['sex'] = filter_input(INPUT_POST,'sex',FILTER_VALIDATE_INT);
        $data['email'] = filter_input(INPUT_POST,'email',FILTER_VALIDATE_EMAIL);
            $data['phone'] = filter_input(INPUT_POST,'phone',FILTER_VALIDATE_INT);
        }

        if ($_SERVER['REMOTE_ADDR']) {//判断SERVER里面有没有ip，因为用户访问的时候会自动给你网这里面存入一个ip
            $cip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv("REMOTE_ADDR")) {//如果没有去系统变量里面取一次 getenv()取系统变量的方法名字
            $cip = getenv("REMOTE_ADDR");
        } elseif (getenv("HTTP_CLIENT_IP")) {//如果还没有在去系统变量里取下客户端的ip
            $cip = getenv("HTTP_CLIENT_IP");
        } else {
            $cip = "unknown";
        }
        $data['last_login_ip'] = $cip;
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $data['flag'] = 0;
        $data['rank'] = 0;
        $rs = $this->obj->save($data);
        if ($rs){
            //发送邮件信息

            $uid = $this->obj->getLastInsID();
            //$to,$title,$content
            //$url = request()->domain().url('home/user/verfiyEmail',['userid'=>$uid]);
            $url='http://localhost/twoshop/public/index.php/home/user/verfiyEmail?uid='.$uid;
            //$content = "茫茫人海，我们于2号店相遇即是缘分，请点击此处链接<a href=' ".$url." '>就是这里</a>";
            $content = "茫茫人海，我们于2号店相遇即是缘分，请点击此处链接<a href='#'>就是这里</a>";
            $res = \phpmailer\Email::send($data['email'],'欢迎来到2号店！^-^',$content);
            if (!$res){
                $this->error('发送邮件失败');
            }else{
                $this->success('注册成功，请求邮箱激活');
            }
        }else{
            $this->error('注册失败');
        }

    }

    public function verfiyEmail($uid)
    {
        $rs = $this->obj->where("userid=$uid")->find();
        if ($rs){
            $this->obj->save(['flag'=>1],['userid'=>$uid]);
            $this->redirect('User/test');
        }
    }

    public function findPass()
    {
        return view('findpass');
    }

    public function checkName($username)
    {
        $user = $this->obj->findByUserName($username);
        if ($user){
            $this->error('用户已存在',$user);

        }else{
            $this->success('用户名可用');
        }
    }

    //测试
    public function test()
    {
        echo config('extra.username');
        return view();
    }

    public function sendEmail()
    {
        
    }
}