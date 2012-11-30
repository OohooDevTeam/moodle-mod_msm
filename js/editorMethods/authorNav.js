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
        
        // this condition is to prevent code from adding child units whenever window is cancelled then opened again
        if($('#msm_element_names').children('.msm_structure_names').length == 0)
        {
            switch(chosenvalue)
            {           
                case 0:
                    $('#msm_type_lecture').attr("checked", "true");
                    $('#msm_structure_input_top').attr("placeholder", "e.g.) Lecture");
                
                    $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                    $('<input class="msm_structure_input" id="msm_structure_input_child-0" name="msm_structure_input_child-0" placeholder="e.g.) Section"/><br class="childNewline" />').insertBefore('#msm_child_add');
                
                    $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                    $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_structure_input_child-1" placeholder="e.g.) Topic"/><br class="childNewline" />').insertBefore('#msm_child_add');
                    break;
           
                case 1:
                    $('#msm_type_book').attr("checked", "true");
                
                    $('#msm_structure_input_top').attr("placeholder", "e.g.) Book");
                
                    $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                    $('<input class="msm_structure_input" id="msm_structure_input_child-0" name="msm_structure_input_child-0" placeholder="e.g.) Chapter"/><br class="childNewline" />').insertBefore('#msm_child_add');
                
                    $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                    $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_structure_input_child-1" placeholder="e.g.) Section"/><br class="childNewline" />').insertBefore('#msm_child_add');
                
                    $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                    $('<input class="msm_structure_input" id="msm_structure_input_child-2" name="msm_structure_input_child-2" placeholder="e.g.) Subsection"/><br class="childNewline" />').insertBefore('#msm_child_add');
                    break;
                
                case 2:
                    $('#msm_type_wbook').attr("checked", "true");
                    $('#msm_structure_input_top').attr("placeholder", "e.g.) WorkBook");
                
                    $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                    $('<input class="msm_structure_input" id="msm_structure_input_child-0" name="msm_structure_input_child-0" placeholder="e.g.) Chapter"/><br class="childNewline" />').insertBefore('#msm_child_add');
                
                    $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                    $('<input class="msm_structure_input" id="msm_structure_input_child-1" name="msm_structure_input_child-1" placeholder="e.g.) Topic"/><br class="childNewline" />').insertBefore('#msm_child_add');
                
                    $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                    $('<input class="msm_structure_input" id="msm_structure_input_child-2" name="msm_structure_input_child-2" placeholder="e.g.) Exercises"/><br class="childNewline" />').insertBefore('#msm_child_add');
                    break;
                
                case 3:
                    $('#msm_type_others').attr("checked", "true");
                    $('#msm_structure_input_top').attr("placeholder", "Please specify the name of the top element of this composition.");
                
                    $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
                    $('<input class="msm_structure_input" id="msm_structure_input_child-0" name="msm_structure_input_child-0" placeholder="Please specify the name of the child element of this composition."/><br class="childNewline" />').insertBefore('#msm_child_add');
                    break;
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
        var prevChildid = $('#msm_child_add').prev().prev().attr("id");     
        var childidNumber = prevChildid.split('-');
        
        var newidNumber = parseInt(childidNumber[1])+1;
        
        $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
        $('<input class="msm_structure_input" id="msm_structure_input_child-'+ newidNumber +  '" name="msm_structure_input_child-'+ newidNumber +  '" placeholder=" Please specify the name of the child element of this composition."/><br class="childNewline" />').insertBefore('#msm_child_add');
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
//function saveSetting()
//{
//    var selectedValue = $('input[name=msm_type]:radio:checked').val(); 
//    if(selectedValue == 'Others')
//    {
//        var specifiedValue = $('#msm_type_specifiedType').val();
//            
//        if((specifiedValue == null)||(specifiedValue == ''))
//        {
//            $("<div class='dialogs' id='msm_emptyComposition'> Please specify the type of your composition. </div>").appendTo('#msm_type_specifiedType');
//            
//            $( "#msm_emptyComposition" ).dialog({
//                modal: true,
//                buttons: {
//                    Ok: function() {
//                        $('#msm_type_specifiedType').css('border-color', '#FFA500');
//                        $( this ).dialog( "close" );
//                    }
//                }
//            });
//        }
//    }
//    
//    var topElement = $('#msm_structure_input_top').val();
//    
//    if(topElement == null)
//    {
//        topElement = selectedValue;
//    }
//    
//    var childvalues = [];
//    $('.msm_structure_input').each(function(i) {
//        if($(this).val() != null)
//        {
//            childvalues[i] = $(this).val();
//        }
//        
//    });
//}

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


