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
    </head>
    <body>
        <button id="button1" type="button">echo</button>
    </body>
</html>

