/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){
    $("#msm_unit_form").submit(function(event) { 
        //         prevents navigation to msmUnitForm.php
        event.preventDefault();      
        
        submitForm();            
    });
});

function submitForm()
{
    var children =  document.getElementById("msm_child_appending_area").childNodes;

    var idString = "";
    for(var i=0; i<children.length; i++)
    {
        if(children[i].tagName == "DIV")
        {
            idString += children[i].id + ",";
        }           
    }
        
    var subordinateArray = [];
    $("textarea").each(function(){ 
        // process information from textarea that are not related to info elements
        if(!this.id.match(/info/))
        {  
            //            subordinateArray.push(prepareSubordinate(this.id));
                        
            this.value = tinymce.get(this.id).getContent({
                format: "html"
            });
        }
        // process associate information
        else if(this.id.match(/_info_/))
        {
            //            subordinateArray.push(prepareSubordinate(this.id));
                
            this.value = tinymce.get(this.id).getContent({
                format: "html"
            });
        }
    });
                
    var urlParam = window.location.search;
       
    var urlParamInfo = urlParam.split("=");
       
    $("#msm_child_order").val(idString+urlParamInfo[1]);
        
    var subordinateData = '';
    for(var i=0; i < subordinateArray.length; i++)
    {
        for(var key in subordinateArray[i])
        {
            subordinateData += key+"|"+subordinateArray[i][key]+",";
        }
    }        
        
    $("#msm_unit_subordinate_container").val(subordinateData);
        
    var formData = $("#msm_unit_form").serializeArray();
    var targetURL = $("#msm_unit_form").attr("action");
    var ids = []; 
    var mode = $("#msm_mode_info").val();
    
    $.ajax({
        type: "POST",
        url: targetURL,
        data: formData,
        success: function(data) { 
            // this section of the code is for detecting empty contents and it gives the user 
            // a warning dialog box and highlights the contents that are empty
            ids = JSON.parse(data);
                
            if(ids instanceof Array)
            {
                
                $("#msm_unit_form").find(".empty_content_error").each(function() {
                    $(this).removeClass("empty_content_error");
                });
                
                for(var i=0; i < ids.length; i++)
                {
                    var numOfContent = ids[i].match(/content/);
                        
                    if(numOfContent)
                    {
                        $("#"+ids[i]).parent().addClass("empty_content_error");
                    }
                    else
                    {
                        alert("not matched to content");
                        $('#'+ids[i]).addClass("empty_content_error");
                    }
                        
                }
                $("<div class=\"dialogs\" id=\"msm_emptyContent\"> Please fill out the highlighted areas to complete the form. </div>").appendTo("#msm_unit_title");

                $("#msm_emptyContent").dialog({
                    modal: true,
                    buttons: {
                        Ok: function() {
                            // scroll to top of the editting area and close the warning message
                            var middlePanel = document.getElementById("msm_editor_middle");
                            middlePanel.scrollTop = 0;
                            $(this).dialog("close");
                        }
                    }
                });  
            }
            else
            {        
                if(typeof mode !== 'undefined')
                {
                    $(".leftbox").append(ids); 
                       
                    var wWidth = $(window).width();
                    var wHeight = $(window).height();
                
                    var dWidth = wWidth*0.8;
                    var dHeight = wHeight*0.8;
                    $( "#msm_preview_dialog" ).dialog({
                        resizable: false,
                        modal: true,
                        height: dHeight,
                        width: dWidth,
                        open: function() {
                            $('#MySplitter').split({
                                orientation: 'vertical',
                                position: '50%'
                            });                           
                        },
                        close: function() {
                            $("#msm_mode_info").empty().remove();
                        }
                    }); 
                    
                    $(".msm_info_dialogs").dialog({
                        resizable: false,
                        autoOpen: false,
                        height: 'auto',
                        modal: false
                    });
                    
                }
                else if(typeof mode === 'undefined')
                {
                    // replace save and reset button to edit and new buttons, respectively
                    
                    $(".msm_editor_buttons").remove();
                    
                    $("<button class=\"msm_editor_buttons\" id=\"msm_editor_edit\" type=\"button\" onclick=\"editUnit('ids')\"> Edit </button>").appendTo("#msm_editor_middle");
                    $("<button class=\"msm_editor_buttons\" id=\"msm_editor_remove\" type=\"button\" onclick=\"removeUnit(event)\"> Remove this Unit </button>").appendTo("#msm_editor_middle");
                    $("#msm_editor_new").removeAttr("disabled");
                    
                    // removes the editor from textarea, extract the content of textarea, append to a new div and replace the textarea with the new div
                    // This is a work-around to display the content when user decides to save the content.  Textarea just gives raw html and cannot be made
                    // to display the html format properly.  Therefore div was created to replace it.
                    removeTinymceEditor();
                                        
                    // disabling all input/selection areas in editor and also disabling all jquery actions such as 
                    // sortable, draggable and droppable
                    disableEditorFunction();      
                    
                    $(".msm_subordinate_hotwords").each(function(i, element) {
                        var idInfo = this.id.split("-");                        
                        var newid = '';
                        
                        for(var i=1; i < idInfo.length-1; i++)
                        {
                            newid += idInfo[i]+"-";
                        }
                            
                        newid += idInfo[idInfo.length-1];
                        
                        $(this).on('mouseover', function(){
                            previewInfo(this.id, "msm_subordinate_info_dialog-"+newid); 
                        });
                    });
                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);                        
                    insertUnitStructure(ids);
                }
                   
            }
        },
        error: function() {
            alert("error in ajax at saveMethod.js");
        }
    });
}

