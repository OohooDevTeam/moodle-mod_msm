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

// e = event
function processReftype(e)
{
//    _index++;
    var selectedReftype = e.target.selectedIndex;
    var selectedtext = null;
    
    var selectedId = e.target.id;    
        
    var element;
    
    var idString = "#"+e.target.parentElement.id + " > div";
    
   // removes any previously added forms for previous choices made by the author
   $(idString).each(function() {
       $(this).empty().remove();   
   })
        
    switch(selectedReftype)
    {
        case 0:
            selectedtext = "None";
            break;
        case 1:
            selectedtext = "Comment";
            element = makeRefComment();
            $(element).insertAfter("#"+selectedId);
            break;
        case 2:
            selectedtext = "Definition";
            element = makeRefDefinition();
            $(element).insertAfter("#"+selectedId);
            break;
        case 3:
            selectedtext = "Theorem";
            element = makeRefTheorem();
            $(element).insertAfter("#"+selectedId);
            break;            
        case 4:
            selectedtext = "Example";
            break;
        case 5:
            selectedtext = "Section of this Composition";
            break; 
    }
    
    alert(selectedtext);
}


function makeRefDefinition()
{
    var clonedCurrentElement = $("<div></div>");
    
    var defCloseButton = $('<a class="msm_element_close" id="msm_def_element_closebutton-'+_index+'" onclick="deleteRefElement(event);">x</a>');
    var defSelectMenu = $('<select name="msm_def_type_dropdown-'+_index+'" class="msm_unit_child_dropdown" id="msm_def_type_dropdown-'+_index+'">\n\
                                <option value="Notation">Notation</option>\n\
                                <option value="Definition">Definition</option>\n\
                                <option value="Agreement">Agreement</option>\n\
                                <option value="Convention">Convention</option>\n\
                                <option value="Axiom">Axiom</option>\n\
                                <option value="Terminology">Terminology</option>\n\
                            </select>');
    var defTitle = $("<span class='msm_element_title'><b> DEFINITION </b></span>");
    var defTitleField = $('<input class="msm_unit_child_title" id="msm_def_title_input-'+_index+'" name="msm_def_title_input-'+_index+'" placeholder=" Title of Definition"/>');
          
    var defContentField = $('<textarea class="msm_unit_child_content" id="msm_def_content_input-'+_index+'" name="msm_def_content_input-'+_index+'"/>');
    var defDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_def_description_label-"+_index+"' for='msm_def_descripton_input-"+_index+"'>Description: </label>");
    var defDescriptionField = $("<input class='msm_child_description_inputs' id='msm_def_descripton_input-"+_index+"' name='msm_def_descripton_input-"+_index+"' placeholder='Insert description to search this element in future. '/>");
               
    clonedCurrentElement.attr("id", "copied_msm_defref-"+_index);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
   
    clonedCurrentElement.append(defCloseButton);
    clonedCurrentElement.append(defSelectMenu);
    clonedCurrentElement.append(defTitle);
    clonedCurrentElement.append(defTitleField);
    clonedCurrentElement.append(defContentField);
    clonedCurrentElement.append(defDescriptionLabel);
    clonedCurrentElement.append(defDescriptionField);
    
    return clonedCurrentElement;
}

