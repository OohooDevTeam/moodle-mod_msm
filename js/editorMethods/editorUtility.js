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
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    initEditor(this.id);                    
                    $(this).sortable("refresh");
                }
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
    
    var unitInfo = [];
    $.ajax({
        type:"POST",
        url: "editorCreation/msmLoadUnit.php",
        data: {
            "mode": "edit",
            "childOrder": $("#msm_child_order").val(),
            "currentUnit": $("#msm_currentUnit_id").val()
        },
        success: function(data) {                
            unitInfo = JSON.parse(data);
            enableContentEditors(unitInfo);  
            enableEditorFunction();   
        }
    });    
    
    $("#msm_editor_edit").remove();
    $("<input type='submit' class='msm_editor_buttons' id='msm_editor_save' value='Save'/>").appendTo("#msm_editor_middle");
                    
    $("#msm_editor_remove").remove();
    $("#msm_editor_new").remove();
    $("#msm_editor_reset").remove();
    $('<button class="msm_editor_buttons" id="msm_editor_cancel" onclick="cancelUnit(event)"> Cancel </button>').appendTo("#msm_editor_middle");      
    
    // reattach all close buttons for deletion of element
    $(".copied_msm_structural_element").each(function(i) {
        var referencematch = this.id.match(/copied_msm_(defref|theoremref|commentref)-.+/);
        
        if(!referencematch)
        {
            var closeButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');      
        
            var intromatch = this.id.match(/copied_msm_intro-.+/);
            var bodymatch = this.id.match(/copied_msm_body-.+/);
        
            if((!intromatch)&&(!bodymatch))
            {
                $(closeButton).attr("style", "margin-top: 2%;");
            }
        
            $(this).prepend(closeButton); //can't use insertBefore since the reference element to insert before can change (eg. intro is header while def is select)
        }
       
    });
    $(".msm_theorem_statement_title_containers").each(function() {
        var closeButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');      
        $(closeButton).insertBefore($(this));
    });
    
    $(".msm_theorem_part_title_containers").each(function() {
        var closeButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');      
        $(closeButton).insertBefore($(this));
    });

    $(".msm_theoremref_statement_title_containers").each(function() {
        var closeButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');      
        $(closeButton).insertBefore($(this));
    });
    
    $(".msm_theoremref_part_title_containers").each(function() {
        var closeButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');      
        $(closeButton).insertBefore($(this));
    });
    
    
    $(".msm_associate_info_headers").each(function() {
        var closeButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
        $(closeButton).insertBefore($(this));
    });
    
    $(".msm_intro_child_dragareas").each(function() {
        var closeButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
        $(closeButton).insertBefore($(this));
    });
    
    $("#msm_editor_save").click(function(event) { 
        //         prevents navigation to msmUnitForm.php
        event.preventDefault();
              
        submitForm();
            
    });
}


/**
 * This method undoes all the actions done by above method(disableEditorFunction) and enables all input and selection fields,
 * reinitalizes all jquery actions, and reattaches close buttons for deletion of each structural element.
 */
