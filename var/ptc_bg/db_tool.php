<?php
//include_once "ptc.config.php";

/**
 * @param string $str
 * @return string 返回被单引号包裹的字符串
 */
function str(string $str): string
{
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
    return $db_conn->insert_data2table(
        ['activation_code' => str(RandString::randString(64)),
            'create_user' => str($user_data['username']),
            'timestamp_create' => mysql_timestamp()
        ], 'ptc_activation_code');
}

/**
 * @param int $ptc_run_status
 * @param string $message
 * @param int $email_send_status 0==failed 1==success
 * @return bool|mysqli_result
 * 向数据库中记录打卡历史
 */
function db_log_write_history_log(int $ptc_run_status, string $message, int $email_send_status=0){
    global $user_data;
    global $db_conn;
    $arr=array(
        'stu_id'=>str($user_data['username']),
        'ptc_run_result'=>$ptc_run_status,
        'ptc_run_msg'=>str($message),
        'ptc_run_time'=>mysql_timestamp(),
        'ptc_email_send_status'=>$email_send_status
    );
//    print_r($arr);
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

function db_user_log_numbers(bool $time_today,int $run_type=1){
    global $db_conn;
    if ($time_today)
        $ct=' where  ptc_run_time>='.mysql_timestamp(strtotime(date("Y-m-d"),time()));

    if ($run_type==1 or $run_type==0)
        $rt="  and ptc_run_result=$run_type ";
    $res=$db_conn->select_all_from_table('ptc_history_record',$ct.$rt);
    $count=$res->num_rows;
    mysqli_free_result($res);
    return $count;
}