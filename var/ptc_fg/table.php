<?php
if (!defined("PTC_ROOT_DIR"))
    exit();

//define('PTC_ROOT_DIR',dirname(__FILE__));
require_once "var/ptc_fg/bootstrap.php";



$url="http://stu.bdu.edu.cn/content/tabledata/student/temp/zzdk?bSortable_0=false&bSortable_1=true&iSortingCols=1&iDisplayStart=0&iDisplayLength=12&iSortCol_0=1&sSortDir_0=desc&_t_s_=".time();
/**
 * @param $data
 * 打卡历史记录
 */
function ptc_table_show_history($data){
    $data=json_decode($data);
    $data=$data->aaData;
    ?>
    <table class="table table-dark">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">打卡时间</th>
            <th scope="col">打卡地点</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i=0;$i<count($data);$i++){ ?>
            <tr>
                <th scope="row"><?php echo $i; ?></th>
                <td><?php echo $data[$i]->UPDATE_TIME; ?></td>
                <td><?php echo $data[$i]->DKD; ?></td>
                <td><a href="#<?php echo $data[$i]->DM; ?>">查看详情</a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php
}

$dm="";
$url="http://stu.bdu.edu.cn/wap/menu/student/temp/zzdk/_child_/detail/$dm?_t_s_=".time();
/**
 * @param $data
 * 打卡详细信息
 */
function ptc_table_show_detail($data){
    $data=json_decode($data);
    $jzdMc='';
    if (!is_null($data->jzdSheng)){
        $jzdMc.=$data->jzdSheng->mc;
    }
    if (!is_null($data->jzdShi)){
        $jzdMc.='/'.$data->jzdShi->mc;
    }
    if (!is_null($data->jzdXian)){
        $jzdMc.='/'.$data->jzdXian->mc;
    }
    $echo_data=[
        '打卡日期'=>$data->dkrq,
        '打卡地点'=>$data->dkd,
        '现居住地'=>$jzdMc,
        '详细地址'=>$data->jzdDz,
        '常驻地址'=>$data->jzdDz2,
        '联系电话'=>$data->lxdh,
        '是否离校'=>$data->sflx=='1'?'是':'否',
        '本人今日体温'=>$data->twM->mc,
        '本人异常症状'=>$data->yczk->mc,
        '发病日期'=>$data->fbrq,
        '是否就诊'=>$data->jtqk,
        '本人身体情况'=>$data->brStzk->mc,
        '本人接触传染源'=>$data->brJccry->mc,
        '家人身体状况'=>$data->jrStzk->mc,
        '家人接触传染源'=>$data->jrJccry->mc,
        '疫苗接种'=>$data->xgym=='0'? "未接种":($data->xgym=='1'?"已接种未完成":"已接种已完成"),
        '备注'=>$data->bz
    ];
    ?>
    <table class="table table-dark">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">名称</th>
            <th scope="col">值</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i=0;
        foreach ($echo_data as $k=>$v){ ?>
            <tr>
                <th scope="row"><?php echo $i;$i++; ?></th>
                <td><?php echo $k; ?></td>
                <td><?php echo $v; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php
}