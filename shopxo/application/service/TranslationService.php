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

/**
 *
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class TranslationService
{
    /**
     *
     * @author  Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-05-22
     * @desc    description
     * @param   [array]           $params [输入参数]
     */
    public static function language($value,$from="zh",$to="en")
    {
        $value_code = $value;
        $appid = "20170419000045190"; //您注册的API Key
        $key = "X6HKNgfZp8eGI_XZZ9Ic"; //密钥
        $salt = rand(1000000000,9999999999); //随机数
        $sign = md5($appid.$value_code.$salt.$key); //签名
        $value_code = urlencode($value_code);
        //生成翻译API的URL
        $languageurl = "http://api.fanyi.baidu.com/api/trans/vip/translate?q=$value_code&appid=$appid&salt=$salt&from=$from&to=$to&sign=$sign";
        $text = json_decode(self::language_text($languageurl));
        $lan = $text->trans_result;
        $result = '';
        foreach ($lan as $k => $v)
        {
            $result .= ucwords($v->dst) ."\n";
        }
        return $result;
    }
    private static function language_text($reqURL)
    {
        $ch = curl_init($reqURL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        $result = curl_exec($ch);
        if($result){
            curl_close($ch);
            return $result;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return ("curl出错，错误码:$error");
        }
    }
}
?>