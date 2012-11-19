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
                $('<input class="msm_structure_input" id="msm_structure_input_child-0" name="msm_top" placeholder="e.g.) Section"/><br class="childNewline" />').insertBefore('#msm_child_add');
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_top" placeholder="e.g.) Topic"/><br class="childNewline" />').insertBefore('#msm_child_add');
                break;
           
            case 1:
                $('#msm_type_book').attr("checked", "true");
                
                $('#msm_structure_input_top').attr("placeholder", "e.g.) Book");
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-0" name="msm_top" placeholder="e.g.) Chapter"/><br class="childNewline" />').insertBefore('#msm_child_add');
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_top" placeholder="e.g.) Section"/><br class="childNewline" />').insertBefore('#msm_child_add');
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_top" placeholder="e.g.) Subsection"/><br class="childNewline" />').insertBefore('#msm_child_add');
                break;
                
            case 2:
                $('#msm_type_wbook').attr("checked", "true");
                $('#msm_structure_input_top').attr("placeholder", "e.g.) WorkBook");
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-0" name="msm_top" placeholder="e.g.) Chapter"/><br class="childNewline" />').insertBefore('#msm_child_add');
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_top" placeholder="e.g.) Topic"/><br class="childNewline" />').insertBefore('#msm_child_add');
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_top" placeholder="e.g.) Exercises"/><br class="childNewline" />').insertBefore('#msm_child_add');
                break;
                
            case 3:
                $('#msm_type_others').attr("checked", "true");
                $('#msm_structure_input_top').attr("placeholder", "Please specify the name of the top element of this composition.");
                
                $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                $('<input class="msm_structure_input" id="msm_structure_input_child-0" name="msm_top" placeholder="Please specify the name of the child element of this composition."/><br class="childNewline" />').insertBefore('#msm_child_add');
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
        var prevChildid = $('#msm_child_add').prev().prev().attr("id");     
        var childidNumber = prevChildid.split('-');
        
        var newidNumber = parseInt(childidNumber[1])+1;
        
        $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
        $('<input class="msm_structure_input" id="msm_structure_input_child-'+ newidNumber +  ' name="msm_top" placeholder=" Please specify the name of the child element of this composition."/><br class="childNewline" />').insertBefore('#msm_child_add');
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
    var selectedValue = $('input[name=msm_type]:radio:checked').val(); 
    if(selectedValue == 'Others')
    {
        var specifiedValue = $('#msm_type_specifiedType').val();
            
        if((specifiedValue == null)||(specifiedValue == ''))
        {
            $("<div class='dialogs' id='msm_emptyComposition'> Please specify the type of your composition. </div>").appendTo('#msm_type_specifiedType');
            
            $( "#msm_emptyComposition" ).dialog({
                modal: true,
                buttons: {
                    Ok: function() {
                        $('#msm_type_specifiedType').css('border-color', '#FFA500');
                        $( this ).dialog( "close" );
                    }
                }
            });
        }
    //        else
    //        {
    //            console.log(document.getElementById('msm_type_specifiedType').style.borderColor);
    //            if(document.getElementById('msm_type_specifiedType').style.borderColor == 'rgb(255, 165, 0)')
    //            {
    //                $('#msm_type_specifiedType').css('border-color', '#228B22');
    //            }
    //        }
    }
    
    var topElement = $('#msm_structure_input_top').val();
    
    if(topElement == null)
    {
        topElement = selectedValue;
    }
    
    var childvalues = [];
    $('.msm_structure_input').each(function(i) {
        if($(this).val() != null)
        {
            childvalues[i] = $(this).val();
        }
        
    });
}

/**
 *  This method checks if the user tried to save without filling in required field (ie, different border color for field)
 *  then changes the border color when user fills in the field. (color change = orange --> green)
 *
 */
function validateBorder()
{
    if(document.getElementById('msm_type_specifiedType').style.borderColor == 'rgb(255, 165, 0)')
    {
        if($('#msm_type_specifiedType').val())
        {
            $('#msm_type_specifiedType').css('border-color', '#228B22');
        }
    }
    else if(document.getElementById('msm_unit_title').style.borderColor == 'rgb(255, 165, 0)')
    {
        if($('#msm_unit_title').val())
        {
            $('#msm_unit_title').css('border-color', '#228B22');
        }
    }
}

/**
 * This function is activated when radio buttons are triggered.  When the selection of the radio buttons are changed, then update the settings composition name
 * input area to be updated accordingly.
 */
