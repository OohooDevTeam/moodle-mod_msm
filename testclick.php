<?php
/*
 * file for testing!! will be deleted before release
 */
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" media = "screen" href="css/MsmDisplay.css" />

<!--        <style type="text/css">
            .proofblock
            {
                visibility:hidden;
            }
        </style>-->
        <script src="development-bundle/jquery-1.7.1.js"></script>
        <script type='text/javascript' src='js/Splitter.js'></script>
        <script type='text/javascript' src='js/showRightPage.js'></script>
        <script type='text/javascript'>
            jQuery(document).ready(function(){         
                $('#MySplitter').splitter();
                $('.proofblock').hide();
            });
        </script>
    </head>
</html>

<?php
//$blockcontent = '';

$content = '';
$content .= "<div id='MySplitter'>";
$content .= "<div class='leftcol'>";
$content .= "<div class='leftbox'>";
$content .= "<div class='theorem'>";

$content .= "<span class='theoremtitle'>";
$content .= "Title of Theorem";
$content .= "</span>";

$content .= "<span class='theoremtype'>";
$content .="Proposition";
$content .= "</span>";

$content .= "<br />";

$content .= "<div class='theoremcontent'>";
$content .= "theorizing something!";
$content .= "</div>"; // end of theoremcontent

$content .= "<ul class=minibuttons>";

$content .= "<li class='minibutton'>";
$content .="<span style='cursor:pointer;'>";
$content .="Comment";
$content .= "</span>";
$content .= "</li>";

$content .= '<li class="proofbutton" onclick="showRightpage(' . 1 . ')">';
$content .="<span style='cursor:pointer;'>";
$content .="Proof";
$content .= "</span>";
$content .= "</li>";

$content .= "</ul>";

$content .= "</div>"; //theorem
$content .= "</div>"; //leftbox
$content .= "</div>"; //leftcol

$content .= "<div class='rightcol'>";
$content .= "<div class='rightbox'>";

$content .= "hey!";

$content .="</div>";
$content .= "</div>";

$content .= "</div>"; //splitter

$content .= "<div id='proofblock-1' class='proofblock'>";
//$content .= "<div id='proofblock-1' class='proofblock'>";
$content .= "hey include me!!";
$content .= "</div>";


echo $content;
?>
