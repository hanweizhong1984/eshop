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
namespace app\admin\controller;

use app\service\BuyService;
use app\service\MessageService;
use app\service\OrderService;
use app\service\PaymentService;
use app\service\ExpressService;
use think\Db;

/**
 * 订单管理
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Order extends Common
{
    /**
     * 构造方法
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-03T12:39:08+0800
     */
    public function __construct()
    {
        // 调用父类前置方法
        parent::__construct();

        // 登录校验
        $this->IsLogin();

        // 权限校验
        $this->IsPower();
        // 小导航
        $this->view_type = input('view_type', 'home');
    }

    /**
     * 订单列表
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-06T21:31:53+0800
     */
    public function Index()
    {
        // 参数
        $params = input();
        //$outputparams = input();
        $params['admin'] = $this->admin;
        $params['user_type'] = 'admin';

        // 分页
        $number = MyC('admin_page_number', 10, true);

        // 条件
        $where = OrderService::OrderListWhere($params);

        // 获取总数
        $total = OrderService::OrderTotal($where);

        // 分页
        $page_params = array(
                'number'    =>  $number,
                'total'     =>  $total,
                'where'     =>  $params,
                'page'      =>  isset($params['page']) ? intval($params['page']) : 1,
                'url'       =>  MyUrl('admin/order/index'),
            );
        $page = new \base\Page($page_params);
        $this->assign('page_html', $page->GetPageHtml());

        // 获取列表
        $data_params = array(
            'm'         => $page->GetPageStarNumber(),
            'n'         => $number,
            'where'     => $where,
            'is_public' => 0,
        );
        //获取所有记录，不只限制分页条数
        $data_params2 = array(
            'm'         =>0,
            'n'         =>$total,
            'where'     => $where,
            'is_public' => 0,
        );

        $data = OrderService::OrderList($data_params);
        $data_all=OrderService::OrderList($data_params2);
        $this->assign('data_list', $data['data']);
        $this->assign('data_list_all', $data_all['data']);

        // 状态
        $this->assign('common_order_admin_status', lang('common_order_admin_status'));

        // 支付状态
        $this->assign('common_order_pay_status', lang('common_order_pay_status'));

        // 订单模式
        $this->assign('common_site_type_list', lang('common_site_type_list'));

        // 快递公司
        $this->assign('express_list', ExpressService::ExpressList());

        // 发起支付 - 支付方式
        $pay_where = [
            'where' => ['is_enable'=>1, 'payment'=>config('shopxo.under_line_list')],
        ];
        $this->assign('buy_payment_list', PaymentService::BuyPaymentList($pay_where));

        // 支付方式
        $this->assign('payment_list', PaymentService::PaymentList());

        // 评价状态
        $this->assign('common_comments_status_list', lang('common_comments_status_list'));

        // 平台
        $this->assign('common_platform_type', lang('common_platform_type'));

        // 参数
        $this->assign('params', $params);
        // Excel地址
        //$this->assign('excel_url', MyUrl('admin/order/ExcelExport', $outputparams));
        // 导航参数
        $this->assign('view_type', $this->view_type);

        if ($this->view_type == 'upload') {
            return $this->fetch('upload');
        }

        return $this->fetch();
    }

    /**
     * [Delete 订单删除]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-15T11:03:30+0800
     */
    public function Delete()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 删除操作
        $params = input();
        $params['user_id'] = $params['value'];
        $params['creator'] = $this->admin['id'];
        $params['creator_name'] = $this->admin['username'];
        $params['user_type'] = 'admin';
        return OrderService::OrderDelete($params);
    }

    /**
     * [Cancel 订单取消]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-15T11:03:30+0800
     */
    public function Cancel()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 取消操作
        $params = input();
        $params['user_id'] = $params['value'];
        $params['creator'] = $this->admin['id'];
        $params['creator_name'] = $this->admin['username'];
        return OrderService::OrderCancel($params);
    }

    /**
     * [Delivery 订单发货/取货]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-15T11:03:30+0800
     */
    public function Delivery()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 发货操作
        $params = input();
        $params['creator'] = $this->admin['id'];
        $params['creator_name'] = $this->admin['username'];
        return OrderService::OrderDelivery($params);
    }

    /**
     * [Collect 订单收货]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-15T11:03:30+0800
     */
    public function Collect()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 收货操作
        $params = input();
        $params['user_id'] = $params['value'];
        $params['creator'] = $this->admin['id'];
        $params['creator_name'] = $this->admin['username'];
        return OrderService::OrderCollect($params);
    }

    /**
     * [Confirm 订单确认]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-15T11:03:30+0800
     */
    public function Confirm()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 订单确认
        $params = input();
        $params['user_id'] = $params['value'];
        $params['creator'] = $this->admin['id'];
        $params['creator_name'] = $this->admin['username'];
        return OrderService::OrderConfirm($params);
    }

    /**
     * 订单支付
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2018-09-28
     * @desc    description
     */
    public function Pay()
    {
        $params = input();
        $params['user'] = $this->admin;
        $params['user']['user_name_view'] = '管理员'.'-'.$this->admin['username'];
        return OrderService::AdminPay($params);
    }

    /**
     * [ExcelExport excel文件导出]
     * @author   hwz
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2020-08-31T15:46:00+0800
     */
    public function ExcelExport()
    {
        $params = input();
        if(empty($params['ordernos']))
        {
            return;
        }
        $params['admin'] = $this->admin;
        $params['user_type'] = 'admin';
        // 条件
        $where = OrderService::OrderListWhere($params);

        $data_params = [
            'where'		=> $where,
            'm'			=> 0,
            'n'			=> 0,
            'is_public' => 0    //用户信息
        ];
        $data = OrderService::OrderList($data_params);

        // Excel驱动导出数据
        $excel = new \base\Excel(array('filename'=>'order', 'title'=>lang('excel_order_title_list'), 'data'=>$data['data'], 'msg'=>'没有相关数据','suffix'=>'xlsx'));
        return $excel->Export();
    }

    /**
     * [导入运单]
     * @author   hwz
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2020-11-18T15:46:00+0800
     */
    public function upload()
    {
        $params = [];
        $params['creator'] = $this->admin['id'];
        $params['creator_name'] = $this->admin['username'];
        // 是否ajax
        if (!IS_AJAX) {
            return $this->error('非法访问');
        }
        require_once 'extend/phpexcel/PHPExcel/IOFactory.php';
        //文件校验
        $file = $_FILES['file'];

        if ($file['error'] == 4)
            return DataReturn('请选择上传excel文件', -2);
        $file_types = explode(".", $file['name']);
        $excel_type = array('xls', 'csv', 'xlsx');
        if (!in_array(strtolower(end($file_types)), $excel_type)) {
            return DataReturn('不是Excel文件，请重新上传', -2);
        }
        //限制文件大小，取DB中设置的文件大小做比较
        $filelimit = MyC('home_max_limit_file');
        if (!empty($filelimit) && $filelimit > 0) {
            if ($file['size'] > $filelimit) {
                return DataReturn('文件大小超出范围。', -2);
            }
        }
        if ($file_types[1] == 'xls') {
            //设置获取excel对象
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        } else {
            //设置获取excel对象
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');//配置成2003版本，因为office版本可以向下兼容
        }
        $objReader->setReadDataOnly(TRUE);  //加上这行速度就提上来了
        set_time_limit(0);//设置页面等待时间
        ini_set('memory_limit', '-1');//不限制内存
        $objPHPExcel = $objReader->load($file['tmp_name'], $encode = 'utf-8');//$file 为解读的excel文件
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $orders_info = [];     //订单数组
        $orderscount = 0;   //订单个数

        for ($j = 2; $j <= $highestRow; $j++) {
            $order_no = trim($objPHPExcel->getActiveSheet()->getCell('A' . $j)->getValue()); //订单号
            $express_number = trim($objPHPExcel->getActiveSheet()->getCell('X' . $j)->getValue());//快递单号
            if (empty($order_no) && empty($express_number)) {
                continue;
            }
            if ($order_no != null) {
                if (is_object($order_no)) {
                    $order_no = $order_no->__toString();//处理PHPExcel_RichText问题
                }
                // 获取订单信息
                $where = ['order_no' => $order_no, 'is_delete_time' => 0, 'user_is_delete_time' => 0];
                $order = Db::name('Order')->where($where)->field('id,status,user_id')->find();
                if (empty($order)) {
                    return DataReturn('第' . $j . '行订单不存在或已被删除', -1);
                }
                if (!in_array($order['status'], [2])) {
                    $status_text = lang('common_order_user_status')[$order['status']]['name'];
                    return DataReturn('第' . $j . '行订单状态不可操作[' . $status_text . ']', -1);
                }
                if (empty($express_number)) {
                    return DataReturn('第' . $j . '行订单的快递单号不能为空！', -2);
                }
            }
        }
        // 开启事务
        Db::startTrans();
        for ($j = 2; $j <= $highestRow; $j++) {
            $order_no = trim($objPHPExcel->getActiveSheet()->getCell('A' . $j)->getValue()); //订单号
            $express_number = trim($objPHPExcel->getActiveSheet()->getCell('X' . $j)->getValue());//快递单号
            if ($order_no != null) {
                if (is_object($order_no)) {
                    $order_no = $order_no->__toString();//处理PHPExcel_RichText问题
                }
                // 获取订单信息
                $where = ['order_no' => $order_no, 'is_delete_time' => 0, 'user_is_delete_time' => 0];
                $order = Db::name('Order')->where($where)->field('id,status,user_id')->find();
            }
            if (is_object($express_number)) {
                $express_number = $express_number->__toString();//处理PHPExcel_RichText问题
            }

            $upd_data = [
                'status' => 3,
                'express_id' => 1,
                'express_number' => $express_number,
                'delivery_time' => time(),
                'upd_time' => time(),
            ];
            if (Db::name('Order')->where($where)->update($upd_data)) {
                // 库存扣除
                $ret = BuyService::OrderInventoryDeduct(['order_id' => $order['id'], 'order_data' => $upd_data]);
                if ($ret['code'] != 0) {
                    // 事务回滚
                    Db::rollback();
                    return DataReturn($ret['msg'], -10);
                }

                // 用户消息
                MessageService::MessageAdd($order['user_id'], '订单发货', '订单已发货', 'The order was delivered', 1, $order['id']);

                // 订单状态日志
                $creator = isset($params['creator']) ? intval($params['creator']) : 0;
                $creator_name = isset($params['creator_name']) ? htmlentities($params['creator_name']) : '';
                OrderService::OrderHistoryAdd($order['id'], $upd_data['status'], $order['status'], '收货', $creator, $creator_name);
            }

            // 事务回滚
            //Db::rollback();
            //return DataReturn('发货失败', -1);
            // 提交事务
            Db::commit();
        }


        return DataReturn('上传成功，订单更新为发货状态', 0);
    }
}
?>