function processChange(e)
{
    switch(e.target.id)
    {
        case "msm_type_lecture":
            $('.msm_structure_names').remove();
            $('.msm_structure_input').remove();
            $('br.childNewline').remove();
            
            $('#msm_structure_input_top').attr("placeholder", "e.g.) Lecture");
                
            $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
            $('<input class="msm_structure_input" id="msm_structure_input_child-0" name="msm_top" placeholder="e.g.) Section"/><br class="childNewline" />').insertBefore('#msm_child_add');
                
            $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
            $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_top" placeholder="e.g.) Topic"/><br class="childNewline" />').insertBefore('#msm_child_add');
            break;
            
        case "msm_type_book":
            $('.msm_structure_names').remove();
            $('.msm_structure_input').remove();
            $('br.childNewline').remove();
            
            $('#msm_structure_input_top').attr("placeholder", "e.g.) Book");
                
            $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
            $('<input class="msm_structure_input" id="msm_structure_input_child-0" name="msm_top" placeholder="e.g.) Chapter"/><br class="childNewline" />').insertBefore('#msm_child_add');
                
            $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
            $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_top" placeholder="e.g.) Section"/><br class="childNewline" />').insertBefore('#msm_child_add');
                
            $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
            $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_top" placeholder="e.g.) Subsection"/><br class="childNewline" />').insertBefore('#msm_child_add');
            break;
                
        case "msm_type_wbook":
            $('.msm_structure_names').remove();
            $('.msm_structure_input').remove();
            $('br.childNewline').remove();
            
            $('#msm_structure_input_top').attr("placeholder", "e.g.) WorkBook");
                
            $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
            $('<input class="msm_structure_input" id="msm_structure_input_child-0" name="msm_top" placeholder="e.g.) Chapter"/><br class="childNewline" />').insertBefore('#msm_child_add');
                
            $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
            $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_top" placeholder="e.g.) Topic"/><br class="childNewline" />').insertBefore('#msm_child_add');
                
            $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
            $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_top" placeholder="e.g.) Exercises"/><br class="childNewline" />').insertBefore('#msm_child_add');
            break;
            
        case "msm_type_others":
            $('.msm_structure_names').remove();
            $('.msm_structure_input').remove();
            $('br.childNewline').remove();
            
            $('#msm_structure_input_top').attr("placeholder", "Please specify the name of the top element of this composition.");
                
            $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
            $('<input class="msm_structure_input" id="msm_structure_input_child-0" name="msm_top" placeholder="Please specify the name of the child element of this composition."/><br class="childNewline" />').insertBefore('#msm_child_add');
            break;
                
    }
    
}

/**
 * This function is activated when user drags one of the structural elememts on the very left side of the panel to middle panel.
 * It adds appropriate fields for the users to fill out for def/theorem/comments/info/content/media and images.
 */
