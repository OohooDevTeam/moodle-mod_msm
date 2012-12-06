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
        if(($('#msm_element_names').children('.msm_structure_names').length == 0) &&($('#msm_element_names').children('.msm_setting_form').length == 0))
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
            var msmInstanceid = document.createElement('input');
            msmInstanceid.id = "msm_instance_id";
            msmInstanceid.name = "msm_instance_id";
            msmInstanceid.style.visibility = "hidden";
            msmInstanceid.style.display = "none";
            
            $(msmInstanceid).val(allUnitNames[allUnitNames.length-1]);
            
            $(msmInstanceid).insertAfter('#msm_child_add');
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
    $(".msm_setting_form").empty().remove();  
    
    var child1Value;
    var child2Value;
    var child3Value;
    var child4Value;
    
    
    switch(e.target.id)
    {
        case "msm_type_lecture":
            $("#msm_structure_input_top").val("Lecture");
            
            child1Value = "Part";
            child2Value = "Topic";
            child3Value = "Section";
            child4Value = "Subsection";           
            break;
        
        case "msm_type_book":
            $("#msm_structure_input_top").val("Book");
            
            child1Value = "Book Part";
            child2Value = "Chapter";
            child3Value = "Section";
            child4Value = "Subsection";
            break;
        
        case "msm_type_wbook":
            $("#msm_structure_input_top").val("Work Book");
            child1Value = "Book Part";
            child2Value = "Chapter";
            child3Value = "Section";
            child4Value = "Subsection";
            
            break;
        
        case "msm_type_others":
            // need to read the input field later
            $("#msm_structure_input_top").val("Others");
            break;
    
    }
    
    var container1 = document.createElement("div");
    container1.className = "msm_setting_form";    
    var container2 = document.createElement("div");
    container2.className = "msm_setting_form";    
    var container3 = document.createElement("div");
    container3.className = "msm_setting_form";    
    var container4 = document.createElement("div");
    container4.className = "msm_setting_form";
    
    var star = document.createElement("span"); //<span style="color: red;">*</span>
    star.style.color = "red";
    star.value = "*";
    
    var labelText1 = document.createTextNode("Child Unit : ");
    var labelText2 = document.createTextNode("Child Unit : ");
    var labelText3 = document.createTextNode("Child Unit : ");
    var labelText4 = document.createTextNode("Child Unit : ");
    
    var label1 = document.createElement("label");
    label1.htmlFor = "msm_structure_input_child-0"; 
    label1.appendChild(labelText1);    
    var inputfield1 = document.createElement("input");
    inputfield1.className = "msm_structure_input";
    inputfield1.id = "msm_structure_input_child-0";
    inputfield1.name = "msm_structure_input_child-0";
    inputfield1.value = child1Value;
    
    var label2 = document.createElement("label");
    label2.htmlFor = "msm_structure_input_child-1";   
    label2.appendChild(labelText2);    
    var inputfield2 = document.createElement("input");
    inputfield2.className = "msm_structure_input";
    inputfield2.id = "msm_structure_input_child-1";
    inputfield2.name = "msm_structure_input_child-1";
    inputfield2.value = child2Value;
    
    var label3 = document.createElement("label");
    label3.htmlFor = "msm_structure_input_child-2";    
    label3.appendChild(labelText3);    
    var inputfield3 = document.createElement("input");
    inputfield3.className = "msm_structure_input";
    inputfield3.id = "msm_structure_input_child-2";
    inputfield3.name = "msm_structure_input_child-2";
    inputfield3.value = child3Value;
    
    var label4 = document.createElement("label");
    label4.htmlFor = "msm_structure_input_child-3";  
    label4.appendChild(labelText4);    
    var inputfield4 = document.createElement("input");
    inputfield4.className = "msm_structure_input";
    inputfield4.id = "msm_structure_input_child-3";
    inputfield4.name = "msm_structure_input_child-3";
    inputfield4.value = child4Value;
    
    // first child element
    container1.appendChild(label1);
    container1.appendChild(inputfield1);
    container1.appendChild(star);
    
    // second child element
    container2.appendChild(label2);
    container2.appendChild(inputfield2);
    container2.appendChild(star);
    
    // third child element
    container3.appendChild(label3);
    container3.appendChild(inputfield3);
    container3.appendChild(star);
    
    // fourth child element
    container4.appendChild(label4);
    container4.appendChild(inputfield4);
    container4.appendChild(star);
    
    $(container1).insertBefore('#msm_child_add');
    $(container2).insertBefore('#msm_child_add');
    $(container3).insertBefore('#msm_child_add');
    $(container4).insertBefore('#msm_child_add');


}


