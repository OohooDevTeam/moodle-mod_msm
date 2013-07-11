/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function processAdditionalChild(event, draggedId)
{
    var type = null;
    var parentId = event.target.parentElement.parentElement.id;
    
    if((parentId.match(/def/)) || (parentId.match(/comment/)))
    {
        if((draggedId !== "msm_associate") && (draggedId !== "msm_internal_ref") && (draggedId !== "msm_external_ref"))
        {
            openErrorDialog();
            return false;
        }
        
        if(draggedId == "msm_associate")
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
        
        if((draggedId == "msm_extra_content") || (draggedId == "msm_associate"))
        {
            type = "theorem";
        }
    }
    
    var idEnding = event.target.id.split("-");
    
    switch(draggedId)
    {
        case "msm_associate":
            addAssociateForm(idEnding[1], type)
            break;
        case "msm_internal_ref":
            addAssociateForm(idEnding[1], type);            
            createRefDialog(idEnding[1], "Internal References");           
            break;
        case "msm_external_ref":
            addAssociateForm(idEnding[1], type);
            createRefDialog(idEnding[1], "External References");        
            break;
        case "msm_new_ref":
            addAssociateForm(idEnding[1], type);
            var associateContainer = event.target.parentElement.id;
            var refAreaId = $("#"+associateContainer).find(".msm_associate_reftype_optionarea").index(0).attr("id");            
            var refAreaIdInfo = refAreaId.split("-");
            
            var index = refAreaIdInfo[1];
            for(var i = 2; i < refAreaIdInfo.length; i++)
            {
                index += "-" + refAreaIdInfo[i];
            }
            
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

function createRefDialog(id, refTypeString)
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
    
    $("#msm_ref_search_window-"+id).dialog({
        resizable: false,
        modal: true,
        height: dHeight,
        width: dWidth,
        dialogClass: "no-close",
        closeOnEscape: false,
        buttons: {
            "Insert" : function() {
                alert("where selected info would get inserted into current element");
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
    
    var msmId = '';
    var msmIdInfo = window.location.search.split("=");   
    var currentmsmId =  msmIdInfo[1];
    
    if(refTypeString == "Internal References")
    {        
        msmId = msmIdInfo[1];  
    }
    
    $("#msm_search_submit").click(function() {
        var param = $("#msm_search_form").serializeArray();
        
        $.ajax({
            type: 'POST',
            url:"editorCreation/getDbInfo.php",
            data: {
                param: param,
                msmId: msmId,
                current_id: currentmsmId
            },
            success: function(data)
            {
                var string = JSON.parse(data);    
                
                $("#msm_search_result").empty(); // empty out any previous values
                $("#msm_search_result").append(string);
                $("#msm_search_accordion-"+id).accordion("option", "active", 1);
                MathJax.Hub.Queue(["Typeset",MathJax.Hub]);       
                
                // only allowing one checkbox to be selected at any given time
                $("#msm_search_result input").click(function() {
                    var checked = $(this).attr("checked");
                    
                    // when the same checkbox is clicked to deselect the box, need to remove the class that is highlighting the row as well
                    if((checked === null) || (typeof checked === "undefined"))
                    {
                        $(this).closest("tr").removeClass("ui-widget-header");
                    }
                    else
                    {
                        // when currently clicked checkbox is selected, then need to deselect all other checkboxes and remove the higlighting class as well
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


}