function prepareSubordinate(id)
{
    var subordinates = [];
    var inst = tinyMCE.getInstanceById(id);    
    var hotwords = inst.getBody().getElementsByTagName("a");  
    
    for(var i=0; i < hotwords.length; i++)
    {
        
        var param = '';
        var currentWord = hotwords[i];
           
        var idInfo = currentWord.id.split("-");     
        
        for(var j=1; j < idInfo.length-2; j++)
        {
            param += idInfo[j]+"-";
        }            
        param += idInfo[idInfo.length-2];
        
        var matchedElement = findSubordinateResult(currentWord, param);        
        
        $(matchedElement).children("div").each(function() {
            subordinates[this.id] = $(this).html();
        });
    }
    
    return subordinates;    
}

function removeTinymceEditor()
{   
    $('#msm_child_appending_area').find('.msm_unit_child_content').each(function() {                        
        tinyMCE.execCommand("mceRemoveControl", true, $(this).attr("id")); 
                        
        var editorContent = document.createElement("div");
        editorContent.id = this.id;
        editorContent.className = "msm_editor_content";
        var content = $(this).val();
                        
        $(editorContent).html(content);
        $(this).replaceWith(editorContent);
    });
                    
    $('#msm_intro_child_container').find('.msm_intro_child_contents').each(function() {
        tinyMCE.execCommand("mceRemoveControl", true, $(this).attr("id")); 
                        
        var editorContent = document.createElement("div");
        editorContent.id = this.id;
        editorContent.className = "msm_editor_content";
        var content = $(this).val();
                        
        $(editorContent).html(content);
        $(this).replaceWith(editorContent);
    });
    
    $('.msm_theorem_part_dropareas').each(function() {
        $(this).find('.msm_theorem_content').each(function() {
            tinyMCE.execCommand("mceRemoveControl", true, $(this).attr("id")); 
                        
            var editorContent = document.createElement("div");
            editorContent.id = this.id;
            editorContent.className = "msm_editor_content";
            var content = $(this).val();
                        
            $(editorContent).html(content);
            $(this).replaceWith(editorContent);
        });
    });
    
    $('.msm_theoremref_part_dropareas').each(function() {
        $(this).find('.msm_theorem_content').each(function() {
            tinyMCE.execCommand("mceRemoveControl", true, $(this).attr("id")); 
                        
            var editorContent = document.createElement("div");
            editorContent.id = this.id;
            editorContent.className = "msm_editor_content";
            var content = $(this).val();
                        
            $(editorContent).html(content);
            $(this).replaceWith(editorContent);
        });
    });
    
    $('.msm_associate_containers').each(function() {
        $(this).find('.msm_info_titles').each(function() {
            tinyMCE.execCommand("mceRemoveControl", true, $(this).attr("id")); 
                        
            var editorContent = document.createElement("div");
            editorContent.id = this.id;
            editorContent.className = "msm_editor_content";
            var content = $(this).val();
                        
            $(editorContent).html(content);
            $(this).replaceWith(editorContent);
        });
        $(this).find('.msm_info_contents').each(function() {
            tinyMCE.execCommand("mceRemoveControl", true, $(this).attr("id")); 
                        
            var editorContent = document.createElement("div");
            editorContent.id = this.id;
            editorContent.className = "msm_editor_content";
            var content = $(this).val();
                        
            $(editorContent).html(content);
            $(this).replaceWith(editorContent);
        });
    });
}

