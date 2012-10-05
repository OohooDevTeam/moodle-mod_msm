<?php
/*
 * file for testing!! will be deleted before release
 */
?>

<html>
    <head>
        <style type="text/css">

            /*            #MySplitter { Main splitter element 
                            height:95%;width:100%;margin:0;padding:0;
                        }
                        #leftcol{
                            float:left;
                            width:15%;
                            height:100%;
                            border-top:solid 1px #9cbdff;
                            background:#c4dcfb;
                            overflow: auto;
                        }
                        #rightcol{	Contains toolbar and horizontal splitter
                            float:right;width:85%;height:100%;
                            background:#f4f4f4;
                        }*/
            /* Splitbar styles; these are the default class names and required styles */
            .splitbarV {
                float:left;width:6px;height:100%;
                line-height:0px;font-size:0px;
                border-left:solid 1px #9cbdff;border-right:solid 1px #9cbdff;
                background:#cbe1fb url(js/jSplitter/img/panev.gif) 0% 50%;
            }
            .splitbarH {
                height:6px;text-align:left;line-height:0px;font-size:0px;
                border-top:solid 1px #9cbdff;border-bottom:solid 1px #9cbdff;
                background:#cbe1fb url(js/jSplitter/img/paneh.gif) 50% 0%;
            }

            /*            .splitbuttonV{
                            margin-top:-41px;margin-left:-4px;top:50%;position:relative;
                            height:83px;width:10px;
                            background:transparent url(js/jSplitter/img/panevc.gif) 10px 50%;
                        }
                        .splitbuttonV.invert{
                            margin-left:0px;background:transparent url(js/jSplitter/img/panevc.gif) 0px 50%;
                        }
                        .splitbuttonH{
                            margin-left:-41px;left:50%;position:relative;
                            height:10px !important;width:83px;
                            background:transparent url(js/jSplitter/img/panehc.gif) 50% 0px;
                        }
                        .splitbuttonH.invert{
                            margin-top:-4px;background:transparent url(js/jSplitter/img/panehc.gif) 50% -10px;
                        }
                        .splitbarV.working,.splitbarH.working,.splitbuttonV.working,.splitbuttonH.working{
                            -moz-opacity:.50; filter:alpha(opacity=50); opacity:.50;
                        }*/
        </style>
        <link rel="stylesheet" type="text/css" media = "screen" href="css/MsmDisplay.css" />
        <link rel="stylesheet" type="text/css" href="css/slideNav.css"/>
        <link rel="stylesheet" href="css/jquery.treeview.css"/>
        <script src="development-bundle/jquery-1.7.1.js"></script>

        <link rel='stylesheet' href='development-bundle/themes/ui-lightness/jquery.ui.all.css'/>
        <script src='development-bundle/external/jquery.bgiframe-2.1.2.js'></script>
        <script src='development-bundle/ui/jquery.ui.core.js'></script>
        <script src='development-bundle/ui/jquery.ui.widget.js'></script>
        <script src='development-bundle/ui/jquery.ui.mouse.js'></script>
        <script src='development-bundle/ui/jquery.ui.draggable.js'></script>
        <script src='development-bundle/ui/jquery.ui.position.js'></script>
        <script src='development-bundle/ui/jquery.ui.resizable.js'></script>
        <script src='development-bundle/ui/jquery.ui.dialog.js'></script>

        <!--<script type="text/javascript" src="js/sweet-menu-latest/sweet-menu/jquery.easing.js"></script>-->

        <script type="text/javascript" src="js/jTreeview/lib/jquery.cookie.js"></script>
        <script type="text/javascript" src="js/jTreeview/jquery.treeview.js"></script>
        <!--<script type="text/javascript" src="js/sweet-menu-latest/sweet-menu/jquery.sweet-menu-1.0.js"></script>-->
        <script type='text/javascript' src='js/jSplitter/splitter.js'></script>
        <script type="text/javascript" src="js/maphilight/jquery.maphilight.js"></script>

        <!--<script type='text/javascript' src='js/showRightPage.js'></script>-->
        <script type='text/javascript' src='js/popup.js'></script>
        <script typr="text/javascript" src="js/navMenu.js"></script>

        <!--<script type='text/javascript' src='http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML,/moodle/mod/msm/js/Mathjaxconfig.js'></script>-->
        <!--<script type ="text/javascript" src="js/jimagemapster.js"></script>-->

        <script type="text/javascript">            
            $(document).ready(function(){                
                // first example
                $("#red").treeview({
                    persist: "cookie",
                    animated: "fast",
                    collapsed: true,
                    control: "#treecontrol"
                });

                //                
                             
                $('.dialogs').dialog({
                    autoOpen: false,
                    width: 'auto'
                });
                
                $(".slidepanelcontent").hide(); //updated line, removing the #panel ID.               
              
                //                $('.mathimagemap').maphilight({
                //                    strokeColor:'379ee0',
                //                    strokeWidth:0,
                //                    fillColor:'379ee0',
                //                    fillOpacity: 0.3
                //                });
               
                $('#MySplitter').splitter({
                    splitVertical:true,
                    A:$('#leftcol'),
                    B:$('#rightcol'),
                    closeableto:0                    
                });   
                
               
        
            });
        </script>       

    </head>
