/**
 **************************************************************************
 **                              MSM                                     **
 **************************************************************************
 * @package     mod                                                      **
 * @subpackage  msm                                                      **
 * @name        msm                                                      **
 * @copyright   University of Alberta                                    **
 * @link        http://ualberta.ca                                       **
 * @author      Ga Young Kim                                             **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 **************************************************************************
 **************************************************************************/

/* This js file contains all the function needed for moodlesubnolink tinyMCE plugin to work*/

/**
 * 
 * @param tinyMCE editor            current editor that the moodlesubnolink plugin was triggered from
 */
function removeSubordinate(editor)
{    
    var selectedNode = '';
    if($.browser.msie)
    {
        selectedNode = editor.selection.getNode().childNodes[0].tagName;
    }
    else
    {
        selectedNode = editor.selection.getNode().tagName;
    }
}

