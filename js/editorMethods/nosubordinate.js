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
 * @param {tinymce.Editor} editor            current editor that the moodlesubnolink plugin was triggered from
 * @param {string} anchorElement             HTML ID of the highlighted anchor element to remove the subordinate
 */
function removeSubordinate(editor, anchorElement)
{  
    if(anchorElement == '')
    {
        var currentSubId = editor.selection.getNode().id;        
        anchorElement = currentSubId;
    }
    
    var currentSubIdInfo = anchorElement.split("-");
    
    var resultId = currentSubIdInfo[1];
    
    for(var i=2; i < currentSubIdInfo.length; i++)
    {
        resultId += "-"+currentSubIdInfo[i];
    }
    
    var resultContainer = $("#msm_subordinate_result-"+resultId);
    
    if((typeof resultContainer === "undefined") || (resultContainer == null) || (resultContainer.length == 0))
    {
        // look for msm_subordinate_hotword_match div with same value as currently selected <a> id
        // (the msm_subordinate_hotword_match div contains the previous id value before view was triggered)
        $(".msm_subordinate_hotword_matchs").each(function() {
            if($(this).text() == anchorElement)
            {
                var idInfo = this.id.split("-");
                resultId=idInfo[1];
                for(var i = 2; i < idInfo.length; i++)
                {
                    resultId += "-"+idInfo[i];
                }
                resultContainer = $("#msm_subordinate_result-"+resultId);                                   
                    
            }
        });
    }
    
    $("#msm_subordinate_infoContent-"+resultId).find("a").each(function() {
        var anchorEndingInfo = this.id.split("-");
        var anchorEnding = anchorEndingInfo[1];
        
        for(var i = 2; i < anchorEndingInfo.length; i++)
        {
            anchorEnding += "-"+anchorEndingInfo[i];
        }
            
        removeSubordinate(editor, this.id);        
    });
   
    $(resultContainer).empty().remove();
        
    if(editor.selection.getNode().id == anchorElement)
    {
        var currentId = editor.selection.getNode();
        var anchorContent = $(currentId).html();    
        $(currentId).replaceWith(anchorContent);
    }
    else
    {
        var anchorContent = $("#"+anchorElement).html();
        $("#"+anchorElement).replaceWith(anchorContent);
    }
    
   
}

