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
            $('#msm_unit_name_input').val(inputValue);
            $("#msm_setting_dialog").dialog("close");

//            $.ajax({
//                type: "POST",
//                url: targetURL,
//                data: formData,
//                dataType: "json",
//                success: function (data) {
////                    var result = JSON.parse(data);
//                    
//                    console.log(data);
//                },
//                error: function () {
//                    alert("error in ajax at saveSetting.js");
//                }
//            });
        }

    });
});