function makeRefTheorem()
{
    var clonedCurrentElement = $("<div></div>");
    var theoremCloseButton = $('<a class="msm_element_close" id="msm_theorem_element_closebutton-'+_index+'" onclick="deleteRefElement(event)">x</a>');

    var theoremSelectMenu = $('<select name="msm_theorem_type_dropdown-'+_index+'" class="msm_unit_child_dropdown" id="msm_theorem_type_dropdown-'+_index+'">\n\
                                <option value="Theorem">Theorem</option>\n\
                                <option value="Proposition">Proposition</option>\n\
                                <option value="Lemma">Lemma</option>\n\
                                <option value="Corollary">Corollary</option>\n\
                            </select>');
                
    var theoremTitle = $("<span class='msm_element_title'><b> THEOREM </b></span>");
    var theoremTitleField = $('<input class="msm_unit_child_title" id="msm_theorem_title_input-'+_index+'" name="msm_theorem_title_input-'+_index+'" placeholder=" Title of Theorem"/>');
            
    var theoremContentWrapper = $('<div class="msm_theorem_content_containers" id="msm_theorem_content_container-'+_index+'"></div>');
            
    var theoremStatementWrapper = $('<div class="msm_theorem_statement_containers" id="msm_theorem_statement_container-'+_index+'"></div>');
            
    var theoremContentHeader = $('<span class="msm_theorem_content_header"><b>Theorem Content</b></span>');
    var theoremContentField = $('<textarea class="msm_unit_child_content" id="msm_theorem_content_input-'+_index+'" name="msm_theorem_content_input-'+_index+'"/>');
            
    var theoremPartWrapper = $('<div class="msm_theorem_part_dropareas" id="msm_theorem_part_droparea-'+_index+'"></div>');
            
    var theoremChildButton = $('<input class="msm_theorem_child_buttons" id="msm_theorem_child_button-'+_index+'" type="button" onclick="addTheoremContent('+_index+')" value="Add content"/>');
    var theoremPartButton = $('<input class="msm_theorem_part_buttons" id="msm_theorem_part_button-'+_index+'" type="button" onclick="addTheoremPart('+_index+')" value="Add more parts"/>');
    var theoremDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_theorem_description_label-"+_index+"' for='msm_theorem_descripton_input-"+_index+"'>Description: </label>");
    var theoremDescriptionField = $("<input class='msm_child_description_inputs' id='msm_theorem_descripton_input-"+_index+"' name='msm_theorem_descripton_input-"+_index+"' placeholder='Insert description to search this element in future. '/>");
            
    clonedCurrentElement.attr("id", "copied_msm_theoremref-"+_index);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
    theoremPartWrapper.append(theoremPartButton);
            
    theoremStatementWrapper.append(theoremContentHeader);
    theoremStatementWrapper.append(theoremContentField); 
    theoremStatementWrapper.append(theoremPartWrapper);
            
    theoremContentWrapper.append(theoremStatementWrapper);
    theoremContentWrapper.append(theoremChildButton);
            
    clonedCurrentElement.append(theoremCloseButton);
    clonedCurrentElement.append(theoremSelectMenu);
    clonedCurrentElement.append(theoremTitle);
    clonedCurrentElement.append(theoremTitleField);
    clonedCurrentElement.append(theoremContentWrapper);
    clonedCurrentElement.append(theoremDescriptionLabel);
    clonedCurrentElement.append(theoremDescriptionField);
            
    return clonedCurrentElement;
}

function makeRefComment()
{
    var clonedCurrentElement = $("<div></div>");
    var commentCloseButton = $('<a class="msm_element_close" id="msm_comment_element_closebutton-'+_index+'" onclick="deleteRefElement(event);">x</a>');
    var commentSelectMenu = $('<select name="msm_comment_type_dropdown-'+_index+'" class="msm_unit_child_dropdown" id="msm_comment_type_dropdown-'+_index+'">\n\
                                <option value="Comment">Comment</option>\n\
                                <option value="Remark">Remark</option>\n\
                                <option value="Information">Information</option>\n\
                            </select>');
    var commentTitle = $("<span class='msm_element_title'><b> COMMENT </b></span>");
    var commentTitleField = $('<input class="msm_unit_child_title" id="msm_comment_title_input-'+_index+'" name="msm_comment_title_input-'+_index+'" placeholder=" Title of Comment"/>');
          
    var commentContentField = $('<textarea class="msm_unit_child_content" id="msm_comment_content_input-'+_index+'" name="msm_comment_content_input-'+_index+'"/>');
    var commentDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_comment_description_label-"+_index+"' for='msm_comment_descripton_input-"+_index+"'>Description: </label>");
    var commentDescriptionField = $("<input class='msm_child_description_inputs' id='msm_comment_descripton_input-"+_index+"' name='msm_comment_descripton_input-"+_index+"' placeholder='Insert description to search this element in future. '/>");
            
    clonedCurrentElement.attr("id", "copied_msm_commentref-"+_index);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
    clonedCurrentElement.append(commentCloseButton);
    clonedCurrentElement.append(commentSelectMenu);
    clonedCurrentElement.append(commentTitle);
    clonedCurrentElement.append(commentTitleField);
    clonedCurrentElement.append(commentContentField);
    clonedCurrentElement.append(commentDescriptionLabel);
    clonedCurrentElement.append(commentDescriptionField);
    
    return clonedCurrentElement;
}

function deleteRefElement(e)
{
    var currentElementType = e.target.id.split("_");
    var currentElementInfo = e.target.id.split("-");
    
    var currentElement = "copied_msm_"+currentElementType[1]+"ref-"+currentElementInfo[1];
    
    $("<div class='dialogs' id='msm_deleteRefComposition'> <span class='ui-icon ui-icon-alert' style='float: left; margin: 0 7px 20px 0;'></span>Are you sure you wish to delete this element from the composition? </div>").appendTo('#'+currentElement);
    $( "#msm_deleteRefComposition" ).dialog({
        resizable: false,
        height:180,
        modal: true,
        buttons: {
            "Yes": function() {
                $('#'+currentElement).empty().remove();
                
                if($('#msm_associate_reftype_optionarea'+currentElementInfo[1]).children().length < 1)
                {
                    $('#msm_associate_reftype-'+currentElementInfo[1]+'-1').val("None");
                }   
                
                $( this ).dialog( "close" );
            },
            "No": function() {
                $( this ).dialog( "close" );                   
            }
        }
    });
}
