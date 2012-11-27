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
                // this section of the code is for detecting empty contents and it gives the user 
                // a warning dialog box and highlights the contents that are empty
                //                console.log(data);
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
                else
                {
                    // later need to add code to switch save button to edit button, add different code for reset so that when it empties the units,
                    // it also clears out the db data, switch off tinymce, disable any modification function(resize..etc)
                    $("#msm_editor_save").remove();
                    $("<button class=\"msm_editor_buttons\" id=\"msm_editor_edit\" type=\"button\" onclick=\"editUnit()\"> Edit </button>").appendTo("#msm_editor_middle");
                    $("#msm_editor_reset").attr("disabled", "disabled");
                    
                    // removes the editor from textarea, extract the content of textarea, append to a new div and replace the textarea with the new div
                    // This is a work-around to display the content when user decides to save the content.  Textarea just gives raw html and cannot be made
                    // to display the html format properly.  Therefore div was created to replace it.
                    $('#msm_child_appending_area').find('.msm_unit_child_content').each(function() {
                        
                        tinyMCE.execCommand("mceRemoveControl", true, $(this).attr("id")); 
                        
                        var editorContent = document.createElement("div");
                        editorContent.id = $(this).attr("id");
                        editorContent.className = "msm_editor_content";
                        var content = $(this).val();
                        
                        $(editorContent).html(content);
                        $(this).replaceWith(editorContent);
                    });
                }
            },
            error: function() {
                alert("fail");
            }
        });
            
    });
});
