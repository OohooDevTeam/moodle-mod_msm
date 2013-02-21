/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function insertUnitStructure(dbId)
{
    var dbIdInfo = dbId.split("|");    
    
    if(dbIdInfo.length > 1)
    {
        $("#msm_unit_tree").find("li").each(function() {           
            var stringid = "msm_unit-"+dbIdInfo[1];
            var parent = $(this);
            var match = this.id.match(/msm_unit-.+/);
            var currentId = '';
            if(!match)
            {
                currentId = "msm_unit-"+this.id;
            }
            else
            {
                currentId = this.id;
            }
            if(currentId == stringid)
            {
                $(this).children("ul").children("li").each(function() {
                    $(this).insertBefore(parent);
                });               
                $(this).empty().remove();
            }
        })
    }   
    
    var treediv = document.getElementById("msm_unit_tree");
    
    var listChild = $("<li></li>");
    $(listChild).attr("id", "msm_unit-"+dbIdInfo[0]);
    
    var linkElement = $("<a href='#'></a>");
    
    var linkText = document.createTextNode(dbIdInfo[0]);
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
    
    var currentUnit = document.getElementById('msm_currentUnit_id');
   
    if((currentUnit == null)||(currentUnit == "undefined"))
    {
        var newInputField = document.createElement("input");
        newInputField.id = "msm_currentUnit_id";
        newInputField.name = "msm_currentUnit_id";
        newInputField.setAttribute("style", "display:none;");
        newInputField.value = dbId;
                        
        var form = document.getElementById("msm_unit_form");
                        
        form.appendChild(newInputField);
    }
    else
    {
        $("#msm_currentUnit_id").val(dbId);
    } 
    
    $("#msm_unit_tree")
    .jstree({
        "plugins": ["themes", "html_data", "ui", "dnd"],
        "dnd": {
            "drop_target": false,
            "drag_target": false
        }       
    })
    .bind("load.jstree", function(){
        $("#msm_unit_tree").jstree("select_node", "msm_unit-"+dbIdInfo[0]).trigger("select_node.jstree");
    })
    .bind("select_node.jstree", function(event, data) {
        var dbInfo = [];         

        $(".msm_editor_buttons").remove();
        $("<button class=\"msm_editor_buttons\" id=\"msm_editor_edit\" type=\"button\" onclick=\"editUnit()\"> Edit </button>").appendTo("#msm_editor_middle");
        
        $("<button class=\"msm_editor_buttons\" id=\"msm_editor_remove\" type=\"button\" onclick=\"removeUnit(event)\"> Remove this Unit </button>").appendTo("#msm_editor_middle");
        
        var nodeId = data.rslt.obj.attr("id");      
        var match = nodeId.match(/msm_unit-.+/);
        var nodeInfo = "";
        if(match)
        {
            var tempInfo = nodeId.split("-");
            nodeInfo = tempInfo[1]+"-"+tempInfo[2];
        }
        else
        {
            nodeInfo = nodeId;
        }


        $.ajax({
            type: "POST",
            url: "editorCreation/msmLoadUnit.php",
            data: {
                "id": "msm_unit-"+nodeInfo
            },
            success: function(data)
            {
                dbInfo = JSON.parse(data);  
                processUnitData(dbInfo); 
                $("#msm_currentUnit_id").val(nodeInfo);
                MathJax.Hub.Queue(["Typeset",MathJax.Hub]);    

            },
            error: function(data)
            {
                alert("ajax error in loading unit");
            }
        })

    })
    .delegate("a", "click", function(event, data){
        event.preventDefault();
    });
}

