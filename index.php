<?php
//<<<<<<< HEAD
define("PTC_ROOT_DIR",dirname(__FILE__));
require_once "var/ptc_fg/bootstrap.php";
//echo $_SERVER['REQUEST_URI'];
//echo "<br>";
//echo dirname($_SERVER['REQUEST_URI']);
//echo "<br>";

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
//=======
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- jQuery and JavaScript Bundle with Popper -->
<script src="js/jquery-3.6.0.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<link href="css/bootstrap.min.css" rel="stylesheet">
<!--<script src="china-area-data/data.js"></script>-->
<div class="container">
    <form id="china">

        <label for="location_depth"></label>
        <input type="text" hidden id="location_depth" value="1">
    </form>
    <button class="btn-block btn alert-warning">submit</button>
    <script>
        function add_select_location(data,location,depth=0) {
            // id of append select
            var fa=location+'_'+depth;
            // append select item
            $(`#china`).append(`<select id='${fa}' name='${depth}' class="custom-select alert-success" required="required"><option selected></option></select>`);
            // add option
            for(var key in data[`${location}`]){
                var value1=data[`${location}`][`${key}`];
                $(`#${fa}`).append(`<option id='${key}'>${value1}</option>`);
            }
            var counter=0;
            $(`#${fa}`).change(function () {
                var value=$(`#${fa} option:selected`).attr('id');
                var location_depth=$('#location_depth').val();
                if (value===undefined){
                    for (var i=1;i<=location_depth;i++){
                        $(`select[name="${i}"]`).remove();
                    }
                    $('#location_depth').attr('value',1);
                }
                if (data.hasOwnProperty(value)){
                    if (counter===1){
                        var val=$(`#${fa}`).attr('name');
                        ++val;
                        for (var i=val;i<=location_depth;i++){
                            $(`select[name="${i}"]`).remove();
                        }
                        add_select_location(data,value,val);
                        $('#location_depth').attr('value',++val);
                    }else {
                        add_select_location(data,value,++depth);
                        counter++;
                        $('#location_depth').attr('value',++location_depth)
                    }
                }
            });
        }
        $.get("china-area-data/data.json",function(json,status){
            if (!status){
                alert('lost data.json');
            }
            var jsonStr = JSON.stringify(json);
            var data=eval('('+jsonStr+')');
            add_select_location(data,'86');
        });

    </script>

</div>
>>>>>>> ea2c3201b11728b7637748878afffd954b2372cb
