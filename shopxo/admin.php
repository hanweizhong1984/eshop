<?php
// +----------------------------------------------------------------------
// | ShopXO 国内领先企业级B2C免费开源电商系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2019 http://shopxo.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Devil
// +----------------------------------------------------------------------

// [ 后台入口文件 ]
namespace think;

// 加载基础文件
require __DIR__ . '/thinkphp/base.php';

// 根目录入口
define('IS_ROOT_ACCESS', true);

// 引入公共入口文件
require __DIR__.'/public/core.php';

// 执行应用并响应
//Container::get('app')->bind('admin')->run()->send();

// 执行应用并响应
/*
if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}elseif($_SERVER['REMOTE_ADDR']!=''){
    $ip = $_SERVER['REMOTE_ADDR'];
}
if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE )){
    //index
    header('location:index.php');
}else{
    // admin
    Container::get('app')->bind('admin')->run()->send();
}*/
Container::get('app')->bind('admin')->run()->send();
?>