function newUnit()
{
    $("#msm_child_appending_area").empty();
    
    $("#msm_unit_title").val('');
    $("#msm_unit_description_input").val('');
    
    $("#msm_child_order").val('');
    $("#msm_currentUnit_id").val('');
    
    $("#msm_unit_title").removeAttr("disabled");
    $("#msm_unit_description_input").removeAttr("disabled");
    
    $("#msm_editor_edit").remove();    
    $("<input class=\"msm_editor_buttons\" id=\"msm_editor_reset\" type=\"button\" onclick=\"resetUnit()\" value=\"Reset\"/> ").appendTo("#msm_editor_middle");
                    
    $("#msm_editor_new").remove();
    $("#msm_editor_remove").remove();
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
    
    var currentUnit = document.getElementById('msm_currentUnit_id');
    var form = document.getElementById("msm_unit_form");
   
    if((currentUnit == null)||(currentUnit == "undefined"))
    {
        var newInputField = document.createElement("input");
        newInputField.id = "msm_currentUnit_id";
        newInputField.name = "msm_currentUnit_id";
        newInputField.setAttribute("style", "display:none;");      
                        
        form.appendChild(newInputField);
    }
    
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
function editUnit()
{
    enableEditorFunction();    
     
    $(".msm_editor_content").each(function() {  
        var currentId = this.id;
        
        var unitInfo = [];
        var index = 0;
        $.ajax({
            type:"POST",
            url: "editorCreation/msmLoadUnit.php",
            data: {
                "childElementId": currentId,
                "childOrder": $("#msm_child_order").val(),
                "currentUnit": $("#msm_currentUnit_id").val(),
                "order": index
            },
            success: function(data) {                
                unitInfo = JSON.parse(data);
                
                console.log(unitInfo);
                index++;
            },
            error: function() {
                
            }
        });
        
        var currentHTMLvalue = $(this).html();
        
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
        
        initEditor(currentId);         
    });
    
    $("#msm_editor_edit").remove();
    $("<input type='submit' class='msm_editor_buttons' id='msm_editor_save' value='Save'/>").appendTo("#msm_editor_middle");
                    
    $("#msm_editor_remove").remove();
    $("#msm_editor_new").remove();
    $("#msm_editor_reset").remove();
    $('<button class="msm_editor_buttons" id="msm_editor_cancel" onclick="cancelUnit(event)"> Cancel </button>').appendTo("#msm_editor_middle");
    
    
    $("#msm_child_appending_area").sortable({
        appendTo: "#msm_child_appending_area",
        connectWith: "#msm_child_appending_area",
        cursor: "move",
        tolerance: "pointer",
        placeholder: "msm_sortable_placeholder",
        handle: ".msm_element_title_containers",
        start: function(event,ui)
        {
            $(".msm_sortable_placeholder").width(ui.item.context.offsetWidth);
            $(".msm_sortable_placeholder").height(ui.item.context.offsetHeight/2);
            $(".msm_sortable_placeholder").css("background-color","#DC143C");
            $(".msm_sortable_placeholder").css("opacity","0.5");
            $("#"+ui.item.context.id).css("background-color", "#F1EDC2");
            
            $(this).find('.msm_unit_child_content').each(function() { 
                if(tinymce.getInstanceById($(this).attr("id")) != null)
                {            
                    tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));          
                    tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
                }                
            });                        
            $(this).find('.msm_intro_child_contents').each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) != null)
                {
                    tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));          
                    tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
                } 
            });
            $(this).find('.msm_info_titles').each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) != null)
                {
                    tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));          
                    tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
                } 
            });
            $(this).find('.msm_info_contents').each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) != null)
                {
                    tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));          
                    tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
                } 
            });
             
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#FFFFFF");  
            
            $(this).find('.msm_unit_child_content').each(function() {                         
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                   initEditor(this.id);                    
                    $(this).sortable("refresh");
                } 
            });
                            
            //             if there are children in intro element, need to refresh the ifram of its editors
            $(this).find('.msm_intro_child_contents').each(function() {
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    initEditor(this.id);                    
                    $(this).sortable("refresh");
                }
            });
                            
            $(this).find('.msm_info_titles').each(function() {
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    initEditor(this.id);                    
                    $(this).sortable("refresh");
                }
            });
            $(this).find('.msm_info_contents').each(function() {
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    initEditor(this.id);                    
                    $(this).sortable("refresh");
                }
            });
        }
    });    
    
    $(".msm_element_title_containers").each(function() {
        $(this).mouseover(function() {
            $(this).children("span").css({
                "visibility": "visible", 
                "color": "#4e6632", 
                "opacity": "0.5",
                "cursor": "move"
            });
        });
        $(this).mouseout(function() {
            $(this).children("span").css("visibility", "hidden");
        });
        $(this).mouseup(function() {
            $(this).children("span").css("visibility", "hidden");
        });
    });
    
    $(".msm_associate_info_headers").each(function() {
        $(this).mouseover(function() {
            $(this).children("span").css({
                "visibility": "visible", 
                "color": "#4e6632", 
                "opacity": "0.5",
                "cursor": "move"
            });
        });
        $(this).mouseout(function() {
            $(this).children("span").css("visibility", "hidden");
        });
        $(this).mouseup(function() {
            $(this).children("span").css("visibility", "hidden");
        });
    })
     
    
    $(".msm_theorem_statement_title_containers").each(function(){
        $(this).mouseover(function() {
            $(this).children("span").css({
                "visibility": "visible", 
                "color": "#4e6632", 
                "opacity": "0.5",
                "cursor": "move"
            });
        });
        $(this).mouseout(function() {
            $(this).children("span").css("visibility", "hidden");
        });
        $(this).mouseup(function() {
            $(this).children("span").css("visibility", "hidden");
        });
    });
        
    
    $("#msm_editor_save").click(function(event) { 
        //         prevents navigation to msmUnitForm.php
        event.preventDefault();
              
        submitForm();
            
    });
}

