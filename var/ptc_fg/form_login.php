<?php
if (!defined("PTC_ROOT_DIR"))
    exit();

?>
<div class="card-body bg-light rounded-lg m-2 mt-4 mb-3">
    <h5 class="card-title">今日<?php echo db_user_log_numbers(true,1);  ?>次，总计<?php echo db_user_log_numbers(false ,3); ?>次</h5>
    <p class="card-text">请如实提交打卡信息，因为本人错误提交导致的错误，与本平台无关。请重视关注平台反馈的邮件信息，如遇到错误，及时联系管理人员。</p>
<!--    <a href="#" class="btn btn-primary">Go somewhere</a>-->
</div>
<div class="m-2 p-3 rounded-lg shadow-lg bg-light  border border-dark">
    <form onsubmit="">
        <h2 class="text-center ">PTC</h2>
        <div class="form-group">
            <label for="stu_id">学号：</label>
            <input type="text" class="form-control" id="stu_id" aria-describedby="inputHelp" name="stu_id">
            <small id="inputHelp" class="form-text text-muted">请输入你的10位数字学号.</small>
        </div>

        <div class="form-group">
            <label for="passwd">密码：</label>
            <input type="password" class="form-control" id="passwd" name="passwd">
        </div>
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input float-left" id="check1" >
            <label class="form-check-label" for="check1">同意协议</label>
            <a href="register" class="float-right">还没有注册？</a>
        </div>
        <div class="row justify-content-center">
            <button type="button" class="btn btn-primary btn-lg" id="login-btn">Login</button>
        </div>

    </form>
</div>
<script>
    $("#login-btn").click(function (){
        if ($("#login-btn").html()==="Login"){
            $("#login-btn").button('dispose');
            $("#login-btn").text('Loading...');
            $.ajax({
                url:'login',
                type:'post',
                timeout:2000,
                data:"hello",
                dataType: "json",
                success:function (result){
                    alert(result);
                },
                error: function(data) {
                    // 请求失败函数
                    console.log(data);
                }
            });}
    });
</script>