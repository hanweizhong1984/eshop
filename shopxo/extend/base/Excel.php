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
namespace base;

/**
 * Excel驱动
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2017-01-10T21:51:08+0800
 */
class Excel
{
    private $filename;
    private $file_type;
    private $suffix;
    private $data;
    private $title;
    private $jump_url;
    private $msg;
    private $horizontal_center;
    private $vertical_center;
    private $warap_text;

    /**
     * [__construct 构造方法]
     * @param    [string]       $params['filename']                [文件名称（追加当前时间）]
     * @param    [string]       $params['suffix']                [文件后缀名（默认xls）]
     * @param    [string]       $params['jump_url']                [出错跳转url地址（默认上一个页面）]
     * @param    [string]       $params['msg']                    [错误提示信息]
     * @param    [string]       $params['file_type']            [导出文件类型（默认excel）]
     * @param    [array]        $params['title']                [标题（二维数组）]
     * @param    [array]        $params['data']                    [数据（二维数组）]
     * @param    [int]          $params['horizontal_center']    [是否水平居中 1是]
     * @param    [int]          $params['vertical_center']        [是否垂直居中 1是]
     * @param    [int]          $params['warap_text']            [是否内容自动换行 1是]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-01-10T15:09:17+0800
     */
    public function __construct($params = [])
    {
        // 文件名称
        $date = date('YmdHis');
        $this->filename = isset($params['filename']) ? $params['filename'] . '-' . $date : $date;

        // 文件类型, 默认excel
        $type_all = array('excel' => 'vnd.ms-excel', 'pdf' => 'pdf');
        $this->file_type = (isset($params['file_type']) && isset($type_all[$params['file_type']])) ? $type_all[$params['file_type']] : $type_all['excel'];

        // 文件后缀名称
        $this->suffix = empty($params['suffix']) ? 'xls' : $params['suffix'];

        // 标题
        $this->title = isset($params['title']) ? $params['title'] : [];

        // 数据
        $this->data = isset($params['data']) ? $params['data'] : [];

        // 出错跳转地址
        $this->jump_url = empty($params['jump_url']) ? (empty($_SERVER['HTTP_REFERER']) ? __MY_URL__ : $_SERVER['HTTP_REFERER']) : $params['jump_url'];

        // 错误提示信息
        $this->msg = empty($params['msg']) ? 'title or data cannot be empty!' : $params['msg'];

        // 水平,垂直居中
        $this->horizontal_center = isset($params['horizontal_center']) ? intval($params['horizontal_center']) : 1;
        $this->vertical_center = isset($params['vertical_center']) ? intval($params['vertical_center']) : 1;

        // 内容自动换行
        $this->warap_text = isset($params['warap_text']) ? intval($params['warap_text']) : 1;

        // 引入PHPExcel类库
        require ROOT . 'extend' . DS . 'phpexcel' . DS . 'PHPExcel.php';
    }

