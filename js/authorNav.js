/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *  This function is triggered by the click on Settings navigation bar.  It opens the modal dialog box that has all the 
 *  forms responsible for changing the settings of the editor tool.
 *
*/
function openNavDialog(chosenvalue)
{
    $('#msm_nav_setting').ready(function() {
        $('#msm_setting_dialog').dialog({
            // disabling the close button 
            open: function(event, ui) {
                $(".ui-dialog-titlebar-close").hide();
            },
            modal:true,
            autoOpen: false,
            height: 500,
            width: 605,
            closeOnEscape: false
        });
        $('#msm_setting_dialog').dialog('open').css('display', 'block');
        switch(chosenvalue)
        {           
            case 0:
                $('#msm_type_lecture').attr("checked", "true");
                $('#msm_structure_input_top').attr("placeholder", "e.g.) Lecture");
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-0" name="msm_top" placeholder="e.g.) Section"/><br />').insertBefore('#msm_child_add');
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_top" placeholder="e.g.) Topic"/><br />').insertBefore('#msm_child_add');
                break;
           
            case 1:
                $('#msm_type_book').attr("checked", "true");
                
                $('#msm_structure_input_top').attr("placeholder", "e.g.) Book");
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-0" name="msm_top" placeholder="e.g.) Chapter"/><br />').insertBefore('#msm_child_add');
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_top" placeholder="e.g.) Section"/><br />').insertBefore('#msm_child_add');
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_top" placeholder="e.g.) Subsection"/><br />').insertBefore('#msm_child_add');
                break;
                
            case 2:
                $('#msm_type_wbook').attr("checked", "true");
                $('#msm_structure_input_top').attr("placeholder", "e.g.) WorkBook");
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-0" name="msm_top" placeholder="e.g.) Chapter"/><br />').insertBefore('#msm_child_add');
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_top" placeholder="e.g.) Topic"/><br />').insertBefore('#msm_child_add');
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_top" placeholder="e.g.) Exercises"/><br />').insertBefore('#msm_child_add');
                break;
                
            case 3:
                $('#msm_type_others').attr("checked", "true");
                $('#msm_structure_input_top').attr("placeholder", "Please specify the name of the top element of this composition.");
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-0" name="msm_top" placeholder="Please specify the name of the child element of this composition."/><br />').insertBefore('#msm_child_add');
                break;
        }
    });
}

/**
 *  This function is activated when the add Children button is clicked in settings form.
 *  It adds more input fields for the users to fill out.
 *
*/
function addChildUnit()
{
    $('#msm_child_add').ready(function () {
        console.log($('#msm_child_add').prev().prev());
        var prevChildid = $('#msm_child_add').prev().prev().attr("id");     
        var childidNumber = prevChildid.split('-');
        
        var newidNumber = parseInt(childidNumber[1])+1;
        
        $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
        $('<input class="msm_structure_input" id="msm_structure_input_child-'+ newidNumber +  ' name="msm_top" placeholder=" Please specify the name of the child element of this composition."/><br />').insertBefore('#msm_child_add');
    });
}

/**
 * This function is activated when the cancel button is pressed in the settings form.
 * It prompts the user for verification on their choice to close the settings form without saving.
 *
*/
function closeSetting()
{
    $('#msm_setting_cancel').ready(function() {
        $("#msm_setting_cancelled > p").css("display", "block");
        $( "#msm_setting_cancelled" ).dialog({
            resizable: false,
            height:180,
            modal: true,
            buttons: {
                "Yes": function() {
                    $( this ).dialog( "close" );
                    $('#msm_setting_dialog').dialog("close");
                },
                "No": function() {
                    $( this ).dialog( "close" );                   
                }
            }
        });
    });
}

/**
 * This function will respond to save button in settings modal dialog and will pass the information in the settins form to update appropriate database field values.
 */
function saveSetting()
{
    
}

/**
 * This function is activated when radio buttons are triggered.  When the selection of the radio buttons are changed, then update the settings composition name
 * input area to be updated accordingly.
 */
function processChange()
{
    
}

/**
 * This function is activated when user drags one of the structural elememts on the very left side of the panel to middle panel.
 * It adds appropriate fields for the users to fill out for def/theorem/comments/info/content/media and images.
 */
