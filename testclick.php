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

        <script type="text/javascript" src="js/jImageMaster/dist/jquery.imagemapster.js"></script>

        <script type='text/javascript' src='js/Splitter.js'></script>
        <script type='text/javascript' src='js/showRightPage.js'></script>
        <script type='text/javascript' src='js/popup.js'></script>

        <script type='text/javascript'>            
            $(document).ready(function(){
                $('.dialogs').dialog({
                    autoOpen: false,
                    width: 'auto'
                });
                
                $('img').mapster({
                    fillColor: 'ff0000',
                    fillOpacity: 0.5
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

$content .= "<img src='newXML/LinearAlgebraRn/Determinants/ims/OrientedAreaPlaneGrid.jpg' usemap='#hey'/>";

$content .= "<map name='hey'>";

$content .= '<area id="pic-1" coords="57,91,383,107" shape="rect" href="#" onmouseover="popup(' . 1 . ')">';
$content .= "<div id='dialog-1' class='dialogs'>";
$content .= '<para>
                                 <para.body>Congratulations! - The grid point you picked is the tip of a vector <math>
                                       <latex>\Vect{y}</latex>
                                    </math> so that.</para.body>
                              </para>
						                        <table column="3">
                                 <tr>
                                    <td rowspan="1" colspan="2" halign="center" valign="middle">
                                       <math>
                                          <latex>\OriVol(\Vect{x},\Vect{y})</latex>
                                       </math>
                                    </td>
                                    <td rowspan="1" colspan="1" halign="center" valign="middle">
                                       <math>
                                          <latex>=</latex>
                                       </math>
                                    </td>
                                    <td rowspan="1" colspan="2" halign="center" valign="middle">
                                       <math>
                                          <latex>+15</latex>
                                       </math>
                                    </td>
                                 </tr>
                              </table>';
$content .= "</div>";
$content .= "</area>";

$content .= '<area id="pic-2" coords="59,222,383,240" shape="rect" href="#" onmouseover="popup(' . 2 . ')">';
$content .= "<div id='dialog-2' class='dialogs'>";
$content .= "<para>
                                 <para.body>Careful! With the vector <math>
                                       <latex>\Vect{y}</latex>
                                    </math> you are specifying you determine a parallelogram whose unoriented area is <math>
                                       <latex>15</latex>
                                    </math>. However, <math>
                                       <latex>(\Vect{x},\Vect{y})</latex>
                                    </math> determine the negative orientation of the plane. So this is not an acceptable grid point.</para.body>
                              </para>";
$content .= "</div>";
$content .= "</area>";

$content .= '<area id="pic-3" coords="57,113,384,217" shape="rect" href="#" onmouseover="popup(' . 3 . ')">';
$content .= "<div id='dialog-3' class='dialogs'>";
$content .= "<para>
                                 <para.body>Sorry! Try another point.</para.body>
                              </para>";
$content .= "</div>";
$content .= "</area>";

$content .= '<area id="pic-4" coords="59,247,382,304" shape="rect" href="#" onmouseover="popup(' . 4 . ')">';
$content .= "<div id='dialog-4' class='dialogs'>";
$content .= "<para>
                                 <para.body>Sorry! Try another point.</para.body>
                              </para>";
$content .= "</div>";
$content .= "</area>";

$content .= '<area id="pic-5" coords="59,26,382,83" shape="rect" href="#" onmouseover="popup(' . 5 . ')">';
$content .= "<div id-'dialog-5' class='dialogs'>";
$content .= "<para>
                                 <para.body>Sorry! Try another point.</para.body>
                              </para>";
$content .= "</div>";
$content .= "</area>";

$content .= "</map>";


//$content .= "<div class='def'>";
//
//$content .= "<span class='deftitle'>";
//$content .= "Title of Theorem";
//$content .= "</span>";
//
//$content .= "<span class='deftype'>";
//$content .="Definition";
//$content .= "</span>";
//
//$content .= "<br />";
//
//$content .= "<div class='defcontent'>";
//$content .= "theorizing something!";
//$content .= "</div>"; // end of theoremcontent
//
//$content .= "<ul class=defminibuttons>";
//
//$content .= '<li id="defminibutton-1" class="defminibutton" onmouseover="popup(' . 1 . ')">';
//$content .="<span style='cursor:pointer;'>";
//$content .="Comment";
//$content .= "</span>";
//$content .= "</li>";
//
//$content .= "<div id='dialog-1' class='dialogs'>";
//$content .= '<table class="mathtable" border="1" cellpadding="3"><tr>
//					                <td style="border-width:1px !important;"><para>
//                           <p>
//                              <b>Speed</b> [km/h]</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>s</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>2s</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>3s</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>4s</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>5s</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>6s</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>7s</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>8s</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>9s</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>10s</p>
//                        </para></td>
//				              </tr><tr>
//					                <td style="border-width:1px !important;"><para>
//                           <p>
//                              <b>Break Distance</b> [m]</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>d</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>4d</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>9d</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>16d</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>25d</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>36d</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>49d</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>64d</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>81d</p>
//                        </para></td>
//					                <td style="border-width:1px !important;"><para>
//                           <p>100d</p>
//                        </para></td>
//				              </tr></table>';
//$content .= "<div>";
//
////$content .= '<li class="proofbutton" onclick="showRightpage(' . 1 . ')">';
////$content .="<span style='cursor:pointer;'>";
////$content .="Proof";
////$content .= "</span>";
////$content .= "</li>";
//
//$content .= "</ul>";
//
//$content .= "</div>"; //theorem
$content .= "</div>"; //leftbox
$content .= "</div>"; //leftcol

$content .= "<div class='rightcol' style='min-width: 542px;'>";
$content .= "<div class='rightbox'>";

$content .= "hey!";

$content .="</div>";
$content .= "</div>";

$content .= "</div>"; //splitter
//$content .= "<div id='refcontent-1' class='refcontent' style='display:none;'>";
//$content .= "reference material?";
//$content .= "</div>";
//$content .= "<div id='proofblock-1' class='proofblock'>";
////$content .= "<div id='proofblock-1' class='proofblock'>";
//$content .= "hey include me!!";
//$content .= "</div>";

echo $content;
?>
