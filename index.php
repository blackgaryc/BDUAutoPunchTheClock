<?php

define("PTC_ROOT_DIR",dirname(__FILE__));
include_once "ptc.config.php";
include_once "var/ptc_bg/db_tool.php";
include_once "var/ptc_clazz/RandString.php";

global $user;

if (isset($_GET['logout'])){
    setcookie('token','',time(),'/');
    echo "<script>window.location.reload();</script>";
}

if(isset($_COOKIE['token'])){
    $user=db_user_get_by_token($_COOKIE['token']);
}

if (isset($_POST['stu_id']) and isset($_POST['passwd'])){
    $stu_id=$_POST['stu_id'];
    $passwd=$_POST['passwd'];
    if (db_user_check_login($stu_id,$passwd)){
        $token= ptc_user_generate_token($stu_id);
        setcookie("token",$token,10*60+time(),'/');
        db_user_update_login_inf($stu_id,$token);
        echo json_encode([
            'status'=>true,
            'url'=>'user'
        ]);
    }else{
        echo json_encode([
            'status'=>false,
            'url'=>null
        ]);
    }
    return;
}



require_once "var/ptc_fg/bootstrap.php";
include_once 'var/ptc_fg/banner.php';
echo "<body class='bg-secondary'><div  class='container'>";

switch ($_SERVER['PATH_INFO']){
    case '':
        include_once 'var/ptc_fg/introduce.php';
        break;
    case '/user':
        if(!isset($_COOKIE['token'])){
            echo "<script>alert(\"请先登录\");window.location.replace('login')</script>";
        }
        ?>
        <div class="jumbotron jumbotron-fluid  mt-4 rounded-lg   ">
            <div class="container">
                <h1 class="display-4">今日打卡计划：</h1>
                <p class="lead">上午打卡 状态：<?php $res=db_user_log_status_1($user['stu_id']); echo $res->num_rows==0?'未完成':'已打卡'; ?> 打卡时间: <?php if ($res->num_rows==1): echo $res->fetch_object()->ptc_run_time; endif; ?></p>
                <p class="lead">下午打卡 状态：<?php $res=db_user_log_status_2($user['stu_id']); echo $res->num_rows==0?'未完成':'已打卡'; ?> 打卡时间: <?php if ($res->num_rows==1): echo $res->fetch_object()->ptc_run_time; endif; ?></p>
            </div>
        </div>
<?php
        echo "用户主页:".$user['stu_id'];
        break;
    case '/login':

        include_once 'var/ptc_fg/form_login.php';
        break;
    case '/register':
        include_once 'var/ptc_fg/form_register.php';
        break;
    default:
        echo "404";
        break;
}
echo "</div></body>";

