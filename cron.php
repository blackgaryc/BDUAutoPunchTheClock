<?php
include_once "ptc.config.php";
include_once "var/ptc_bg/ptc_curl.php";
include_once "var/ptc_bg/db_tool.php";
include_once "var/ptc_bg/email.php";

define('TEST_MODE',true);

global $db_conn;
$user_all=$db_conn->select_all_from_table("ptc_user",' where ptc_status=1 ');
//print_r($user_all);
if ($user_all==false)
    return;
while ($user=$user_all->fetch_object()){
    $user_data=[
        'username'=>$user->stu_id,
        'password'=>$user->stu_password
    ];
//    print_r($user_data);
    ptc_cron($user_data);
}

mysqli_free_result($user_all);

function ptc_cron($user_data){

    $PTC_email_header=[
        '打卡失败',
        '打卡成功',
    ];
    $PTC_run_status=0;
    $user_email=db_user_get_inf($user_data['username'],'email');
    $url= "http://stu.bdu.edu.cn/index";
    $cookies_full_path=ptc_get_cookies_from_url($url,PTC_DEFAULT_COOKIES_DIR);
    $login_status=ptc_do_login($user_data,$cookies_full_path);
    if (!$login_status['status']){
        //  登录失败发送邮件
        $PTC_run_message=$login_status['msg'];
        $ret=ptc_email_send($user_email,$PTC_email_header[$PTC_run_status],$PTC_run_message);
        //  数据库记录
        db_log_write_history_log($PTC_run_status,$PTC_run_message,$ret['email_send_status']);
        //  结束本次
        return;
    }
    //  获取上一次打卡数据
    $this_data= ptc_curl_get_lastone_array($cookies_full_path);
    global $PTC_DEFAULT_HTTP_USER_AGENT;
    db_write_data2post($PTC_DEFAULT_HTTP_USER_AGENT,$this_data,array());
    //  提交上一次的打卡数据
    $sub_ret= ptc_submit_string(array2string_for_get($this_data),$cookies_full_path);
    //  解析返回的数据
    $sub_ret=json_decode($sub_ret);
    if(defined('TEST_MODE')){
        print_r($sub_ret);
    }

    $PTC_run_status=$sub_ret->result==1?1:0;

    $email_body=is_null($sub_ret->errorInfoList[0]->message)?mysql_timestamp():$sub_ret->errorInfoList[0]->message;
    $e_ret=ptc_email_send($user_email,$PTC_email_header[$PTC_run_status],$email_body);
    $msg=$sub_ret->errorInfoList[0]->message==null?'打卡成功':$sub_ret->errorInfoList[0]->message;
    db_log_write_history_log($PTC_run_status,$msg,$e_ret['return_code']==0?1:0);
}