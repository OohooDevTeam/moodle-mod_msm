/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){
    $("#msm_unit_form").submit(function(event) { 
        //         prevents navigation to msmUnitForm.php
        event.preventDefault();   
        $("#msm_unit_short_title").removeAttr("readonly");
        $("#msm_unit_description_input").removeAttr("readonly");
        $(".copied_msm_structural_element select").removeAttr("disabled");
        $(".copied_msm_structural_element input").removeAttr("disabled");
        $("#msm_child_appending_area").find(".msm_editor_content").each(function() {
            $(this).removeClass("msm_editor_content");
            var newdata = document.createElement("textarea");
            newdata.id = this.id;
            newdata.name = this.id;
            newdata.className = this.className;
                    
            $(this).find("span.matheditor").each(function() {
                var newspan = document.createElement("span");
                newspan.className = "matheditor";
    
                var scriptChild = $(this).find("script");
        
                var scriptWithMath = scriptChild[scriptChild.length-1];
                var mathContent = "\\("+$(scriptWithMath).text()+"\\)"; 
                $(newspan).append(mathContent);
                
                $(this).replaceWith(newspan);
            });
            
            newdata.value = $(this).html();            
            $(this).replaceWith(newdata);   
        });
        
        submitForm();            
    });
});

function submitForm()
{        
    var disabled = $("#msm_comp_done").attr("disabled");
        
    if((typeof disabled !== "undefined") && (disabled !== false))
    {
        $("#msm_comp_done").removeAttr("disabled");
    }
        
    //    $("#msm_unit_title").removeAttr("readonly");
    $("#msm_unit_short_title").removeAttr("readonly");
    $("#msm_unit_description_input").removeAttr("readonly");
    
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
            subordinateArray.push(prepareSubordinate(this.id));
            if(typeof tinymce.get(this.id) !== "undefined")
            {                
                this.value = tinymce.get(this.id).getContent({
                    format: "html"
                });
            }
           
        }
        // process associate information
        else if(this.id.match(/_info_/))
        {
            //            subordinateArray.push(prepareSubordinate(this));
            subordinateArray.push(prepareSubordinate(this.id));
            if(typeof tinymce.get(this.id) !== "undefined")
            {                
                this.value = tinymce.get(this.id).getContent({
                    format: "html"
                });
            }
        }
    });  
    
    $("#msm_unit_title").val(tinymce.getInstanceById("msm_unit_title").getContent({
        format:"html"
    }));
    
    var urlParam = window.location.search;
       
    var urlParamInfo = urlParam.split("=");
       
    $("#msm_child_order").val(idString+urlParamInfo[1]);
        
    var subordinateData = '';    
    for(var i=0; i < subordinateArray.length; i++)
    {
        for(var key in subordinateArray[i])
        {
            subordinateData += key+"||"+subordinateArray[i][key]+"//|";
        }
    }
    
    $("#msm_unit_subordinate_container").val(subordinateData);
    
    $("#msm_file_options").val(JSON.stringify(tinymce_filepicker_options));
    
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
                    $("#msm_preview_dialog .leftbox").append(ids); 
                    
                    var wWidth = $(window).width();
                    var wHeight = $(window).height();
                
                    var dWidth = wWidth*0.8;
                    var dHeight = wHeight*0.8;
                    $( "#msm_preview_dialog").dialog({
                        resizable: false,
                        modal: true,
                        height: dHeight,
                        width: dWidth,
                        open: function() {
                            $('#MySplitter').split({
                                orientation: 'vertical',
                                position: '50%'
                            });   
                            MathJax.Hub.Queue(["Typeset",MathJax.Hub]);   
                        },
                        close: function() {
                            $("#msm_preview_dialog").find(".msm_subordinate_hotwords").each(function() {
                                $(this).unbind();
                            });
                            $(".msm_info_dialogs").dialog("destroy");
                            $("#msm_mode_info").empty().remove();                            
                        } 
                    }); 

                    $(".msm_info_dialogs").dialog({
                        autoOpen: false,
                        height: "auto",
                        modal: false,
                        width: 605
                    });  
                    
                    $("#msm_preview_dialog .leftbox").find(".msm_subordinate_hotwords").each(function(i, element) {
                        var idInfo = this.id.split("-");
                        var newid = '';
                        
                        for(var i=1; i < idInfo.length-1; i++)
                        {
                            newid += idInfo[i]+"-";
                        }
                            
                        newid += idInfo[idInfo.length-1];
                        
                        previewInfo(this.id, "dialog-"+newid);
                    });
                    
                    $(".msm_info_dialogs").find(".msm_subordinate_hotwords").each(function() {
                        var idInfo = this.id.split("-");
                        var newid = '';
                        
                        for(var i=1; i < idInfo.length-1; i++)
                        {
                            newid += idInfo[i]+"-";
                        }
                            
                        newid += idInfo[idInfo.length-1];
                                               
                        previewInfo(this.id, "dialog-"+newid);
                    });
                    
                    //                    $("#msm_unit_title").attr("readonly", "true");
                    //                    $(this).removeClass("msm_add_border");
                    $("#msm_unit_short_title").attr("readonly", "true");
                    $(this).removeClass("msm_add_border");
                    $("#msm_unit_description_input").attr("readonly", "true");         
                    $(this).removeClass("msm_add_border");
                }
                else if(typeof mode === 'undefined')
                {
                    // replace save and reset button to edit and new buttons, respectively
                    
                    $(".msm_editor_buttons").remove();
                    
                    $("<button class=\"msm_editor_buttons\" id=\"msm_editor_new\" type=\"button\" onclick=\"newUnit()\"> New Unit </button>").appendTo("#msm_editor_middle");

                    $("<button class=\"msm_editor_buttons\" id=\"msm_editor_remove\" type=\"button\" onclick=\"removeUnit(event)\"> Remove this Unit </button>").appendTo("#msm_editor_middle");
                    
                    $("#msm_child_appending_area").find(".msm_editor_content").each(function() {
                        $(this).empty().remove();
                    });                 
                    
                    // removes the editor from textarea, extract the content of textarea, append to a new div and replace the textarea with the new div
                    // This is a work-around to display the content when user decides to save the content.  Textarea just gives raw html and cannot be made
                    // to display the html format properly.  Therefore div was created to replace it.
                    removeTinymceEditor();
                                        
                    // disabling all input/selection areas in editor and also disabling all jquery actions such as 
                    // sortable, draggable and droppable
                    disableEditorFunction();       
                    
                    $(".msm_structural_element").draggable({
                        appendTo: "msm_editor_middle_droparea",
                        containment: "msm_editor_middle_droparea",
                        scroll: true,
                        cursor: "move",
                        helper: "clone"                   
                    });        
                    
                    $(".msm_child_element").draggable({
                        appendTo: ".msm_dnd_containers",
                        containment: ".msm_dnd_containers",
                        scroll: true,
                        cursor: "move",
                        helper: "clone"                   
                    }); 
        
                    $("#msm_editor_middle_droparea").droppable({
                        accept: "#msm_component_tabs-1 > div",
                        hoverClass: "ui-state-hover",
                        tolerance: "pointer",
                        drop: function( event, ui ) { 
                            processDroppedChild(event, ui.draggable.context.id);
                            allowDragnDrop();        
                        }
                    }); 
                    $(".msm_dnd_containers").droppable({
                        accept: "#msm_component_tabs-2 > div",
                        hoverClass: "ui-state-hover",
                        tolerance: "pointer",
                        drop: function( event, ui ) {                             
                            processAdditionalChild(event, ui.draggable.context.id);      
                            allowDragnDrop();  
                        }
                    });
                    
                    $("#msm_unit_title").dblclick(function(){
                        processTitleContent(this.id);
                        allowDragnDrop();
                    });
                    $("#msm_unit_short_title").dblclick(function(){
                        $(this).removeAttr("readonly");
                        $(this).addClass("msm_add_border");
                        allowDragnDrop();
                    });
                    $("#msm_unit_description_input").dblclick(function(){
                        $(this).removeAttr("readonly");
                        $(this).addClass("msm_add_border");
                        allowDragnDrop();
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
    $("#msm_child_appending_area").find(".msm_subordinate_results").each(function() { 
        $(this).children("div").each(function() {
            subordinates[this.id] = $(this).html();
        })
    });
    return subordinates;    
}

function removeTinymceEditor()
{ 
    titleInput2Div("msm_unit_title");
    
    $('#msm_child_appending_area').find('.msm_unit_child_content').each(function() {     
        textArea2Div($(this).attr("id"));
    });
                    
    $('#msm_intro_child_container').find('.msm_intro_child_contents').each(function() {
        textArea2Div($(this).attr("id"));
    });
    
    $('.msm_theorem_part_dropareas').each(function() {
        if(this.id.match(/theoremref/))
        {
            $(this).find(".msm_theorem_child").each(function() {
                $(this).find(".msm_theorem_content").each(function() {
                    textArea2Div($(this).attr("id"));

                });
            }); 
        }
        else
        {
            textArea2Div($(this).attr("id"));
        }
    });
    
    //    $('.msm_theoremref_part_dropareas').each(function() {
    //        $(this).find('.msm_theorem_content').each(function() {
    //            textArea2Div($(this).attr("id"));
    //        });
    //    });
    
    //    $(".msm_theoremref_statement_containers").each(function() {
    //    $(".msm_theorem_part_dropareas").each(function() {
    //        console.log($(this));
    //        $(this).find(".msm_theorem_child").each(function() {
    //            console.log($(this));
    //            $(this).find(".msm_theorem_content").each(function() {
    //                console.log($(this));
    //                textArea2Div($(this).attr("id"));
    //
    //            })
    //        }); 
    //    });
    //    });
    
    $('.msm_associate_containers').each(function() {
        $(this).find('.msm_info_titles').each(function() {
            textArea2Div($(this).attr("id"));
        });
        $(this).find('.msm_info_contents').each(function() {
            textArea2Div($(this).attr("id"));
        });
    });
}

function textArea2Div(id)
{
    console.log("id?: "+id);
    var edInstance = tinyMCE.getInstanceById(id);
    if(edInstance)
    {
        if (edInstance.isHidden())
        {
            tinyMCE.remove(edInstance);
        }
        tinyMCE.execCommand('mceRemoveControl', true, id);
    }
    tinyMCE.execCommand("mceRemoveControl", true, id); 
                        
    var editorContent = document.createElement("div");
    editorContent.id = id;
    var content = null;
    if($(this).hasClass("msm_editor_content"))
    {
        editorContent.className = this.className;
        content = $("#"+id).html();
    }
    else
    {
        editorContent.className = document.getElementById(id).className+" msm_editor_content";
        content = $("#"+id).val();
    }        
        
    $(editorContent).html(content);
    $("#"+id).replaceWith(editorContent);
}

function titleInput2Div(id)
{
    var edInstance = tinyMCE.getInstanceById(id);
    if(edInstance)
    {
        if (edInstance.isHidden())
        {
            tinyMCE.remove(edInstance);
        }
        tinyMCE.execCommand('mceRemoveControl', true, id);
    }
    tinyMCE.execCommand("mceRemoveControl", true, id); 
                        
    var editorTitle = document.createElement("div");
    editorTitle.id = id;
    var title = null;
    if($(this).hasClass("msm_editor_titles"))
    {
        editorTitle.className = this.className;
        title = $("#"+id).html();
    }
    else
    {
        editorTitle.className = document.getElementById(id).className+" msm_editor_titles";
        title = $("#"+id).val();
    }        
        
    $(editorTitle).html(title);
    $("#"+id).replaceWith(editorTitle);
}

/**
* disabling all input/selection areas in editor and also disabling all jquery actions such as 
* sortable, draggable and droppable
*/
function disableEditorFunction()
{
    //    $('.msm_title_input').attr("readonly", "true");
    //    $('.msm_title_input').removeClass("msm_add_border");
    $('.msm_unit_short_titles').attr("readonly", "true");
    $('.msm_unit_short_titles').removeClass("msm_add_border");
    $('.msm_unit_description_inputs').attr("readonly", "true");
    $('.msm_unit_description_inputs').removeClass("msm_add_border");
                    
    $(".copied_msm_structural_element select").attr("disabled", "disabled");
    $(".copied_msm_structural_element input").attr("disabled", "disabled");
                    
    $(".copied_msm_structural_element .msm_element_close").remove();
    
    //    if($("#msm_child_appending_area").hasClass("ui-sortable"))
    //    {
    //        $("#msm_child_appending_area").sortable("destroy");
    //    }
    //    
    //    if($("#msm_intro_child_container").hasClass("ui-sortable"))
    //    {
    //        $("#msm_intro_child_container").sortable("destroy");
    //    }
                    
        
    //    $(".msm_theorem_content_containers").each(function(){
    //        if($(this).hasClass("ui-sortable"))
    //        {
    //            $(this).sortable("destroy");
    //        }
    //    });
    //        
    //    $(".msm_theorem_part_dropareas").each(function() {
    //        if($(this).hasClass("ui-sortable"))
    //        {
    //            $(this).sortable("destroy");
    //        }
    //    });  
    //        
    //    $(".msm_associate_containers").each(function() {
    //        if($(this).hasClass("ui-sortable"))
    //        {
    //            $(this).sortable("destroy");
    //        }
    //    });  
    
    
    //    if($(".msm_structural_element").hasClass("ui-draggable"))
    //    {
    //        $(".msm_structural_element").draggable("destroy");
    //    }
    //        
    //    if($("#msm_editor_middle_droparea").hasClass("ui-droppable"))
    //    {
    //        $("#msm_editor_middle_droparea").droppable("destroy");
    //    }
        
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
    
    $(".copied_msm_structural_element").unbind();
    $("#msm_child_appending_area > .copied_msm_structural_element").mouseenter(
        function() {                
            var idNumber = $(this).attr("id").split("-");
            var overlayheight = $(this).height();

            $("#msm_element_overlay-"+idNumber[1]).css("top", this.offsetTop);

            $("#msm_element_overlay-"+idNumber[1]).css("display", "block");

            $("#msm_element_overlay-"+idNumber[1]).stop(true, true).animate({
                height: overlayheight+50
            }, 700);                      
        });
    $("#msm_child_appending_area > .copied_msm_structural_element").mouseleave(function() {   
        var idNumber = $(this).attr("id").split("-");

        $("#msm_element_overlay-"+idNumber[1]).stop(true, true).animate({
            height: "30px"
        }, 300);
        $("#msm_element_overlay-"+idNumber[1]).css("display", "none");
    }
    );
    
}


// to activate the dialog box for display purposes
function previewInfo(elementid, dialogid)
{       
    var x = 0; // stores the x-axis position of the mouse
    var y = 0; // stores the y-axis position of the mouse
 
    $("#msm_preview_dialog #"+elementid).unbind();
    $("#msm_preview_dialog #"+elementid).click(function(e) {
        x = e.clientX+5;
        y = e.clientY+5;
    
        $("#"+dialogid).dialog('open').css("display", "block");
        $("#msm_preview_dialog #"+elementid).mousemove(function () {
            $("#"+dialogid).dialog("option", {
                position: [x, y]
            });
        });
    
        $("#msm_preview_dialog #"+elementid).mouseout(function(){
            $("#"+dialogid).dialog('open').css("display", "block");
        });
    
    });
   
    $("#msm_preview_dialog #"+elementid).mouseover(function (e) {
        $("#"+dialogid).dialog("option", {
            position: [e.clientX+5, e.clientY+5]
        });
        $("#"+dialogid).dialog('open').css("display", "block");
    });
    
    $("#msm_preview_dialog #"+elementid).mouseout(function(){
        $("#"+dialogid).dialog("close").css("display", "none");
    });
    //----------------------------------------------------------------------
    
    $("#msm_search_result_table").find("#"+elementid).unbind();    
                
    $("#msm_search_result_table").find("#"+elementid).ready(function(){ 
        $("#msm_search_result_table").find("#"+elementid).mousemove(function (e) {   
            e.preventDefault();
            $("#"+dialogid).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });
            $("#"+dialogid).dialog('open');
        });
         
        $("#msm_search_result_table").find("#"+elementid).mouseout(function(e){
            e.preventDefault();
            $("#"+dialogid).dialog('close');
        });
        $("#msm_search_result_table").find("#"+elementid).click(function(e){
            e.preventDefault();
        });

    });
   
    //----------------------------------------------------------------------
    // for displaying dialog boxes from hotwords in associate info boxes
    $(".msm_info_dialogs").find("#"+elementid).unbind("click");
    $(".msm_info_dialogs").find("#"+elementid).click(function(e) {
        x = e.clientX+5;
        y = e.clientY+5;
    
        $("#"+dialogid).dialog('open').css("display", "block");
        $(".msm_info_dialogs").find("#"+elementid).mousemove(function () {
            $("#"+dialogid).dialog("option", {
                position: [x, y]
            });
        });
    
        $(".msm_info_dialogs").find("#"+elementid).mouseout(function(){
            $("#"+dialogid).dialog('open').css("display", "block");
        });
    
    });
   
    $(".msm_info_dialogs").find("#"+elementid).mouseover(function (e) {
        $("#"+dialogid).dialog("option", {
            position: [e.clientX+5, e.clientY+5]
        });
        $("#"+dialogid).dialog('open').css("display", "block");
    });
    
    $(".msm_info_dialogs").find("#"+elementid).mouseout(function(){
        $("#"+dialogid).dialog("close").css("display", "none");
    });
    
} 


