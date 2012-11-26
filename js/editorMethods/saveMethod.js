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
        
        $("textarea").each(function(){
            this.value = tinymce.get(this.id).getContent();
        });
        
        var urlParam = window.location.search;
       
        var urlParamInfo = urlParam.split("=");
       
        $("#msm_child_order").val(idString+urlParamInfo[1]);
        
        var formData = $("#msm_unit_form").serializeArray();
        var targetURL = $("#msm_unit_form").attr("action");
        var ids = [];
        $.ajax({
            type: "POST",
            url: targetURL,
            data: formData,
            success: function(data) {   
                
                console.log(data);
                // this section of the code is for detecting empty contents and it gives the user 
                // a warning dialog box and highlights the contents that are empty
                ids = JSON.parse(data);
                if(ids instanceof Array)
                {
                    for(var i=0; i < ids.length; i++)
                    {
                        var numOfContent = ids[i].match(/content/);
                        
                        if(numOfContent)
                        {
                            $('#'+ids[i]).parent().css("border", "solid 4px #FFA500");
                        }
                        else
                        {
                            $('#'+ids[i]).css("border-color", "#FFA500");
                        }
                        
                    }
                    $("<div class=\"dialogs\" id=\"msm_emptyContent\"> Please fill out the highlighted areas to complete the form. </div>").appendTo("#msm_unit_title");

                    $("#msm_emptyContent").dialog({
                        modal: true,
                        buttons: {
                            Ok: function() {
                                $(this).dialog("close");
                            }
                        }
                    });  
                }
            },
            error: function() {
                alert("fail");
            }
        });
            
    });
});
