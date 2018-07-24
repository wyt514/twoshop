<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/16
 * Time: 22:16
 */
namespace app\common\model;
use think\Model;
class Category extends Model
{

    protected $table = 'shop_cats';

    //展示第一级分类信息
    public function showData()
    {
        $data = $this->where('parentid=0')->order('catid desc')->paginate(10);
        return $data;
    }

    //修改分类状态
    public function status($catid,$flag)
    {
        $rs = $this->save(['flag'=>$flag],['catid'=>$catid]);
        return $rs;
    }

    //获取该catid的直接子分类
    public function getSonCats($catid=0)
    {
        return $this->where("parentid=$catid")->paginate(10);
    }

    //获取第一级无分页
    public function getFirstCats()
    {
        return $this->where('parentid=0')->select();
    }

    public function findSon($data,$catid)
    {
        static $arr = array();
        //$data = $this->where("parentid=$catid")->select();
        //递归查找子类的父id是该id
        foreach ($data as $k=>$v){
            //二级分类下面还有三级分类
            if ($v['parentid']==$catid){
                $arr[] = $v;
                $this->findSon($data,$v['catid']);
            }
        }

return $arr;

    }


    //通过id查找
    public function findById($id)
    {
        return $this->where('id',$id)->find();
    }

    //查找所有分类
    public function allCats()
    {
        $data = $this->where('flag=1')->select();

    }

    function subtree($arr,$catid,$lev=1)
    {
        $subs=array();
        foreach ($arr as $k=>$value) {
            if ($value['parentid']==$catid) {
                $value['lev']=$lev;
                $subs[]=$value;
                $subs=array_merge($subs,subtree($arr,$value['catid'],$lev+1));
            }
        }

        return $subs;

    }


}