</html>

<?php
//$blockcontent = '';

$content = '';
$content .= '<ul id="navigation">
            <li class="toc"><a href="" id="toc"><span>Table of Contents</span></a></li>
            <li class="author"><a href=""><span>Authors</span></a></li>
            <li class="symbol"><a href=""><span>Symbols</span></a></li>
            <li class="glossary"><a href=""><span>Glossary</span></a></li>
            <li class="biblio"><a href=""><span>Bibliography</span></a></li>
            <li class="contact"><a href=""><span>Contact</span></a></li>
        </ul>';
$content .= "<div id='MySplitter'>";

$content .= "<div id='leftcol'>";
//$content .= "hello";
$content .= "<div class='leftbox'>";
$content .= "hello";
//
////$content .= "<img id='#image-1'  class='mathimagemap' src='newXML/LinearAlgebraRn/Determinants/ims/OrientedAreaPlaneGrid.jpg' usemap='#hey'/>";
////
////$content .= "<map name='hey'>";
////
////$content .= '<area id="pic-1" coords="57,91,383,107" shape="rect" href="#" onmouseover="popup(' . 1 . ')">';
////$content .= "<div id='dialog-1' class='dialogs'>";
////$content .= '<para>
////                                 <para.body>Congratulations! </para.body>
////                              </para>';
////$content .= "</div>";
////$content .= "</area>";
////
////$content .= '<area id="pic-2" coords="59,222,383,240" shape="rect" href="#" onmouseover="popup(' . 2 . ')">';
////$content .= "<div id='dialog-2' class='dialogs'>";
////$content .= "<para>
////                                 <para.body>Careful! </para.body>
////                              </para>";
////$content .= "</div>";
////$content .= "</area>";
////
////$content .= '<area id="pic-3" coords="57,113,384,217" shape="rect" href="#" onmouseover="popup(' . 3 . ')">';
////$content .= "<div id='dialog-3' class='dialogs'>";
////$content .= "<para>
////                                 <para.body>Sorry! 1</para.body>
////                              </para>";
////$content .= "</div>";
////$content .= "</area>";
////
////$content .= '<area id="pic-4" coords="59,247,382,304" shape="rect" href="#" onmouseover="popup(' . 4 . ')">';
////$content .= "<div id='dialog-4' class='dialogs'>";
////$content .= "<para>
////                                 <para.body>Sorry! 2</para.body>
////                              </para>";
////$content .= "</div>";
////$content .= "</area>";
////
////$content .= '<area id="pic-5" coords="59,26,382,83" shape="rect" href="#" onmouseover="popup(' . 5 . ')">';
////$content .= "<div id-'dialog-5' class='dialogs'>";
////$content .= "<para>
////                                 <para.body>Sorry! 3</para.body>
////                              </para>";
////$content .= "</div>";
////$content .= "</area>";
////
////$content .= "</map>";
////
////$content .= "<img id='image-2' class='mathimagemap' src='newXML/LinearAlgebraRn/Determinants/ims/OrientedAreaPlaneGrid.jpg' usemap='#hey1'/>";
////
////$content .= "<map name='hey1'>";
////
////$content .= '<area id="pic-6" coords="57,91,383,107" shape="rect" href="#" onmouseover="popup(' . 6 . ')">';
////$content .= "<div id='dialog-6' class='dialogs'>";
////$content .= '<para>
////                                 <para.body>Congratulations! - The grid point you picked is the tip of a vector <math>
////                                       <latex>\Vect{y}</latex>
////                                    </math> so that.</para.body>
////                              </para>
////						                        <table border="1" column="3">
////                                 <tr>
////                                    <td rowspan="1" colspan="2" halign="center" valign="middle">
////                                       <table class="matharray" border="0">
////                                            <tr>
////                                               <td rowspan="1" colspan="2" halign="center" valign="middle">
////                                                   <math>
////                                                     <latex>\Vect{a}</latex>
////                                                  </math>
////                                               </td>
////                                                <td rowspan="1" colspan="1" halign="center" valign="middle">
////                                                   <math>
////                                                      <latex>=</latex>
////                                                   </math>
////                                                </td>
////                                                <td rowspan="1" colspan="2" halign="center" valign="middle">
////                                                   <math>
////                                                      <latex>1\cdot \Vect{a}</latex>
////                                                   </math>
////                                                </td>
////                                             </tr>
////                                      </table>
////                                    </td>
////                                    <td rowspan="1" colspan="1" halign="center" valign="middle">
////                                       <math>
////                                          <latex>=</latex>
////                                       </math>
////                                    </td>
////                                    <td rowspan="1" colspan="2" halign="center" valign="middle">
////                                       <math>
////                                          <latex>+15</latex>
////                                       </math>
////                                    </td>
////                                 </tr>
////                              </table>';
////$content .= "</div>";
////$content .= "</area>";
////
////$content .= '<area id="pic-7" coords="59,222,383,240" shape="rect" href="#" onmouseover="popup(' . 7 . ')">';
////$content .= "<div id='dialog-7' class='dialogs'>";
////$content .= "<para>
////                                 <para.body>Careful! With the vector <math>
////                                       <latex>\Vect{y}</latex>
////                                    </math> you are specifying you determine a parallelogram whose unoriented area is <math>
////                                       <latex>15</latex>
////                                    </math>. However, <math>
////                                       <latex>(\Vect{x},\Vect{y})</latex>
////                                    </math> determine the negative orientation of the plane. So this is not an acceptable grid point.</para.body>
////                              </para>";
////$content .= "</div>";
////$content .= "</area>";
////
////$content .= '<area id="pic-8" coords="57,113,384,217" shape="rect" href="#" onmouseover="popup(' . 8 . ')">';
////$content .= "<div id='dialog-8' class='dialogs'>";
////$content .= "<para>
////                                 <para.body>Sorry! Try another point.</para.body>
////                              </para>";
////$content .= "</div>";
////$content .= "</area>";
////
////$content .= '<area id="pic-9" coords="59,247,382,304" shape="rect" href="#" onmouseover="popup(' . 9 . ')">';
////$content .= "<div id='dialog-9' class='dialogs'>";
////$content .= "<para>
////                                 <para.body>Sorry! Try another point.</para.body>
////                              </para>";
////$content .= "</div>";
////$content .= "</area>";
////
////$content .= '<area id="pic-10" coords="59,26,382,83" shape="rect" href="#" onmouseover="popup(' . 10 . ')">';
////$content .= "<div id-'dialog-10' class='dialogs'>";
////$content .= "<para>
////                                 <para.body>Sorry! Try another point.</para.body>
////                              </para>";
////$content .= "</div>";
////$content .= "</area>";
////
////$content .= "</map>";
//
//
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
//$content .= "<div class='mathcontent'>";
//$content .= "theorizing something!";
//$content .= "</div>"; // end of theoremcontent
//
//$content .= "<ul class=defminibuttons>";
//
//$content .= '<li id="defminibutton-1" class="defminibutton" onmouseover="showonRight(' . 1 . ')">';
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
////$content .= '<li class="proofbutton" onclick="showRightpage(' . 1 . ')">';
////$content .="<span style='cursor:pointer;'>";
////$content .="Proof";
////$content .= "</span>";
////$content .= "</li>";
//$content .= "</ul>";
//$content .= "</div>"; //theorem
//$xmlcontent = "<table> <tr> <td>hello</td> <td>=</td> <td>hey</td> </tr> <tr> <td> </td> <td>=</td> <td>hi</td></tr></table>";
//
//$doc = new DOMDocument();
//$doc->loadXML($xmlcontent);
//
//$content .= $doc->saveXML($doc->documentElement);
//
$content .= "</div>"; //leftbox
$content .= "</div>"; //leftcol

