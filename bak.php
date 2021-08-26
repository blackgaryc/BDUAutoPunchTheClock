<?php
?>
<!-- jQuery and JavaScript Bundle with Popper -->
<script src="js/jquery-3.6.0.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<link href="css/bootstrap.min.css" rel="stylesheet">
<!--<script src="china-area-data/data.js"></script>-->
<div class="container">
    <form id="china">
    </form>

    <script>
        function add_select_location(data,location) {
            $(`#china`).append(`<select id='${location}'></select>`)
            for(var key in data[`${location}`]){
                var value1=data[`${location}`][`${key}`];
                $(`#${location}`).append(`<option id='${key}'>${value1}</option>`);
            }
        }

        $.get("china-area-data/v5/data.json",function(json,status){
            var jsonStr = JSON.stringify(json);
            var data=eval('('+jsonStr+')');
            add_select_location(data,'86');
        });

        $('#select_address_location').click(function () {
        });
        $(`#select_address_location`).change(function () {
            var value=$('select option:selected').attr('id');
            // $(`#select_address_location`).after('<>');
        });
    </script>

</div>
