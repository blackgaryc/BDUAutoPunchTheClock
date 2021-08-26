<?php
include_once 'ptc.config.php';
function ptc_email_send($email_to,$email_header,$email_body){
    $time_start=time();
    global $swaks;
    $str='';
    foreach ($swaks as $k => $v){
        $str.= " $k $v ";
    }
    $cmd= "timeout 12 swaks $str --to $email_to --header 'Subject:$email_header' --body '$email_body' ".PTC_EMAIL_LOG." &";
    $send_data=[
        'to'=>$email_to,
        'header'=>$email_header,
        'body'=>$email_body
    ];
    $ret_code=0;
    $out_put=array();
    exec($cmd,$out_put,$ret_code) ;
    $time_spend=time()-$time_start;
    //    print_r($ret);
    return [
        'time_spend'=>$time_spend,
        'send_log'=>json_encode($send_data),
        'email_send_status'=>$ret_code==0?1:0
    ];
}
