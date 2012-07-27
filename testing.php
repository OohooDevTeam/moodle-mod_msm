<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of testing
 *
 * @author User
 */
class testing
{

    function printText($string)
    {
        echo $string;
    }

}
?>


<html>
    <head>
        <script type='text/javascript' src='../msm/js/jquery-1.7.1.min.js'></script>
        <script type="text/javascript">
            $().ready(function() {
                $('#button1').click(function(){
                    $('body').load("testpage.php");
                    });
                });
            
        </script>
        <style>
            .box
            {
                width: 300px;
                background-color: blue;
            }
            .title{
                float: left;
            }
            .deftext
            {
                float: right;
            }
        </style>
    </head>
    <body>
        <button id="button1" type="button">echo</button>
        <div class="box">
        <div class="title"> hello <span class="deftext"> hey </span></div>
        </div>
    </body>
</html>

