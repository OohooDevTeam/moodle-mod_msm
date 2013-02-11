/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function insertUnitStructure(dbId)
{
    var treediv = document.getElementById("msm_unit_tree");
    
    var listChild = $("<li></li>");
    $(listChild).attr("id", "msm_unit-"+dbId);
    
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
    
    $("#msm_unit_tree")
    .jstree({
        "plugins": ["themes", "html_data", "ui", "dnd"],
        "dnd": {
            "drop_target": false,
            "drag_target": false
        }
    })
    .bind("select_node.jstree", function(event, data) {
        var dbInfo = [];
        
        $.ajax({
            type: "POST",
            url: "editorCreation/msmLoadUnit.php",
            data: {
                'id': data.rslt.obj.attr("id")
            },
            success: function(data)
            {
                // dbInfo is all data making up the one unit in HTML format
                dbInfo = JSON.parse(data);  
                
                // need to process the info to append appropriate domElements to correct parent elements
                processUnitData(dbInfo); // need to also change the unit title --> need to get value of label in title and get unitName and replace <h2> under middle editor panel
            },
            error: function(data)
            {
                alert("ajax error in loading unit");
            }
        })
        
    })
    .delegate("a", "click", function(event, data){
        event.preventDefault();
    });  // delegate for preventing the anchored element default action--> ie. navigating to another page/same page
   
}

function newUnit()
{
    $("#msm_child_appending_area").empty();
    
    $("#msm_unit_title").val('');
    $("#msm_unit_description_input").val('');
    
    $("#msm_child_order").val('');
    
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
    
    $("#msm_editor_save").click(function(event) { 
        //         prevents navigation to msmUnitForm.php
        event.preventDefault();
              
        submitForm();
            
    });
}

function processUnitData(htmlData)
{
    $('#msm_unit_form').empty();
    
    $('#msm_unit_form').append(htmlData);
    
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
//    var titleLabel = $('#msm_unit_title_label-'+idpair).val();
//    
//    var titleLabelInfo = titleLabel.split("-");
//    
//    $("#msm_editor_middle > h2").val(titleLabelInfo[0] + " Design Area");
}

function saveComp(e)
{
    // TODO need to ask dialog to save unit that is currently focused on in middle panel if it is in edit mode
    
    e.preventDefault();
    
    var treeInnerHTML = $("#msm_unit_tree").html();
   
    var params = {
        tree_content: treeInnerHTML
    };
    var ids = [];
    $.ajax({
        type: 'POST',
        url:"editorCreation/msmLoadUnit.php",
        data: params,
        success: function(data)
        {
            ids = JSON.parse(data);
            
            var idInfos = ids[0].split("-");
            if(ids != '')
            {
                window.location = "view.php?msmid="+idInfos[0]+"&unitid="+idInfos[1];                        
            }
        },
        error: function(data)
        {
            alert("ajax error in loading unit");
        }
    }); 
     
// TODO navigate to view page once the db is updated
    
}

// triggered by edit button when either saved after making the unit, or when edit button is clicked after returning to edit mode from display mode
function editUnit(dbIds)
{
    enableEditorFunction();    
     
    $(".msm_editor_content").each(function() {  
        var currentId = this.id;
        var currentHTMLvalue = $(this).html();
        console.log(this.id);
        console.log($(this).html());
        
        var idInfo = currentId.split("-");
        
        var newTextarea = document.createElement("textarea");
        newTextarea.id = currentId;
        newTextarea.name = currentId;
      
        if((idInfo[0] == "msm_theorem_content_input")||(idInfo[0] == "msm_theoremref_content_input"))
        {
            newTextarea.className = "msm_unit_child_content msm_theorem_content";
        }
        else if((idInfo[0] == "msm_theoremref_part_content")||(idInfo[0] == "msm_theorem_part_content"))
        {
            newTextarea.className = "msm_theorem_content";
        }
        else if(idInfo[0] == "msm_intro_child_content")
        {
            newTextarea.className = "msm_intro_child_contents";
        }
        else if(idInfo[0] == "msm_info_title")
        {
            newTextarea.className = "msm_info_titles";
        }
        else if(idInfo[0] == "msm_info_content")
        {
            newTextarea.className = "msm_info_contents";
        }
        else
        {
            newTextarea.className = "msm_unit_child_content";
        }
        
        $(newTextarea).val(currentHTMLvalue);
            
        this.parentNode.insertBefore(newTextarea, this);
        this.parentNode.removeChild(this);
            
                
        tinyMCE.init({
            mode:"exact",
            elements: currentId,
            plugins : "subordinate,autolink,lists,advlist,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
            width: "100%",
            height: "70%",
            theme: "advanced",
            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image, cleanup,help,code,|,insertdate,inserttime,preview",
            theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,advhr,|,ltr,rtl,|,subordinate",
            theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,forecolor,backcolor",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            skin : "o2k7",
            skin_variant : "silver"
        });
       
    });
    
    $("#msm_editor_edit").remove();
    $("<input type='submit' class='msm_editor_buttons' id='msm_editor_save' value='Save'/>").appendTo("#msm_editor_middle");
                    
    $("#msm_editor_remove").remove();
    $("#msm_editor_new").remove();
    $('<button class="msm_editor_buttons" id="msm_editor_cancel" onclick="cancelUnit()"> Cancel </button>').appendTo("#msm_editor_middle");
    
    $("#msm_editor_save").click(function(event) { 
        //         prevents navigation to msmUnitForm.php
        event.preventDefault();
              
        submitForm();
            
    });
}

// triggered by 'Remove this Unit' button due to transition from view to edit
// should remove the unit --> javascript code should remove all the display functions then have AJAX call to a php page that will
// update the compositor and related db information (ie. delete unit from table data, update all parent/sibling information)
function removeUnit()
{
    
}

// triggered by cancel button during edit mode after save has been already implemented.  basically its role is to popup a warning message about
// losing unsaved content and ignore any changes done if answered yes otherwise just close the popup window.  When yes is triggered, just load screen back to
// display of previous state
function cancelUnit()
{
    
}
//
//function updateUnit()
//{
//    var formData = $("#msm_unit_form").serializeArray();
//    var targetURL = $("#msm_unit_form").attr("action");
//    
//    $.ajax({
//        type: "POST",
//        url: targetURL,
//        data: formData,
//        success: function(data)
//        {
//            console.log(data);
//        }
//    });
//}