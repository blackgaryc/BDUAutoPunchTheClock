<?php
/**
 * 本页面封装了关于stu.bdu.edu.cn的一些第三方方法，基于用户帐号密码的认证可以实现自动化。
 */

/**
 * 默认的USER-AGENT，即服务器识别你是什么浏览器的一种判别方法，更改此属性，可以让服务器识别连接终端的基本信息
 */
const PTC_DEFAULT_HTTP_USER_AGENT = "Mozilla/5.0 (Linux; Android 6.0.1; Moto G (4)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Mobile Safari/537.36";
/**
 * 默认的cookies存放地址,需要www-data有权限读写
 */
define('PTC_DEFAULT_COOKIES_DIR', dirname(__FILE__) . '/tmp');


/**
 * @param string $url
 * @param string|null $cookies_dir
 * @return string|null return the cookie filename
 */
function ptc_get_cookies_from_url(string $url, string $cookies_dir = null): ?string
{
    if (!$cookies_dir) {
        $cookies_dir = sys_get_temp_dir();
    }
    $cl = curl_init($url);
    curl_setopt($cl, CURLOPT_USERAGENT, PTC_DEFAULT_HTTP_USER_AGENT);
    $cookies_filename = tempnam($cookies_dir, 'cookie_');
    curl_setopt($cl, CURLOPT_COOKIEJAR, $cookies_filename);
    curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($cl);
    curl_close($cl);
    return $cookies_filename;
}

/**
 * @param string $url
 * @param array $data
 * @param string|null $cookie_file
 * @return string|null 返回提交后的响应信息。
 */
function ptc_post_array2url(string $url, array $data, string $cookie_file = null): ?string
{
    $string_data = array2string_for_get($data);
    return extracted($url, $cookie_file, $string_data);
}

/**
 * @param string $url
 * @param string $data
 * @param string|null $cookie_file
 * @return string|null post字符串到url
 */
function ptc_post_string2url(string $url, string $data, string $cookie_file = null): ?string
{
    $string_data = $data;
    return extracted($url, $cookie_file, $string_data);
}

/**
 * @param string $url
 * @param string|null $cookie_file
 * @param string $string_data
 * @return bool|string
 */
function extracted(string $url, ?string $cookie_file, string $string_data)
{
    $cl = curl_init($url);
    curl_setopt($cl, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($cl, CURLOPT_USERAGENT, PTC_DEFAULT_HTTP_USER_AGENT);
    curl_setopt($cl, CURLOPT_POST, true);
    curl_setopt($cl, CURLOPT_POSTFIELDS, $string_data);
    curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1);
    $ret = curl_exec($cl);
    curl_close($cl);
    return $ret;
}

/**
 * @param array $post_data
 * @return array 返回加密完成后的帐号密码
 */
function ptc_password_encode(array $post_data): array
{
    $pd = md5($post_data['password']);
    $post_data['password'] = substr($pd, 0, 5) . 'a' . substr($pd, 5, 4) . 'b' . substr($pd, 9);
    $post_data['password'] = substr($post_data['password'], 0, 32);
    return $post_data;
}

/**
 * @param string $url
 * @param string $cookie_path
 * @return bool|string 返回服务器get得到的数据
 */
function ptc_get_data_from_server(string $url, string $cookie_path)
{
    $cl = curl_init($url);
    curl_setopt($cl, CURLOPT_USERAGENT, PTC_DEFAULT_HTTP_USER_AGENT);
    curl_setopt($cl, CURLOPT_COOKIEFILE, $cookie_path);
    curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1);
    $ret = curl_exec($cl);
    curl_close($cl);
    return $ret;
}

/**
 * @param array $user
 * @param string $cookies_full_path
 * @return array
 */
function ptc_do_login(array $user, string $cookies_full_path)
{
    if (strlen($user['password'] != 32)) {
        $user = ptc_password_encode($user);
    }
//    print_r($user);
    $url = "http://stu.bdu.edu.cn/website/login";
    $login_ret_mes = ptc_post_array2url($url, $user, $cookies_full_path);
    $login_ret_mes = json_decode($login_ret_mes);
    $status = 0;
    if (!is_null($login_ret_mes->goto2)) {
        $msg = "登录成功";
        $status = 1;
    } else if ($login_ret_mes->error == true) {
        $msg = $login_ret_mes->msg;
    } else {
        $msg = "打卡系统错误";
    }

    return array('msg' => $msg, 'status' => $status);

}

