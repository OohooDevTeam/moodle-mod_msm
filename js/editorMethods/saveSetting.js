/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
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
                                $('#msm_type_specifiedType').css('border-color', '#FFA500');
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
                    if(errorids instanceof Array)
                    {
                        for(var i=0; i < errorids.length; i++)
                        {
                            var numOfContent = errorids[i].match(/content/);
                        
                            if(numOfContent)
                            {
                                $('#'+errorids[i]).parent().css("border", "solid 4px #FFA500");
                            }
                            else
                            {
                                $('#'+errorids[i]).css("border-color", "#FFA500");
                            }
                        
                        }
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
                    else
                    {
                        $('#msm_unit_name_input').val(inputValue);
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