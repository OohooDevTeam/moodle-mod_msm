<?php
/*
 * file for testing!! will be deleted before release
 */
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" media = "screen" href="css/MsmDisplay.css" />

        <script src="development-bundle/jquery-1.7.1.js"></script>

        <link rel='stylesheet' href='development-bundle/themes/ui-lightness/jquery.ui.all.css'/>
        <script src='development-bundle/jquery-1.7.1.js'></script>
        <script src='development-bundle/external/jquery.bgiframe-2.1.2.js'></script>
        <script src='development-bundle/ui/jquery.ui.core.js'></script>
        <script src='development-bundle/ui/jquery.ui.widget.js'></script>
        <script src='development-bundle/ui/jquery.ui.mouse.js'></script>
        <script src='development-bundle/ui/jquery.ui.draggable.js'></script>
        <script src='development-bundle/ui/jquery.ui.position.js'></script>
        <script src='development-bundle/ui/jquery.ui.resizable.js'></script>
        <script src='development-bundle/ui/jquery.ui.dialog.js'></script>

        <script type='text/javascript' src='js/Splitter.js'></script>
        <script type='text/javascript' src='js/showRightPage.js'></script>
        <script type='text/javascript' src='js/popup.js'></script>

        <script type='text/javascript'>
            jQuery(document).ready(function(){
                $('.dialogs').dialog({
                    autoOpen: false,
                    width: 'auto'
                });
         
                $('#MySplitter').splitter();
        
        
            });
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

$content .= '<li id="defminibutton-1" class="defminibutton" onmouseover="popup(' . 1 . ')">';
$content .="<span style='cursor:pointer;'>";
$content .="Comment";
$content .= "</span>";
$content .= "</li>";

$content .= "<div id='dialog-1' class='dialogs'>";
$content .= '<table class="mathtable" border="1" cellpadding="3"><tr>
					                <td style="border-width:1px !important;"><para>
                           <p>
                              <b>Speed</b> [km/h]</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>s</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>2s</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>3s</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>4s</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>5s</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>6s</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>7s</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>8s</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>9s</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>10s</p>
                        </para></td>
				              </tr><tr>
					                <td style="border-width:1px !important;"><para>
                           <p>
                              <b>Break Distance</b> [m]</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>d</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>4d</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>9d</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>16d</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>25d</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>36d</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>49d</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>64d</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>81d</p>
                        </para></td>
					                <td style="border-width:1px !important;"><para>
                           <p>100d</p>
                        </para></td>
				              </tr></table>';
$content .= "<div>";

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
