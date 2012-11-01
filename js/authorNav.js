/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function openNavDialog()
{
    $('#msm_nav_setting').ready(function() {
        $('#msm_setting_dialog').dialog({
            modal:true,
            autoOpen: false,
            height: 500,
            width: 605
        });
        $('#msm_setting_dialog').dialog('open').css('display', 'block');
    //        $('#msm_setting_type').tabs().css('display', 'block');
    });
}

function addChildUnit()
{
    $('#msm_child_add').ready(function () {
        $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
        $('<input class="msm_structure_input" name="msm_top" placeholder=" Please specify the name of the child element of this composition."/><br />').insertBefore('#msm_child_add');
    });
}

function closeSetting()
{
    $('#msm_setting_cancel').ready(function() {
        $( "#msm_setting_cancelled" ).dialog({
            resizable: false,
            height:180,
            modal: true,
            buttons: {
                "Yes": function() {
                    $( this ).dialog( "close" );
                    $('#msm_setting_dialog').dialog("close");
                },
                "No": function() {
                    $( this ).dialog( "close" );
                   
                }
            }
        });
    });
}

function allowDrop(event)
{
    event.preventDefault();
}

function dragStart(event)
{    
    var style = window.getComputedStyle(event.target, null);
    event.dataTransfer.setData("text/plain",
        (parseInt(style.getPropertyValue("left"),10) - event.clientX) + ',' + (parseInt(style.getPropertyValue("top"),10) - event.clientY) + ',' + event.target.id);
    //    event.dataTransfer.setData("Text", event.target.id);
    //    console.log(style);
    event.target.style.cursor = 'move';   
}

function drag_over(event) {
    event.preventDefault();
    return false;
} 

function drop(event)
{
    var offset = event.dataTransfer.getData("text/plain").split(',');
    //    var id = event.dataTransfer.getData("text")
    var draggedElement = document.getElementById(offset[2]);
  
    var stringmatch = draggedElement.id.match(/copied-/);
    
    if(stringmatch == null)
    {
        var clonedDraggedElement = draggedElement.cloneNode(true);
        clonedDraggedElement.id = "copied-"+draggedElement.id;
        clonedDraggedElement.className = "copied-"+draggedElement.className;

        if(clonedDraggedElement.id == 'copied-msm_def')
        {
            var defSelectMenu = '<select class="msm_unit_child_dropdown">\n\
                                <option value="Notation">Notation</option>\n\
                                <option value="Definition">Definition</option>\n\
                                <option value="Agreement">Agreement</option>\n\
                                <option value="Convention">Convention</option>\n\
                                <option value="Axiom">Axiom</option>\n\
                                <option value="Terminology">Terminology</option>\n\
                            </select>';
            var defTitleField = '<input class="msm_unit_child_title" id="msm_def_title_input" name="msm_def_title" placeholder=" Title of Definition"/>';
            var defContentField = '<input class="msm_unit_child_content" id="msm_def_content_input" name="msm_def_content" placeholder="Need to add moodle form here?"/>';
            
            var defAddChildButton = '<button class= "msm_unit_child_button" id="msm_def_child_add" type="button"> (+) Add Associated Information </button>';
        
            while(clonedDraggedElement.firstChild)
            {
                clonedDraggedElement.removeChild(clonedDraggedElement.firstChild);
            }
        
            clonedDraggedElement.innerHTML = defSelectMenu + defTitleField + defContentField + defAddChildButton;
        }
        else if(clonedDraggedElement.id == 'copied-msm_theorem')
        {
            var theoremSelectMenu = '<select class="msm_unit_child_dropdown">\n\
                                <option value="Theorem">Theorem</option>\n\
                                <option value="Proposition">Proposition</option>\n\
                                <option value="Lemma">Lemma</option>\n\
                                <option value="Corollary">Corollary</option>\n\
                            </select>';
            var theoremTitleField = '<input class="msm_unit_child_title" id="msm_theorem_title_input" name="msm_theorem_title" placeholder=" Title of Theorem"/>';
            var theoremContentField = '<input class="msm_unit_child_content" id="msm_theorem_content_input" name="msm_theorem_content" placeholder=" Need to add moodle form here?"/>';
            var theoremAddChildButton = '<button class= "msm_unit_child_button" id="msm_theorem_child_add" type="button"> (+) Add Associated Information </button>';
        
            while(clonedDraggedElement.firstChild)
            {
                clonedDraggedElement.removeChild(clonedDraggedElement.firstChild);
            }
        
            clonedDraggedElement.innerHTML = theoremSelectMenu + theoremTitleField + theoremContentField + theoremAddChildButton;
        }
        
        
        event.target.appendChild(clonedDraggedElement);
    }  
    else
    {        
        
        // correct target met, then move the draggable object to specified coordinates
        draggedElement.style.left = (event.clientX + parseInt(offset[0],10)) + 'px';
        draggedElement.style.top = (event.clientY + parseInt(offset[1],10)) + 'px';
        event.target.appendChild(draggedElement);   
    }
    
    event.preventDefault();
    return false;
}

function resizeDiv(event)
{    
    var id = event.srcElement.id;
     
    var idMatch = id.match(/copied-/);
    var targetElement = document.getElementById(id);
     
    if(idMatch != null)
    {      
        targetElement.style.borderStyle = "dotted";
        targetElement.style.borderWidth = "thin";
        targetElement.className = "copied-msm_structural_element_selected";
        $("#"+id).resizable({
            containement: "parent",
            ghost: true,
            helper: "resizable-helper",
            minHeight: 150,
            minWidth: 320
        });
    }
    
//    else
//    {
//        alert("blur");
//        targetElement.style.borderStyle = "dashed";
//        targetElement.style.borderWidth = "2";
//    }
}