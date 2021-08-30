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

/**
 * 模块语言包
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
return array(
    // 站点关闭状态列表
    'site_site_state_list'          =>  array(
        0 => array('value' => 0, 'name' => '关闭', 'checked' => true),
        1 => array('value' => 1, 'name' => '开启'),
    ),

    // 是否开启用户登录
    'site_user_login_state_list'            =>  array(
        0 => array('value' => 0, 'name' => '关闭'),
        1 => array('value' => 1, 'name' => '开启', 'checked' => true),
    ),

    // 获取验证码-强制使用图片验证码状态列表
    'site_img_verify_state_list'        =>  array(
        0 => array('value' => 0, 'name' => '关闭'),
        1 => array('value' => 1, 'name' => '开启', 'checked' => true),
    ),

    // 图片验证码
    'site_images_verify_rules_list'  => array(
        0 => array('value' => 'bgcolor', 'name' => '彩色背景'),
        1 => array('value' => 'textcolor', 'name' => '彩色文本'),
        2 => array('value' => 'point', 'name' => '干扰点'),
        3 => array('value' => 'line', 'name' => '干扰线'),
    ),

    // 时区
    'site_timezone_list' => array(
        'Pacific/Pago_Pago' => '(标准时-11:00) 中途岛、萨摩亚群岛',
        'Pacific/Rarotonga' => '(标准时-10:00) 夏威夷',
        'Pacific/Gambier' => '(标准时-9:00) 阿拉斯加',
        'America/Dawson' => '(标准时-8:00) 太平洋时间(美国和加拿大)',
        'America/Creston' => '(标准时-7:00) 山地时间(美国和加拿大)',
        'America/Belize' => '(标准时-6:00) 中部时间(美国和加拿大)、墨西哥城',
        'America/Eirunepe' => '(标准时-5:00) 东部时间(美国和加拿大)、波哥大',
        'America/Antigua' => '(标准时-4:00) 大西洋时间(加拿大)、加拉加斯',
        'America/Argentina/Buenos_Aires' => '(标准时-3:00) 巴西、布宜诺斯艾利斯、乔治敦',
        'America/Noronha' => '(标准时-2:00) 中大西洋',
        'Atlantic/Cape_Verde' => '(标准时-1:00) 亚速尔群岛、佛得角群岛',
        'Africa/Ouagadougou' => '(格林尼治标准时) 西欧时间、伦敦、卡萨布兰卡',
        'Europe/Andorra' => '(标准时+1:00) 中欧时间、安哥拉、利比亚',
        'Europe/Mariehamn' => '(标准时+2:00) 东欧时间、开罗，雅典',
        'Asia/Bahrain' => '(标准时+3:00) 巴格达、科威特、莫斯科',
        'Asia/Dubai' => '(标准时+4:00) 阿布扎比、马斯喀特、巴库',
        'Asia/Kolkata' => '(标准时+5:00) 叶卡捷琳堡、伊斯兰堡、卡拉奇',
        'Asia/Dhaka' => '(标准时+6:00) 阿拉木图、 达卡、新亚伯利亚',
        'Indian/Christmas' => '(标准时+7:00) 曼谷、河内、雅加达',
        'Australia/Eucla' => '(标准时+8:00)北京、重庆、香港、新加坡',
        'Australia/Darwin' => '(标准时+9:00) 东京、汉城、大阪、雅库茨克',
        'Australia/Adelaide' => '(标准时+10:00) 悉尼、关岛',
        'Australia/Currie' => '(标准时+11:00) 马加丹、索罗门群岛',
        'Pacific/Fiji' => '(标准时+12:00) 奥克兰、惠灵顿、堪察加半岛'
    ),

    // seo
    // url模式列表
    'seo_url_model_list'        =>  array(
        0 => array('value' => 0, 'name' => '兼容模式', 'checked' => true),
        1 => array('value' => 1, 'name' => 'PATHINFO模式'),
        2 => array('value' => 2, 'name' => 'PATHINFO模式+短地址'),
    ),

    // 用户excel导出标题列表
    'excel_user_title_list'     =>  [
        'username'      =>  [
            'name' => '用户名',
            'type' => 'string',
        ],
        'nickname'      =>  [
            'name' => '昵称',
            'type' => 'int',
        ],
        'gender_text'   =>  [
            'name' => '性别',
            'type' => 'string',
        ],
        'birthday_text'=>   [
            'name' => '生日',
            'type' => 'string',
        ],
        'status_text'=>   [
            'name' => '状态',
            'type' => 'string',
        ],
        'mobile'        =>  [
            'name' => '手机号码',
            'type' => 'int',
        ],
        'email'         =>  [
            'name' => '电子邮箱',
            'type' => 'string',
        ],
        'province'      =>  [
            'name' => '所在省',
            'type' => 'string',
        ],
        'city'      =>  [
            'name' => '所在市',
            'type' => 'string',
        ],
        'address'       =>  [
            'name' => '详细地址',
            'type' => 'string',
        ],
        'add_time'      =>  [
            'name' => '注册时间',
            'type' => 'string',
        ],
    ],
    // 订单excel导出标题列表
    'excel_order_title_list'     =>  [
            'order_no'      =>  [
                    'name' => '订单号',
                    'type' => 'string',
                    'width'=> 150,
                ],
            'order_model_name'      =>  [
                    'name' => '订单模式',
                    'type' => 'string',
                    'width'=> 100,
                ],
            'client_type_name'   =>  [
                    'name' => '客户端类型',
                    'type' => 'string',
                    'width'=> 100,
                ],
            'items'  =>   [
                    'name' => '商品',
                    'type' => 'arrays',
                    'contents'=>   [
                    	'images'=>[
		                    'name' => '商品图片',
		                    'type' => 'string',
                            'width'=> 300,
	                    ],
                    	'title'=>[
		                    'name' => '商品标题',
		                    'type' => 'string',
                            'width'=> 300,
	                    ],
	                    'price'=>[
		                    'name' => '商品价格',
		                    'type' => 'float',
                            'width'=> 100,
	                    ],
	                    'total_price'=>[
		                    'name' => '商品总价',
		                    'type' => 'float',
                            'width'=> 100,
	                    ],
	                    'spec_text'=>[
		                    'name' => '商品规格（欧码）',
		                    'type' => 'string',
                            'width'=> 100,
	                    ],
                        'spec_coding'=>[
                            'name' => '商品规格（美码）',
                            'type' => 'string',
                            'width'=> 200,
                        ],
	                    'model'=>[
		                    'name' => '商品型号',
		                    'type' => 'string',
                            'width'=> 100,
	                    ],
	                    'spec_weight'=>[
		                    'name' => '商品重量',
		                    'type' => 'string',
                            'width'=> 100,
	                    ],
	                    'spec_barcode'=>[
		                    'name' => '商品条形码',
		                    'type' => 'string',
                            'width'=> 200,
	                    ],
	                    'buy_number'=>[
		                    'name' => '商品购买数量',
		                    'type' => 'int',
                            'width'=> 100,
	                    ],
	                   
                    ],
                ],
            'user'=> [
            			'name'=>'用户',
            			'type'=>'array',
            			'content'=>   [
		                	'username' => [
			                    'name' => 'NetWork ID',
			                    'type' => 'string',
                                'width'=> 100,
		                    ],
		                	'nickname' => [
			                    'name' => 'Employee Name',
			                    'type' => 'string',
                                'width'=> 100,
		                    ],
		                    'mobile'=>[
			                    'name' => '用户手机',
			                    'type' => 'string',
                                'width'=> 100,
		                    ],
		                    'email'=>[
			                    'name' => '用户邮箱',
			                    'type' => 'string',
                                'width'=> 100,
		                    ],
		                ],
                    ],
            'address_data'=>[
            			'name'=>'收件人地址信息',
            			'type'=>'array',
            			'content'=>[
            				'name'=>[
            					'name'=>'收件人-姓名',
            					'type'=>'string',
                                'width'=> 100,
            				],
            				'tel'=>[
            					'name'=>'收件人-电话',
            					'type'=>'string',
                                'width'=> 100,
            				],
            				'province_name'=>[
            					'name'=>'收件人-省',
            					'type'=>'string',
                                'width'=> 100,
            				],
            				'city_name'=>[
            					'name'=>'收件人-市',
            					'type'=>'string',
                                'width'=> 100,
            				],
            				'county_name'=>[
            					'name'=>'收件人-县/区',
            					'type'=>'string',
                                'width'=> 100,
            				],
            				'address'=>[
            					'name'=>'详细地址',
            					'type'=>'string',
                                'width'=> 200,
            				],
            			],
        			],
        	'express_number'   =>  [
                        'name' => '快递单号',
                        'type' => 'string',
                        'width'=> 200,
                    ],
            'status_name'   =>  [
                        'name' => '订单状态',
                        'type' => 'string',
                        'width'=> 100,
                    ],
            'pay_status_name'   =>  [
                        'name' => '支付状态',
                        'type' => 'string',
                        'width'=> 100,
                    ],
            'payment_name'   =>  [
                        'name' => '支付方式',
                        'type' => 'string',
                        'width'=> 100,
                    ],
            'user_note'   =>  [
                        'name' => '用户备注',
                        'type' => 'string',
                        'width'=> 100,
                    ],
            'price'   =>  [
                        'name' => '订单单价',
                        'type' => 'string',
                        'width'=> 100,
                    ],
            'increase_price'=>[
                        'name' => '增加的金额',
                        'type' => 'float',
                        'width'=> 100,
                	],
            'preferential_price'=>[
                        'name' => '优惠金额',
                        'type' => 'float',
                        'width'=> 100,
                	],
            'total_price'=>[
                        'name' => '订单总价',
                        'type' => 'float',
                        'width'=> 100,
                	],
            'pay_price'=>[
                        'name' => '已支付金额',
                        'type' => 'float',
                        'width'=> 100,
                	],
            'refund_price'=>[
                    'name' => '退款金额',
                    'type' => 'float',
                    'width'=> 100,
                	],
            'pay_time'=>[
                    'name' => '支付时间',
                    'type' => 'string',
                    'width'=> 100,
                	],
            'confirm_time'=>[
                    'name' => '完成时间',
                    'type' => 'string',
                    'width'=> 100,
                	],
            'cancel_time'=>[
                    'name' => '取消时间',
                    'type' => 'string',
                    'width'=> 100,
                	],
            'close_time'=>[
                    'name' => '关闭时间',
                    'type' => 'string',
                    'width'=> 100,
                	],
            'add_time'=>[
                    'name' => '创建时间',
                    'type' => 'string',
                    'width'=> 100,
                	],
            'upd_time'=>[
                    'name' => '更新时间',
                    'type' => 'string',
                    'width'=> 100,
                	],
     ]
);
?>