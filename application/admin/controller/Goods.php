<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\File;
class Goods extends Base
{

    private $obj = null;

    public function _initialize()
    {
        $this->obj = model('Goods');
    }
    public function index()
    {
       $category = model('Category');
       $parentcats = $category->getSonCats();
        return view('',compact('parentcats'));
    }

    public function imgUpload()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = Request::instance()->file('file');

        if($file){

            $info = $file->move('upload');
            // print_r($info->getPathname());
            $path = $info->getPathname();
            if($info && $path){
                // 成功上传后 获取上传信息
                // 输出 jpg
                //return show(1, 'success','/'.$path);
                $rs = ['code'=>1,'msg'=>'上传成功','data'=>'/'.$path];
                return json_encode($rs);

            }else{
                // 上传失败获取错误信息
                //return show(0,$file->getError());
                $rs = ['code'=>0,'msg'=>'上传失败'];
                return json_encode($rs);
                //return show(1, 'success','/'.$info->getPathname());

            }
        }else{
           $this->error('文件上传失败');
        }
    }

    public function create()
    {
        if (request()->isPost()) {
            $data = input('param.');
            //var_dump($data);exit();
            $validate = validate('Register');
            $res = $this->validate($data,'Register.goods');
            if (true!==$res) {
                $this->error($validate->getError());
            } else {
                // 判定提交的用户是否存在
                /*$accountResult = Model('BisAccount')->get(['username' => $data['username']]);
                if ($accountResult) {
                    $this->error('该用户存在，请重新分配');
                }*/

                //商品入库
                $goodsData = [
                    'goodsname' =>$data['goodsname'],
                    'catid'     =>$data['thirdcats'],
                    'goodsimg'     =>$data['logo'],
                    'marketprice' => $data['marketprice'],
                    'shopprice' => $data['shopprice'],
                    'goodsstock' =>$data['goodsstock'],
                    'create_time' => date('Y-m-d H:i:s',time()),
                    'goodsdesc' => $data['goodsdesc'],
                         ];
              $goods = model('Goods');
              $rs = $goods->addGoods($goodsData);
              if ($rs!=false){
                  $this->success('上传商品成功');
              }else{
                  $this->error('上传商品失败');
              }
                }
            }else{
            $this->error('非法操作');
        }
    }

    //商品列表包括管理逻辑
    public function manage()
    {
        $rs = $this->obj->goodsData();
        if ($rs){
            //所属分类，是否上架，是否删除，是否热销，是否推荐
            /*$cat = model('Category');
            $data = $cat->findById($rs['catid']);
            $rs['catid'] = $data['catname'];*/
            return view('',compact('rs'));
        }else{
            $this->error('没有商品');
        }
    }
}
