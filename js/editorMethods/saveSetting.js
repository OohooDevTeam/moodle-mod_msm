/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    $("#msm_setting_form").submit(function(event) { 
        // prevents page from navigating to the form posted php page
        event.preventDefault();
        
        var formData = $("#msm_setting_form").serializeArray();
//        var targetURL = $("#msm_setting_form").attr("action");  
        
        var newInputField = $("<input id='msm_unit_name_input' name='msm_unit_name_input' style='visibility:hidden;'/>");
        
        var inputValue = '';
        
        $(formData).each(function(index, element){
          
            if(index != formData.length-1)
            {
                inputValue += element.name + "|" + element.value + ",";
            }
            else
            {
                inputValue += element.name + "|" + element.value;
            }
        });

        newInputField.val(inputValue);
        
        $("#msm_unit_form").append(newInputField);
        
        $("#msm_setting_dialog").dialog("close");
        
    //        var newdata = [];
    //        $.ajax({
    //            type: "POST",
    //            url: targetURL,
    //            data: formData,
    //            success: function(d) { 
    //                
    //                
    //            },
    //            error: function() {
    //                alert("error in ajax at saveSetting.js");
    //            }
    //        });
        
    });
});
