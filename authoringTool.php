<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                      **
 * @subpackage  msm                                                      **
 * @name        msm                                                      **
 * @copyright   University of Alberta                                    **
 * @link        http://ualberta.ca                                       **
 * @author      Ga Young Kim                                             **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * *************************************************************************
 * ************************************************************************ */
require_once('../../config.php');
require_once($CFG->dirroot . '/mod/msm/lib.php');


//$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$m = optional_param('mid', 0, PARAM_INT);  // msm instance ID - it should be named as the first character of the module

if ($m)
{
    $msm = $DB->get_record('msm', array('id' => $m), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);
}
else
{
    error('You must specify a course_module ID');
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, 'createbook', 'createbook', 'view.php?id=' . $cm->id, $msm->id);

$PAGE->set_url('/mod/msm/authoringTool.php', array('mid' => $msm->id));
$PAGE->set_title(format_string($msm->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();

echo " <link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/jquery.splitter.css'/>";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/superfish.css' media='screen'/> ";
echo "<link rel='stylesheet' href='$CFG->wwwroot/mod/msm/development-bundle/themes/ui-lightness/jquery.ui.all.css'/>";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/msmAuthoring.css'/>";

echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/jquery-1.7.1.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/external/jquery.bgiframe-2.1.2.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.core.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.widget.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.mouse.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.draggable.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.droppable.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.position.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.resizable.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.dialog.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.tabs.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.sortable.js'></script>";

echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/jquery.splitter-0.6.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/hoverIntent.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/superfish.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/authorNav.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/editorActions.js'></script>";

echo "<script type='text/javascript' src='$CFG->wwwroot/lib/editor/tinymce/tiny_mce/3.4.6/tiny_mce.js'></script>";
//echo "<script type='text/javascript src='$CFG->wwwroot/lib/editor/tinymce/3.4.6/tiny_mce_jquery.js></script>";
//echo "<script type='text/javascript;"

echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML,local/local'></script>";

$selectedValue = $DB->get_record('msm', array('id' => $msm->id))->comptype;

$msm_nav = '';
$msm_nav .= '<ul class="sf-menu">
            <li>
                <a href="#a"><span >File</span></a>
                <span class="sf-sub-indicator"> >> </span>
                <ul>
                    <li>
                        <a href="#aa"> <span>Open</span></a>
                    </li>
                    <li>
                        <a href="#ab"> <span>Save</span></a>
                    </li>
                    <li>
                        <a href="#ac"> <span>Save as...</span></a>
                    </li>
                    <li>
                        <a href="#ad"> <span>Rename Composition</span></a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#b"> <span>Edit</span></a>       
                <span class="sf-sub-indicator"> >> </span>
                <ul>
                    <li>
                        <a href="#ba"> <span>Undo</span></a>
                    </li>
                    <li>
                        <a href="#bb"> <span>Redo</span></a>
                    </li>
                    <li>
                        <a href="#bc"> <span>Cut</span></a>
                    </li>
                    <li>
                        <a href="#bd"> <span>Copy</span></a>
                    </li>
                    <li>
                        <a href="#be"> <span>Paste</span></a>
                    </li>
                    <li>
                        <a href="#bf"> <span>Delete</span></a>
                    </li>

                    <li>
                        <a href="#bg"> <span>Select All</span></a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#c"> <span>View</span></a>                 
            </li>
            <li>
                <a href="#d"> <span>Sound</span></a>  
            </li>
            <li>
                <a id="msm_nav_setting" onclick="openNavDialog(' . $selectedValue . ')"> <span>Setting</span></a>  

                <div class="dialogs" id="msm_setting_dialog">
                    <div id="msm_setting_type">
                        <ul>
                            <li>
                                <a href="#msm_type">Composition Type</a>
                            </li>
                            <li>
                                <a href="#msm_theme">MSM Theme</a>
                            </li>
                            <li>
                                <a href="#msm_element_names">Name of Structural Elements</a>
                            </li>
                        </ul>

                        <div id="msm_type" class="msm_tab">
                            <span> <b>Type of Composition: </b></span>
                            <br/><br/>
                            <form>
                                <input type="radio" name="msm_type" id="msm_type_lecture" value="Lecture" onclick="processChange(event)"> Lecture <br/><br/>
                                <input type="radio" name="msm_type" id="msm_type_book" value="Book" onclick="processChange(event)"> Book <br/><br/>
                                <input type="radio" name="msm_type" id="msm_type_wbook" value="Work book" onclick="processChange(event)"> Work book <br/><br/>
                                <input type="radio" name="msm_type" id="msm_type_others" value="Others" onclick="processChange(event)"> Others:  
                                <input class="msm_type_input" id="msm_type_specifiedType" name="msm_type_input" placeholder=" Please specify the type of Composition." onkeypress="validateBorder()"/>
                                <span style="color: red;">*</span>
                            </form>

                        </div> 
                        <div id="msm_theme" class="msm_tab">
                            Big content...
                        </div> 
                        <div id="msm_element_names" class="msm_tab">
                            <span class="msm_structure_top_names">Top Unit :  </span>
                            <input class="msm_structure_top_input" id="msm_structure_input_top" name="msm_top"/>
                            <span style="color: red;">*</span>
                            <br />                            
                            <button id="msm_child_add" type="button" onclick="addChildUnit()"> (+) Add more Units </button>
                        </div> 
                    </div> 
                    <br style="clear:both;" />
                    <button class="msm_setting_buttons" id="msm_setting_save" type="button" onclick="saveSetting()"> Save </button>
                    <button class="msm_setting_buttons" id="msm_setting_cancel" type="button" onclick="closeSetting()"> Cancel </button>
                    <div style="float: right; font-style:italic; color: red;"><span style="color: red;">*</span> required information</div>
                    <div id="msm_setting_cancelled">
                        <p style="display:none;"><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Would you like to exit without saving the changes?</p>
                    </div>
                </div>
            </li>
        </ul>';


echo $OUTPUT->heading($msm->name);

$formContent = '';

$formContent .= '<div id="msm_editor_container">
            <div id="msm_editor_left">
                <h2> Structural Elements </h2>

                <div class="msm_structural_element" id="msm_def">
                    Drag to add Definition
                </div>
                <br />
                <div class="msm_structural_element" id="msm_theorem">
                    Drag to add Theorem
                </div>
                <br />
                <div class="msm_structural_element" id="msm_body">
                    Drag to add content
                </div>
                <br />
                <div class="msm_structural_element" id="msm_intro">
                    Drag to add Intro
                </div>
                <br />
                <div class="msm_structural_element" id="msm_pic">
                    Drag to add Images
                </div>
                <br />
                <div class="msm_structural_element" id="msm_media">
                    Drag to add Other Media types
                </div>
                <br />
            </div>
            <div id="msm_editor_middleright">
                <div id="msm_editor_middle" >
                    <h2> ___ Design Area </h2> <!-- grab the string from the setting values -->
                    <input class="msm_title_input" id="msm_unit_title" name="msm_unit_title" placeholder=" Please enter the title of this _____." onkeypress="validateBorder()"/>
                    <div id="msm_editor_middle_droparea">
                        <div id="msm_trash_droparea">
                            <img id="msm_trash_icon" src="' . $CFG->wwwroot . '/mod/msm/pix/trash_recyclebin_empty_closed.png"/><br><span style="margin-left: 40%;"><b>Remove Unit</b></span>                            
                        </div>
                    </div>
                     <button class="msm_editor_buttons" id="msm_editor_reset" type="button" onclick="resetUnit()"> Reset </button>
                     
                    <button class="msm_editor_buttons" id="msm_editor_save" type="button" disabled="disabled" onclick="saveUnit()"> Save </button>
                   
                </div>

                <div id="msm_editor_right">
                    <h2> XML Hierarchy </h2>
                </div>
            </div>            
        </div>
        <button class="msm_comp_buttons" id="msm_comp_done" type="button" onclick="saveComp()"> Done </button>';

$formContent .= '<script type="text/javascript">    
            $(document).ready(function() {
                var selectedId = 0;  
                
//                 $("#msm_editor_container :not(.msm_unit_child_content)").click(function(){
//                        alert("clicked!");
//                 });

                $("#msm_editor_middle_droparea").sortable({
                    connectWith: "#msm_editor_middle_droparea"
                });
                
                $(".msm_structural_element").draggable({
                    appendTo: "msm_editor_middle_droparea",
                    containment: "msm_editor_middle_droparea",
                    cursor: "move",
                    helper: "clone"                   
                });              
        
                $("#msm_editor_middle_droparea").droppable({
                    accept: "#msm_editor_left > div",
                    hoverClass: "ui-state-hover",
                    tolerance: "pointer",
                    drop: function( event, ui ) { 
                        selectedId = processDroppedChild(event, ui.draggable.context.id, selectedId);                        
                    }
                });                  
                               
                $("ul.sf-menu").superfish({
                    autoArrows: false
                });             

                $("#msm_setting_type").tabs();  
                
                $("#msm_editor_container").split({
                    orientation: "vertical",
                    limit: 100,
                    position: "20%"
                });
                
                $("#msm_editor_middleright").split({
                    orientation: "vertical",
                    limit: 100,
                    position: "80%"
                });             
            })
        </script>';

echo $OUTPUT->box($msm_nav . $formContent);
echo $OUTPUT->footer();
?>
