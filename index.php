<?php

define("PTC_ROOT_DIR",dirname(__FILE__));
require_once "var/ptc_fg/bootstrap.php";
include_once "ptc.config.php";
include_once "var/ptc_bg/db_tool.php";
include_once 'var/ptc_fg/banner.php';
echo "<body class='bg-secondary'><div  class='container'>";

switch ($_SERVER['PATH_INFO']){
    case '':
        include_once 'var/ptc_fg/introduce.php';
        break;
    case '/user':
        echo "用户主页";
        break;
    case '/login':
        if (isset($_POST['stu_id'])and isset($_POST['passwd'])){
            echo "checking login";
            return;
        }
        include_once 'var/ptc_fg/form_login.php';
        break;
    case '/register':
        include_once 'var/ptc_fg/form_register.php';
        break;
    default:
        echo '404.php';
        break;
}
echo "</div></body>";