function enableEditorFunction()
{
    $('.msm_title_input').removeAttr("disabled");
    $('.msm_unit_description_inputs').removeAttr("disabled");
                    
    $(".copied_msm_structural_element select").removeAttr("disabled");
    $(".copied_msm_structural_element input").removeAttr("disabled");
    
        
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
        handle: ".msm_element_title_containers",
        start: function(event,ui)
        {
            $(".msm_sortable_placeholder").width(ui.item.context.offsetWidth);
            $(".msm_sortable_placeholder").height(ui.item.context.offsetHeight/2);
            $(".msm_sortable_placeholder").css("background-color","#DC143C");
            $(".msm_sortable_placeholder").css("opacity","0.5");
            $("#"+ui.item.context.id).css("background-color", "#F1EDC2");
                
            var id = $(this).attr("id");            
            
            $("#"+id+" textarea").each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) != null)
                {
                    console.log("removing this editor: "+$(this).attr("id"));
                    tinymce.execCommand('mceFocus', false, $(this).attr("id")); 
                    tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
                }
            });
                 
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#FFFFFF");  
                
            var id = $(this).attr("id");
            
            $("#"+id+" textarea").each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) == null)
                {
                    initEditor(this.id);                    
                    $(this).sortable("refresh");
                }
            });
        }
    });
    
    $(".msm_associate_containers").each(function() {
        $("#"+this.id).sortable({
            appendTo: this.id,
            connectWith: this.id,
            cursor: "move",
            tolerance: "pointer",
            placeholder: "msm_sortable_placeholder",      
            handle: ".msm_associate_info_headers",
            start: function(event,ui)
            {
                $(".msm_sortable_placeholder").width(ui.item.context.offsetWidth);
                $(".msm_sortable_placeholder").height(ui.item.context.offsetHeight/2);
                $(".msm_sortable_placeholder").css("background-color","#DC143C");
                $(".msm_sortable_placeholder").css("opacity","0.5");
                $("#"+ui.item.context.id).css("background-color", "#F1EDC2");
            
                // this code along with the one in stop is needed for enabling sortable on the div containing
                // the tinymce editor so the iframe part of the editor doesn't become disabled
                var id = $(this).attr("id");            
            
                $("#"+id+" textarea").each(function() {
                    if(tinymce.getInstanceById($(this).attr("id")) != null)
                    {
                        console.log("removing this editor: "+$(this).attr("id"));
                        tinymce.execCommand('mceFocus', false, $(this).attr("id")); 
                        tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
                    }
                });
            },
            stop: function(event, ui)
            {
                $("#"+ui.item.context.id).css("background-color", "#FFFFFF");
            
                // if there are children in intro element, need to refresh the ifram of its editors
                var id = $(this).attr("id");
            
                $("#"+id+" textarea").each(function() {
                    if(tinymce.getInstanceById($(this).attr("id")) == null)
                    {
                        initEditor(this.id);                    
                        $(this).sortable("refresh");
                    }
                });  
            }
        });    
    });    
    
    $("#msm_intro_child_container").sortable({
        appendTo: "msm_intro_child_container",
        connectWith: "msm_intro_child_container",
        cursor: "move",
        tolerance: "pointer",
        placeholder: "msm_sortable_placeholder",   
        handle: ".msm_intro_child_dragareas",
        start: function(event,ui)
        {
            $(".msm_sortable_placeholder").width(ui.item.context.offsetWidth);
            $(".msm_sortable_placeholder").height(ui.item.context.offsetHeight/2);
            $(".msm_sortable_placeholder").css("background-color","#DC143C");
            $(".msm_sortable_placeholder").css("opacity","0.5");
            $("#"+ui.item.context.id).css("background-color", "#F1EDC2");
            
            // this code along with the one in stop is needed for enabling sortable on the div containing
            // the tinymce editor so the iframe part of the editor doesn't become disabled
            $(this).find('.msm_intro_child_contents').each(function() {
                tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));          
                tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
            });
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#FFFFFF");
            
            // if there are children in intro element, need to refresh the ifram of its editors
            $(this).find('.msm_intro_child_contents').each(function() {
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    initEditor(this.id);                    
                    $(this).sortable("refresh");
                }
            });
        }
    });

    $(".msm_theorem_content_containers").each(function() {
        $("#"+this.id).sortable({
            appendTo: this.id,
            connectWith: this.id,
            cursor: "move",
            tolerance: "pointer",
            placeholder: "msm_sortable_placeholder",   
            handle: ".msm_theorem_statement_title_containers",
            start: function(event,ui)
            {
                $(".msm_sortable_placeholder").width(ui.item.context.offsetWidth);
                $(".msm_sortable_placeholder").height(ui.item.context.offsetHeight/2);
                $(".msm_sortable_placeholder").css("background-color","#DC143C");
                $(".msm_sortable_placeholder").css("opacity","0.5");
                $("#"+ui.item.context.id).css("background-color", "#F1EDC2");
            
                var id = $(this).attr("id");
            
                $("#"+id+" textarea").each(function() {
                    if(tinymce.getInstanceById($(this).attr("id")) != null)
                    {
                        tinymce.execCommand('mceFocus', false, $(this).attr("id")); 
                        tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
                    }
                });
            
            },
            stop: function(event, ui)
            {
                $("#"+ui.item.context.id).css("background-color", "#FFFFFF");
            
                var id = $(this).attr("id");
            
                $("#"+id+" textarea").each(function() {
                    if(tinymce.getInstanceById($(this).attr("id")) == null)
                    {
                        initEditor(this.id);                    
                        $(this).sortable("refresh");
                    }
                });
            }
        });
    });
    $(".msm_theorem_part_dropareas").each(function(){
        $("#"+this.id).sortable({
            appendTo: this.id,
            connectWith: this.id,
            cursor: "move",
            tolerance: "pointer",
            placeholder: "msm_sortable_placeholder",    
            handle: ".msm_theorem_part_title_containers",
            start: function(event,ui)
            {
                $(".msm_sortable_placeholder").width(ui.item.context.offsetWidth);
                $(".msm_sortable_placeholder").height(ui.item.context.offsetHeight/2);
                $(".msm_sortable_placeholder").css("background-color","#DC143C");
                $(".msm_sortable_placeholder").css("opacity","0.5");
                $("#"+ui.item.context.id).css("background-color", "#F1EDC2");
            
                // this code along with the one in stop is needed for enabling sortable on the div containing
                // the tinymce editor so the iframe part of the editor doesn't become disabled
                $(this).find('.msm_theorem_content').each(function() {
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
            
                // if there are children in intro element, need to refresh the ifram of its editors
                $(this).find('.msm_theorem_content').each(function() {
                    if(tinymce.getInstanceById($(this).attr("id"))==null)
                    {
                        initEditor(this.id);                    
                        $(this).sortable("refresh");
                    }
                });
            }
        });
    })
    enableDragTitleToggle();
}


function enableDragTitleToggle()
{   
    $(".msm_element_title_containers").each(function() {
        $(this).mouseover(function() {
            $(this).children("span").each(function(){
                $(this).css({
                    "visibility": "visible",
                    "color": "#4e6632",
                    "opacity": "0.5",
                    "cursor": "move",
                    "display": "inline"
                });
            });
        });
        $(this).mouseout(function() {
            $(this).children("span").css("visibility", "hidden");
        });
        $(this).mouseup(function() {
            $(this).children("span").css("visibility", "hidden");
        });
    });
    
    $(".msm_intro_child_dragareas").each(function() {
        $(this).children("span").css("display", "inline");
        $(this).mouseover(function() {
            $(this).children("span").each(function(){
                $(this).css({
                    "visibility": "visible",
                    "color": "#4e6632",
                    "opacity": "0.5",
                    "cursor": "move",
                    "display": "inline"
                });
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
                "cursor": "move",
                "display": "inline"
            });
        });
        $(this).mouseout(function() {
            $(this).children("span").css({
                "visibility": "hidden",
                "display": "none"
            });
        });
        $(this).mouseup(function() {
            $(this).children("span").css({
                "visibility": "hidden",
                "display": "none"
            });
        });
    });     
    
    $(".msm_theorem_statement_title_containers").each(function(){
        $(this).mouseover(function() {
            $(this).children("span").css({
                "visibility": "visible",
                "color": "#4e6632",
                "opacity": "0.5",
                "cursor": "move",
                "display": "inline"
            });
        });
        $(this).mouseout(function() {
            $(this).children("span").css("visibility", "hidden");
        });
        $(this).mouseup(function() {
            $(this).children("span").css("visibility", "hidden");
        });
    });
    
    $(".msm_theorem_part_title_containers").each(function() { 
        $(this).children("span").css("display", "inline");
        $(this).mouseover(function() {
            $(this).children("span").css({
                "visibility": "visible",
                "color": "#4e6632",
                "opacity": "0.5",
                "cursor": "move",
                "display": "inline"
            });
        });
        $(this).mouseout(function() {
            $(this).children("span").css("visibility", "hidden");
        });
        $(this).mouseup(function() {
            $(this).children("span").css("visibility", "hidden");
        });
    });
    
    $(".msm_theoremref_statement_title_containers").each(function(){
        $(this).mouseover(function() {
            $(this).children("span").css({
                "visibility": "visible",
                "color": "#4e6632",
                "opacity": "0.5",
                "cursor": "move",
                "display": "inline"
            });
        });
        $(this).mouseout(function() {
            $(this).children("span").css("visibility", "hidden");
        });
        $(this).mouseup(function() {
            $(this).children("span").css("visibility", "hidden");
        });
    });
    
    $(".msm_theoremref_part_title_containers").each(function() {
        $(this).children("span").css("display", "inline");
        $(this).mouseover(function() {
            $(this).children("span").css({
                "visibility": "visible",
                "color": "#4e6632",
                "opacity": "0.5",
                "cursor": "move",
                "display": "inline"
            });
        });
        $(this).mouseout(function() {
            $(this).children("span").css("visibility", "hidden");
        });
        $(this).mouseup(function() {
            $(this).children("span").css("visibility", "hidden");
        });
    });
}

function enableContentEditors(unitArray)
{
    var unitChildInfo = $("#msm_child_order").val().split(",");
    
    var intromatch = /^copied_msm_intro-\d+$/;
    var bodymatch = /^copied_msm_body-\d+$/;
    var defmatch = /^copied_msm_def-\d+$/;
    var commentmatch = /^copied_msm_comment-\d+$/;
    var theoremmatch = /^copied_msm_theorem-\d+$/;   
    
    for(var i = 0; i < unitChildInfo.length-1; i++) // last element is msm_id which is not needed
    {
        if(unitChildInfo[i].match(intromatch))
        {
            createIntroText(unitChildInfo, unitArray, i);
        } 
        else if(unitChildInfo[i].match(bodymatch))
        {
            createBodyText(unitChildInfo, unitArray, i);    
        }
        else if(unitChildInfo[i].match(defmatch))
        {
            createDefText(unitChildInfo, unitArray, i);                    
        }
        else if(unitChildInfo[i].match(commentmatch))
        {
            createCommentText(unitChildInfo, unitArray, i);                     
        }
        else if(unitChildInfo[i].match(theoremmatch))
        {
            createTheoremText(unitChildInfo, unitArray, i);                    
        }
    }
}

function createTheoremText(child, unitArray, key)
{
    var unitInfo = unitArray["children"][key];
    
    var theoremStatementInfo = $("#"+child[key]).find(".msm_theorem_statement_containers");
    
    for(var i = 0; i < theoremStatementInfo.length; i++)
    {
        var theoremcontent = unitInfo["contents"][i]["content"];
        var statementidInfo = theoremStatementInfo[i].id.split("-");
            
        var statementid = '';
        for(var j = 1; j < statementidInfo.length-1; j++)
        {
            statementid += statementidInfo[j]+"-";
        }
                
        statementid += statementidInfo[statementidInfo.length-1]; // now containering theoremid-statementid pair for first content then for rest it's theoremid-topstatementid-statementid        
        
        var theoremStatementTextArea = document.createElement("textarea");
        theoremStatementTextArea.id = "msm_theorem_content_input-"+statementid;
        theoremStatementTextArea.name = "msm_theorem_content_input-"+statementid;
        theoremStatementTextArea.className = "msm_unit_child_content";
            
        $(theoremStatementTextArea).val(theoremcontent);   
        
        $("#"+theoremStatementInfo[i].id).children(".msm_editor_content").each(function(index, element){            
            $(this).replaceWith(theoremStatementTextArea);   
        });  
        
        initEditor(theoremStatementTextArea.id);
        
        var theoremPartInfo = $("#"+theoremStatementInfo[i].id).find(".msm_theorem_child");
     
        for(var k=0; k < theoremPartInfo.length; k++)
        {
            var theoremPartContent = unitInfo["contents"][i]["children"][k]["content"];
            var partidInfo = theoremPartInfo[k].id.split("-");
             
            var partid = '';
            for(var m = 1; m < partidInfo.length-1; m++)
            {
                partid += partidInfo[m]+"-";
            }
                
            partid += partidInfo[partidInfo.length-1]; 
        
            var theoremPartTextArea = document.createElement("textarea");
            theoremPartTextArea.id = "msm_theorem_part_content-"+partid;
            theoremPartTextArea.name = "msm_theorem_part_content-"+partid;
            theoremPartTextArea.className = "msm_theorem_content";
            
            $(theoremPartTextArea).val(theoremPartContent);   
        
            $("#"+theoremPartInfo[k].id).children(".msm_editor_content").each(function(index, element){    
                var elementidInfo = this.id.split("-");
                
                var elementid = '';
                for(var n=1; n < elementidInfo.length-1; n++)
                {
                    elementid += elementidInfo[n] + "-";
                }
                elementid += elementidInfo[elementidInfo.length-1];
                if(elementid == partid)
                {
                    $(this).replaceWith(theoremPartTextArea);  
                }                 
            });     
            initEditor(theoremPartTextArea.id);  
        }    
    }
    
    createAssociateText(child, unitInfo, key);
    enableEditorFunction();
}


function createAssociateText(mainElement, aArray, key)
{
    var associateArray = aArray["children"];
    var associateIds = $("#"+mainElement[key]).find(".msm_associate_childs");
    
    for(var i = 0; i < associateIds.length; i++)
    {
        var infos = $("#"+associateIds[i].id).find(".msm_editor_content"); // includes the reference
       
        var currentInfo = infos[0].id.split("-");
        
        var infoid = '';
        for(var j=1; j < currentInfo.length-1; j++)
        {
            infoid += currentInfo[j]+"-";
        }
        infoid += currentInfo[currentInfo.length-1];
        
        //-------------------processing info----------------------
        
        var associateTitle = associateArray[i]["infos"][0]["caption"];
        var associateContent = associateArray[i]["infos"][0]["content"];
  
        var infoTitleArea = document.createElement("textarea");
        infoTitleArea.id = "msm_info_title-"+infoid;
        infoTitleArea.name = "msm_info_title-"+infoid;
        infoTitleArea.className = "msm_info_titles";
        
        var infoContentArea = document.createElement("textarea");
        infoContentArea.id = "msm_info_content-"+infoid;
        infoContentArea.name = "msm_info_content-"+infoid;
        infoContentArea.className = "msm_info_contents";
        
        $(infoTitleArea).val(associateTitle);   
    
        $("#"+infos[0].id).replaceWith(infoTitleArea);    
        
        initEditor(infoTitleArea.id);    
        
        $(infoContentArea).val(associateContent);   
    
        $("#"+infos[1].id).replaceWith(infoContentArea);    
        
        initEditor(infoContentArea.id);    
        
        //-------------------processing references----------------------
        
        if(infos.length >2)
        {
            var refIdInfo = infos[2].id.split("-");
        
            var refid = '';
            for(var n=1; n < refIdInfo.length-1; n++)
            {
                refid += refIdInfo[n]+"-";
            }
            refid += refIdInfo[refIdInfo.length-1];
        
            var refContent = associateArray[i]["infos"][0]["ref"]["content"];
            var refTableName = associateArray[i]["infos"][0]["ref"]["tablename"];
            switch(refTableName)
            {
                case "msm_def":
                    var defrefTextArea = document.createElement("textarea");
                    defrefTextArea.id = "msm_defref_content_input-"+refid;
                    defrefTextArea.name = "msm_defref_content_input-"+refid;
                    defrefTextArea.className = "msm_unit_child_content";
                    
                    $(defrefTextArea).val(refContent);   
                    $("#"+infos[2].id).replaceWith(defrefTextArea); 
                    initEditor(defrefTextArea.id);
                    break;
                case "msm_theorem":
                    var theoremStatementInfo = $("#msm_associate_reftype_option-"+infoid).find(".msm_theoremref_statement_containers");
    
                    for(var ind = 0; ind < theoremStatementInfo.length; ind++)
                    {
                        var theoremcontent = associateArray[i]["infos"][0]["ref"]["contents"][ind]["content"];
                        var statementidInfo = theoremStatementInfo[ind].id.split("-");
            
                        var statementid = '';
                        for(var j = 1; j < statementidInfo.length-1; j++)
                        {
                            statementid += statementidInfo[j]+"-";
                        }
                
                        statementid += statementidInfo[statementidInfo.length-1]; // now containering theoremid-statementid pair for first content then for rest it's theoremid-topstatementid-statementid        
        
                        var theoremStatementTextArea = document.createElement("textarea");
                        theoremStatementTextArea.id = "msm_theoremref_content_input-"+statementid;
                        theoremStatementTextArea.name = "msm_theoremref_content_input-"+statementid;
                        theoremStatementTextArea.className = "msm_unit_child_content msm_theorem_content";
            
                        $(theoremStatementTextArea).val(theoremcontent);   
        
                        $("#"+theoremStatementInfo[ind].id).children(".msm_editor_content").each(function(index, element){     
                            $(this).replaceWith(theoremStatementTextArea);   
                        });  
        
                        initEditor(theoremStatementTextArea.id);
        
                        var theoremPartInfo = $("#"+theoremStatementInfo[ind].id).find(".msm_theorem_child");
     
                        for(var k=0; k < theoremPartInfo.length; k++)
                        {
                            var theoremPartContent = associateArray[i]["infos"][0]["ref"]["contents"][ind]["children"][k]["content"];
                            var partidInfo = theoremPartInfo[k].id.split("-");
             
                            var partid = '';
                            for(var m = 1; m < partidInfo.length-1; m++)
                            {
                                partid += partidInfo[m]+"-";
                            }
                
                            partid += partidInfo[partidInfo.length-1]; 
        
                            var theoremPartTextArea = document.createElement("textarea");
                            theoremPartTextArea.id = "msm_theoremref_part_content-"+partid;
                            theoremPartTextArea.name = "msm_theoremref_part_content-"+partid;
                            theoremPartTextArea.className = "msm_theorem_content";
            
                            $(theoremPartTextArea).val(theoremPartContent);   
        
                            $("#"+theoremPartInfo[k].id).children(".msm_editor_content").each(function(index, element){ 
                                var elementidInfo = this.id.split("-");
                
                                var elementid = '';
                                for(var n=1; n < elementidInfo.length-1; n++)
                                {
                                    elementid += elementidInfo[n] + "-";
                                }
                                elementid += elementidInfo[elementidInfo.length-1];
                                
                                if(elementid == partid)
                                {
                                    $(this).replaceWith(theoremPartTextArea);  
                                }                 
                            });     
                            initEditor(theoremPartTextArea.id);               
                        }        
                    }
                    break;
                case "msm_comment":
                    var commentrefTextArea = document.createElement("textarea");
                    commentrefTextArea.id = "msm_commentref_content_input-"+refid;
                    commentrefTextArea.name = "msm_commentref_content_input-"+refid;
                    commentrefTextArea.className = "msm_unit_child_content";
                    
                    $(commentrefTextArea).val(refContent);   
                    $("#"+infos[2].id).replaceWith(commentrefTextArea); 
                    initEditor(commentrefTextArea.id);
                    break;
            }
        } 
            
       
    }
    enableEditorFunction();
}

function createCommentText(child, unitArray, key)
{
    var unitInfo = unitArray["children"][key];
    
    var commentcontent = unitInfo["content"];
    
    var currentId = $("#"+child[key]).children(".msm_editor_content").first().attr("id");
    
    var commentInfo = currentId.split("-");
                
    var commentTextArea = document.createElement("textarea");
    commentTextArea.id = "msm_comment_content_input-"+commentInfo[1];
    commentTextArea.name = "msm_comment_content_input-"+commentInfo[1];
    commentTextArea.className = "msm_unit_child_content";
            
    $(commentTextArea).val(commentcontent);   
    
    $("#"+currentId).replaceWith(commentTextArea);    
        
    initEditor(commentTextArea.id);    
    
    createAssociateText(child, unitInfo, key);
    enableEditorFunction();
}

function createDefText(child, unitArray, key)
{
    var unitInfo = unitArray["children"][key];
    
    var defcontent = unitInfo["content"];
    
    var currentId = $("#"+child[key]).children(".msm_editor_content").first().attr("id");
    
    var defInfo = currentId.split("-");
                
    var defTextArea = document.createElement("textarea");
    defTextArea.id = "msm_def_content_input-"+defInfo[1];
    defTextArea.name = "msm_def_content_input-"+defInfo[1];
    defTextArea.className = "msm_unit_child_content";
            
    $(defTextArea).val(defcontent);   
    
    $("#"+currentId).replaceWith(defTextArea);    
        
    initEditor(defTextArea.id); 
    
    createAssociateText(child, unitInfo, key);
    
    enableEditorFunction();
   
}


function createIntroText(child, unitArray, key)
{
    var unitInfo = unitArray["children"][key];
    
    var introcontent = '';
               
    if(unitInfo["blocks"] == '')
    {
        for(var index=0; index < unitInfo["children"].length; index++)
        {
            introcontent += unitInfo["children"][index]["content"];
        }
    }
    else
    {       
        for(var index=0; index < unitInfo["blocks"][0]["content"].length; index++)
        {
            introcontent += unitInfo["blocks"][0]["content"][index]["content"];
        }
    }
    
    var currentId = $("#"+child[key]).children(".msm_editor_content").first().attr("id");
    
    var introInfo = currentId.split("-");
                
    var introTextArea = document.createElement("textarea");
    introTextArea.id = "msm_intro_content_input-"+introInfo[1];
    introTextArea.name = "msm_intro_content_input-"+introInfo[1];
    introTextArea.className = "msm_unit_child_content";
            
    $(introTextArea).val(introcontent);   
    
    $("#"+currentId).replaceWith(introTextArea);    
        
    initEditor(introTextArea.id);     
               
    // intro has children
    for(var j = 1; j < unitInfo["blocks"].length; j++)
    {
        var introchildcontent = '';

        for(var k=0; k < unitInfo["blocks"][j]["content"].length; k++)
        {
            introchildcontent += unitInfo["blocks"][j]["content"][k]["content"];
        }        
        
        var childArray = $("#msm_intro_child_container").children(".msm_intro_child");
        
        var childidInfo = childArray[j-1].id.split("-"); 
                
        var introChildTextArea = document.createElement("textarea");
        introChildTextArea.id = "msm_intro_child_content-"+childidInfo[1];
        introChildTextArea.name = "msm_intro_child_content-"+childidInfo[1];
        introChildTextArea.className = "msm_intro_child_contents";                
          
        $(introChildTextArea).val(introchildcontent);
        
        $("#msm_intro_child_content-"+childidInfo[1]).replaceWith(introChildTextArea); 
        
        initEditor(introChildTextArea.id);  
    }                   
    
    enableEditorFunction();
}

function createBodyText(child, unitArray, key)
{
    var unitInfo = unitArray["children"][key];
    
    var bodycontent = '';
    for(var index=0; index < unitInfo["content"].length; index++)
    {
        bodycontent += unitInfo["content"][index]["content"];
    }
    
    var currentId = $("#"+child[key]).children(".msm_editor_content").first().attr("id");
    var bodyInfo = currentId.split("-");
                
    var bodyTextArea = document.createElement("textarea");
    bodyTextArea.id = "msm_body_content_input-"+bodyInfo[1];
    bodyTextArea.name = "msm_body_content_input-"+bodyInfo[1];
    bodyTextArea.className = "msm_unit_child_content";
            
    $(bodyTextArea).val(bodycontent);
    
    $("#"+currentId).replaceWith(bodyTextArea);    
        
    initEditor(bodyTextArea.id); 
    enableEditorFunction();    
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