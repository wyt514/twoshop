<?php
namespace app\home\controller;

use think\Controller;
use think\Session;

class Index extends Controller
{
    public function index()
    {
        //商品，分类显示
        $goods = model('Goods');
        $category = model('Category');
        $featured = db('featured');
        /*
         * 获取所有的分类信息子孙树
         * */
        $con['flag']=1;
        $con['parentid']=0;

        //一级分类前十条
            $cats = $category->where($con)->limit(0,10)->select();
            //var_dump($cats);exit();
        $featureddata = $featured->order('listorder desc')->limit(0,3)->select();
        $second = array();
        $third = array();
       /* for ($i=0;$i<10;$i++){
            $second[$i] = $category->where('parentid',$cats[$i]['catid'])->select();
        }*/

       // dump(count($second));exit();
            //二级分类显示
             //var_dump($cats);exit();
            $data = $goods->order('goodsid desc')->limit(0, 10)->select();
            $rs = $this->xietong();
           // var_dump($rs);exit();
        return view('',compact('data','cats','featureddata','rs'));

    }

    public function login()
    {
        return view();
    }

    public function hotGoods()
    {


    }

    public function test()
    {

        $api = \Elasticsearch\ClientBuilder::create();
        var_dump($api);exit();
        $search = model('Search');
        $rs = $search->addOne();
        echo $rs;
    }


    //推荐算法
    public function xietong()
    {
        $appraises = db('appraises');
        //获取到评价表数据
        $data = $appraises->select();
        $alltotal = count($data);
        //var_dump($data);exit();
        $cos = array();
        $cos[0] = 0;
        $fm1 = 0;
        $user = Session::get('vipuser');
        $userid = $user['userid'];
        //计算评价表一个多少个用户
        $totaluser = $appraises->distinct(true)->field('userid')->select();
        //var_dump($totaluser);exit();
        //计算评价表一共多少商品被评价
        $totalgoods = $appraises->distinct(true)->field('goodsid')->select();
        //var_dump($totaluser);exit();
        $goodsnum = count($totalgoods);
        $usernum = count($totaluser);
        //开始计算当前用户评分过的商品平方值总值
        $nowusergivefenshu = $appraises->where('userid',$userid)->select();
        //var_dump($nowusergivefenshu);exit();
        //$l是当前用户给了分数的商品数
        $l = count($nowusergivefenshu);
        for ($i=0;$i<$l;$i++){
            $fm1+=$nowusergivefenshu[$i]['goodsscore']*$nowusergivefenshu[$i]['goodsscore'];
        }
        $fm1 =sqrt($fm1);


        //开始计算相邻用户--除开自己
        for ($j=0;$j<$usernum-1;$j++){
            $fz=0;
            $fm2=0;
            //计算分子,当前用户有评分的商品并且其他用户有评分商品
            //$nowusergivefenshu = $appraises->where('userid',$userid)->select();
          //  for ($m=0;$m<$alltotal;$m++) {

                for ($a = 0; $a < $l; $a++) {
                    //当前用户评价过得商品其他用户也评价的二维数组
                    $nowall = $appraises->where('goodsid', $nowusergivefenshu[$a]['goodsid'])->select();
                    //这里要注意会把自己也计算在内
                    $ca = count($nowall)-2;
                    //var_dump($ca);exit();
                    //在所有评论中查找当前用户第一个第二个相同商品id
                    for ($c = 0; $c < $ca; $c++) {
                        $fz += $nowusergivefenshu[$a]['goodsscore'] * $nowall[$c]['goodsscore'];
                    }
                }
            //}
                //循环查找当前用户评论商品在表中其他人也评论的分值并且相乘
                //计算其他用户评价分数的评分
                for ($n=0;$n<$alltotal;$n++){
                    if ($data[$n]['userid']!==$userid ){
                        $fm2+=$data[$n]['goodsscore']*$data[$n]['goodsscore'];
                    }
            }
            $fm2 = sqrt($fm2);
            //echo $fz.'...'.$fm1.'...'.$fm2.'xxx';
            $cos[$j] = $fz/$fm1/$fm2;
           // echo $cos[$j]."<br />";
        }

        $neighbour = rsort($cos);

        //自己思考，上面可以得出两个人喜好相似度，所以我们可以通过最高的三个人评分好的，并且属于同一分类的推荐给当前用户
        $nextuser = mt_rand(0,$usernum-1);
        $nextuser = $totaluser[$nextuser]['userid'];
        $data =  $appraises->where('userid',$nextuser)->select();
       //var_dump($data);exit();
        $goods = db('goods');
        $ll = count($data);
        $rs = array();
        for ($h=0;$h<$ll;$h++){
            $rs[$h] = $goods->where('goodsid',$data[$h]['goodsid'])->find();
        }
        return $rs;
        //$appraises->where('')->where('')->select();
        /*$neighbour_set = array();
        for($i=0;$i<3;$i++){
            for($j=0;$j<5;$j++){
                if($neighbour[$i] == $cos[$j]){
                    $neighbour_set[$i][0] = $j;
                    $neighbour_set[$i][1] = $cos[$j];
                    $neighbour_set[$i][2] = $array[$j][6];//邻居对f的评分
                    $neighbour_set[$i][3] = $array[$j][7];//邻居对g的评分
                    $neighbour_set[$i][4] = $array[$j][8];//邻居对h的评分
                }
            }
        }
        print_r($neighbour_set);
        echo "<p><br/>";*/



    }


}
