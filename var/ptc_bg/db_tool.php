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
function mysql_timestamp(int $time = null)
{
    return date('"Y-m-d H:i:s"', is_null($time) ? time() : $time);
}

/**
 * @return bool|mysqli_result
 *  创建生成激活码，会记录当前生成激活码的管理
 */
function db_create_activation_code()
{
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
function db_log_write_history_log(int $ptc_run_status, string $message, int $email_send_status = 0)
{
    global $user_data;
    global $db_conn;
    $arr = array(
        'stu_id' => str($user_data['username']),
        'ptc_run_result' => $ptc_run_status,
        'ptc_run_msg' => str($message),
        'ptc_run_time' => mysql_timestamp(),
        'ptc_email_send_status' => $email_send_status
    );
//    print_r($arr);
    return $db_conn->insert_data2table($arr, 'ptc_history_record');
}

/**
 * @param string $stu_id
 * @param string $to_get
 * @return mixed 根据学号返回指定的属性值，只适合缺少单独的数据时使用。
 */
function db_user_get_inf(string $stu_id, string $to_get)
{
    global $db_conn;
    $res = $db_conn->select([$to_get], 'ptc_user', "where stu_id='$stu_id'");
    return $res->fetch_array()[$to_get];
}

/**
 * @param bool $time_today
 * @param int $run_type
 * @return int
 */
function db_user_log_numbers(bool $time_today, int $run_type = 1): int
{
    global $db_conn;
    if ($time_today)
        $ct = ' where  ptc_run_time>=' . mysql_timestamp(strtotime(date("Y-m-d"), time()));
    if ($run_type == 1 or $run_type == 0)
        $rt = "  and ptc_run_result=$run_type ";
    $res = $db_conn->select_all_from_table('ptc_history_record', $ct . $rt);
    $count = $res->num_rows;
    mysqli_free_result($res);
    return $count;
}

function db_user_log(string $stu_id, int $start, int $end)
{
    global $db_conn;
    return $db_conn->select_all_from_table('ptc_history_record', " where stu_id='$stu_id' and ptc_run_time>" . mysql_timestamp($start) . " and ptc_run_time<" . mysql_timestamp($end) . '');
}

function db_user_log_status_1(string $stu_id){
    $res=db_user_log($stu_id,strtotime(date("Y-m-d 6:00"), time()),strtotime(date("Y-m-d 12:00"), time()));
    $rows=$res->num_rows;
    mysqli_free_result($res);
    return $rows;
}
function db_user_log_status_2(string $stu_id){
    $res=db_user_log($stu_id,strtotime(date("Y-m-d 12:00"), time()),strtotime(date("Y-m-d 17:00"), time()));
    $rows=$res->num_rows;
    mysqli_free_result($res);
    return $rows;
}


/**
 * @param string $stu_id
 * @param string $passwd
 * @return bool
 */
function db_user_check_login(string $stu_id, string $passwd): bool
{
    global $db_conn;
    $sql = "select * from ptc_user where stu_id=? and stu_password=? ";
    $stmt = $db_conn->getDbConn()->prepare($sql);
    $stmt->bind_param('ss', $stu_id, $passwd);
    $stmt->execute();
    return $stmt->get_result()->num_rows == 1;
}

/**
 * @param string $stu_id
 * @param string $token
 */
function db_user_update_login_inf(string $stu_id, string $token)
{
    global $db_conn;
    $sql = "update ptc_user SET token='$token' WHERE stu_id='$stu_id';";
    $db_conn->getDbConn()->query($sql);
}

/**
 * @param string $token
 * @return false|mixed
 */
function db_user_get_by_token(string $token)
{
    global $db_conn;
    $res = $db_conn->select_all_from_table('ptc_user', " where token='$token'; ");
    if ($res->num_rows == 1) {
        $u = $res->fetch_array();
        mysqli_free_result($res);
        return $u;
    }
    return false;
}

/**
 * @param string $stu_id
 * @return string
 */
function ptc_user_generate_token(string $stu_id): string
{
    $db_max_len = 32;
    $len = rand(0, $db_max_len);
    $token = substr(md5($stu_id) . md5(time()), rand(0, $db_max_len - $len), $db_max_len - $len) . RandString::randString($len);
    return $token;
}

function db_write_data2post(string $ua,array $post,array $setting){
    global $user_data;
    $stuid=$user_data['username'];
    $data_post=json_encode($post);
    $ptc_setting=json_encode($setting);
    $sql= "INSERT INTO ptc_data2post VALUES ('$stuid','$data_post','$ua','$ptc_setting') ON DUPLICATE KEY UPDATE stu_id='" . $stuid . "';";
//    echo $sql;
    global $db_conn;
    $db_conn->getDbConn()->query($sql);
}