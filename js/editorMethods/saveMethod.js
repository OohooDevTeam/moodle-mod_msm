/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){
    $(".msm_form_error").hide();
    $("#msm_unit_form").submit(function(event) { 
        
        event.preventDefault();
        
        var children =  document.getElementById("msm_child_appending_area").childNodes;

        var idString = "";
        for(var i=0; i<children.length; i++)
        {
            if(children[i].tagName == "DIV")
            {
                idString += children[i].id + ",";
            }           
        }
        //        
        //        if(children[children.length-1].tagName == "DIV")
        //        {
        //            idString += children[children.length-1].id;
        //        }
        
        var urlParam = window.location.search;
       
        var urlParamInfo = urlParam.split("=");
       
        $("#msm_child_order").val(idString+urlParamInfo[1]);
        
        var formData = $("#msm_unit_form").serializeArray();
        var targetURL = $("#msm_unit_form").attr("action");
        
        console.log(formData);
        
        $.ajax({
           type: "POST",
           url: targetURL,
           data: formData,
           success: function(data) {
                alert(data);
           },
           error: function() {
               alert("fail");
           }
        });
            
    });
});
