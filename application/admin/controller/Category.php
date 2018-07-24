<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/16
 * Time: 22:15
 */

namespace app\admin\controller;
//use app\model\Category as categoryModel;
use think\Controller;

class Category extends Base
{

    private $obj = null;

    public function _initialize()
    {
        $this->obj = model('Category');
    }

    //显示分类,这里只显示第一级分类
    public function show()
    {
        //$category = new categoryModel();
        $data = $this->obj->showData();
        return view('index', compact('data'));
    }

    //修改状态
    public function status($catid, $flag)
    {
        if ($flag == 1) {
            $rs = $this->obj->status($catid, 0);
        } elseif ($flag == 0) {
            $rs = $this->obj->status($catid, -1);
        } else {
            $rs = $this->obj->status($catid, 1);
        }
        if ($rs !== false) {
            $this->success('修改状态成功');
        } else {
            $this->error('修改状态失败');
        }
    }

    //获取子分类
    public function getSonCats($catid)
    {
        $data = $this->obj->getSonCats($catid);
        return view('index', compact('data'));
    }

    //添加分类
    public function create()
    {
        //获取一级分类和二级分类信息
        $parentcats = $this->obj->getFirstCats();
        return view('add', compact('parentcats'));
    }

    //获取子分类
    public function getSonCatsByCatid($catid)
    {
        $catid = input('post.catid', '', 'intval');
        $data = $this->obj->getSonCats($catid);
        //$this->success('查询成功','',$data);
        $rs = ['msg' => '查询成功', 'data' => $data];
        // return json_encode($rs);
        return $rs;

    }

    //新增分类
    public function save()
    {

        //一级分类
        $data['catname'] = input('name', '');
        $secondcats = input('secondcats', '');
        $parent_id = input('parent_id', '');
        //var_dump($parent_id);exit();
        if ($parent_id === 0) {
            $data['parentid'] = 0;
        } else {
            if ($secondcats==0){
                $data['parentid'] = $parent_id;
            }else {
                $data['parentid'] = $secondcats;
            }
        }

        //var_dump($data['parentid']);exit();   -0
        //二级分类
        $data['flag'] = 0;
        $data['create_time'] = date('Y-m-d H:i:s', time());
        $rs = $this->obj->save($data);


        if ($rs != false) {
            $this->success('新增成功');
            exit();
        } else {
            $this->error('新增失败');
            exit();
        }

    }

    //删除分类
    public function delete($catid)
    {
        //查找是否有子分类，有的话一起删除
        $data = $this->obj->select();
        $arr = $this->obj->findSon($data,$catid);
        //var_dump($arr);exit();
        $len = count($arr);
        static $ar = array();
        for ($i=0;$i<$len;$i++){
            $ar[] = $arr[$i]['catid'];
        }
        $ar[] = $catid;
        //var_dump($ar);exit();
        $rs = $this->obj->destroy($ar);
        if ($rs){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}