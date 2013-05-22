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
require_once('editorCreation/EditorElement.php');
require_once('editorCreation/EditorUnit.php');
require_once('editorCreation/EditorDefinition.php');
require_once('editorCreation/EditorTheorem.php');
require_once('editorCreation/EditorComment.php');
require_once('editorCreation/EditorImage.php');
require_once('editorCreation/EditorIntro.php');
require_once('editorCreation/EditorBlock.php');
require_once('editorCreation/EditorPara.php');
require_once('editorCreation/EditorInContent.php');
require_once('editorCreation/EditorPartTheorem.php');
require_once('editorCreation/EditorStatementTheorem.php');
require_once('editorCreation/EditorAssociate.php');
require_once('editorCreation/EditorInfo.php');
require_once('editorCreation/EditorTable.php');
require_once('editorCreation/EditorSubordinate.php');
require_once('editorCreation/EditorExtraInfo.php');
require_once('editorCreation/EditorMedia.php');

$m = optional_param('mid', 0, PARAM_INT);  // msm instance ID - it should be named as the first character of the module
//// to get the msm instance id when the save button is clicked

if ($m)
{
    $msm = $DB->get_record('msm', array('id' => $m), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);
}
else
{
    print_error('You must specify a course_module ID');
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, 'createbook', 'createbook', 'view.php?id=' . $cm->id, $msm->id);