$content .= "<div id='rightcol'>";
$content .= "<div class='rightbox'>";
//
$content .= "hey!";
//
$content .="</div>";
$content .= "</div>";

$content .= "</div>"; //splitter
//
//$content = preg_replace('/^<math xmlns=(.+)>/', '<math>', $content);
//
//$content = preg_replace('/<math>\s+<latex>/', '$', $content);
//$content = preg_replace('/<\/latex>\s+<\/math>/', '$', $content);
//$content = preg_replace('/<math>\s+<latex\/>\s+<\/math>/', '', $content);
//// need to escape twice because it is parsed twice 
//$content = preg_replace('/\\\\(RNr|CNr|QNr|ZNr|NNr|IdMtrx|Id)\[(\S+)?\]/', '\\\\$1{$2}', $content);
//$content .= "<div id='refcontent-1' class='refcontent' style='display:none;'>";
//$content .= "reference material?";
//$content .= "</div>";
//$content .= "<div id='proofblock-1' class='proofblock'>";
////$content .= "<div id='proofblock-1' class='proofblock'>";
//$content .= "hey include me!!";
//$content .= "</div>";
//$content .= "<div id='tocpanel' class='panel'>";
//$content .="<div class='slidepanelcontent' id='toccontent'>";
//$content .= '<div id="treecontrol">
//		<a title="Collapse the entire tree below" href="#"> Collapse All</a> | 
//		<a title="Expand the entire tree below" href="#"> Expand All</a> | 
//		<a title="Toggle the tree below, opening closed branches, closing open branches" href="#">Toggle All</a>
//	</div>';
//$content .= '<ul id="red" class="treeview-red">
//	<li><span>Item 1</span>
//		<ul>
//			<li><span>Item 1.0</span>
//				<ul>
//					<li><span>Item 1.0.0</span></li>
//				</ul>
//			</li>
//			<li><span>Item 1.1</span></li>
//			<li><span>Item 1.2</span>
//				<ul>
//					<li><span>Item 1.2.0</span>
//					<ul>
//						<li><span>Item 1.2.0.0</span></li>
//						<li><span>Item 1.2.0.1</span></li>
//						<li><span>Item 1.2.0.2</span></li>
//					</ul>
//				</li>
//					<li><span>Item 1.2.1</span>
//					<ul>
//						<li><span>Item 1.2.1.0</span></li>
//					</ul>
//				</li>
//					<li><span>Item 1.2.2</span>
//					<ul>
//						<li><span>Item 1.2.2.0</span></li>
//						<li><span>Item 1.2.2.1</span></li>
//						<li><span>Item 1.2.2.2</span></li>
//					</ul>
//				</li>
//				</ul>
//			</li>
//		</ul>
//	</li>
//	<li><span>Item 2</span>
//		<ul>
//			<li><span>Item 2.0</span>
//				<ul>
//					<li><span>Item 2.0.0</span>
//					<ul>
//						<li><span>Item 2.0.0.0</span></li>
//						<li><span>Item 2.0.0.1</span></li>
//					</ul>
//				</li>
//				</ul>
//			</li>
//			<li><span>Item 2.1</span>
//				<ul>
//					<li><span>Item 2.1.0</span>
//					<ul>
//						<li><span>Item 2.1.0.0</span></li>
//					</ul>
//				</li>
//					<li><span>Item 2.1.1</span>
//					<ul>
//						<li><span>Item 2.1.1.0</span></li>
//						<li><span>Item 2.1.1.1</span></li>
//						<li><span>Item 2.1.1.2</span></li>
//					</ul>
//				</li>
//					<li><span>Item 2.1.2</span>
//					<ul>
//						<li><span>Item 2.1.2.0</span></li>
//						<li><span>Item 2.1.2.1</span></li>
//						<li><span>Item 2.1.2.2</span></li>
//					</ul>
//				</li>
//				</ul>
//			</li>
//		</ul>
//	</li>
//	<li><span>Item 3</span>
//		<ul>
//			<li class="open"><span>Item 3.0</span>
//				<ul>
//					<li><span>Item 3.0.0</span></li>
//					<li><span>Item 3.0.1</span>
//					<ul>
//						<li><span>Item 3.0.1.0</span></li>
//						<li><span>Item 3.0.1.1</span></li>
//					</ul>
//					
//				</li>
//					<li><span>Item 3.0.2</span>
//					<ul>
//						<li><span>Item 3.0.2.0</span></li>
//						<li><span>Item 3.0.2.1</span></li>
//						<li><span>Item 3.0.2.2</span></li>
//					</ul>
//				</li>
//				</ul>
//			</li>
//		</ul>
//	</li>
//	</ul>';
//$content .="</div>"; // end of slidepanelcontent
//$content .= "</div>"; // end of panel
//
//$content .= "<div id='symbolpanel' class='panel'>";
//$content .="<div class='slidepanelcontent'>";
//$content .= " symbols?" . "</div>"; // end of slidepanelcontent
//$content .= "</div>"; // end of panel
//
//$content .= "<div id='glossarypanel' class='panel'>";
//$content .="<div class='slidepanelcontent'>";
//$content .= "glossary!";
//$content .="</div>"; // end of slidepanelcontent
//$content .= "</div>"; // end of panel


echo $content;
?>
