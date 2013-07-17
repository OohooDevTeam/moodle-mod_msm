/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function processAdditionalChild(event, draggedId)
{   
    var type = null;
    var parentId = event.target.parentElement.parentElement.id;
    
    var editorDivs = $("#"+parentId).find(".msm_editor_content");
    
    if(editorDivs.length > 0)
    {
        editUnit(parentId);
    }
    
    if((parentId.match(/def/)) || (parentId.match(/comment/)))
    {
        if((draggedId !== "msm_associate") && (draggedId !== "msm_internal_ref") && (draggedId !== "msm_external_ref"))
        {
            openErrorDialog();
            return false;
        }
        
        if((draggedId == "msm_associate") ||(draggedId == "msm_internal_ref") || (draggedId == "msm_external_ref"))
        {
            if(parentId.match(/def/))
            {
                type = "def";
            }
            else{
                type = "comment";
            }
        }
    }
    else if(parentId.match(/intro/))
    {
        if((draggedId !== "msm_def") && (draggedId !== "msm_theorem") && (draggedId !== "msm_comment") && (draggedId !== "msm_extra_content"))
        {
            openErrorDialog();
            return false;
        }
        
        if(draggedId == "msm_extra_content")
        {
            type = "intro";
        }
    }
    else if(parentId.match(/statement/))
    {
        if(draggedId !== "msm_part_theorem")
        {
            openErrorDialog();
            return false;
        }
    }
    else if(parentId.match(/theorem/))
    {
        if((draggedId !== "msm_associate") && (draggedId !== "msm_internal_ref") && (draggedId !== "msm_external_ref") && (draggedId !== "msm_extra_content"))
        {
            openErrorDialog();
            return false;
        }
        
        if((draggedId == "msm_extra_content") || (draggedId == "msm_associate") ||(draggedId == "msm_internal_ref") || (draggedId == "msm_external_ref"))
        {
            type = "theorem";
        }
    }
    
    var idEnding = event.target.id.split("-");  
    var index = '';
    switch(draggedId)
    {
        case "msm_associate":
            addAssociateForm(idEnding[1], type)
            break;
        case "msm_internal_ref":
            addAssociateForm(idEnding[1], type); 
            index = getAssociateIndex(event);
            createRefDialog(idEnding[1], "Internal References", index);           
            break;
        case "msm_external_ref":
            addAssociateForm(idEnding[1], type);
            index = getAssociateIndex(event);
            createRefDialog(idEnding[1], "External References", index);        
            break;
        case "msm_new_ref":
            addAssociateForm(idEnding[1], type);
            index = getAssociateIndex(event);
            var associateContainer = event.target.parentElement.id;
            var selectMenu = "<select name='msm_associate_reftype-//"+index+"' class='msm_associate_reftype_dropdown' id='msm_associate_reftype-"+index+"' onchange='processReftype(event);'>\n\
                                  <option value='Comment'>Comment</option>\n\
                                  <option value='Definition'>Definition</option>\n\
                                  <option value='Theorem'>Theorem</option>\n\
                               </select>"
            $("#"+associateContainer).append(selectMenu);
            break;
        case "msm_extra_content":
            if(type == "theorem")
            {
                addTheoremContent(event)
            }
            else if(type == "intro")
            {
                addIntroContent(idEnding[1])
            }
            break;
        case "msm_part_theorem":
            addTheoremPart(event)
            break;
    }

}

function getAssociateIndex(e)
{
    var associateContainer = e.target.parentElement.id;
    
    var refdropareas = $("#"+associateContainer).find(".msm_associate_reftype_optionarea");
    
    var refAreaId = refdropareas[refdropareas.length-1].id;            
    var refAreaIdInfo = refAreaId.split("-");
    
    var index = refAreaIdInfo[1];
    for(var i = 2; i < refAreaIdInfo.length; i++)
    {
        index += "-" + refAreaIdInfo[i];
    }
    
    return index;
}

function openErrorDialog()
{
    var message = $("<div id='msm_child_addition_error' title='Wrong child element type'>\n\
                                <p> This is not a valid child type. Please add acceptable child elements listed in the drop area.</p>\n\
                    </div>");
    $(message).appendTo("#msm_child_appending_area");
    
    $( "#msm_child_addition_error" ).dialog({
        resizable: false,
        height:180,
        modal: false,
        buttons: {
            "Ok": function() {
                $(this).dialog("close");
            }
        }
    });
}

