<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/2/21
 * Time: 8:32
 */
namespace  app\common\validate;

use think\Validate;

class Register extends Validate
{
    protected $rule = [
        'username'  =>  'require',
        'email' => 'require|email',
        'phone' => 'require|number',
        'password' => 'require',
        'verifycode'=>'require|captcha',
        'city_id'=>'require',
        'se_city_id'=>'require',
        'logo' => 'require',
        'licence_logo' => 'require',
        'description' => 'require',
        'bank_info' => 'require|number',
        'bank_name' => 'require',
        'bank_user' => 'require',
        'faren' => 'require',
        'faren_tel' => 'require|number',
        'contact' => 'require',
        'category_id' => 'require',
        'se_category_id' => 'require',
        'address' => 'require',
        'goodsname' => 'require',
        'goodsdesc' => 'require',
        'goodsstock' => 'require|number',
        'thirdcats' => 'require|number',
        'shopprice' => 'require|float',
        'marketprice' => 'require|float',
    ];

    protected $scene = [
        'register'  =>  ['username', 'password','email','phone', 'content', 'verifycode'],
        'goods'     => ['goodsstock','thirdcats','goodsname', 'shopprice','marketprice']
    ];





}