/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){
    $("#msm_unit_form").submit(function(event) { 
        //         prevents navigation to msmUnitForm.php
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
            this.value = tinymce.get(this.id).getContent({
                format: "html"
            });
        });
        //        
        var urlParam = window.location.search;
       
        var urlParamInfo = urlParam.split("=");
       
        $("#msm_child_order").val(idString+urlParamInfo[1]);
        
        var formData = $("#msm_unit_form").serializeArray();
        var targetURL = $("#msm_unit_form").attr("action");
        var ids = [];
        
        //        console.log("unit form data being passed to server side");
        //        console.log(formData);
        
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
                    for(var i=0; i < ids.length; i++)
                    {
                        var numOfContent = ids[i].match(/content/);
                        
                        if(numOfContent)
                        {
                            $('#'+ids[i]).parent().css("border", "solid 4px #FFA500");
                        }
                        else
                        {
                            alert("not matched to content");
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
                    // replace save and reset button to edit and new buttons, respectively
                    $("#msm_editor_save").remove();
                    $("<button class=\"msm_editor_buttons\" id=\"msm_editor_edit\" type=\"button\" onclick=\"editUnit()\"> Edit </button>").appendTo("#msm_editor_middle");
                    
                    $("#msm_editor_reset").remove();
                    $("<button class=\"msm_editor_buttons\" id=\"msm_editor_new\" type=\"button\" onclick=\"newUnit()\"> New </button>").appendTo("#msm_editor_middle");
                    
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
                }
            },
            error: function() {
                alert("error in ajax at saveMethod.js");
            }
        });
            
    });
});

function removeTinymceEditor()
{   
    $('#msm_child_appending_area').find('.msm_unit_child_content').each(function() {
                        
        tinyMCE.execCommand("mceRemoveControl", true, $(this).attr("id")); 
                        
        var editorContent = document.createElement("div");
        editorContent.id = $(this).attr("id");
        editorContent.className = "msm_editor_content";
        var content = $(this).val();
                        
        $(editorContent).html(content);
        $(this).replaceWith(editorContent);
    });
                    
    $('#msm_intro_child_container').find('.msm_intro_child_contents').each(function() {
                        
        tinyMCE.execCommand("mceRemoveControl", true, $(this).attr("id")); 
                        
        var editorContent = document.createElement("div");
        editorContent.id = $(this).attr("id");
        editorContent.className = "msm_editor_content";
        var content = $(this).val();
                        
        $(editorContent).html(content);
        $(this).replaceWith(editorContent);
    });
    
    $('.msm_theorem_part_dropareas').each(function() {
        $(this).find('.msm_theorem_content').each(function() {
                        
            tinyMCE.execCommand("mceRemoveControl", true, $(this).attr("id")); 
                        
            var editorContent = document.createElement("div");
            editorContent.id = $(this).attr("id");
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
            editorContent.id = $(this).attr("id");
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
            editorContent.id = $(this).attr("id");
            editorContent.className = "msm_editor_content";
            var content = $(this).val();
                        
            $(editorContent).html(content);
            $(this).replaceWith(editorContent);
        });
        $(this).find('.msm_info_contents').each(function() {
                        
            tinyMCE.execCommand("mceRemoveControl", true, $(this).attr("id")); 
                        
            var editorContent = document.createElement("div");
            editorContent.id = $(this).attr("id");
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
}

/**
 * This method undoes all the actions done by above method(disableEditorFunction) and enables all input and selection fields,
 * reinitalizes all jquery actions, and reattaches close buttons for deletion of each structural element.
 * 
 * (not tested yet, theoretical method)
 */
function enableEditorFunction()
{
    $('.msm_title_input').removeAttr("disabled");
    $('.msm_unit_description_inputs').removeAttr("disabled");
                    
    $(".copied_msm_structural_element select").removeAttr("disabled");
    $(".copied_msm_structural_element input").removeAttr("disabled");
    
    // reattach all close buttons for deletion of element
    $(".copied_msm_structural_element").each(function(i) {
        var closeButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');        
        $(this).append(closeButton);
    });
    
    // reinitalize all jquery actions
    $(".msm_structural_element").draggable({
        appendTo: "msm_editor_middle_droparea",
        containment: "msm_editor_middle_droparea",
        scroll: true,
        cursor: "move",
        helper: "clone"                   
    });              
        
    $("#msm_editor_middle_droparea").droppable({
        accept: "#msm_editor_left > div",
        hoverClass: "ui-state-hover",
        tolerance: "pointer",
        drop: function( event, ui ) { 
            processDroppedChild(event, ui.draggable.context.id);                        
        }
    }); 
    
    
    $("#msm_child_appending_area").sortable({
        appendTo: "#msm_child_appending_area",
        connectWith: "#msm_child_appending_area",
        cursor: "move",
        tolerance: "pointer",
        placeholder: "msm_sortable_placeholder",
        start: function(event, ui)
        {
            $(".msm_sortable_placeholder").width(ui.item.context.offsetWidth);
            // this code along with the one in stop is needed for enabling sortable on the div containing
            // the tinymce editor so the iframe part of the editor doesn't become disabled
            $(this).find('.msm_unit_child_content').each(function() {
                tinyMCE.execCommand("mceRemoveControl", false, $(this).attr("id")); 
            });
        },
        stop: function(event, ui)
        {
            $(this).find('.msm_unit_child_content').each(function() {
                tinyMCE.execCommand("mceAddControl", false, $(this).attr("id")); 
                $(this).sortable("refresh");
            });
        }
    });    
                
    $("#msm_child_appending_area").disableSelection();
                    
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
