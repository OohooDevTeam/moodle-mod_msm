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
 * This JS file contains all the functions that "extra" features of the editor such as navigating the 
 * menu at the top, giving function to settings window...etc
 */

/**
 *  This function is triggered by the click on Settings navigation bar.  It opens the modal dialog box that has all the 
 *  forms responsible for changing the settings of the editor tool.
 *
 */
function openNavDialog()
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
        
        // this condition is to prevent code from adding child units whenever window is cancelled then opened again
        if($('#msm_element_names').children('.msm_structure_names').length == 0)
        {
            var allUnitNames = $('#msm_unit_name_input').val().split(",");
            
            switch(allUnitNames[0])
            {
                case "Lecture":
                    $('#msm_type_lecture').attr("checked", "true");
                    $('#msm_structure_input_top').attr("value", allUnitNames[0]);
                    break;
                case "Book":
                    $('#msm_type_book').attr("checked", "true");
                    $('#msm_structure_input_top').attr("value", allUnitNames[0]);
                    break;
                case "Work Book":
                    $('#msm_type_wbook').attr("checked", "true");
                    $('#msm_structure_input_top').attr("value", allUnitNames[0]);
                    break;
                case "Others":
                    $('#msm_type_others').attr("checked", "true");
                    $('#msm_structure_input_top').attr("value", allUnitNames[0]);
                    break;
            } 
            for(var i=1; i < allUnitNames.length-1; i++)
            {
              console.log(allUnitNames[i]);
                if(allUnitNames[i] != '')
                {
                    var inputLabel = document.createElement('label');
                    inputLabel.htmlFor = "msm_structure_input_child-"+(i-1);
                    
                    var labelText = document.createTextNode("Child Unit : ");
                    
                    inputLabel.appendChild(labelText);   
                    
                    var inputField = document.createElement('input');
                    inputField.className = "msm_structure_input";
                    inputField.id = "msm_structure_input_child-"+(i-1);
                    inputField.name = "msm_structure_input_child-"+(i-1);
                    
                    $(inputField).val(allUnitNames[i]);
                    
                    var settingContainer = document.createElement('div');
                    settingContainer.className = "msm_setting_form";
                    
                    settingContainer.appendChild(inputLabel);
                    settingContainer.appendChild(inputField);
                    
                    $(settingContainer).insertBefore('#msm_child_add');
                }               
                
            }            
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
        
        var lastId = $(".msm_setting_form").last().children('.msm_structure_input').attr("id");
        
        //        var prevChildid = $('#msm_child_add').prev().prev().attr("id");     
        var childidNumber = lastId.split('-');
        
        var newidNumber = parseInt(childidNumber[1])+1;
        
        var inputLabel = document.createElement('label');
        inputLabel.htmlFor = "msm_structure_input_child-"+newidNumber;
                    
        var labelText = document.createTextNode("Child Unit : ");
                    
        inputLabel.appendChild(labelText);   
                    
        var inputField = document.createElement('input');
        inputField.className = "msm_structure_input";
        inputField.id = "msm_structure_input_child-"+newidNumber;
        inputField.name = "msm_structure_input_child-"+newidNumber;
                    
        $(inputField).attr("placeholder","Please specify the name of the child element of this composition.");
                    
        var settingContainer = document.createElement('div');
        settingContainer.className = "msm_setting_form";
                    
        settingContainer.appendChild(inputLabel);
        settingContainer.appendChild(inputField);
                    
        $(settingContainer).insertBefore('#msm_child_add');        
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


