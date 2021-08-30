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

use think\Db;
use think\facade\Hook;
use app\service\ResourcesService;
use app\service\GoodsService;
use app\service\RegionService;
use app\service\BrandService;

/**
 * 商品管理
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Goods extends Common
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
     * [Index 商品列表]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-06T21:31:53+0800
     */
    public function Index()
    {
        // 参数
        $params = input();

        // 条件
        $where = GoodsService::GetAdminIndexWhere($params);

        // 总数
        $total = GoodsService::GoodsTotal($where);

        // 分页
        $number = MyC('admin_page_number', 10, true);
        $page_params = array(
            'number'    =>    $number,
            'total'        =>    $total,
            'where'        =>    $params,
            'page'        =>    isset($params['page']) ? intval($params['page']) : 1,
            'url'        =>    MyUrl('admin/goods/index'),
        );
        $page = new \base\Page($page_params);

        // 获取数据列表
        $data_params = [
            'where'                => $where,
            'm'                    => $page->GetPageStarNumber(),
            'n'                    => $number,
            'is_category'        => 1,
            'is_admin_access'    => 1,
        ];
        $ret = GoodsService::GoodsList($data_params);

        // 商品分类
        $this->assign('goods_category_list', GoodsService::GoodsCategoryAll());

        // 品牌分类
        $this->assign('brand_list', BrandService::CategoryBrand());

        // 是否上下架
        $this->assign('common_is_shelves_list', lang('common_is_shelves_list'));

        // 是否首页推荐
        $this->assign('common_is_text_list', lang('common_is_text_list'));

        $this->assign('params', $params);
        $this->assign('page_html', $page->GetPageHtml());
        $this->assign('data_list', $ret['data']);
        // 导航参数
        $this->assign('view_type', $this->view_type);

        if ($this->view_type == 'upload') {
            return $this->fetch('upload');
        }
        return $this->fetch();
    }

    /**
     * [SaveInfo 商品添加/编辑页面]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-14T21:37:02+0800
     */
    public function SaveInfo()
    {
        // 参数
        $params = input();

        // 商品信息
        $data = [];
        if (!empty($params['id'])) {
            $data_params = [
                'where'                => ['id' => $params['id']],
                'm'                    => 0,
                'n'                    => 1,
                'is_photo'            => 1,
                'is_content_app'    => 1,
                'is_category'        => 1,
            ];
            $ret = GoodsService::GoodsList($data_params);
            if (empty($ret['data'][0])) {
                return $this->error('商品信息不存在', MyUrl('admin/goods/index'));
            }
            $data = $ret['data'][0];

            // 获取商品编辑规格
            $specifications = GoodsService::GoodsEditSpecifications($ret['data'][0]['id']);
            $this->assign('specifications', $specifications);
        }

        // 地区信息
        $this->assign('region_province_list', RegionService::RegionItems(['pid' => 0]));

        // 商品分类
        $this->assign('goods_category_list', GoodsService::GoodsCategoryAll());

        // 品牌分类
        $this->assign('brand_list', BrandService::CategoryBrand());

        // 规格扩展数据
        $goods_spec_extends = GoodsService::GoodsSpecificationsExtends($params);
        $this->assign('goods_specifications_extends', $goods_spec_extends['data']);

        // 商品编辑页面钩子
        $hook_name = 'plugins_view_admin_goods_save';
        $this->assign($hook_name . '_data', Hook::listen(
            $hook_name,
            [
                'hook_name'        => $hook_name,
                'is_backend'       => true,
                'goods_id'      => isset($params['id']) ? $params['id'] : 0,
                'data'            => &$data,
                'params'           => &$params,
            ]
        ));

        // 编辑器文件存放地址
        $this->assign('editor_path_type', 'goods');

        // 数据
        $this->assign('data', $data);
        $this->assign('params', $params);
        return $this->fetch();
    }

    /**
     * [Save 商品添加/编辑]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-14T21:37:02+0800
     */
    public function Save()
    {
        // 是否ajax
        if (!IS_AJAX) {
            return $this->error('非法访问');
        }

        // 开始操作
        $params = input('post.');
        $params['admin'] = $this->admin;
        return GoodsService::GoodsSave($params);
    }

    /**
     * [Delete 商品删除]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-15T11:03:30+0800
     */
    public function Delete()
    {
        // 是否ajax
        if (!IS_AJAX) {
            return $this->error('非法访问');
        }

        // 开始操作
        $params = input('post.');
        $params['admin'] = $this->admin;
        return GoodsService::GoodsDelete($params);
    }

    /**
     * [StatusShelves 上下架状态更新]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-01-12T22:23:06+0800
     */
    public function StatusShelves()
    {
        // 是否ajax
        if (!IS_AJAX) {
            return $this->error('非法访问');
        }

        // 开始操作
        $params = input('post.');
        $params['admin'] = $this->admin;
        $params['field'] = 'is_shelves';
        return GoodsService::GoodsStatusUpdate($params);
    }

    /**
     * [StatusHomeRecommended 是否首页推荐状态更新]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-01-12T22:23:06+0800
     */
    public function StatusHomeRecommended()
    {
        // 是否ajax
        if (!IS_AJAX) {
            return $this->error('非法访问');
        }

        // 开始操作
        $params = input('post.');
        $params['admin'] = $this->admin;
        $params['field'] = 'is_home_recommended';
        return GoodsService::GoodsStatusUpdate($params);
    }
    /**
     * 通过xlsx文件批量上传商品信息
     * @author   hwz
     * @version  0.0.1
     * @datetime 2020-08-12T14:21:09
     */
    public function Upload()
    {

        // 是否ajax
        if (!IS_AJAX) {
            return $this->error('非法访问');
        }
        require_once 'extend/phpexcel/PHPExcel/IOFactory.php';
        //文件校验
        $file = $_FILES['file'];
        //$this->importCsv($file['tmp_name'],0,2);
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
        //设置获取excel对象
        if ($file_types[1] == 'xls') {
            //设置获取excel对象
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        } else {
            //设置获取excel对象
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007'); //配置成2003版本，因为office版本可以向下兼容
        }
        //$objReader = \PHPExcel_IOFactory::createReader('Excel2007');//配置成2003版本，因为office版本可以向下兼容
        $objReader->setReadDataOnly(TRUE);  //加上这行速度就提上来了
        set_time_limit(0); //设置页面等待时间
        ini_set('memory_limit', '-1'); //不限制内存
        $objPHPExcel = $objReader->load($file['tmp_name'], $encode = 'utf-8'); //$file 为解读的excel文件
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumn = $sheet->getHighestColumn(); // 取得总列数
        if ($highestRow <= 2 && $highestColumn != 'V') {
            return DataReturn('上传的文件内容不合规，请修改文件后重新上传', -2);
        }
        $success_item = $fail_item = 0;

        $goods_info = [];     //商品基础信息
        $goodscount = 0;   //商品个数
        for ($j = 3; $j <= $highestRow; $j++) {
            $good_title = trim($objPHPExcel->getActiveSheet()->getCell('A' . $j)->getValue()); //标题名称
            $spec_type_value = trim($objPHPExcel->getActiveSheet()->getCell('Q' . $j)->getValue()); //规格值->欧码
            $spec_base_price = trim($objPHPExcel->getActiveSheet()->getCell('S' . $j)->getValue()); //价格(规格基础)
            $spec_base_inventory = trim($objPHPExcel->getActiveSheet()->getCell('T' . $j)->getValue()); //库存(规格基础)
            $spec_base_weight = trim($objPHPExcel->getActiveSheet()->getCell('U' . $j)->getValue()); //重量(规格基础)
            $spec_base_coding = trim($objPHPExcel->getActiveSheet()->getCell('R' . $j)->getValue()); //规格编码(规格基础)->美码
            $spec_base_barcode = trim($objPHPExcel->getActiveSheet()->getCell('V' . $j)->getValue()); //条形码(规格基础)
            $spec_base_original_price = trim($objPHPExcel->getActiveSheet()->getCell('W' . $j)->getValue()); //原价(规格基础)
            if (empty($spec_type_value) && empty($spec_base_price) && empty($spec_base_inventory) && empty($spec_base_weight) && empty($spec_base_coding) && empty($spec_base_barcode) && empty($spec_base_original_price)) {
                continue;
            }
            if ($good_title != null) {
                if (is_object($good_title)) {
                    $good_title = $good_title->__toString(); //处理PHPExcel_RichText问题

                }
                if (mb_strlen($good_title) < 2 || mb_strlen($good_title) > 60) {
                    return DataReturn('上传的文件的A' . $j . '单元格格式有错误，标题名称格式 2~60 个字符，请修改文件后重新上传', -2);
                }

                $goods_info[$goodscount]['title'] = $good_title;
                $good_simple_desc = $objPHPExcel->getActiveSheet()->getCell('C' . $j)->getValue(); //简述
                if (is_object($good_simple_desc)) {
                    $good_simple_desc = $good_simple_desc->__toString();
                }
                if (mb_strlen($good_simple_desc) > 160) {
                    return DataReturn('上传的文件的C' . $j . '单元格格式有错误，商品简述格式 最多160个字符，请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['simple_desc'] = $good_simple_desc;
                $good_model = $objPHPExcel->getActiveSheet()->getCell('D' . $j)->getValue(); //型号1
                if (is_object($good_model)) {
                    $good_model = $good_model->__toString();
                }
                if (mb_strlen($good_model) > 30) {
                    return DataReturn('上传的文件的D' . $j . '单元格格式有错误，商品型号格式 最多30个字符，请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['model'] = $good_model;
                $good_category_name = $objPHPExcel->getActiveSheet()->getCell('E' . $j)->getValue(); //商品分类名
                if (is_object($good_category_name)) {
                    $good_category_name = $good_category_name->__toString();
                }
                if ($good_category_name != 'AP' && $good_category_name != 'FT' && $good_category_name != 'AC') {
                    return DataReturn('上传的文件的E' . $j . '单元格格式有错误，商品分类必须填写为AP,AC或FT，请修改文件后重新上传', -2);
                }
                $brand_name = $objPHPExcel->getActiveSheet()->getCell('E' . $j)->getValue(); //性别
                if (is_object($brand_name)) {
                    $brand_name = $brand_name->__toString();
                }
                if ($brand_name == 'MEN') {
                    $goods_info[$goodscount]['brand_id'] = 3;
                } else if ($brand_name == 'WOMEN') {
                    $goods_info[$goodscount]['brand_id'] = 4;
                } else if ($brand_name == 'UNISEX') {
                    $goods_info[$goodscount]['brand_id'] = 5;
                } else {
                    return DataReturn('上传的文件的E' . $j . '单元格格式有错误，GENDER必须填写为MEN,WOMEN或UNISEX，请修改文件后重新上传', -2);
                }
                //$goods_info['brand_id']=$brand_name;
                $product_type = $objPHPExcel->getActiveSheet()->getCell('F' . $j)->getValue(); //产品类型,二级/三级，前面的BU为一级
                if (is_object($product_type)) {
                    $product_type = $product_type->__toString();
                }
                $product_type_array = explode('/', $product_type);
                $product_type = implode(',', $product_type_array);
                $good_category_name = $good_category_name . ',' . $product_type;
                $params['category'] = $good_category_name;
                $categoryids = GoodsService::GoodsCategoryIds($params);
                $goods_info[$goodscount]['category_id'] = $categoryids;

                $good_inventory_unit = $objPHPExcel->getActiveSheet()->getCell('G' . $j)->getValue(); //商品库存单位
                if (is_object($good_inventory_unit)) {
                    $good_inventory_unit = $good_inventory_unit->__toString();
                }
                if (mb_strlen($good_inventory_unit) < 1 || mb_strlen($good_inventory_unit) > 6) {
                    return DataReturn('上传的文件的G' . $j . '单元格格式有错误，库存单位格式 1~6 个字符，请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['inventory_unit'] = $good_inventory_unit;
                $good_photos = $objPHPExcel->getActiveSheet()->getCell('H' . $j)->getValue(); //商品封面图片,对应s_good_photo
                if (is_object($good_photos)) {
                    $good_photos = $good_photos->__toString();
                }
                //去除图片路径中的换行符
                $order = array("\r\n", "\n", "\r");
                $replace = '';
                $good_photos = str_replace($order, $replace, $good_photos);
                $arr = array();
                $array = explode(';', $good_photos);
                for ($i = 0; $i < sizeof($array); $i++) {
                    if (substr($array[$i], 0, 1) != '/') {
                        return DataReturn('上传的文件的H' . $j . '图片路径有错误，请修改文件后重新上传', -2);
                    }
                    $arr[$i] = '/static/upload/images/goods' . $array[$i];
                }
                $goods_info[$goodscount]['photo'] = $arr;
                $buy_min_number = $objPHPExcel->getActiveSheet()->getCell('I' . $j)->getValue(); //商品最低起购数量
                if (is_object($buy_min_number)) {
                    $buy_min_number = $buy_min_number->__toString();
                }
                if (empty($buy_min_number) || !is_numeric($buy_min_number) || (is_numeric($buy_min_number) && $buy_min_number <= 0)) {
                    return DataReturn('上传的文件的I' . $j . '单元格格式有错误，请填写有效的最低起购数量，请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['buy_min_number'] = strval(round($buy_min_number));
                $buy_max_number = $objPHPExcel->getActiveSheet()->getCell('J' . $j)->getValue(); //商品最大购买数量
                if (is_object($buy_max_number)) {
                    $buy_max_number = $buy_max_number->__toString();
                }
                $goods_info[$goodscount]['buy_max_number'] = strval(round($buy_max_number));

                $is_deduction_inventory = $objPHPExcel->getActiveSheet()->getCell('K' . $j)->getValue(); //是否扣减库存（0否, 1是）
                if (is_object($is_deduction_inventory)) {
                    $is_deduction_inventory = $is_deduction_inventory->__toString();
                }
                $goods_info[$goodscount]['is_deduction_inventory'] = strval(round($is_deduction_inventory));
                $is_shelves = $objPHPExcel->getActiveSheet()->getCell('L' . $j)->getValue(); //是否上架（0否, 1是）
                if (is_object($is_shelves)) {
                    $is_shelves = $is_shelves->__toString();
                }
                $goods_info[$goodscount]['is_shelves'] = strval(round($is_shelves));
                $is_home_recommended = $objPHPExcel->getActiveSheet()->getCell('M' . $j)->getValue(); //是否首页推荐（0否, 1是）
                if (is_object($is_home_recommended)) {
                    $is_home_recommended = $is_home_recommended->__toString();
                }
                $goods_info[$goodscount]['is_home_recommended'] = strval(round($is_home_recommended));
                $home_recommended_images = $objPHPExcel->getActiveSheet()->getCell('N' . $j)->getValue(); //首页推荐图片
                if (is_object($home_recommended_images)) {
                    $home_recommended_images = $home_recommended_images->__toString();
                }
                if (substr($home_recommended_images, 0, 1) != '/') {
                    return DataReturn('上传的文件的N' . $j . '图片路径有错误，请修改文件后重新上传', -2);
                }
                $home_recommended_images = '/static/upload/images/goods' . $home_recommended_images;
                $goods_info[$goodscount]['home_recommended_images'] = $home_recommended_images;
                //                $is_exist_many_spec = $objPHPExcel->getActiveSheet()->getCell('U' . $j)->getValue();//是否存在多个规格（0否, 1是）
                //                if (is_object($is_exist_many_spec)) {
                //                    $is_exist_many_spec = $is_exist_many_spec->__toString();
                //                }
                $spec_type_name = $objPHPExcel->getActiveSheet()->getCell('O' . $j)->getValue(); //规格名
                if (is_object($spec_type_name)) {
                    $spec_type_name = $spec_type_name->__toString();
                }
                $goods_info[$goodscount]['spec_type_name'] = $spec_type_name;
                //$spec_type_value = $objPHPExcel->getActiveSheet()->getCell('P' . $j)->getValue();//规格值
                if (is_object($spec_type_value)) {
                    $spec_type_value = $spec_type_value->__toString();
                }
                if (empty($spec_type_value)) {
                    return DataReturn('上传的文件的P' . $j . '单元格格式有错误，请填写规格值（欧码），请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['spec_type_value'][] = is_float($spec_type_value) ? strval(round($spec_type_value)) : $spec_type_value;
                //$spec_base_price = $objPHPExcel->getActiveSheet()->getCell('Q' . $j)->getValue();//价格(规格基础)
                if (is_object($spec_base_price)) {
                    $spec_base_price = $spec_base_price->__toString();
                }
                if (empty($spec_base_price)) {
                    return DataReturn('上传的文件的R' . $j . '单元格格式有错误，请填写价格，请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['spec_base_price'][] = $spec_base_price;
                //$spec_base_inventory = $objPHPExcel->getActiveSheet()->getCell('R' . $j)->getValue();//库存(规格基础)
                if (is_object($spec_base_inventory)) {
                    $spec_base_inventory = $spec_base_inventory->__toString();
                }
                if (empty($spec_base_inventory)) {
                    return DataReturn('上传的文件的S' . $j . '单元格格式有错误，请填写库存，请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['spec_base_inventory'][] = $spec_base_inventory;
                //$spec_base_weight = $objPHPExcel->getActiveSheet()->getCell('S' . $j)->getValue();//重量(规格基础)
                if (is_object($spec_base_weight)) {
                    $spec_base_weight = $spec_base_weight->__toString();
                }
                if (empty($spec_base_weight)) {
                    return DataReturn('上传的文件的T' . $j . '单元格格式有错误，请填写重量，请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['spec_base_weight'][] = $spec_base_weight;
                //$spec_base_coding = $objPHPExcel->getActiveSheet()->getCell('T' . $j)->getValue();//规格编码(规格基础)
                if (is_object($spec_base_coding)) {
                    $spec_base_coding = $spec_base_coding->__toString();
                }
                if (empty($spec_base_coding)) {
                    return DataReturn('上传的文件的Q' . $j . '单元格格式有错误，请填写规格值（美码），请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['spec_base_coding'][] = $spec_base_coding;
                //$spec_base_barcode = $objPHPExcel->getActiveSheet()->getCell('U' . $j)->getValue();//条形码(规格基础)
                if (is_object($spec_base_barcode)) {
                    $spec_base_barcode = $spec_base_barcode->__toString();
                }
                if (empty($spec_base_barcode)) {
                    return DataReturn('上传的文件的U' . $j . '单元格格式有错误，请填写条形码，请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['spec_base_barcode'][] = $spec_base_barcode;
                //$spec_base_original_price = $objPHPExcel->getActiveSheet()->getCell('V' . $j)->getValue();//原价(规格基础)
                if (is_object($spec_base_original_price)) {
                    $spec_base_original_price = $spec_base_original_price->__toString();
                }
                if (empty($spec_base_original_price)) {
                    return DataReturn('上传的文件的V' . $j . '单元格格式有错误，请填写原价，请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['spec_base_original_price'][] = $spec_base_original_price;
                $goods_info[$goodscount]['spec_base_extends'][] = '';
            } else {
                //$spec_type_value = $objPHPExcel->getActiveSheet()->getCell('P' . $j)->getValue();//规格值
                if (is_object($spec_type_value)) {
                    $spec_type_value = $spec_type_value->__toString();
                }
                if (empty($spec_type_value)) {
                    return DataReturn('上传的文件的P' . $j . '单元格格式有错误，请填写规格值（欧码），请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['spec_type_value'][] = is_float($spec_type_value) ? strval(round($spec_type_value)) : $spec_type_value;
                //$spec_base_price = $objPHPExcel->getActiveSheet()->getCell('Q' . $j)->getValue();//价格(规格基础)
                if (is_object($spec_base_price)) {
                    $spec_base_price = $spec_base_price->__toString();
                }
                if (empty($spec_base_price)) {
                    return DataReturn('上传的文件的R' . $j . '单元格格式有错误，请填写价格，请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['spec_base_price'][] = $spec_base_price;
                //$spec_base_inventory = $objPHPExcel->getActiveSheet()->getCell('R' . $j)->getValue();//库存(规格基础)
                if (is_object($spec_base_inventory)) {
                    $spec_base_inventory = $spec_base_inventory->__toString();
                }
                if (empty($spec_base_inventory)) {
                    return DataReturn('上传的文件的S' . $j . '单元格格式有错误，请填写库存，请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['spec_base_inventory'][] = $spec_base_inventory;
                //$spec_base_weight = $objPHPExcel->getActiveSheet()->getCell('S' . $j)->getValue();//重量(规格基础)
                if (is_object($spec_base_weight)) {
                    $spec_base_weight = $spec_base_weight->__toString();
                }
                if (empty($spec_base_weight)) {
                    return DataReturn('上传的文件的T' . $j . '单元格格式有错误，请填写重量，请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['spec_base_weight'][] = $spec_base_weight;
                //$spec_base_coding = $objPHPExcel->getActiveSheet()->getCell('T' . $j)->getValue();//规格编码(规格基础)
                if (is_object($spec_base_coding)) {
                    $spec_base_coding = $spec_base_coding->__toString();
                }
                if (empty($spec_base_coding)) {
                    return DataReturn('上传的文件的Q' . $j . '单元格格式有错误，请填写规格值（美码），请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['spec_base_coding'][] = $spec_base_coding;
                //$spec_base_barcode = $objPHPExcel->getActiveSheet()->getCell('U' . $j)->getValue();//条形码(规格基础)
                if (is_object($spec_base_barcode)) {
                    $spec_base_barcode = $spec_base_barcode->__toString();
                }
                if (empty($spec_base_barcode)) {
                    return DataReturn('上传的文件的U' . $j . '单元格格式有错误，请填写条形码，请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['spec_base_barcode'][] = $spec_base_barcode;
                //$spec_base_original_price = $objPHPExcel->getActiveSheet()->getCell('V' . $j)->getValue();//原价(规格基础)
                if (is_object($spec_base_original_price)) {
                    $spec_base_original_price = $spec_base_original_price->__toString();
                }
                if (empty($spec_base_original_price)) {
                    return DataReturn('上传的文件的V' . $j . '单元格格式有错误，请填写原价，请修改文件后重新上传', -2);
                }
                $goods_info[$goodscount]['spec_base_original_price'][] = $spec_base_original_price;
                $goods_info[$goodscount]['spec_base_extends'][] = '';
            }

            $nextline = $j + 1;
            $nextline_goodstitle = $objPHPExcel->getActiveSheet()->getCell('A' . $nextline)->__toString();
            if ($nextline_goodstitle != null && $nextline_goodstitle != $good_title) {
                $goodscount = $goodscount + 1;
            }
        }

        // 启动事务
        Db::startTrans();

        //获取商品列表
        $goods = Db::name('Goods')->field('id')->where('is_shelves', '=', 1)->select();
        //更新商品（将以前的商品状态设为下架状态）
        foreach ($goods as $k => $v) {
            $params['admin'] = $this->admin;
            $params['field'] = 'is_shelves';
            $params['state'] = 0;
            $params['id'] = $v['id'];
            $ret = GoodsService::GoodsStatusUpdate($params);
            if ($ret['code'] != 0) {
                // 回滚事务
                Db::rollback();
                return $ret;
            }
        }
        //将excel中的商品插入数据库
        foreach ($goods_info as $item) {
            $len = count($item['spec_type_value']);
            $catid = explode(',', $item['category_id'])[0];
            if ($catid != 3) {
                $arr = array();
                for ($i = 0; $i < $len; $i++) {
                    $arr[$i] = array(
                        'spec_type_value' => $item['spec_type_value'][$i],
                        'specifications_price' => $item['spec_base_price'][$i],
                        'specifications_number' => $item['spec_base_inventory'][$i],
                        'specifications_weight' => $item['spec_base_weight'][$i],
                        'specifications_coding' => $item['spec_base_coding'][$i],
                        'specifications_barcode' => $item['spec_base_barcode'][$i],
                        'specifications_original_price' => $item['spec_base_original_price'][$i]
                    );
                }

                if ($catid == 1) {
                    usort($arr, 'self::cmp');
                } else if ($catid == 2) {
                    usort($arr, 'self::mySort');
                }

                for ($i = 0; $i < $len; $i++) {
                    $item['spec_type_value'][$i] = $arr[$i]['spec_type_value'];
                    $item['spec_base_price'][$i] = $arr[$i]['specifications_price'];
                    $item['spec_base_inventory'][$i] = $arr[$i]['specifications_number'];
                    $item['spec_base_weight'][$i] = $arr[$i]['specifications_weight'];
                    $item['spec_base_coding'][$i] = $arr[$i]['specifications_coding'];
                    $item['spec_base_barcode'][$i] = $arr[$i]['specifications_barcode'];
                    $item['spec_base_original_price'][$i] = $arr[$i]['specifications_original_price'];
                }
            }


            $params = [
                'id' => '',
                'title' => $item['title'], //标题
                'title_color' => '',
                'simple_desc' => $item['simple_desc'], //简述
                'model' => $item['model'], //Material Code
                'category_id' => $item['category_id'], //商品分类
                'brand_id' => $item['brand_id'], //GENDER
                'place_origin' => '9', //产地，默认上海
                //'product_type'=>0,//
                'inventory_unit' => $item['inventory_unit'], //库存单位
                'give_integral' => '0', //购买赠送积分，默认为0
                'buy_min_number' => $item['buy_min_number'], //最低起购数量
                'buy_max_number' => $item['buy_max_number'], //最大购买数量
                'home_recommended_images' => $item['home_recommended_images'], //首页推荐图片
                'is_deduction_inventory' => $item['is_deduction_inventory'], //是否扣减库存
                'is_shelves' => $item['is_shelves'], //是否上架
                'is_home_recommended' => $item['is_home_recommended'], //是否首页推荐
                'spec_base_title_663649' => $item['spec_type_name'],
                'spec_base_value_663649' => $item['spec_type_value'],
                'specifications_name_534454' => $item['spec_type_name'],
                'specifications_value_534454' => $item['spec_type_value'],
                'specifications_price' => $item['spec_base_price'],
                'specifications_number' => $item['spec_base_inventory'],
                'specifications_weight' => $item['spec_base_weight'],
                'specifications_coding' => $item['spec_base_coding'],
                'specifications_barcode' => $item['spec_base_barcode'],
                'specifications_original_price' => $item['spec_base_original_price'],
                'specifications_extends' => $item['spec_base_extends'],
                'photo' => $item['photo'],
                'content_web' => '',
                'seo_title' => '',
                'seo_keywords' => '',
                'seo_desc' => ''
            ];


            $params['admin'] = $this->admin;
            $ret = GoodsService::GoodsSave($params);
            if ($ret['code'] != 0) {
                // 回滚事务
                Db::rollback();
                return $ret;
            }
        }

        // 提交事务
        Db::commit();
        return DataReturn('操作成功', 0);
    }

    //衣服尺码排序回调函数
    function mySort($a, $b)
    { //固定格式
        $size = array('XS', 'S', 'S/M', 'M', 'L', 'L/XL', 'XL', 'XXL', 'XXL/XXXL', 'XXXL');
        $key1 = array_search($a['spec_type_value'], $size); //获取索引值
        $key2 = array_search($b['spec_type_value'], $size);
        return strnatcmp($key1, $key2); //比较索引值并排序
    }
    //鞋子尺码排序回调函数
    function cmp($a, $b)
    {
        if ($a['spec_type_value'] == $b['spec_type_value']) {
            return 0;
        }
        return ($a['spec_type_value'] < $b['spec_type_value']) ? -1 : 1;
    }
}