// triggered by 'Remove this Unit' button due to transition from view to edit
// should remove the unit --> javascript code should remove all the display functions then have AJAX call to a php page that will
// update the compositor and related db information (ie. delete unit from table data, update all parent/sibling information)
function removeUnit(e)
{
    e.preventDefault();
    
    var currentUnitIdPair = $("#msm_currentUnit_id").val();
    
    var param = {
        removeUnit:currentUnitIdPair
    };
    
    var ids = [];
    
    $.ajax({
        type: 'POST',
        url:"editorCreation/msmUnitForm.php",
        data: param,
        success: function(data)
        {
            ids = JSON.parse(data);
            
            // show root Unit
            processUnitData(ids); 
            $("#msm_currentUnit_id").val(currentUnitIdPair);
            
            $("#msm_unit_tree").find("li").each(function() {           
                var stringid = "msm_unit-"+currentUnitIdPair;
                var parent = $(this);
                var match = this.id.match(/msm_unit-.+/);
                var currentId = '';
                if(!match)
                {
                    currentId = "msm_unit-"+this.id;
                }
                else
                {
                    currentId = this.id;
                }
                if(currentId == stringid)
                {
                    $(this).children("ul").children("li").each(function() {
                        // to prevent having two elements indicated as the last leaf --> problem with this code = also removes 
                        // indicator for the last leaf at the leaf that is actually last in the list
                        //                        if(this.className == "jstree-leaf jstree-last")
                        //                        {
                        //                            this.className = "jstree-leaf";
                        //                        }
                        $(this).insertBefore(parent);
                    });               
                    $(this).empty().remove();
                }
            });
            
            newUnit();
            
            MathJax.Hub.Queue(["Typeset",MathJax.Hub]); 
            
        },
        error: function(data)
        {
            alert("ajax error in loading unit");
        }
    });
    
}

// triggered by cancel button during edit mode after save has been already implemented.  basically its role is to popup a warning message about
// losing unsaved content and ignore any changes done if answered yes otherwise just close the popup window.  When yes is triggered, just load screen back to
// display of previous state
function cancelUnit(e)
{
    e.preventDefault();
    
    var currentUnitIdPair = $("#msm_currentUnit_id").val();
    
    var param = {
        cancelUnit:currentUnitIdPair
    };
    
    var ids = [];
    
    $.ajax({
        type: 'POST',
        url:"editorCreation/msmLoadUnit.php",
        data: param,
        success: function(data)
        {
            ids = JSON.parse(data);
            
            $("#msm_unit_tree").find("li").each(function() {           
                var stringid = "msm_unit-"+ids[0];
                var parent = $(this);
                var match = this.id.match(/msm_unit-.+/);
                var currentId = '';
                if(!match)
                {
                    currentId = "msm_unit-"+this.id;
                }
                else
                {
                    currentId = this.id;
                }
                if(currentId == stringid)
                {
                    $(this).children("ul").children("li").each(function() {
                        $(this).insertBefore(parent);
                    });               
                    $(this).empty().remove();
                }
            });
            
            removeTinymceEditor();
                  
            disableEditorFunction();   
            
            $(".msm_editor_buttons").remove();
            $("<button class=\"msm_editor_buttons\" id=\"msm_editor_edit\" type=\"button\" onclick=\"editUnit()\"> Edit </button>").appendTo("#msm_editor_middle");
        
            $("<button class=\"msm_editor_buttons\" id=\"msm_editor_remove\" type=\"button\" onclick=\"removeUnit(event)\"> Remove this Unit </button>").appendTo("#msm_editor_middle");
            
        },
        error: function(data)
        {
            alert("ajax error in loading unit");
        }
    });
}

function swapButtons(e) {
    var target = e.target.id;
    
    var basicWindow = null;
    var text = null;
    var fullScreen = null;
    
    if(target == "msm_comp_fullscreen")
    {
        basicWindow = document.createElement("button");
        basicWindow.id = "msm_comp_basicwindow";
        text = document.createTextNode("Basic Window");
            
        basicWindow.appendChild(text);
            
        $(basicWindow).insertBefore($("#msm_comp_fullscreen"));
        $("#msm_comp_fullscreen").remove();
            
    }
    else
    {
        fullScreen = document.createElement("button");
        fullScreen.id = "msm_comp_fullscreen";
        text = document.createTextNode("Full Screen");
            
        fullScreen.appendChild(text);
            
        $(fullScreen).insertBefore($("#msm_comp_basicwindow"));
        $("#msm_comp_basicwindow").remove();
    }
    
    $("#msm_comp_fullscreen").click(function(event) {
        $("#page-header").css("display", "none");
        $(".block").addClass("dock_on_load");
        $("#region-main").addClass("nomargin"); 
        $("#msm_editor_container").trigger("spliter.resize");
        $("#msm_editor_middleright").trigger("spliter.resize");
        swapButtons(event);
    });
                
    $("#msm_comp_basicwindow").click(function(event) {
        $("#page-header").css("display", "block");
        $(".block").removeClass("dock_on_load");
        $("#region-main").removeClass("nomargin");   
        $("#msm_editor_container").trigger("spliter.resize");
        $("#msm_editor_middleright").trigger("spliter.resize");
        swapButtons(event);
    });   
    
}