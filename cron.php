<?php
include_once "ptc.config.php";
include_once "var/ptc_bg/ptc_curl.php";
include_once "var/ptc_bg/db_tool.php";
include_once "var/ptc_bg/email.php";


$user_data=[
    'username'=>'',
    'password'=>''
];

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

function ptc_cron($user_data){
    $user_email=db_user_get_inf($user_data['username'],'email');
    $url= "http://stu.bdu.edu.cn/index";
    $cookies_full_path=ptc_get_cookies_from_url($url,PTC_DEFAULT_COOKIES_DIR);
    $login_status=ptc_do_login($user_data,$cookies_full_path);
    if (!$login_status['status']){
        $ret=ptc_email_send($user_email,'登录失败',$login_status['msg']);
//    登录失败发送邮件
        db_log_write_login_failed($login_status,$ret['return_code']==0?1:0);
//    结束本次
        return;
    }
//登录成功则在数据库中获取要提交的数据，cookie如果为空则不打卡，并发送邮件反馈。
    $this_data= ptc_curl_get_lastone_array($cookies_full_path);
//echo array2string_for_get($this_data);
    $sub_ret= ptc_submit_string(array2string_for_get($this_data),$cookies_full_path);
    $sub_ret=json_decode($sub_ret);
    print_r($sub_ret);
    if ($sub_ret->result==1){
        $e_ret=ptc_email_send($user_email,'打卡成功',"打卡时间：".mysql_timestamp());
    }else{
        $e_ret=ptc_email_send($user_email,'打卡失败',$sub_ret->errorInfoList[0]->message);
    }
}