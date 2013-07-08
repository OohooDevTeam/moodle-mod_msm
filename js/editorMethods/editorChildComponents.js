/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function processAdditionalChild(event, draggedId)
{
    var type = null;
    var parentId = event.target.parentElement.id;
    
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
            break;
        case "msm_external_ref":
            break;
        case "msm_new_ref":
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