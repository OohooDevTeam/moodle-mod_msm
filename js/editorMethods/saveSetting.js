/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$("#msm_setting_dialog").ready(function() {
    $("#msm_setting_form").submit(function(event) { 
        // prevents page from navigating to the form posted php page
//        event.preventDefault();
//        
        var formData = $("#msm_setting_form").serializeArray();
        var targetURL = $("#msm_setting_form").attr("action");   
//        var newdata = [];
        $.ajax({
            type: "POST",
            url: targetURL,
            data: formData,
            success: function(data) { 
//                newdata = JSON.parse(data);
                console.log(data);
                $("#msm_setting_dialog").dialog("close");
            },
            error: function() {
                alert("error in ajax at saveSetting.js");
            }
        });
        
    });
});