function array2string_for_get(array $data)
{
    $string_data = '';
    foreach ($data as $k => $v) {
        $string_data .= "$k=$v&";
    }
    return substr($string_data, 0, strlen($string_data) - 1);
}

/**
 * @param $key
 * @return string 疫苗接种情况 文字转化
 */
function ymjzmc($key)
{
    $data = [
        '未接种', '已接种未完成', '已接种已完成'
    ];
    return $data[$key];
}

/**
 * @param $ptc_last_one
 * @return false|string 居住地代码拼接
 */
function jzdvalue($ptc_last_one)
{
    $str = '';
    if (is_null($ptc_last_one->jzdSheng->dm)) {
        $str .= '';
        //
    } else {
        //11111,
        $str .= $ptc_last_one->jzdSheng->dm . ',';
    }
    if (is_null($ptc_last_one->jzdShi->dm)) {
        //11111.
        $str .= '';
    } else {
        $str .= $ptc_last_one->jzdShi->dm . ',';
        //11111,22222,
    }
    if (is_null($ptc_last_one->jzdXian->dm)) {
        //11111,22222
        $str = substr($str, 0, strlen($str) - 1);
    } else {
        $str .= $ptc_last_one->jzdXian->dm;
    }
    return $str;
}

function ptc_curl_get_lastone_array(string $cookies_full_path)
{
    //获取上次提交的数据并转化成本次要提交的数据
    $url = "http://stu.bdu.edu.cn/content/student/temp/zzdk/lastone?_t_s_=" . time();
    $ptc_last_one = ptc_get_data_from_server($url, $cookies_full_path);
    $ptc_last_one = json_decode($ptc_last_one);
    $post_array = array(
        'dkdz' => $ptc_last_one->dkdz,
        'dkly' => $ptc_last_one->dkly,
        'dkd' => $ptc_last_one->dkd,
        'jzdValue' => jzdvalue($ptc_last_one),
        'jzdSheng.dm' => $ptc_last_one->jzdSheng->dm,
        'jzdShi.dm' => $ptc_last_one->jzdShi->dm,
        'jzdXian.dm' => $ptc_last_one->jzdXian->dm,
        'jzdDz' => $ptc_last_one->jzdDz,
        'jzdDz2' => $ptc_last_one->jzdDz2,
        'lxdh' => $ptc_last_one->lxdh,
        'sflx' => $ptc_last_one->sflx,
        'twM.dm' => $ptc_last_one->twM->dm,
        'tw1' => $ptc_last_one->twM->mc,
        'yczk.dm' => $ptc_last_one->yczk->dm,
        'yczk1' => $ptc_last_one->yczk->mc,
        'fbrq' => $ptc_last_one->fbrq,
        'jzInd' => $ptc_last_one->jzInd,
        'jzYy' => $ptc_last_one->jzYy,
        'zdjg' => $ptc_last_one->zdjg,
        'fxrq' => $ptc_last_one->fxrq,
        'brStzk.dm' => $ptc_last_one->brStzk->dm,
        'brStzk1' => $ptc_last_one->brStzk->mc,
        'brJccry.dm' => $ptc_last_one->brJccry->dm,
        'brJccry1' => $ptc_last_one->brJccry->mc,
        'jrStzk.dm' => $ptc_last_one->jrStzk->dm,
        'jrStzk1' => $ptc_last_one->jrStzk->mc,
        'jrJccry.dm' => $ptc_last_one->jrJccry->dm,
        'jrJccry1' => $ptc_last_one->jrJccry->mc,
        'xgym' => $ptc_last_one->xgym,
        'xgym1' => ymjzmc($ptc_last_one->xgym),
        'hsjc' => $ptc_last_one->hsjc,
        'hsjc1' => is_null($ptc_last_one->hsjc) ? '' : $ptc_last_one->hsjc->mc,
        'bz' => $ptc_last_one->bz,
        'operationType' => $ptc_last_one->operationType,
        'dm' => ''
    );
    return $post_array;
}

function ptc_submit_string(string $data, string $cookie_file)
{
//    $url = "http://stu.bdu.edu.cn/content/student/temp/zzdk?_t_s_=" . time();
    $url = "http://stu.bdu.edu.cn/content/student/temp/zzdk";
    return extracted($url, $cookie_file, $data);
}