function processDroppedChild(e, droppedId, _index)
{ 
    var clonedCurrentElement = $("<div></div>");
    
    _index++;
    
    var id = droppedId;
    
    switch(droppedId)
    {
        case "msm_def":
            var defSelectMenu = $('<select class="msm_unit_child_dropdown" id="msm_def_type_dropdown-'+_index+'" onchange="processType(event);">\n\
                                <option value="Notation">Notation</option>\n\
                                <option value="Definition">Definition</option>\n\
                                <option value="Agreement">Agreement</option>\n\
                                <option value="Convention">Convention</option>\n\
                                <option value="Axiom">Axiom</option>\n\
                                <option value="Terminology">Terminology</option>\n\
                            </select>');
            var defTitleField = $('<input class="msm_unit_child_title" id="msm_def_title_input-'+_index+'" name="msm_def_title" placeholder=" Title of Definition"/>');
            //            var defEditButtonArea = $('<span id="msm_def_edit_button_location-'+_index+'"></span>');
            //            var defContentField = $('<textarea class="msm_unit_child_content" id="msm_def_content_input-'+_index+'" name="msm_def_content" placeholder="Need to add moodle form here?"  onclick="showtinyMce(event);"/>');
            var defContentField = $('<textarea class="msm_unit_child_content" id="msm_def_content_input-'+_index+'" name="msm_def_content" placeholder="Need to add moodle form here?"/>');

            
            var defAssoMenu = $('<div class="msm_associate_optionarea"><b> Choose an associated information: </b>\n\
                            <select class="msm_associated_dropdown" id="msm_def_associate_dropdown-'+_index+'" onchange="processAssociate(event);">\n\
                                <option value="None">None</option>\n\
                                <option value="Info">Quick Info</option>\n\
                                <option value="Comment">Comment</option>\n\
                                <option value="Explanation">Explanation</option>\n\
                                <option value="Example">Example</option>\n\
                                <option value="Illustration">Illustration</option>\n\
                            </select></div>');
        
            //        var currentElement = document.getElementById(droppedId);
            clonedCurrentElement.attr("id", "copied_msm_def-"+_index);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            //            clonedCurrentElement.attr("onclick", "resizeElement(event)");
            clonedCurrentElement.append(defSelectMenu);
            clonedCurrentElement.append(defTitleField);
            //            clonedCurrentElement.append(defEditButtonArea);
            clonedCurrentElement.append(defContentField);
            clonedCurrentElement.append(defAssoMenu);
            clonedCurrentElement.appendTo('#msm_child_appending_area');
            break;
        
        case "msm_theorem":
            var theoremSelectMenu = $('<select class="msm_unit_child_dropdown" id="msm_theorem_type_dropdown-'+_index+'" onchange="processType(event);">\n\
                                <option value="Theorem">Theorem</option>\n\
                                <option value="Proposition">Proposition</option>\n\
                                <option value="Lemma">Lemma</option>\n\
                                <option value="Corollary">Corollary</option>\n\
                            </select>');
            var theoremTitleField = $('<input class="msm_unit_child_title" id="msm_theorem_title_input-'+_index+'" name="msm_theorem_title" placeholder=" Title of Theorem"/>');
            //            var theoremEditButtonArea = $('<span id="msm_theorem_edit_button_location-'+_index+'"></span>');
            //            var theoremContentField = $('<textarea class="msm_unit_child_content" id="msm_theorem_content_input-'+_index+'" name="msm_theorem_content" placeholder=" Need to add moodle form here?" onclick="showtinyMce(event);"/>');
            var theoremContentField = $('<textarea class="msm_unit_child_content" id="msm_theorem_content_input-'+_index+'" name="msm_theorem_content" placeholder=" Need to add moodle form here?"/>');

            var theoremAssoMenu = $('<div class="msm_associate_optionarea"><b> Choose an associated information: </b>\n\
                            <select class="msm_associated_dropdown" id="msm_theorem_associate_dropdown-'+_index+'" onchange="processAssociate(event);">\n\
                                <option value="None">None</option>\n\
                                <option value="Info">Quick Info</option>\n\
                                <option value="Comment">Comment</option>\n\
                                <option value="Explanation">Explanation</option>\n\
                                <option value="Example">Example</option>\n\
                                <option value="Illustration">Illustration</option>\n\
                                <option value="Proof">Proof</option>\n\
                            </select></div>');
        
            clonedCurrentElement.attr("id", "copied_msm_theorem-"+_index);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            //            clonedCurrentElement.attr("onclick", "resizeElement(event)");
            clonedCurrentElement.append(theoremSelectMenu);
            clonedCurrentElement.append(theoremTitleField);
            //            clonedCurrentElement.append(theoremEditButtonArea);
            clonedCurrentElement.append(theoremContentField);
            clonedCurrentElement.append(theoremAssoMenu);
            clonedCurrentElement.appendTo('#msm_child_appending_area');
            break;
            
        case "msm_pic":
            alert("pic");
            break;
            
        case "msm_intro":
            var introTitle = $('<h3> Introduction </h3>');
            //            var introEditButtonArea = $('<span id="msm_intro_edit_button_location-'+_index+'"></span>');
            //            var introContentField = $('<textarea class="msm_unit_child_content" id="msm_intro_content_input-'+_index+'" name="msm_intro_content" placeholder=" Need to add moodle form here?" onclick="showtinyMce(event);"/>');
            var introContentField = $('<textarea class="msm_unit_child_content" id="msm_intro_content_input-'+_index+'" name="msm_intro_content" placeholder=" Need to add moodle form here?"/>');

            
            clonedCurrentElement.attr("id", "copied_msm_intro-"+_index);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            //            clonedCurrentElement.attr("onclick", "resizeElement(event)");
            
            clonedCurrentElement.append(introTitle);
            //            clonedCurrentElement.append(introEditButtonArea);
            clonedCurrentElement.append(introContentField);
            clonedCurrentElement.appendTo('#msm_child_appending_area');
            break;
            
        case "msm_body":
            //            var bodyEditButtonArea = $('<span id="msm_body_edit_button_location-'+_index+'"></span>');
            //            var bodyContentField = $('<textarea class="msm_unit_child_content" id="msm_body_content_input-'+_index+'" name="msm_body_content" placeholder=" Need to add moodle form here?" onclick="showtinyMce(event);"/>');
            var bodyContentField = $('<textarea class="msm_unit_child_content" id="msm_body_content_input-'+_index+'" name="msm_body_content" placeholder=" Need to add moodle form here?"/>');

            
            clonedCurrentElement.attr("id", "copied_msm_body-"+_index);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            //            clonedCurrentElement.attr("onclick", "resizeElement(event)");
            
            //            clonedCurrentElement.append(bodyEditButtonArea);
            clonedCurrentElement.append(bodyContentField);
            clonedCurrentElement.appendTo('#msm_child_appending_area');
            break;
            
        case "msm_media":
            alert("media");
            break;       
            
    }
    if($('#msm_editor_save').attr("disabled"))
    {
        $('#msm_editor_save').removeAttr('disabled');
    }
    
    tinyMCE.init({
        mode:"textareas",
        plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width: "96%",
        height: "70%",
        theme: "advanced",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,ltr,rtl",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,forecolor,backcolor",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        skin : "o2k7",
        skin_variant : "silver"
    });
    //    
    //enabling the save button since there are contents in the middle editor area  
//    
//    $("#msm_trash_droparea").droppable({
//        accepted: "#mem_editor_middle_droparea > div",
//        hoverClass: "ui-state-hover",
//        tolerance: "pointer",
//        drop: function( event, ui ) {
//            $(ui.draggable).remove();
//            $('#msm_trash_droparea').css('display', 'none');
//            if($("#msm_child_appending_area").children().length < 1)
//            {
//                $("#msm_editor_save").attr("disabled", "disabled");
//            }
//        }
//    }); 
    $("#msm_child_appending_area").sortable({
        appendTo: "#msm_child_appending_area",
        connectWith: "#msm_child_appending_area",
        cursor: "move",
        tolerance: "pointer",
        placeholder: "msm_sortable_placeholder",
        start: function(event, ui)
        {
            $(".msm_sortable_placeholder").width(ui.item.context.offsetWidth);
            $('#msm_trash_droparea').css('display', 'block');
        },
        stop: function(event, ui)
        {
            $("#msm_trash_droparea").css("display", "none");     
        }
    });
                
    $("#msm_child_appending_area").disableSelection();
    
    return _index;
}

