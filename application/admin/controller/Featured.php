<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/19
 * Time: 23:22
 */

namespace app\admin\controller;


class Featured extends Base
{
    //添加前台三个大图推荐位
    public function add()
    {
        $category = model('Category');
        $parentcats = $category->getFirstCats();
        //获取第三级所属分类
        //相对应商品url


        return view('', compact('parentcats'));
    }

    public function savedata()
    {
        if (request()->isPost()) {
            $data = input('param.');
            //var_dump($data);exit();
          /*  $validate = validate('Register');
            $res = $this->validate($data,'Register.goods');
            if (true!==$res) {
                $this->error($validate->getError());
            } else {}*/
                // 判定提交的用户是否存在
                /*$accountResult = Model('BisAccount')->get(['username' => $data['username']]);
                if ($accountResult) {
                    $this->error('该用户存在，请重新分配');
                }*/

                //商品入goods库
            //catid,goodsname,goodsimg.marketprice,shopprice,goodsstock,issale=1,create_time,saletime,goodsdesc,isrecom=1,goodstips

            $goodsData = [
                'goodsname' =>$data['title'],
                'catid'     =>$data['thirdcats'],
                'goodsimg'     =>$data['image'],
                'marketprice' => $data['marketprice'],
                'shopprice' => $data['shopprice'],
                'goodsstock' =>$data['goodsstock'],
                'issale' => 1,
                'create_time' => date('Y-m-d H:i:s',time()),
                'saletime' => date('Y-m-d H:i:s',time()),
                'isrecom' => 1,
                'goodsdesc' => $data['desc']?$data['desc']:'',
                'goodstips' => $data['goodstips']?$data['goodstips']:'',
            ];
            $goods = model('Goods');
            $rs = $goods->addGoods($goodsData);
            if ($rs!=false){
            //获取goodsid
                $goodsid = $goods->getLastInsID();
                //商品入库
                $featuredData = [
                    'title' =>$data['title'],
                    'catid'     =>$data['thirdcats'],
                    'goodsid' =>$goodsid,
                    'image'     =>$data['image'],
                    'create_time' => date('Y-m-d H:i:s',time()),
                    'desc' => $data['desc'],
                ];
                $featured = db('featured');
                $rs = $featured->insert($featuredData);
                if ($rs!=false){
                    $this->success('上传商品成功');
                }else{
                    $this->error('上传商品失败');
                }
            }else{
                $this->error('信息入库失败');
            }


        }else{
            $this->error('非法操作');
        }
    }

    public function featuredlist()
    {
        $data = db('featured')->where('status=1')->order('listorder desc')->paginate(2);
        //$cats = model('Category');
        //$data['catname'] = $cats->findById($data['catid']);
        return view('index',compact('data'));
    }

    public function changeorder()
    {
        //数据校验
       $featured = model('featured');
        $data = input('post.');
        //var_dump($data);exit();

        $len = count($data['featuredid']);
        $list = array(array());
        for ($i=1;$i<=$len;$i++){
            $list[$i-1]['id'] = $data['featuredid'][$i];
            $list[$i-1]['listorder'] = $data['list'][$i];
        }
        //var_dump($list);exit();
        $rs = $featured->saveAll($list);
        if ($rs!=false){
            $this->success('修改排序成功');
        }else{
            $this->error('修改排序失败');
        }

       // var_dump(input('post.'));exit();
        /*$name = input('post.featuredid','');
        $list = input('post.list','');*/

        $featured = db('featured');

    }

    public function changestatus()
    {
        $id = input('post.id');
        $featured = model('featured');
        $rs = $featured->save(['status'=>0],['id'=>$id]);
        if ($rs){
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }
}