function processDroppedChild(e, droppedId, _index)
{    
    var clonedCurrentElement = $("<div></div>");
    
    switch(droppedId)
    {
        case "msm_def":
            var defSelectMenu = $('<select class="msm_unit_child_dropdown">\n\
                                <option value="Notation">Notation</option>\n\
                                <option value="Definition">Definition</option>\n\
                                <option value="Agreement">Agreement</option>\n\
                                <option value="Convention">Convention</option>\n\
                                <option value="Axiom">Axiom</option>\n\
                                <option value="Terminology">Terminology</option>\n\
                            </select>');
            var defTitleField = $('<input class="msm_unit_child_title" id="msm_def_title_input" name="msm_def_title" placeholder=" Title of Definition"/>');
            var defContentField = $('<textarea class="msm_unit_child_content" id="msm_def_content_input" name="msm_def_content" placeholder="Need to add moodle form here?"/>');
            
            var defAssoMenu = $('<b> Choose an associated information: </b>\n\
                            <select class="msm_associated_dropdown">\n\
                                <option value="None">None</option>\n\
                                <option value="Info">Quick Info</option>\n\
                                <option value="Comment">Comment</option>\n\
                                <option value="Explanation">Explanation</option>\n\
                                <option value="Example">Example</option>\n\
                                <option value="Illustration">Illustration</option>\n\
                            </select>');
        
            //        var currentElement = document.getElementById(droppedId);
            clonedCurrentElement.attr("id", "copied_msm_def-"+_index++);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            clonedCurrentElement.attr("ondblclick", "resizeElement(event)");
            clonedCurrentElement.append(defSelectMenu);
            clonedCurrentElement.append(defTitleField);
            clonedCurrentElement.append(defContentField);
            clonedCurrentElement.append(defAssoMenu);
       
            clonedCurrentElement.appendTo('#msm_editor_middle_droparea');
            break;
        
        case "msm_theorem":
            var theoremSelectMenu = $('<select class="msm_unit_child_dropdown">\n\
                                <option value="Theorem">Theorem</option>\n\
                                <option value="Proposition">Proposition</option>\n\
                                <option value="Lemma">Lemma</option>\n\
                                <option value="Corollary">Corollary</option>\n\
                            </select>');
            var theoremTitleField = $('<input class="msm_unit_child_title" id="msm_theorem_title_input" name="msm_theorem_title" placeholder=" Title of Theorem"/>');
            var theoremContentField = $('<textarea class="msm_unit_child_content" id="msm_theorem_content_input" name="msm_theorem_content" placeholder=" Need to add moodle form here?"/>');
            var theoremAssoMenu = $('<b> Choose an associated information: </b>\n\
                            <select class="msm_associated_dropdown">\n\
                                <option value="None">None</option>\n\
                                <option value="Info">Quick Info</option>\n\
                                <option value="Comment">Comment</option>\n\
                                <option value="Explanation">Explanation</option>\n\
                                <option value="Example">Example</option>\n\
                                <option value="Illustration">Illustration</option>\n\
                                <option value="Proof">Proof</option>\n\
                            </select>');
        
            clonedCurrentElement.attr("id", "copied_msm_theorem"+_index++);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            clonedCurrentElement.attr("ondblclick", "resizeElement(event)");
            clonedCurrentElement.append(theoremSelectMenu);
            clonedCurrentElement.append(theoremTitleField);
            clonedCurrentElement.append(theoremContentField);
            clonedCurrentElement.append(theoremAssoMenu);
        
            clonedCurrentElement.appendTo('#msm_editor_middle_droparea');
            break;
            
        case "msm_pic":
            alert("pic");
            break;
            
        case "msm_intro":
            var introTitle = $('<h3> Introduction </h3>');
            var introContentField = $('<textarea class="msm_unit_child_content" id="msm_intro_content_input" name="msm_intro_content" placeholder=" Need to add moodle form here?"/>');
            clonedCurrentElement.attr("id", "copied_msm_intro"+_index++);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            clonedCurrentElement.attr("ondblclick", "resizeElement(event)");
            
            clonedCurrentElement.append(introTitle);
            clonedCurrentElement.append(introContentField);
            clonedCurrentElement.appendTo('#msm_editor_middle_droparea');
            break;
            
        case "msm_body":
            var bodyContentField = $('<textarea class="msm_unit_child_content" id="msm_body_content_input" name="msm_body_content" placeholder=" Need to add moodle form here?"/>');
            
            clonedCurrentElement.attr("id", "copied_msm_body"+_index++);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            clonedCurrentElement.attr("ondblclick", "resizeElement(event)");
            
            clonedCurrentElement.append(bodyContentField);
            clonedCurrentElement.appendTo('#msm_editor_middle_droparea');
            break;
            
        case "msm_media":
            alert("media");
            break;           
            
    }
    
    $('.copied_msm_structural_element').draggable({
        appendTo: "msm_editor_middle_droparea",
        containment: "msm_editor_middle_droparea",
        cursor: "move"
    });  
    
    return _index;
}

/**
 * This method is activated when the dragged item in the middle panel is double clicked.
 * It allows for the div to be resized by calling on jQuery UI resizable.
 */
function resizeElement(e)
{   
    var currentid = e.target.id;
    var match = currentid.match(/copied_/);
    
    // to prevent textarea from being trigger to resize when double clicked
    if(match != null)
    {
        $('#'+currentid).resizable({
            containement: "parent",
            ghost: true,
            helper: "resizable-helper",
            minHeight: 150,
            minWidth: 320
        });  
    }
   
}