function createRefDialog(id, refTypeString, currentId)
{  
    var dialogDiv = $("<div class='msm_ref_search_windows' id='msm_ref_search_window-"+id+"' title='"+refTypeString+"'></div>");
    
    var accordionMenu = $("<div id='msm_search_accordion-"+id+"'>\n\
                            <h3>Search </h3>\n\
                            <div>\n\
                                <form id='msm_search_form'>\n\
                                    <label for='msm_search_type'>Type: </label>\n\
                                    <select id='msm_search_type' name='msm_search_type'>\n\
                                        <option value='definition'>Definition</option>\n\
                                        <option value='theorem'>Theorem</option>\n\
                                        <option value='comment'>Comment</option>\n\
                                        <option value='unit'>Unit</option>\n\
                                    </select>\n\
                                    <br /><br />\n\
                                    <label for='msm_search_word'>Search: </label>\n\
                                    <input id='msm_search_word' name='msm_search_word' style='width: 80%;'/>\n\
                                    <select id='msm_search_word_type' name='msm_search_word_type' style='margin-left: 1%;'>\n\
                                        <option value='title'>Title</option>\n\
                                        <option value='content'>Content</option>\n\
                                        <option value='description'>Description</option>\n\
                                        <option value='all'>Title/Content/Description</option>\n\
                                    </select>\n\
                                    <br /><br />\n\
                                    <input type='button' value='Search' id='msm_search_submit' class='msm_search_buttons'/>\n\
                                </form>\n\
                            </div>\n\
                            <h3>Search Result</h3>\n\
                            <div id='msm_search_result'></div>\n\
                         </div>");
    
    $(dialogDiv).append(accordionMenu);
    $("#msm_dnd_container-"+id).append(dialogDiv);
    var wWidth = $(window).width();
    var wHeight = $(window).height();
    
    var dWidth = wWidth*0.8;
    var dHeight = wHeight*0.8;
    
    $("#msm_ref_search_window-"+id).ready(function() {
        $("#msm_ref_search_window-"+id).dialog({
            resizable: false,
            modal: true,
            height: dHeight,
            width: dWidth,
            dialogClass: "no-close",
            closeOnEscape: false,
            buttons: {
                "Insert" : function() {
                    var selectedBox =  $("#msm_search_result_table input").filter(":checked");
              
                    if(selectedBox.length == 0)
                    {
                        var message = $("<div id='msm_search_error' title='No results Selected'>\n\
                                         <p> No reference material was selected.  Please select one of the following search results or click 'Cancel' to exit.</p>\n\
                                     </div>");
                        $(message).appendTo("#msm_child_appending_area");
                    
                        $( "#msm_search_error" ).dialog({
                            resizable: false,
                            height:180,
                            modal: false,
                            buttons: {
                                "Ok": function() {
                                    $(this).dialog("close");
                                }
                            }
                        });
                    }
                    else if(selectedBox.length > 0)
                    {
                        var refSelectType = $("#msm_search_type").val();
                        
                        console.log("type of reference defined by dropdown menu: comment?def?..etc");
                        console.log(refSelectType);
                        
                        $(".msm_info_dialogs").dialog("destroy").css("display", "none");
                        var selectedRow = $(selectedBox).closest("tr");                    
                        var selectedCells = $(selectedRow).find(".msm_search_result_table_cells");
                        var selectedCheckbox = $(selectedCells[0]).find("input");
                    
                        var selectedId = $(selectedCheckbox[0]).attr("id").split("-");
                    
                        addRefElements(refSelectType, selectedCells, currentId, selectedId[1])
                        //                        addDefRef(selectedCells, currentId, selectedId[1]);
                    
                        $(this).dialog("close");
                    }
            
            
                },
                "Cancel": function() {
                    $(this).dialog("close");
                }
            },
            open: function() {
                $("#msm_search_accordion-"+id).accordion({
                    heightStyle: "content"
                });           
            },
            close: function() {
                $(this).empty().remove();
            }
        });
    
        var msmIdInfo = window.location.search.split("=");   
        var msmId = msmIdInfo[1]; 
    
        $("#msm_search_submit").click(function(e) {
            var param = $("#msm_search_form").serializeArray();
        
            $.ajax({
                type: 'POST',
                url:"editorCreation/getDbInfo.php",
                data: {
                    param: param,
                    msmId: msmId,
                    refereceType: refTypeString
                },
                success: function(data)
                {                
                    var string = JSON.parse(data);    
                                
                    $("#msm_search_result").empty(); // empty out any previous values
                    $("#msm_search_result").append(string);
                    $("#msm_search_accordion-"+id).accordion("option", "active", 1);       
                
                    $("#msm_search_result_table .msm_info_dialogs").dialog({
                        autoOpen: false,
                        height: "auto",
                        modal: false,
                        width: 605
                    });  
                                
                    $("#msm_search_result_table").find(".msm_subordinate_hotwords").each(function(i, element) {
                        var idInfo = this.id.split("-");
                        var newid = '';
                                        
                        for(var i=1; i < idInfo.length-1; i++)
                        {
                            newid += idInfo[i]+"-";
                        }
                                            
                        newid += idInfo[idInfo.length-1];
                                                        
                        previewInfo(this.id, "dialog-"+newid);
                    });
                                    
                    $("#msm_search_result_table .msm_info_dialogs").find(".msm_subordinate_hotwords").each(function() {
                        var idInfo = this.id.split("-");
                        var newid = '';
                                        
                        for(var i=1; i < idInfo.length-1; i++)
                        {
                            newid += idInfo[i]+"-";
                        }
                                            
                        newid += idInfo[idInfo.length-1];
                                                               
                        previewInfo(this.id, "dialog-"+newid);
                    });
                
                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);       
                
                    //                 only allowing one checkbox to be selected at any given time
                    $("#msm_search_result input").click(function(e) {
                        var checked = $(this).attr("checked");
                    
                        //                     when the same checkbox is clicked to deselect the box, need to remove the class that is highlighting the row as well
                        if((checked === null) || (typeof checked === "undefined"))
                        {
                            $(this).closest("tr").removeClass("ui-widget-header");
                        }
                        else
                        {
                            //                         when currently clicked checkbox is selected, then need to deselect all other checkboxes and remove the higlighting class as well
                            $("#msm_search_result input").filter(":checked").not(this).closest("tr").removeClass("ui-widget-header");
                            $("#msm_search_result input").filter(":checked").not(this).removeAttr("checked");
                        
                            $(this).closest("tr").addClass("ui-widget-header");
                        }
                
                    });
                
                }, 
                erorr: function()
                {
                    alert("error");      
                }
            });
        });
    });
    
}

