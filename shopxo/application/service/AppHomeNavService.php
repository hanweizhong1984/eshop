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
namespace app\service;

use think\Db;
use app\service\ResourcesService;

/**
 * APP首页导航服务层
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class AppHomeNavService
{
    /**
     * 首页导航列表
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-06T21:31:53+0800
     * @param    [array]          $params [输入参数]
     */
    public static function AppHomeNavList($params = [])
    {
        $where = empty($params['where']) ? [] : $params['where'];
        $field = empty($params['field']) ? '*' : $params['field'];
        $order_by = empty($params['order_by']) ? 'sort asc' : trim($params['order_by']);

        $m = isset($params['m']) ? intval($params['m']) : 0;
        $n = isset($params['n']) ? intval($params['n']) : 10;

        // 获取品牌列表
        $data = Db::name('AppHomeNav')->where($where)->order($order_by)->limit($m, $n)->select();
        if(!empty($data))
        {
            $common_platform_type = lang('common_platform_type');
            $common_is_enable_tips = lang('common_is_enable_tips');
            $common_app_event_type = lang('common_app_event_type');
            $common_is_text_list = lang('common_is_text_list');
            foreach($data as &$v)
            {
                // 是否需要登录
                if(isset($v['is_need_login']))
                {
                    $v['is_need_login_text'] = $common_is_text_list[$v['is_need_login']]['name'];
                }

                // 是否启用
                if(isset($v['is_enable']))
                {
                    $v['is_enable_text'] = $common_is_enable_tips[$v['is_enable']]['name'];
                }

                // 平台类型
                if(isset($v['platform']))
                {
                    $v['platform_text'] = $common_platform_type[$v['platform']]['name'];
                }

                // 事件类型
                if(isset($v['event_type']) && $v['event_type'] != -1)
                {
                    $v['event_type_text'] = $common_app_event_type[$v['event_type']]['name'];
                }

                // 图片地址
                if(isset($v['images_url']))
                {
                    $v['images_url_old'] = $v['images_url'];
                    $v['images_url'] = ResourcesService::AttachmentPathViewHandle($v['images_url']);
                }

                // 时间
                if(isset($v['add_time']))
                {
                    $v['add_time_time'] = date('Y-m-d H:i:s', $v['add_time']);
                    $v['add_time_date'] = date('Y-m-d', $v['add_time']);
                }
                if(isset($v['upd_time']))
                {
                    $v['upd_time_time'] = date('Y-m-d H:i:s', $v['upd_time']);
                    $v['upd_time_date'] = date('Y-m-d', $v['upd_time']);
                }
            }
        }
        return DataReturn('处理成功', 0, $data);
    }

    /**
     * 首页导航总数
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-10T22:16:29+0800
     * @param    [array]          $where [条件]
     */
    public static function AppHomeNavTotal($where)
    {
        return (int) Db::name('AppHomeNav')->where($where)->count();
    }

    /**
     * 首页导航列表条件
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2018-09-29
     * @desc    description
     * @param   [array]          $params [输入参数]
     */
    public static function AppHomeNavListWhere($params = [])
    {
        $where = [];

        if(!empty($params['keywords']))
        {
            $where[] = ['name', 'like', '%'.$params['keywords'].'%'];
        }

        // 是否更多条件
        if(isset($params['is_more']) && $params['is_more'] == 1)
        {
            // 等值
            if(isset($params['is_enable']) && $params['is_enable'] > -1)
            {
                
                $where[] = ['is_enable', '=', intval($params['is_enable'])];
            }
            if(isset($params['is_need_login']) && $params['is_need_login'] > -1)
            {
                $where[] = ['is_need_login', '=', intval($params['is_need_login'])];
            }
            if(isset($params['event_type']) && $params['event_type'] > -1)
            {
                $where[] = ['event_type', '=', intval($params['event_type'])];
            }
            if(!empty($params['platform']))
            {
                $where[]= ['platform', '=', $params['platform']];
            }

            if(!empty($params['time_start']))
            {
                $where[] = ['add_time', '>', strtotime($params['time_start'])];
            }
            if(!empty($params['time_end']))
            {
                $where[] = ['add_time', '<', strtotime($params['time_end'])];
            }
        }

        return $where;
    }

    /**
     * 首页导航数据保存
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2018-12-19
     * @desc    description
     * @param   [array]          $params [输入参数]
     */
    public static function AppHomeNavSave($params = [])
    {
        // 请求类型
        $p = [
            [
                'checked_type'      => 'length',
                'key_name'          => 'name',
                'checked_data'      => '2,60',
                'error_msg'         => '名称长度 2~60 个字符',
            ],
            [
                'checked_type'      => 'in',
                'key_name'          => 'platform',
                'checked_data'      => array_column(lang('common_platform_type'), 'value'),
                'error_msg'         => '平台类型有误',
            ],
            [
                'checked_type'      => 'in',
                'key_name'          => 'event_type',
                'checked_data'      => array_column(lang('common_app_event_type'), 'value'),
                'is_checked'        => 2,
                'error_msg'         => '事件值类型有误',
            ],
            [
                'checked_type'      => 'length',
                'key_name'          => 'event_value',
                'checked_data'      => '255',
                'error_msg'         => '事件值最多 255 个字符',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'images_url',
                'checked_data'      => '255',
                'error_msg'         => '请上传图片',
            ],
            [
                'checked_type'      => 'length',
                'key_name'          => 'sort',
                'checked_data'      => '3',
                'error_msg'         => '顺序 0~255 之间的数值',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }

        // 附件
        $data_fields = ['images_url'];
        $attachment = ResourcesService::AttachmentParams($params, $data_fields);

        // 数据
        $data = [
            'name'          => $params['name'],
            'platform'      => $params['platform'],
            'event_type'    => isset($params['event_type']) ? intval($params['event_type']) : -1,
            'event_value'   => $params['event_value'],
            'images_url'    => $attachment['data']['images_url'],
            'bg_color'      => isset($params['bg_color']) ? $params['bg_color'] : '',
            'sort'          => intval($params['sort']),
            'is_enable'     => isset($params['is_enable']) ? intval($params['is_enable']) : 0,
            'is_need_login' => isset($params['is_need_login']) ? intval($params['is_need_login']) : 0,
        ];

        if(empty($params['id']))
        {
            $data['add_time'] = time();
            if(Db::name('AppHomeNav')->insertGetId($data) > 0)
            {
                return DataReturn('添加成功', 0);
            }
            return DataReturn('添加失败', -100);
        } else {
            $data['upd_time'] = time();
            if(Db::name('AppHomeNav')->where(['id'=>intval($params['id'])])->update($data))
            {
                return DataReturn('编辑成功', 0);
            }
            return DataReturn('编辑失败', -100); 
        }
    }

    /**
     * 首页导航删除
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2018-12-18
     * @desc    description
     * @param   [array]          $params [输入参数]
     */
    public static function AppHomeNavDelete($params = [])
    {
        // 请求参数
        $p = [
            [
                'checked_type'      => 'empty',
                'key_name'          => 'id',
                'error_msg'         => '操作id有误',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }

        // 删除操作
        if(Db::name('AppHomeNav')->where(['id'=>$params['id']])->delete())
        {
            return DataReturn(lang('删除成功'));
        }

        return DataReturn(lang('删除失败或资源不存在'), -100);
    }

    /**
     * 首页导航状态更新
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-06T21:31:53+0800
     * @param    [array]          $params [输入参数]
     */
    public static function AppHomeNavStatusUpdate($params = [])
    {
        // 请求参数
        $p = [
            [
                'checked_type'      => 'empty',
                'key_name'          => 'id',
                'error_msg'         => '操作id有误',
            ],
            [
                'checked_type'      => 'empty',
                'key_name'          => 'field',
                'error_msg'         => '操作字段有误',
            ],
            [
                'checked_type'      => 'in',
                'key_name'          => 'state',
                'checked_data'      => [0,1],
                'error_msg'         => '状态有误',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }

        // 数据更新
        if(Db::name('AppHomeNav')->where(['id'=>intval($params['id'])])->update([$params['field']=>intval($params['state'])]))
        {
           return DataReturn('编辑成功');
        }
        return DataReturn('编辑失败或数据未改变', -100);
    }

    /**
     * APP获取首页导航
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2018-11-19
     * @desc    description
     * @param   array           $params [description]
     */
    public static function AppHomeNav($params = [])
    {
        // 平台
        $platform = APPLICATION_CLIENT_TYPE;

        // web端手机访问
        if($platform == 'pc' && IsMobile())
        {
            $platform = 'h5';
        }

        // 缓存
        $key = config('shopxo.cache_navigation_key').$platform;
        $data = cache($key);

        if(empty($data))
        {
            // 获取导航数据
            $data = Db::name('AppHomeNav')->field('id,name,images_url,event_value,event_type,bg_color,is_need_login')->where(['platform'=>$platform, 'is_enable'=>1])->order('sort asc')->select();
            if(!empty($data))
            {
                foreach($data as &$v)
                {
                    $v['images_url_old'] = $v['images_url'];
                    $v['images_url'] = ResourcesService::AttachmentPathViewHandle($v['images_url']);
                    $v['event_value'] = empty($v['event_value']) ? null : $v['event_value'];
                }
            }

            // 存储缓存
            cache($key, $data, 3600*24);
        }
        return $data;
    }
}
?>