    /**
     * [Export excel文件导出]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-01-10T15:12:01+0800
     */
    public function Export()
    {
        // 是否有数据
        if (empty($this->title) && empty($this->data)) {
            echo '<script>alert("' . $this->msg . '");</script>';
            echo '<script>window.location.href="' . $this->jump_url . '"</script>';
            die;
        }

        // excel对象
        $excel = new \PHPExcel();

        // 操作第一个工作表
        $excel->setActiveSheetIndex(0);

        // 文件输出类型
        switch ($this->file_type) {
            // PDF
            case 'pdf':
                $writer = PHPExcel_IOFactory::createWriter($excel, 'PDF');
                $writer->setSheetIndex(0);
                break;

            // 默认EXCEL
            default:
                //$writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel5'); //xls版本
                $writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007'); //xlsx版本
        }

        // 获取配置编码类型
        $excel_charset = MyC('admin_excel_charset', 0);
        $charset = lang('common_excel_charset_list')[$excel_charset]['value'];

        // 水平,垂直居中
        if ($this->horizontal_center == 1) {
            $excel->getActiveSheet()->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        if ($this->vertical_center == 1) {
            $excel->getActiveSheet()->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        }

        //设置自动换行
        if ($this->warap_text == 1) {
            $excel->getActiveSheet()->getDefaultStyle()->getAlignment()->setWrapText(true);
        }

        // 标题
        $temp_key = 0;
        foreach ($this->title as $k => $v) {
            $col = \PHPExcel_Cell::stringFromColumnIndex($temp_key) . '1';
            if (isset($v['type']) && !empty($v['type']) && $v['type'] == 'arrays') {
                if (isset($v['contents']) && !empty($v['contents'])) {
                    foreach ($v['contents'] as $kk => $vv) {
                        $col = \PHPExcel_Cell::stringFromColumnIndex($temp_key) . '1';
                        $excel->getActiveSheet()->setCellValue($col, ($excel_charset == 0) ? $vv['name'] : iconv('utf-8', $charset, $vv['name']));
                        $temp_key++;
                    }
                }
            } else if (isset($v['content']) && !empty($v['content'])) {
                foreach ($v['content'] as $kk => $vv) {
                    $col = \PHPExcel_Cell::stringFromColumnIndex($temp_key) . '1';
                    $excel->getActiveSheet()->setCellValue($col, ($excel_charset == 0) ? $vv['name'] : iconv('utf-8', $charset, $vv['name']));
                    $temp_key++;
                }
            } else {
                $excel->getActiveSheet()->setCellValue($col, ($excel_charset == 0) ? $v['name'] : iconv('utf-8', $charset, $v['name']));
                $temp_key++;
            }
        }


        // 内容

        foreach ($this->data as $k => $v) {
            if($k > 0){
                $i = $i + 1;
            }else{
                $i = $k + 2;
            }
            $en = 0;
            $j = '';
            if (is_array($v) && !empty($v)) {
                $temp_key = 0;
                $j = $i;
                $break = 0;
                foreach ($this->title as $tk => $tv) {
                    $height = isset($tv['height']) ? intval($tv['height']) : 0;
                    $width = isset($tv['width']) ? intval($tv['width']) : $height;
                    $col = \PHPExcel_Cell::stringFromColumnIndex($temp_key);

                    if ($tv['type'] == 'images') {
                        $drawing = new \PHPExcel_Worksheet_Drawing();
                        $drawing->setPath($v[$tk]);

                        // 设置宽度高度
                        $number = empty($height) ? 50 : $height - 10;
                        $drawing->setHeight($number);
                        $drawing->setWidth($number);
                        $drawing->setCoordinates($col . $i);

                        // 图片偏移距离
                        $x = ($width > 0) ? (($width - $number) / 2) + 15 : 15;
                        $drawing->setOffsetX($x);
                        $drawing->setOffsetY(15);
                        $drawing->setWorksheet($excel->getActiveSheet());
                    } else {
                        if (is_array($v[$tk]) && !empty($v) && $tv['type'] == 'arrays') {
                            //这部分是循环订单中的商品的
                            foreach ($v[$tk] as $kk => $vv) {
                                $vv['spec_text']=explode(':', $vv['spec_text'])[1];
                                $break++;
                                $startTmpkey = $temp_key;
                                if (isset($tv['contents'])) {
                                    foreach ($tv['contents'] as $ck => $cv) {
                                        $height = isset($cv['height']) ? intval($cv['height']) : 0;
                                        $width = isset($cv['width']) ? intval($cv['width']) : $height;
                                        $col = \PHPExcel_Cell::stringFromColumnIndex($temp_key);
                                        $datatype=\PHPExcel_Cell_DataType::TYPE_STRING;
                                        if($col=='F'||$col=='G'||$col=='K'||$col=='M')//F,G,K,M
                                        {
                                            $datatype=\PHPExcel_Cell_DataType::TYPE_NUMERIC;
                                        }
                                        $excel->getActiveSheet()->setCellValueExplicit($col . $i, ($excel_charset == 0) ? $vv[$ck] : iconv('utf-8', $charset, $vv[$ck]), $datatype);
                                        $temp_key++;
                                        // 单元格宽高
                                        if ($width > 0) {
                                            $excel->getActiveSheet()->getColumnDimension($col)->setWidth($width / 5);
                                        }
                                        if ($height > 0) {
                                            $excel->getActiveSheet()->getRowDimension($i)->setRowHeight($height);
                                        }
                                    }
                                }

                                $en = $temp_key;
                                if(count($v[$tk]) == $break){
                                    break;
                                }
                                if (count($v[$tk]) > 1) {
                                    $temp_key = $startTmpkey;
                                    $i = $i + 1;
                                }
                            }
                        } else if (isset($tv['content'])) {
                            //这部分是循环订单中的用户信息和收件人地址信息的
                            foreach ($tv['content'] as $ck => $cv) {
                                if (isset($v[$tk][$ck])==false) {
                                    $response = array(
                                        'success' => true,
                                        'error'=>'买家数据不完整',
                                        'url' =>$this->jump_url
                                    );
                                    return $response;
                                }
                                $height = isset($cv['height']) ? intval($cv['height']) : 0;
                                $width = isset($cv['width']) ? intval($cv['width']) : $height;
                                $col = \PHPExcel_Cell::stringFromColumnIndex($en);
                                if($ck=='username'&&strstr($v[$tk][$ck],'@')){
                                    $v[$tk][$ck]=substr($v[$tk][$ck],0,strpos($v[$tk][$ck], '@'));
                                }
                                $excel->getActiveSheet()->setCellValueExplicit($col . $j, ($excel_charset == 0) ? $v[$tk][$ck] : iconv('utf-8', $charset, $v[$tk][$ck]), \PHPExcel_Cell_DataType::TYPE_STRING);
                                $en++;
                                $temp_key++;
                                // 单元格宽高
                                if ($width > 0) {
                                    $excel->getActiveSheet()->getColumnDimension($col)->setWidth($width / 5);
                                }
                                if ($height > 0) {
                                    $excel->getActiveSheet()->getRowDimension($i)->setRowHeight($height);
                                }
                            }
                        } else {
                            $col = \PHPExcel_Cell::stringFromColumnIndex($en);
                            $dtype= \PHPExcel_Cell_DataType::TYPE_STRING;
                            if($col=='AC'||$col=='AD'||$col=='AE'||$col=='AF'||$col=='AG'||$col=='AH') //AC,AD,AE,AF,AG,AH
                            {
                                  $dtype= \PHPExcel_Cell_DataType::TYPE_NUMERIC;
                            }
                            $excel->getActiveSheet()->setCellValueExplicit($col . $j, ($excel_charset == 0) ? $v[$tk] : iconv('utf-8', $charset, $v[$tk]), $dtype);
                            $en++;
                            $temp_key++;
                        }
                    }

                    // 单元格宽高
                    if ($width > 0) {
                        $excel->getActiveSheet()->getColumnDimension($col)->setWidth($width / 5);
                    }
                    if ($height > 0) {
                        $excel->getActiveSheet()->getRowDimension($i)->setRowHeight($height);
                    }
                }
            }
        }

        // 头部
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control:must-revalidate, post-check=0, pre-check=0');
        header('Content-Type:application/force-download');
        header('Content-Type: application/' . $this->file_type . ';charset=' . $charset);
        header('Content-Type:application/octet-stream');
        header('Content-Type:application/download');
        header('Content-Disposition:attachment;filename=' . $this->filename . '.' . $this->suffix);
        header('Content-Transfer-Encoding:binary');
        //$writer->save('php://output');
        $filePath = 'orders/'.$this->filename . '.' . $this->suffix;
        $writer->save($filePath);
        $response = array(
            'success' => true,
            'url' => $filePath
        );
        return $response;
    }

    /**
     * [Import excel文件导入]
     * @param    [string]    $file [文件位置,空则读取全局excel的临时文件]
     * @return   [array]           [数据列表]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-04-06T18:18:55+0800
     */
    public function Import($file = '')
    {
        // 文件为空则取全局文变量excel的临时文件
        if (empty($file) && !empty($_FILES['excel']['tmp_name'])) {
            $file = $_FILES['excel']['tmp_name'];
        }

        // 文件地址是否有误,title数据是否有数据
        if (empty($file) || empty($this->title)) {
            echo '<script>alert("' . $this->msg . '");</script>';
            echo '<script>window.location.href="' . $this->jump_url . '"</script>';
            die;
        }

        // 取得文件基础数据
        $reader = \PHPExcel_IOFactory::createReader('Excel5');
        $excel = $reader->load($file);

        // 取得总行数
        $worksheet = $excel->getActiveSheet();

        // 取得总列数
        $highest_row = $worksheet->getHighestRow();

        // 取得最高的列
        $highest_column = $worksheet->getHighestColumn();

        // 总列数
        $highest_column_index = \PHPExcel_Cell::columnIndexFromString($highest_column);

        // 定义变量
        $result = [];
        $field = [];

        // 读取数据
        for ($row = 1; $row <= $highest_row; $row++) {
            // 临时数据
            $info = [];

            // 注意 highest_column_index 的列数索引从0开始
            for ($col = 0; $col < $highest_column_index; $col++) {
                $value = $worksheet->getCellByColumnAndRow($col, $row)->getFormattedValue();
                if ($row == 1) {
                    foreach ($this->title as $tk => $tv) {
                        if ($value == $tv['name']) {
                            $tv['field'] = $tk;
                            $field[$col] = $tv;
                        }
                    }
                } else {
                    if (!empty($field)) {
                        $info[$field[$col]['field']] = ($field[$col]['type'] == 'int') ? trim(ScienceNumToString($value)) : trim($value);
                    }
                }
            }
            if ($row > 1) {
                $result[] = $info;
            }
        }
        return $result;
    }
}

?>