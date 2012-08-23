<?php
/*
 * file for testing!! will be deleted before release
 */
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" media = "screen" href="css/MsmDisplay.css" />

        <script src="development-bundle/jquery-1.7.1.js"></script>
        <script type='text/javascript' src='js/Splitter.js'></script>
        <script type='text/javascript' src='js/showRightPage.js'></script>
        <script type='text/javascript'>
            function showme(i){
                $('#defminibutton-'+i).unbind('click');
                $('#defminibutton-'+i).click(function() {
                    alert("pressed the button");
                    $('.rightbox').empty();
                    $('.rightbox').append($('#refcontent-'+i));
                    alert("done append");
    
                    $('#refcontent-'+i).css('display', 'block');
                    alert("done css");
                    $('#refcontent-'+i).toggleClass('refcontent', 'shownrefcontent');
                    alert("done toggling class");
                });
            }
        </script>
    </head>
</html>

<?php
//$blockcontent = '';

$content = '';
$content .= "<div id='MySplitter'>";
$content .= "<div class='leftcol' style='min-width: 542px;'>";
$content .= "<div class='leftbox'>";
$content .= "<div class='def'>";

$content .= "<span class='deftitle'>";
$content .= "Title of Theorem";
$content .= "</span>";

$content .= "<span class='deftype'>";
$content .="Definition";
$content .= "</span>";

$content .= "<br />";

$content .= "<div class='defcontent'>";
$content .= "theorizing something!";
$content .= "</div>"; // end of theoremcontent

$content .= "<ul class=defminibuttons>";

$content .= '<li id="defminibutton-1" class="defminibutton" onmouseover="showme(' . 1 . ')">';
$content .="<span style='cursor:pointer;'>";
$content .="Comment";
$content .= "</span>";
$content .= "</li>";

//$content .= '<li class="proofbutton" onclick="showRightpage(' . 1 . ')">';
//$content .="<span style='cursor:pointer;'>";
//$content .="Proof";
//$content .= "</span>";
//$content .= "</li>";

$content .= "</ul>";

$content .= "</div>"; //theorem
$content .= "</div>"; //leftbox
$content .= "</div>"; //leftcol

$content .= "<div class='rightcol' style='min-width: 542px;'>";
$content .= "<div class='rightbox'>";

$content .= "hey!";

$content .="</div>";
$content .= "</div>";

$content .= "</div>"; //splitter

$content .= "<div id='refcontent-1' class='refcontent' style='display:none;'>";
$content .= "reference material?";
$content .= "</div>";

//$content .= "<div id='proofblock-1' class='proofblock'>";
////$content .= "<div id='proofblock-1' class='proofblock'>";
//$content .= "hey include me!!";
//$content .= "</div>";

echo $content;
?>