/**
 * to save contents created in middle editor panel
 *  --> diplay change: from filled in content --> maintain same content in middle but with edit button instead of  & thumbnail of composed unit added to tree in right panel
 *  --> disable drag&drop/resize...etc
 */
function saveUnit()
{
    $('#msm_editor_save').ready(function() {
        var titleinput = $('#msm_unit_title').val();
        console.log(titleinput);
        if((titleinput == null)||(titleinput == ''))
        {
            $("<div class='dialogs' id='msm_emptyMsmTitle'> Please specify the title of this unit. </div>").appendTo('#msm_unit_title');
            
            $( "#msm_emptyMsmTitle" ).dialog({
                modal: true,
                buttons: {
                    Ok: function() {
                        $('#msm_unit_title').css('border-color', '#FFA500');
                        $( this ).dialog( "close" );
                    }
                }
            });   
        }
        else
        {
            $('#msm_editor_save').remove();
            $('<button class="msm_editor_buttons" id="msm_editor_edit" type="button" onclick="editUnit()"> Edit </button>').appendTo('#msm_editor_middle');
            $('#msm_editor_reset').attr("disabled", "disabled");
        }
       
    });
//AJAX call to php file/function that will put appropriate info into db tables   
}

/**
 * need to enable drag/drop/resize again... and also enable edit button
 *
 */
function editUnit()
{
    
}

/**
 * needs to ask if the user wants to save the content if save button has not been pressed, if was saved, then call saveUnit and close dialog,
 * else if not save then empty out content in middle, save button disabled while reset is abled
 */
function resetUnit()
{    
    $('#msm_editor_reset').ready(function() {
        $("<div class='dialogs' id='msm_resetComposition'> <span class='ui-icon ui-icon-alert' style='float: left; margin: 0 7px 20px 0;'></span>Are you sure you wish to discard the current composition? </div>").appendTo('#msm_editor_middle');
        $( "#msm_resetComposition" ).dialog({
            resizable: false,
            height:180,
            modal: true,
            buttons: {
                "Yes": function() {
                    // empty out the contents in the middle editting area and diable the save button
                    $('#msm_editor_middle_droparea').empty();
                    $('#msm_editor_save').attr("disabled", "disabled");
                    $( this ).dialog( "close" );
                },
                "No": function() {
                    $( this ).dialog( "close" );                   
                }
            }
        });
    });
}
