<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints a particular instance of msm
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 * 
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
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/XMLImporter/Compositor.php');
require_once(dirname(__FILE__) . '/XMLImporter/TableOfContents.php');
//$PAGE->requires->css('/mod/msm/MsmDisplay.css');

global $PAGE, $OUTPUT;

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$m = optional_param('n', 0, PARAM_INT);  // msm instance ID - it should be named as the first character of the module

if ($id)
{
    $cm = get_coursemodule_from_id('msm', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $msm = $DB->get_record('msm', array('id' => $cm->instance), '*', MUST_EXIST);
}
elseif ($m)
{
    $msm = $DB->get_record('msm', array('id' => $m), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);
}
else
{
    error('You must specify a course_module ID or an instance ID');
}

$rootcomp = $DB->get_record('msm_compositor', array('msm_id' => $msm->id, 'parent_id' => 0, 'prev_sibling_id' => 0), '*', MUST_EXIST);

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, 'msm', 'view', "view.php?id={$cm->id}", $msm->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/msm/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($msm->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

//*********************************************************************************************************
// activate later when editor is done
//if ($PAGE->user_allowed_editing())
//{
//    $buttons = '<form method="get" action="' . $CFG->wwwroot . '/course/mod.php"><div>' .
//            '<input type="hidden" name="update" value="' . $cm->id . '" />' .
//            '<input type="submit" value="' . get_string('updatecomp', 'msm') . '" /></div></form>';
//    $PAGE->set_button($buttons);
//}
//*********************************************************************************************************
//$PAGE->set_cacheable(true);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('msm-'.$somevar);
// Output starts here

echo $OUTPUT->header();

//echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/jquery-1.8.2.js'></script>";
//echo "<meta http-equiv='X-UA-Compatible' content='IE=edge'>";

echo "<link rel='stylesheet' href='$CFG->wwwroot/mod/msm/development-bundle/themes/ui-lightness/jquery.ui.all.css'/>";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/jquery.splitter.css'/>";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/MsmDisplay.css'/>";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/slideNav.css'/>";
echo "<link rel='stylesheet' href='$CFG->wwwroot/mod/msm/css/jquery.treeview.css'/>";
echo "<link rel='stylesheet' href='$CFG->wwwroot/mod/msm/css/jshowoff.css' type='text/css'/>";

echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/jquery-1.7.1.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/external/jquery.bgiframe-2.1.2.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.core.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.widget.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.mouse.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.draggable.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.position.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.resizable.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.dialog.js'></script>";

// these js files need to be after the development-bundle
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/jquery.splitter-0.6.js'></script>";


echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/jquery.jshowoff.js'></script>";
//echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/maphilight/jquery.maphilight.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/popup.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/infoopen.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/navMenu.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/navToPage.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/jTreeview/lib/jquery.cookie.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/jTreeview/jquery.treeview.js'></script>";


//echo "<script type ='text/javascript' src='$CFG->wwwroot/mod/msm/js/jimagemapster.js'></script>";
//echo "<script type='text/x-mathjax-config' src='$CFG->wwwroot/mod/msm/js/mathjax/config/local/local.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML,local/local'></script>";

if ($msm->intro)
{ // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('msm', $msm, $cm->id), 'generalbox mod_introbox', 'msmintro');
}


// Replace the following lines with you own code
echo $OUTPUT->heading($msm->name);
echo $OUTPUT->heading('<ul id="navigation">
            <li class="toc"><a href="" id="toc"><span>Table of Contents</span></a></li>
            <li class="author"><a href="" id="author"><span>Authors</span></a></li>
            <li class="symbol"><a href="" id="symbol"><span>Symbols</span></a></li>
            <li class="glossary"><a href="" id="glossary"><span>Glossary</span></a></li>            
            <li class="contact"><a href="" id="contact"><span>Contact</span></a></li>
        </ul>');
//<li class="biblio"><a href=""><span>Bibliography</span></a></li> --> add in later when info is given
$content = '';
//$content .= "hello";
//$content .= "<div>";
$content .= "<div id = 'MySplitter' padding:10px;>";

$content .= "<div id = 'leftcol'>";

$content .= "<div class = 'leftbox'>";
$content .= "<div id='features'>";


$compositor = new Compositor();
$string = '';

// the stack that is created below is in reverse order
$stack = $compositor->makeStack($rootcomp);

$stack = array_reverse($stack); // needed to access the contents in proper order
//last element is added separately to prevent having ending comma
for ($i = 0; $i < sizeof($stack) - 1; $i++)
{
    $string .= $stack[$i]->id . "/" . $stack[$i]->unit_id . "/" . $stack[$i]->parent_id . "/" . $stack[$i]->prev_sibling_id . ",";
}
$string .= $stack[sizeof($stack) - 1]->id . "/" . $stack[sizeof($stack) - 1]->unit_id . "/" . $stack[sizeof($stack) - 1]->parent_id . "/" . $stack[sizeof($stack) - 1]->prev_sibling_id;

//foreach ($stack as $key => $record)
//{
//    $string .= $record->id . "/" . $record->unit_id . "/" . $record->parent_id . "/" . $record->prev_sibling_id . ",";
//}

$content.= $compositor->loadAndDisplay(null, $string, null, '');
//print_object($stack);
//die;

$content .= "</div>"; //features

$content .= "</div>"; // leftbox

$content .= "<div class='controller'>";
$content .= "</div>";
$content .= "</div>"; // leftcol

$content .= "<div id = 'rightcol'>";

$content .= "<div class = 'rightbox'>";

$content .= "</div>";
$content .= "</div>";

$content .= "</div>"; // splitter

//$content .= '<input id="coursemod" type="text" name="coursemodids" style="visibility:hidden;" value="' . $msm->course . '-' . $msm->id .'"/>';

$content .= "<div id='tocpanel' class='panel'>";
$content .="<div class='slidepanelcontent' id='toccontent'>";
$tableofcontents = new TableOfContents();
$content .= $tableofcontents->makeToc();
$content .="</div>"; // end of slidepanelcontent
$content .= "</div>"; // end of panel

$content .= "<div id='symbolpanel' class='panel'>";
$content .="<div class='slidepanelcontent' id='symbolcontent'>";
$content .="</div>"; // end of slidepanelcontent
$content .= "</div>"; // end of panel

$content .= "<div id='glossarypanel' class='panel'>";
$content .="<div class='slidepanelcontent' id='glossarycontent'>";
$content .="</div>"; // end of slidepanelcontent
$content .= "</div>"; // end of panel

$content .= "<div id='authorpanel' class='panel'>";
$content .="<div class='slidepanelcontent' id='authorcontent'>";
$content .="</div>"; // end of slidepanelcontent
$content .= "</div>"; // end of panel

//$symbolfilename = $msm->course . '-' . $msm->id . '-msm_symbolindex.html';
//if (file_exists($symbolfilename))
//{
//    $symbolfile = fopen($symbolfilename, 'r');
//    $content .= fread($symbolfile, filesize($symbolfilename));
//    fclose($symbolfile);
//}
//else
//{
//    echo "file " . $symbolfilename . "does not exist.";
//}
//
//$glossryfilename = $msm->course . '-' . $msm->id . '-msm_glossaryindex.html';
//if (file_exists($glossryfilename))
//{
//    $glossaryfile = fopen($glossryfilename, 'r');
//    $content .= fread($glossaryfile, filesize($glossryfilename));
//    fclose($glossaryfile);
//}
//else
//{
//    echo "file " . $glossryfilename . "does not exist.";
//}$authorfilename = $msm->course . '-' . $msm->id . '-msm_authorindex.html';
//if (file_exists($authorfilename))
//{
//    $authorfile = fopen($authorfilename, 'r');
//    $content .= fread($authorfile, filesize($authorfilename));
//    fclose($authorfile);
//}
//else
//{
//    echo "file " . $authorfilename . "does not exist.";
//}

//

$content .= "<div id='contactpanel' class='panel'>";
$content .="<div class='slidepanelcontent' id='contactcontent'>";
$content .= "<h3> C O N T A C T S </h3>";

$content .= "<div> where prof's/TAs' email...etc contact information would go... </div>";

$content .="</div>"; // end of slidepanelcontent
$content .= "</div>"; // end of panel

$content .= "<div class='loadingscreen'></div>";

$content .= '<input id="instanceid" type="text" name="moduleinfo" value="' . $msm->course . ',' . $msm->id . '" style="visibility:hidden;"/>';

// need to have it in this order or dialog breaks
// if implementing jImageMapster, need to insert the jquery code in the space between dialogs and jshowoff
//** make sure the sliding panel hiding method comes AFTER treeview method declaration!! (otherwise, it gives a bug 
// when the page is refreshed.  The plus/minus pics become reversed.)

$content .= "
    <script type='text/javascript'>
            jQuery(document).ready(function(){                 
                 $('#tableofcontent').treeview({
                    persist: 'cookie',
                    animated: 'fast',
                    collapsed: true,
                    control: '#treecontrol'
                });              
                
                $('.slidepanelcontent, #glossarycontent, #symbolcontent, #authorcontent').hide();
                     
                $('.dialogs').dialog({
                    autoOpen: false,
                    height: 'auto',
                    width: 605
                });  
                
                $('#features').jshowoff({
                    autoplay:false,
                    links:true                  
                });
                 
                $('#MySplitter').split({
                    orientation: 'vertical',
                    position: '50%'
                });
        
                $('.loadingscreen').on({
                    ajaxStart: function() {
                        $(this).show();
                    },
                    ajaxStop: function() {
                        $(this).hide();
                    }
                });  
                
            });
        </script>";


// where the display method would go...

echo $OUTPUT->box($content);
// Finish the page
echo $OUTPUT->footer();

