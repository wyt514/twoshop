<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/18
 * Time: 21:01
 */

namespace app\home\controller;


use think\Controller;
use \aliyunmsg\SignatureHelper;
use think\Session;

class Login extends Controller
{

    private $obj = null;

    public function _initialize()
    {
        $this->obj = model('User');
    }
    //登录逻辑处理
    public function login()
    {
        if (request()->isGet()){
            return view();
        }elseif(request()->isPost()){
            //登录信息处理
            /*
             * 查找用户名
             * 比对密码
             * 写入session
             * */
            $username = input('post.username','','htmlspecialchars');
            $password= input('post.password','','htmlspecialchars');
            $user = $this->obj->where('username',$username)->find();
            if ($user){
                $checkpass = md5($password.'lanco');
                if ($user['pass'] ===$checkpass){
                    //这里要判断有没有勾选记住密码，写cookie，session处理
                    if ($user['flag']!=1){
                        $this->error('请激活');
                    }
                    $usersession['usernname'] = $user['username'];
                    $usersession['userid'] = $user['userid'];
                    session('vipuser',$usersession);
                   // $this->redirect('home/index/index',['username'=>$username]);
                    $this->redirect('home/index/index');

                }else{
                    $this->error('用户名或密码错误');
                }
            }else{
                $this->error('用户不存在');
            }

        }

    }

    //注销登录
    public function logout()
    {
        // 清除think作用域
        session('vipuser', null);
        $this->redirect('index/index');
    }
    public function findpass()
    {
        return view();
    }

    public function sendMsg($phonenumber)
    {
            $code = mt_rand(1000, 9999);
        //$this->success('发送短信验证码成功','',$code);exit();
            $rs = $this->sendSms($phonenumber, $code);
            if ($rs) {
                $this->success('发送短信验证码成功','',$code);
                exit();
            } else {
                $this->error('发送验证码失败', $rs);
            }
        }


//短信
   public function sendSms($to,$codenum) {

        $params = array ();

        // *** 需用户填写部分 ***

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAI07wOEjc0tXiF";
        $accessKeySecret = "NI3eavbGzNQcdF3M16MUOn5In0Pfez";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $to;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "刘晋帆";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_117513109";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            "code" => $codenum

        );

        // fixme 可选: 设置发送短信流水号
        //$params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        //$params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        // fixme 选填: 启用https
        // ,true
        );

        return $content;
    }

    public function checkcode($checkcode,$code)
    {
        if ($code===$checkcode){
            $this->redirect('Login/login');
        }else{
            $this->error('验证码错误');
        }
    }

    //测试session信息单元
    public function getvipusersession()
    {
       // $data = session('vipuser','','shop');
       // Session::delete('vipuser');
        $data = session('vipuser','');
       // $data = $_SESSION;
        var_dump($data);exit();

    }
}