$PAGE->set_url('/mod/msm/authoringTool.php', array('mid' => $msm->id));
$PAGE->set_title(format_string($msm->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();

// getting the relative path for tinymce editor
$tinymce = new tinymce_texteditor();
// need to remove the moodle/.. part since it's included in $CFG->wwwroot path
$basepath = explode("/", $tinymce->get_tinymce_base_url()->get_path());

$editorpath = $basepath[2];
for($i = 3; $i < sizeof($basepath); $i++)
{
    $editorpath .= "/" . $basepath[$i];
}

echo " <link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/jquery.splitter.css'/>";
echo " <link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/jshowoff.css'/>";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/superfish.css' media='screen'/> ";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/msmAuthoring.css'/>";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/MsmDisplay.css'/>";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/imageMapperDisplay.css'/>";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/jqueryUI/development-bundle/themes/ui-lightness/jquery.ui.all.css'/>";
echo "<link rel='styelsheet' type='text/css' href='$CFG->wwwroot/$editorpath/plugins/subordinate/css/subordinate.css'/>";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/js/jstree/themes/default/style.css'/>";

echo "<script src='$CFG->wwwroot/mod/msm/jqueryUI/js/jquery-1.7.1.js'></script>"; // can't use 1.9.1 due to jssplitter not being compatible with it
echo "<script src='$CFG->wwwroot/mod/msm/jqueryUI/development-bundle/ui/jquery.ui.core.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/jqueryUI/development-bundle/ui/jquery.ui.widget.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/jqueryUI/development-bundle/ui/jquery.ui.button.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/jqueryUI/development-bundle/ui/jquery.ui.mouse.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/jqueryUI/development-bundle/ui/jquery.ui.draggable.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/jqueryUI/development-bundle/ui/jquery.ui.droppable.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/jqueryUI/development-bundle/ui/jquery.ui.position.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/jqueryUI/development-bundle/ui/jquery.ui.resizable.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/jqueryUI/development-bundle/ui/jquery.ui.dialog.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/jqueryUI/development-bundle/ui/jquery.ui.tabs.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/jqueryUI/development-bundle/ui/jquery.ui.sortable.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/jqueryUI/development-bundle/ui/jquery.ui.accordion.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/jqueryUI/development-bundle/ui/jquery.ui.menu.js'></script>";

echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/jquery.splitter-0.6.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/hoverIntent.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/superfish.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/editorMethods/authorNav.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/editorMethods/editorCore.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/editorMethods/editorActions.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/editorMethods/editorUtility.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/editorMethods/saveMethod.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/editorMethods/saveSetting.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/editorMethods/editorFileBrowser.js'></script>";

echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/popup.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/infoopen.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/navMenu.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/navToPage.js'></script>";

echo "<script type='text/javascript' src='$CFG->wwwroot/$editorpath/plugins/subordinate/js/subordinate.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/$editorpath/plugins/imagemapper/js/imagemapper.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/jstree/jquery.jstree.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/$editorpath/tiny_mce.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/lib/editor/tinymce/module.js'></script>";

echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML,local/local'></script>";

//$selectedValue = $DB->get_record('msm', array('id' => $msm->id))->comptype;

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
                <a id="msm_nav_preview" onclick="showUnitPreview()"> <span>View</span></a>                 
            </li>
            <li>
                <a href="#d"> <span>Sound</span></a>  
            </li>
            <li>
                <a id="msm_nav_setting" onclick="openNavDialog()"> <span>Setting</span></a>  

                <div class="dialogs" id="msm_setting_dialog"> 
                        <form id="msm_setting_form" name="msm_setting_form" action="editorCreation/ajaxSettingUpdate.php" method="post">
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
                                    <input type="radio" name="msm_type" id="msm_type_lecture" value="Lecture" onclick="processChange(event)"> Lecture <br/><br/>
                                    <input type="radio" name="msm_type" id="msm_type_book" value="Book" onclick="processChange(event)"> Book <br/><br/>
                                    <input type="radio" name="msm_type" id="msm_type_wbook" value="Work book" onclick="processChange(event)"> Work book <br/><br/>
                                    <input type="radio" name="msm_type" id="msm_type_others" value="Others" onclick="processChange(event)"> Others:  
                                    <input class="msm_type_input" id="msm_type_specifiedType" name="msm_type_input" placeholder=" Please specify the type of Composition." onkeypress="validateBorder()"/>
                                    <span style="color: red;">*</span> 
                                </div> 
                                <div id="msm_theme" class="msm_tab">
                                    Big content...
                                </div> 
                                <div id="msm_element_names" class="msm_tab">                            
                                    <span class="msm_structure_top_names">Top Unit :  </span>
                                    <input class="msm_structure_top_input" id="msm_structure_input_top" name="msm_structure_input_top"/>
                                    <span style="color: red;">*</span>
                                    <br />                            
                                    <button id="msm_child_add" type="button" onclick="addChildUnit()"> (+) Add more Units </button>
                                </div> 
                            </div> 
                            <br style="clear:both;" />
                            <input type="submit" name="msm_setting_save" class="msm_setting_buttons" id="msm_setting_save" value="Save"/>
                            <button class="msm_setting_buttons" id="msm_setting_cancel" type="button" onclick="closeSetting()"> Cancel </button>
                            <div style="float: right; font-style:italic; color: red;"><span style="color: red;">*</span> required information</div>
                        </form>

                    <div id="msm_setting_cancelled">
                        <p style="display:none;"><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Would you like to exit without saving the changes?</p>
                    </div>                       
                </div>
            </li>
        </ul>';


echo $OUTPUT->heading($msm->name);

$formContent = '';

$topContainer = $DB->get_record('msm_unit_name', array('msmid' => $msm->id, 'depth' => 0))->unitname;

$unitNames = '';

foreach ($DB->get_records('msm_unit_name', array('msmid' => $msm->id), 'depth') as $key => $record)
{
    $unitNames .= $record->unitname . ",";
}
$unitNames .= $msm->id;

$unittable = $DB->get_record('msm_table_collection', array('tablename' => 'msm_unit'));

$existingUnit = $DB->get_record('msm_compositor', array('msm_id' => $msm->id, 'table_id' => $unittable->id, 'parent_id' => 0, 'prev_sibling_id' => 0));

$treeContent = '';
$rootUnit = '';

if (!empty($existingUnit))
{
    $unitRecord = $DB->get_record("msm_unit", array("id" => $existingUnit->unit_id));
    $treeContent .= '<ul>';
    $treeContent .= "<li id='msm_unit-$existingUnit->id-$existingUnit->unit_id'>";
    if (!empty($unitRecord->short_name))
    {
        $treeContent .= "<a href='#'>$unitRecord->short_name</a>";
    }
    else
    {
        $treeContent .= "<a href='#'>$existingUnit->id-$existingUnit->unit_id</a>";
    }
    $treeContent .= makeUnitTree($existingUnit->id, $existingUnit->unit_id);
    $treeContent .= "</li>";
    $treeContent .= '</ul>';
    $rootUnit .= displayRootUnit($existingUnit->id);
}

$formContent .= '<div id="msm_editor_container">
            <div id="msm_editor_left">
                <h2> Structural Elements </h2>

                <div class="msm_structural_element" id="msm_def">
                    <span class="msm_element_label">Definition</span>
                </div>                
                <div class="msm_structural_element" id="msm_theorem">
                    <span class="msm_element_label">Theorem</span>
                </div>                
                <br />
                <div class="msm_structural_element" id="msm_comment">
                    <span class="msm_element_label">Comment</span>
                </div>                
                <div class="msm_structural_element" id="msm_body">
                    <span class="msm_element_label">Content</span>
                </div>                
                <br />
                <div class="msm_structural_element" id="msm_extra_info">
                    <span class="msm_element_label" style="margin-top: 20%;">Extra
                    Information</span>
                </div>
                <div class="msm_structural_element" id="msm_intro">
                    <span class="msm_element_label" style="margin-left: 25%;">Intro</span>
                </div>                
                <br />
                
            </div>
            <div id="msm_editor_middleright">
                <div id="msm_editor_middle" >
                    <h2> ' . $topContainer . ' Design Area </h2> <!-- grab the string from the setting values -->
                    <form id="msm_unit_form" name="msm_unit_form" action="editorCreation/msmUnitForm.php" method="post">
                        <div id = "msm_unit_info_div">
                        <label class="msm_unit_title_labels" id="msm_unit_title_label" for="msm_unit_title">' . $topContainer . ' title: </label>
                        <input class="msm_title_input" id="msm_unit_title" name="msm_unit_title" placeholder=" Please enter the title of this ' . $topContainer . '." onkeypress="validateBorder()"/>
                            
                        <label class="msm_unit_short_title_labels" for="msm_unit_short_title"> XML hierarchy
                        Name: </label>
                        <input class="msm_unit_short_titles" id="msm_unit_short_title" placeholder="Please enter short title for this ' . $topContainer . '." name="msm_unit_short_title"/> 

                        <label class="msm_unit_description_labels" id="msm_unit_description_label" for="msm_unit_description_input">Description: </label>
                        <input class="msm_unit_description_inputs" id="msm_unit_description_input" name="msm_unit_description_input" placeholder="Insert description to search this element in future."/>
                        </div>
                        <div id="msm_editor_middle_droparea">                        
                            <div id="msm_child_appending_area">';

if (!empty($rootUnit))
{
    $formContent .= $rootUnit;
}

$formContent .= '</div>
                            <input id="msm_child_order" name="msm_child_order" style="visibility: hidden;"/>
                            <input id="msm_unit_subordinate_container" name="msm_unit_subordinate_container" style="visibility: hidden;"/>
                        </div>
                        <input class="msm_editor_buttons" id="msm_editor_reset" type="button" onclick="resetUnit()" value="Reset"/> 
                        <input type="submit" name="msm_editor_save" class="msm_editor_buttons" id="msm_editor_save" disabled="disabled" value="Save"/>

                        <input id="msm_unit_name_input" name="msm_unit_name_input" style="visibility:hidden;" value="' . $unitNames . '"/> 
                        <input id="msm_file_options" name="msm_file_options" style="display:none;"/>
                    </form>
                </div>

                <div id="msm_editor_right">
                    <h2> XML Hierarchy </h2>
                    <div id="msm_unit_tree">';
// adding preexistng unit strcuture
if (!empty($treeContent))
{
    $formContent .= $treeContent;
}

$formContent .= '</div>';
if (empty($treeContent))
{
    $formContent .= '<button id="msm_editor_new" type="button" onclick="newUnit()" disabled="disabled"> New </button>';
}
else
{
    $formContent .= '<button id="msm_editor_new" type="button" onclick="newUnit()"> New </button>';
}
$formContent .=' </div>
                </div>     
       </div>
       <div class="msm_loadingscreen"></div>
        <button class="msm_comp_buttons" id="msm_comp_done" type="button" onclick="saveComp(event)"> Done </button>
        <button id="msm_comp_fullscreen"> Full Screen </button>';

//Replace this with your current context
//You can replace this with the default > $CFG->maxbytes
$options['maxbytes'] = $CFG->maxbytes;

$imgTableid = $DB->get_record("msm_table_collection", array("tablename" => "msm_img"))->id;
$existingImg = $DB->get_records("msm_compositor", array("msm_id" => $msm->id, "table_id" => $imgTableid));

$draftitemid = file_get_unused_draft_itemid();

// added code to prevent moodle from generating a new draft itemid when this page is triggered to edit already existing unit
// if a new draft itemid is generated, the moodle file system does not know where to find the already existing files created from 
// the first time the editor was loaded.
if (!empty($existingImg))
{
    $arrayvalues = array_values($existingImg);
    $firstRecord = array_shift($arrayvalues);

    $imgRecord = $DB->get_record("msm_img", array("id" => $firstRecord->unit_id));

    $srcInfo = explode("/", $imgRecord->src);

    $draftitemid = $srcInfo[sizeof($srcInfo) - 2];
}

//The options
$fpoptions = array();

$args = new stdClass();
// need these three to filter repositories list
$args->accepted_types = array('web_image');
$args->return_types = (FILE_INTERNAL | FILE_EXTERNAL);
$args->context = $context;
$args->env = 'filepicker';

// advimage plugin
$image_options = initialise_filepicker($args);
$image_options->context = $context->id;
$image_options->client_id = uniqid();
$image_options->maxbytes = $options['maxbytes'];
$image_options->env = 'editor';
$image_options->itemid = $draftitemid;

// moodlemedia plugin
$args->accepted_types = array('video', 'audio');
$media_options = initialise_filepicker($args);
$media_options->context = $context->id;
$media_options->client_id = uniqid();
$media_options->maxbytes = $options['maxbytes'];
$media_options->env = 'editor';
$media_options->itemid = $draftitemid;

// advlink plugin
$args->accepted_types = '*';
$link_options = initialise_filepicker($args);
$link_options->context = $context->id;
$link_options->client_id = uniqid();
$link_options->maxbytes = $options['maxbytes'];
$link_options->env = 'editor';
$link_options->itemid = $draftitemid;

$fpoptions['image'] = $image_options;
$fpoptions['media'] = $media_options;
$fpoptions['link'] = $link_options;


$formContent .= '<script type="text/javascript"> 
            var tinymce_filepicker_options = ' . json_encode($fpoptions) . ';
            
            $(document).ready(function() {   
            $("#msm_file_options").val(JSON.stringify(tinymce_filepicker_options));
                $(".msm_loadingscreen").on({
                    ajaxStart: function() {
                        $(this).show();
                    },
                    ajaxStop: function() {
                        $(this).hide();
                    }
                });
                $("#msm_comp_fullscreen").click(function(event) {
                         $("#page-header").css("display", "none");
                         $(".block").addClass("dock_on_load");
                         $("#region-main").addClass("nomargin");
                         
                         $("#msm_editor_container").trigger("spliter.resize");
                         $("#msm_editor_middleright").trigger("spliter.resize");
                
                          swapButtons(event);
                });   
                
                $(window).resize(function() {               
                    $("#msm_editor_container").trigger("spliter.resize");
                         $("#msm_editor_middleright").trigger("spliter.resize");
                });
                               
               $(".msm_subordinate_info_dialogs").dialog({
                    autoOpen: false,
                    height: "auto",
                    modal: false,
                    width: 605
                });               
                    
                $(".msm_structural_element").draggable({
                    appendTo: "msm_editor_middle_droparea",
                    containment: "msm_editor_middle_droparea",
                    scroll: true,
                    cursor: "move",
                    helper: "clone"                   
                }); 
                
                $("#msm_editor_middle_droparea").droppable({
                    accept: "#msm_editor_left > div",
                    hoverClass: "ui-state-hover",
                    tolerance: "pointer",
                    drop: function( event, ui ) { 
                        processDroppedChild(event, ui.draggable.context.id);                        
                    }
                });                  
                               
                $("ul.sf-menu").superfish({
                    autoArrows: false
                });             

                $("#msm_setting_type").tabs();                
              
                $("#msm_editor_container").split({
                    orientation: "vertical",
                    limit: 100,
                    position: "15%"
                });
                
                $("#msm_editor_middleright").split({
                    orientation: "vertical",
                    limit: 100,
                    position: "82%"
                });
                ';

//
if (!empty($existingUnit))
{
    $formContent .= '// need it for the loading of jstree when in edit mode
                $("#msm_unit_tree")
                    .jstree({
                        "plugins": ["themes", "html_data", "ui", "dnd"],
                        "core" : { "initially_open" : ["msm_unit-' . $existingUnit->id . '-' . $existingUnit->unit_id . '"]},
                        "ui" : {"initially_select" : ["msm_unit-' . $existingUnit->id . '-' . $existingUnit->unit_id . '"]},
                        "dnd": {
                            "drop_target": false,
                            "drag_target": false
                        }
                    })
                      .bind("load.jstree", function(){
                            $("#msm_unit_tree").jstree("select_node", "msm_unit-' . $existingUnit->id . '-' . $existingUnit->unit_id . '").trigger("select_node.jstree");
                        })
                    .bind("select_node.jstree", function(event, data) {
                        var dbInfo = [];      
                        
                        $(".msm_editor_buttons").remove();
                        
                        $("<button class=\"msm_editor_buttons\" id=\"msm_editor_edit\" type=\"button\" onclick=\"editUnit()\"> Edit </button>").appendTo("#msm_editor_middle");              
                        $("<button class=\"msm_editor_buttons\" id=\"msm_editor_remove\" type=\"button\" onclick=\"removeUnit(event)\"> Remove this Unit </button>").appendTo("#msm_editor_middle");
                        $("<button id=\"msm_editor_new\" type=\"button\" onclick=\"newUnit()\"> New Unit </button>").appendTo("#msm_editor_right");
        
                        var nodeId = data.rslt.obj.attr("id");      
                        var match = nodeId.match(/msm_unit-.+/);
                        var nodeInfo = "";
                        if(match)
                        {
                           var tempInfo = nodeId.split("-");
                           nodeInfo = tempInfo[1]+"-"+tempInfo[2];
                        }
                        else
                        {
                            nodeInfo = nodeId;
                        }


                        $.ajax({
                            type: "POST",
                            url: "editorCreation/msmLoadUnit.php",
                            data: {
                                "id": "msm_unit-"+nodeInfo
                            },
                            success: function(data)
                            {
                                dbInfo = JSON.parse(data);  
                                processUnitData(dbInfo); 
                                $("#msm_currentUnit_id").val(nodeInfo);
                                MathJax.Hub.Queue(["Typeset",MathJax.Hub]);    

                            },
                            error: function(data)
                            {
                                alert("ajax error in loading unit");
                            }
                        })

                    })
                    .delegate("a", "click", function(event, data){
                        event.preventDefault();
                    });
              });               
        </script>';
}
else
{
    $formContent .= '});               
        </script>';
}


echo $OUTPUT->box($msm_nav . $formContent);
echo $OUTPUT->footer();

function makeUnitTree($compid, $unitid)
{
    global $DB;

    $treeHtml = '';

    $unittableid = $DB->get_record("msm_table_collection", array("tablename" => "msm_unit"))->id;

    $treeHtml .= "<ul>";

    $childElements = $DB->get_records("msm_compositor", array("parent_id" => $compid, "table_id" => $unittableid), "prev_sibling_id");

    foreach ($childElements as $child)
    {
        $childUnitElement = $DB->get_record("msm_unit", array("id" => $child->unit_id));
        $treeHtml .= "<li id='msm_unit-$child->id-$child->unit_id'>";
        $treeHtml .= "<a href='#'>$childUnitElement->short_name</a>";
        $treeHtml .= makeUnitTree($child->id, $child->unit_id);
        $treeHtml .= "</li>";
    }

    $treeHtml .= "</ul>";

    return $treeHtml;
}

function displayRootUnit($unitcompid)
{
    global $DB;

    $unitCompRecord = $DB->get_record('msm_compositor', array('id' => $unitcompid));

    $unitRecord = $DB->get_record('msm_unit', array('id' => $unitCompRecord->unit_id));
    ?>
    <script type="text/javascript">
        $(document).ready(function() {
            var titleString = "<?php echo $unitRecord->plain_title ?>";
            $('#msm_unit_title').val(titleString);
            var descriptionString = "<?php echo $unitRecord->description ?>";
            $("#msm_unit_description_input").val(descriptionString);
                                                                                                            
            $("#msm_editor_save").remove();
            $("<button class=\"msm_editor_buttons\" id=\"msm_editor_edit\" type=\"button\" onclick=\"editUnit()\"> Edit </button>").appendTo("#msm_editor_middle");
                                                                                                                    
            $("#msm_editor_reset").remove();
            $("<button class=\"msm_editor_buttons\" id=\"msm_editor_remove\" type=\"button\" onclick=\"removeUnit(event)\"> Remove this Unit </button>").appendTo("#msm_editor_middle");
        });
    </script>
    <?php
    $rootUnitHtml = '';

    $rootUnit = new EditorUnit();
    $rootUnit->loadData($unitcompid);

    foreach ($rootUnit->children as $childElement)
    {
        $rootUnitHtml .= $childElement->displayData();
    }

    return $rootUnitHtml;
}
?>