function addRefElements(type, tbcellArray, ind, databaseId)
{
    switch(type)
    {
        case "definition":
            addDefRef(tbcellArray, ind, databaseId);
            break;
        case "comment":
            addCommentRef(tbcellArray, ind, databaseId);
            break;
    }
}

function addDefRef(cellArray, index, dbId)
{
    var type = $(cellArray[1]).html();   
    var title = $(cellArray[2]).html();
    var description = $(cellArray[4]).html();
    
    var defelement = makeRefDefinition(index, dbId);   
    $("#msm_associate_reftype_option-"+index).append(defelement);
    
    var contentobject = processSubContent(cellArray[3], "defrefcontent"+index);
    var content = $(contentobject).html();
    
    $("#msm_defref_type_dropdown-"+index).find("option").each(function() {
        var currentType = $(this).val();
        
        if(currentType == type)
        {                                
            $(this).attr("selected", "selected");
        }
    });
    
    $("#msm_defref_title_input-"+index).val(title);
    $("#msm_defref_description_input-"+index).val(description);
    $("#msm_defref_content_input-"+index).val(content);
    
    $("#msm_defref_type_dropdown-"+index).attr("disabled", "disabled");
    $("#msm_defref_title_input-"+index).attr("disabled", "disabled");
    $("#msm_defref_description_input-"+index).attr("disabled", "disabled");
    
    textArea2Div("msm_defref_content_input-"+index);
}