/**
 * disabling all input/selection areas in editor and also disabling all jquery actions such as 
 * sortable, draggable and droppable
 */
function disableEditorFunction()
{
    $('.msm_title_input').attr("disabled", "disabled");
    $('.msm_unit_short_titles').attr("disabled", "disabled");
    $('.msm_unit_description_inputs').attr("disabled", "disabled");
                    
    $(".copied_msm_structural_element select").attr("disabled", "disabled");
    $(".copied_msm_structural_element input").attr("disabled", "disabled");
                    
    $(".copied_msm_structural_element .msm_element_close").remove();
                    
    $("#msm_child_appending_area").sortable("destroy");
    $("#msm_intro_child_container").sortable("destroy");
    
    $(".msm_theorem_content_containers").each(function(){
        $(this).sortable("destroy");
    });
    
    $(".msm_theorem_part_dropareas").each(function() {
        $(this).sortable("destroy");
    });  
    
    $(".msm_associate_containers").each(function() {
        $(this).sortable("destroy");
    });
    
    $(".msm_structural_element").draggable("destroy");
    $("#msm_editor_middle_droparea").droppable("destroy");
    
    $(".msm_associate_info_headers").each(function() {
        $(this).children("span").css("display", "none");
        $(this).unbind("mouseover");
    });
    
    $(".msm_intro_child_dragareas").each(function() {
        $(this).children("span").css("display", "none");
        $(this).unbind("mouseover");
    });
    
    $(".msm_element_title_containers").each(function() {
        $(this).children("span").css("display", "none");
        $(this).unbind("mouseover");
    });
    
    $(".msm_theorem_statement_title_containers").each(function() {
        $(this).children("span").css("display", "none");
        $(this).unbind("mouseover");
    });
    
    $(".msm_theorem_part_title_containers").each(function() {
        $(this).children("span").css("display", "none");
        $(this).unbind("mouseover");
    });
    
    $(".msm_theoremref_title_containers").each(function() {
        $(this).children("span").css("display", "none");
        $(this).unbind("mouseover");
    });
    
    $(".msm_theoremref_statement_title_containers").each(function() {
        $(this).children("span").css("display", "none");
        $(this).unbind("mouseover");
    });
    
    $(".msm_theoremref_part_title_containers").each(function() {
        $(this).children("span").css("display", "none");
        $(this).unbind("mouseover");
    });
    
}


// to activate the dialog box for display purposes
function previewInfo(elementid, dialogid)
{
    $(".msm_subordinate_info_dialogs").dialog({
        autoOpen: false,
        height: "auto",
        modal: false,
        width: 605
    });    
    
    var x = 0; // stores the x-axis position of the mouse
    var y = 0; // stores the y-axis position of the mouse  

    $("#"+elementid).unbind("click");
    $("#"+elementid).click(function(e) {
        x = e.clientX+5;
        y = e.clientY+5;

        $("#"+dialogid).dialog('open').css("display", "block");
        $("#"+elementid).mousemove(function () {
            $("#"+dialogid).dialog("option", {
                position: [x, y]
            });
        });

        $("#"+elementid).mouseout(function(){
            $("#"+dialogid).dialog('open').css("display", "block");
        });

    });

    $("#"+elementid).ready(function(e){        
        $("#"+elementid).mousemove(function (e) {
            $("#"+dialogid).dialog("option", {
                position: [e.clientX+5, e.clientY+5]
            });
            $("#"+dialogid).dialog('open').css("display", "block");
        });

        $("#"+elementid).mouseout(function(){
            $("#"+dialogid).dialog("close").css("display", "none");
        });
    });
} 


