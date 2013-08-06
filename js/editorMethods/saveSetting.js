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

/* 
 * This file deals with any changes that is saved in the settings menu.  Any changes made to
 * each unit names for each depth of nesting are saved into database by calling the ajaxSettingUpdate.php
 * script by an AJAX call.  It also updates the hidden input field with all the names of the unit.
 */

$(document).ready(function () {
    $("#msm_setting_form").submit(function (event) {
        // prevents page from navigating to the form posted php page
        event.preventDefault();

        var formData = $("#msm_setting_form").serializeArray();
        var targetURL = $("#msm_setting_form").attr("action");

        var inputValue = '';
        var isEmpty = false;

        $(formData).each(function (index, element) {
            if (element.value == "Others") {
                if ($("#msm_type_specifiedType").val() == '') {
                    $("<div class='dialogs' id='msm_emptyComposition'> Please specify the type of your composition. </div>").appendTo('#msm_type_specifiedType');

                    $("#msm_emptyComposition").dialog({
                        modal: true,
                        buttons: {
                            Ok: function () {
                                $('#msm_type_specifiedType').addClass("empty_content_error");
                                $(this).dialog("close");
                            }
                        }
                    });
                    isEmpty = true;
                }
            }

            if (!isEmpty) {
                if (element.value != '') {
                    inputValue += element.value + ",";
                }
            }

        });


        if (!isEmpty) {            
            var errorids = [];
            
            $.ajax({
                type: "POST",
                url: targetURL,
                data: formData,
                success: function (data) {
                    errorids = JSON.parse(data);
                    
                    // indicates if the border change occurred or not, 
                    // if the border change has occured then fires a message dialog warning user to fill the content in
                    var flag = false;
                    
                    if(errorids instanceof Array)
                    {
                        $("#msm_setting_form").find(".empty_content_error").each(function() {
                            $(this).removeClass("empty_content_error");
                        });
                        
                        for(var i=0; i < errorids.length; i++)
                        {
                            
                            if(errorids[i] == 'msm_strcutre_input_top')
                            {
                                flag = true;
                                $('#'+errorids[i]).addClass("empty_content_error"); 
                            }
                            else
                            {
                                var idNumber  = errorids[i].split("-");
                                        
                                if(idNumber[1] <= 3)
                                {
                                    flag = true
                                    $('#'+errorids[i]).addClass("empty_content_error");
                                }
                            }
                        }
                        if(flag)
                        {
                            $("<div class=\"dialogs\" id=\"msm_emptySettingContent\"> Please fill out the highlighted areas to complete the form. </div>").appendTo("#msm_setting_dialog");

                            $("#msm_emptySettingContent").dialog({
                                modal: true,
                                buttons: {
                                    Ok: function() {
                                        $(this).dialog("close");
                                    }
                                }
                            });  
                        }
//                        else
//                        {
//                            $('#msm_unit_name_input').val(inputValue);
//                            var eachName = inputValue.split(",");
//                            
//                            console.log($("#msm_unit_title_label"));
//                            console.log($("#msm_top_unit_label"));
//                            $("#msm_unit_title_label").text(eachName[0]+" title: ");
//                            $("#msm_top_unit_label").text(eachName[0]+" Design Area");
//                            $("#msm_setting_dialog").dialog("close");
//                        }
                        
                    }
                    else
                    {
                        $('#msm_unit_name_input').val(errorids);
                        
                        // eachName has order of: reference name, top unit, 2nd unit...etc
                        var eachName = errorids.split(",");     
                        // to dynamically change the name when user change the names
                        $("#msm_unit_title_label").text(eachName[1]+" title: ");
                        $("#msm_top_unit_label").text(eachName[1]+" Design Area");                            
                        $("#msm_setting_dialog").dialog("close");
                    }
                },
                error: function () {
                    alert("error in ajax at saveSetting.js");
                }
            });
        }

    });
});