function addCommentRef(cellArray, index, dbId)
{
    var type = $(cellArray[1]).html();   
    var title = $(cellArray[2]).html();
    var description = $(cellArray[4]).html();
    
    var commentelement = makeRefComment(index, dbId);   
    $("#msm_associate_reftype_option-"+index).append(commentelement);
    
    var contentobject = processSubContent(cellArray[3], "commentrefcontent"+index);
    var content = $(contentobject).html();
    
    $("#msm_commentref_type_dropdown-"+index).find("option").each(function() {
        var currentType = $(this).val();
        
        if(currentType == type)
        {                                
            $(this).attr("selected", "selected");
        }
    });
    
    $("#msm_commentref_title_input-"+index).val(title);
    $("#msm_commentref_description_input-"+index).val(description);
    $("#msm_commentref_content_input-"+index).val(content);
    
    $("#msm_commentref_type_dropdown-"+index).attr("disabled", "disabled");
    $("#msm_commentref_title_input-"+index).attr("disabled", "disabled");
    $("#msm_commentref_description_input-"+index).attr("disabled", "disabled");
    
    textArea2Div("msm_commentref_content_input-"+index);
}

function processSubContent(contentobj, id)
{
    var idEnding = '';
    
    $(contentobj).find(".msm_subordinate_hotwords").each(function() {
        var idEndingInfo = this.id.split("-");
       
        idEnding = idEndingInfo[1];
        for(var i = 2; i < idEndingInfo.length; i++)
        {
            idEnding += "-"+idEndingInfo[i];
        }
        
        var subContainer = document.createElement("div");
        subContainer.id = "msm_subordinate_result-"+idEnding;
        subContainer.className = "msm_subordinate_results";
        
        var selectDiv = document.createElement("div");
        selectDiv.id = "msm_subordinate_select-"+idEnding;
        var selectTextNode = null;
        var selectUrlText = '';  
        var subUrlDiv = '';
        
        if($(this).attr("href") == "#")
        {
            selectTextNode = document.createTextNode("Information");
            selectUrlText = document.createTextNode('');
        }
        else
        {
            selectTextNode = document.createTextNode("External Link");
            selectUrlText = document.createTextNode($(this).attr("href"));
            subUrlDiv = document.createElement("div");
            subUrlDiv.id = "msm_subordinate_url-"+idEnding;
            subUrlDiv.appendChild(selectUrlText);
        }
        
        selectDiv.appendChild(selectTextNode);
                
        var subHotwordMatch = document.createElement("div");
        subHotwordMatch.id = "msm_subordinate_hotword_match-"+idEnding;
        subHotwordMatch.className = "msm_subordinate_hotword_matchs";
        var subHotwordText = document.createTextNode(this.id);
        subHotwordMatch.appendChild(subHotwordText);
        
        var subinfoTitleDiv = document.createElement("div");
        subinfoTitleDiv.id = "msm_subordinate_infoTitle-"+idEnding;
        
        var titleValue = $(contentobj).find("#dialog-"+idEnding).attr("title");
        var matchingInfoTitle = '';
        if(typeof titleValue !== "undefined")
        {
            matchingInfoTitle = document.createTextNode(titleValue);
        }
        else
        {
            matchingInfoTitle = document.createTextNode('');
        }
        subinfoTitleDiv.appendChild(matchingInfoTitle);
        
        var subinfoContentDiv = document.createElement("div");
        subinfoContentDiv.id = "msm_subordinate_infoContent-"+idEnding;
        
        var matchingInfoContent = document.createTextNode($(contentobj).find("#dialog-"+idEnding).html());
        subinfoContentDiv.appendChild(matchingInfoContent);
        
        subContainer.appendChild(selectDiv);
        if(subUrlDiv != '')
        {
            subContainer.appendChild(subUrlDiv);

        }
        subContainer.appendChild(subHotwordMatch);
        subContainer.appendChild(subinfoTitleDiv);
        subContainer.appendChild(subinfoContentDiv);
        
        $("#msm_subordinate_result_container-"+id).append(subContainer);
        
        $(contentobj).find("#dialog-"+idEnding).remove();
        
    });
    
    return contentobj;
}