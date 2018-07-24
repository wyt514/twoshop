<?php
//修改状态
function status($state){
    if ($state==1) {
        return '<font style="color:green">正常</font>';
    }elseif ($state==0){
        return '<font style="color:orange">待审</font>';
    }else{
        return '<font style="color:red">删除</font>';
    }


    function show($code,$msg='',$data=[]){
        $code =  intval($code);
        return [
            'code' =>$code,
            'msg'=>$msg,
            'data'=>$data

        ];

    }
}

function sex($sex){
    return $sex=='1'?'男':'女';
}

function flag($flag){
    if ($flag===-1){
        return '<font style="color:red">违规</font>';
    }elseif ($flag===0){
        return '<font style="color:orange">待激活</font>';
    }else{
        return '<font style="color:green">正常</font>';
    }


}

function viprank($rank){
    //会员等级对应消费折扣：0普通用户9.8折，1：白银会员：9.5折，2：黄金会员：9.0折；3：白金会员：8.8折，4：钻石会员：8.5折
    if ($rank===0){
        return '<font style="color:#999988">普通用户</font>';
    }elseif ($rank===1){
        return '<font style="color:#c0c0c0">白银会员</font>';
    }elseif($rank===2){
        return '<font style="color:#FEFE41">黄金会员</font>';
    }elseif($rank===3){
        return '<font style="color:#DCDCDC">白金会员</font>';
    }else{
        return '<font style="color:	#48D1CC">钻石会员</font>';
    }
}