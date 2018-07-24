<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/19
 * Time: 7:45
 */

namespace app\admin\controller;


class User extends Base
{
    public function userList()
    {
        $user = model('User');
        $data = $user->userlist();
       return view('userlist',compact('data'));
    }

    public function deleteuserlist()
    {
        $user = model('User');
        $data = $user->deleteuserlist();
        return view('deleteuserlist',compact('data'));
    }

    //将违规设置为正常
    public function editflag()
    {
        $user = model('User');
        if (request()->isGet()){
            $userid = input('param.userid','','intval');
            $flag = input('param.flag','','intval');
            $con = array();
            $con['flag'] = $flag;
            $con['userid'] = $userid;

            $data['desc'] = $user->where($con)->value('desc');
            $data['userid'] = $userid;
            //var_dump($data);exit();
            return view('desc',compact('data'));

        }elseif(request()->isPost()){
            $userid = input('post.userid','','intval');
            $data['desc'] = '';
            $data['flag'] = 0;
           // $rs = $user->fetchSql(true)->save($data,['userid'=>$userid]);
            $rs = $user->save($data,['userid'=>$userid]);

          //  var_dump($rs);exit();
            if ($rs){
                $this->success('设置为正常成功');

            }else{
                $this->success('设置为正常失败');
            }
        }else{
            $this->error('位置请求');
        }

    }

    //将正常设置为违规
    public function weigui($userid)
    {
        if (request()->isGet()){
            return view('',compact('userid'));
        }elseif (request()->isPost()){
            $userid = input('post.userid','','intval');
            $data['desc']  =input('post.desc','','htmlspecialchars');
            $data['flag'] = -1;
            $user = model('User');
            $rs = $user->save($data,['userid'=>$userid]);
            if ($rs){
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        }

    }
}