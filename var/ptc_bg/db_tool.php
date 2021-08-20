<?php
//include_once "ptc.config.php";

/**
 * @param string $str
 * @return string 返回被单引号包裹的字符串
 */
function str(string $str){
    return "'$str'";
}

/**
 * @param int|null $time
 * @return false|string 返回一个mysql timestamp字符串，时间如果不指定则是当前时间
 */
function mysql_timestamp(int $time=null){
    return date('"Y-m-d H:i:s"', is_null($time)?time():$time);
}

/**
 * @return bool|mysqli_result
 *  创建生成激活码，会记录当前生成激活码的管理
 */
function db_create_activation_code(){
    global $db_conn;
    global $user_data;
    $res = $db_conn->insert_data2table(
        ['activation_code' => str(RandString::randString(64)),
            'create_user' => str($user_data['username']),
            'timestamp_create' => mysql_timestamp()
        ], 'ptc_activation_code');
    return $res;
}

/**
 * @param array $login_status
 * @param int $email_send_status 0==failed 1==success
 * @return bool|mysqli_result
 * 向数据库中插入登录失败的记录
 */
function db_log_write_login_failed(array $login_status,int $email_send_status=0){
    global $user_data;
    global $db_conn;
    $arr=array(
        'stu_id'=>str($user_data['username']),
        'ptc_run_result'=>$login_status['status'],
        'ptc_run_msg'=>str($login_status['msg']),
        'ptc_run_time'=>mysql_timestamp(),
        'ptc_email_send_status'=>$email_send_status
    );
    return $db_conn->insert_data2table($arr,'ptc_history_record');
}

/**
 * @param string $stu_id
 * @param string $to_get
 * @return mixed 根据学号返回指定的属性值，只适合缺少单独的数据时使用。
 */
function db_user_get_inf(string $stu_id,string $to_get){
    global $db_conn;
    $res=$db_conn->select([$to_get],'ptc_user',"where stu_id='$stu_id'");
    return $res->fetch_array()[$to_get];
}

function db_user_login(string $user,string $passwd){
//    $sql=
}