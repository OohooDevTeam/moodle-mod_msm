/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function insertUnitStructure(dbId)
{
    var treediv = document.getElementById("msm_unit_tree");
    
    var listChild = $("<li></li>");
    listChild.id = "msm_unit-"+dbId;
    
    var linkElement = $("<a href='#'></a>");
    
    var linkText = document.createTextNode(dbId);
    $(linkElement).append(linkText);    
    $(listChild).append(linkElement);
    
    if(!treediv.hasChildNodes())
    {
        var rootul = $("<ul></ul>");
            
        rootul.append(listChild);
        $("#msm_unit_tree").append(rootul);
    }
    else
    {
        $("#msm_unit_tree > ul").append(listChild);
    }
    
    $("#msm_unit_tree").jstree({
        "plugins": ["themes", "html_data", "ui", "dnd"],
        "dnd": {
            "drop_target": false,
            "drag_target": false
        }
    }); 
   
}

function newUnit()
{
    $("#msm_child_appending_area").empty();
    
    $("#msm_unit_title").removeAttr("disabled");
    $("#msm_unit_description_input").removeAttr("disabled");
    
    $("#msm_editor_edit").remove();
    $("<input class=\"msm_editor_buttons\" id=\"msm_editor_reset\" type=\"button\" onclick=\"resetUnit()\" value=\"Reset\"/> ").appendTo("#msm_editor_middle");
                    
    $("#msm_editor_new").remove();
    $("<input type=\"submit\" name=\"msm_editor_save\" class=\"msm_editor_buttons\" id=\"msm_editor_save\" disabled=\"disabled\" value=\"Save\"/>").appendTo("#msm_editor_middle");
    
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
    
    $("#msm_editor_save").click(function(event) { 
        //         prevents navigation to msmUnitForm.php
        event.preventDefault();
              
        submitForm();
            
    });
}