<?php
if (!defined("PTC_ROOT_DIR"))
    exit();
?>

<div class="p-2">
    <div class="alert alert-danger" role="alert">
        暂未开放，如需使用，请联系管理人员。
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
<div class="m-2 p-3 rounded-lg shadow-lg bg-light border border-dark">
    <form class="mb-4">
        <h2 class="text-center ">PTC-register</h2>
        <div class="form-group">
            <label for="stu_id">学号：</label>
            <input type="text" class="form-control" id="stu_id" aria-describedby="inputHelp">
            <small id="inputHelp" class="form-text text-muted">请输入你的10位数字学号.</small>
        </div>

        <div class="form-group">
            <label for="passwd">密码：</label>
            <input type="password" class="form-control" id="passwd" aria-describedby="inputHelp2">
            <small id="inputHelp2" class="form-text text-muted">请输入你的教辅平台密码，默认是身份证后六位.</small>
        </div>
        <div class="form-group">
            <label for="v-code">注册码：</label>
            <input type="text" class="form-control" id="v-code">
            <small id="inputHelp2" class="form-text text-muted">注册需要激活码，请联系<a href="mqqwpa://im/chat?chat_type=wpa&uin=1808865537&version=1&src_type=web&web_src=oicqzone.com">管理人员</a>.</small>
        </div>
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input float-left" id="check1" >
            <label class="form-check-label" for="check1">同意协议</label>
            <a href="login" class="float-right">已经注册了吗？</a>
        </div>
        <div class="row justify-content-center">
            <button type="submit" class="btn btn-primary">Register</button>
        </div>
    </form>
</div>

