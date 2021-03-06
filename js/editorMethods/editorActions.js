/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                       **
 * @subpackage  msm                                                       **
 * @name        msm                                                       **
 * @copyright   University of Alberta                                     **
 * @link        http://ualberta.ca                                        **
 * @author      Ga Young Kim                                              **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************* */

/**
 *  This js file contains the methods for making forms for the new reference materials
 *  that are added to def/comment/theorem elements by associate elements.
 */

/**
 * This method is triggered when the selection of dropdown menu for selecting different type of reference material
 * in associate element forms changes.  It calls various other function in this js file to change the form according to the 
 * selection of the dropdown menu.
 * 
 * @param {eventObject} e          event object from user changing the dropdown menu selection
 */
function processReftype(e)
{
    var selectedReftype = e.target.selectedIndex;
    
    var selectedId = e.target.id;
        
    var element;
    
    var idString = "#"+e.target.parentElement.id + " > div";   
    
    // removes any previously added forms for previous choices made by the author
    $(idString).each(function() {
        $(this).empty().remove();
    })
    var currentId = '';
    var currentTitle = '';
    var associateInfo = selectedId.split("-");
    
    // associate[1] = parent structural element id
    // associate[2] = associate id
    var indexNumber = associateInfo[1]+"-"+associateInfo[2]+"-1";
        
    switch(selectedReftype)
    {
        case 0:
            break;
        case 1: //comment
            element = makeRefComment(indexNumber);
            $(element).insertAfter("#"+selectedId);
            currentTitle = 'msm_commentref_title_input-'+indexNumber;
            currentId = 'msm_commentref_content_input-'+indexNumber;
            break;
        case 2: //def
            element = makeRefDefinition(indexNumber);
            $(element).insertAfter("#"+selectedId);
            currentTitle = 'msm_defref_title_input-'+indexNumber;
            currentId = 'msm_defref_content_input-'+indexNumber;
            break;
        case 3: //theorem
            element = makeRefTheorem(indexNumber);
            $(element).insertAfter("#"+selectedId);
            currentTitle = 'msm_theoremref_title_input-'+indexNumber;
            currentId = 'msm_theoremref_content_input-'+indexNumber+"-1";
            break;
    }
    
    // activate the tinymce editors for title input fields and content textareas
    initTitleEditor(currentTitle, "26%");
    initEditor(currentId);
    
    $(".msm_dnd_containers").droppable({
        accept: "#msm_component_tabs-2 > div",
        hoverClass: "ui-state-hover",
        tolerance: "pointer",
        drop: function( event, ui ) { 
            processAdditionalChild(event, ui.draggable.context.id);      
            allowDragnDrop();  
        }
    });
    
    $("#msm_theoremref_title_container-"+indexNumber).mouseover(function () {
        $(this).children("span").css({
            "visibility": "visible",
            "color": "#4e6632",
            "opacity": "0.5",
            "cursor": "move"
        });
    });
    $("#msm_theoremref_title_container-"+indexNumber).mouseout(function () {
        $(this).children("span").css("visibility", "hidden");
    });
    $("#msm_theoremref_title_container-"+indexNumber).mouseup(function () {
        $(this).children("span").css("visibility", "hidden");
    });
    
    $("#msm_theoremref_statement_title_container-"+indexNumber+'-1').mouseover(function () {
        $(this).children("span").css({
            "visibility": "visible",
            "color": "#4e6632",
            "opacity": "0.5",
            "cursor": "move"
        });
    });
    $("#msm_theoremref_statement_title_container-"+indexNumber+'-1').mouseout(function () {
        $(this).children("span").css("visibility", "hidden");
    });
    $("#msm_theoremref_statement_title_container-"+indexNumber+'-1').mouseup(function () {
        $(this).children("span").css("visibility", "hidden");
    });   
}

/**
 * This method creates the form for making the definition reference materials.
 * 
 * @param {string} idindex                        string to attach to end of HTML ID to make this element unique
 * @param {integer} dbId                              if user added internal/external reference, this value flags this reference
 *                                              when the values are passed to the server side to be inserted into db
 *                                              (if dbId is not an empty value, then the data already exists so do not insert data to msm_def table)
 * @return {object} clonedCurrentElement          definition form container to be inserted below associate info element forms
 */
function makeRefDefinition(idindex, dbId)
{
    var clonedCurrentElement = $("<div></div>");
    
    var selectAndTitleDiv = $("<div class='msm_select_title_containers'></div>");
    var defSelectMenu = $('<select name="msm_defref_type_dropdown-'+idindex+'" class="msm_unit_child_dropdown" id="msm_defref_type_dropdown-'+idindex+'">\n\
                                <option value="Notation">Notation</option>\n\
                                <option value="Definition">Definition</option>\n\
                                <option value="Agreement">Agreement</option>\n\
                                <option value="Convention">Convention</option>\n\
                                <option value="Axiom">Axiom</option>\n\
                                <option value="Terminology">Terminology</option>\n\
                                </select>');
    var defTitle = $("<span class='msm_element_title'><b style='margin-left: 40%;'> DEFINITION </b></span>");
    var defTitleField = $('<input class="msm_unit_child_title" id="msm_defref_title_input-'+idindex+'" name="msm_defref_title_input-'+idindex+'" placeholder=" Title of Definition"/>');
          
    var defContentField = $('<textarea class="msm_unit_child_content" id="msm_defref_content_input-'+idindex+'" name="msm_defref_content_input-'+idindex+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-defrefcontent'+idindex+'"></div>');
    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-defrefcontent'+idindex+'"></div>');
    var defDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_defref_description_label-"+idindex+"' for='msm_defref_description_input-"+idindex+"'>Description: </label>");
  
    var defDescriptionField = null;
  
    if((dbId != '') && (typeof dbId !== "undefined"))
    {
        defDescriptionField = $("<input class='msm_child_description_inputs' id='msm_defref_description_input-"+idindex+"' name='msm_defref_description_input-"+idindex+"__"+dbId+"' placeholder='Insert description to search this element in future. '/>");
    }
    else
    {
        defDescriptionField = $("<input class='msm_child_description_inputs' id='msm_defref_description_input-"+idindex+"' name='msm_defref_description_input-"+idindex+"' placeholder='Insert description to search this element in future. '/>");
    }
               
    clonedCurrentElement.attr("id", "copied_msm_defref-"+idindex);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
    
    selectAndTitleDiv.append(defSelectMenu);
    selectAndTitleDiv.append(defTitleField);
   
    clonedCurrentElement.append(defTitle);
    clonedCurrentElement.append(selectAndTitleDiv);
    clonedCurrentElement.append(defContentField);
    clonedCurrentElement.append(subordinateContainer);
    clonedCurrentElement.append(subordinateResult);
    clonedCurrentElement.append(defDescriptionLabel);
    clonedCurrentElement.append(defDescriptionField);
    
    return clonedCurrentElement;
}

/**
 * This method creates the form for making the theorem reference materials.
 * 
 * @param {string} idindex                        string to attach to end of HTML ID to make this element unique
 * @param {int} dbId                              if user added internal/external reference, this value flags this reference
 *                                                when the values are passed to the server side to be inserted into db
 *                                                (if dbId is not an empty value, then the data already exists so do not insert data to msm_theorem table)
 * @return {object} clonedCurrentElement          theorem form container to be inserted below associate info element forms
 */
function makeRefTheorem(idindex, dbId)
{
    var clonedCurrentElement = $("<div></div>");

    var selectAndTitleDiv = $("<div class='msm_select_title_containers'></div>");
    var theoremSelectMenu = $('<select name="msm_theoremref_type_dropdown-'+idindex+'" class="msm_unit_child_dropdown" id="msm_theoremref_type_dropdown-'+idindex+'">\n\
                                    <option value="Theorem">Theorem</option>\n\
                                    <option value="Proposition">Proposition</option>\n\
                                    <option value="Lemma">Lemma</option>\n\
                                    <option value="Corollary">Corollary</option>\n\
                                </select>');
    
    var theoremTitle = $("<span class='msm_element_title'><b style='margin-left: 41%;'> Theorem </b></span>");
    var theoremTitleField = $('<input class="msm_unit_child_title" id="msm_theoremref_title_input-'+idindex+'" name="msm_theoremref_title_input-'+idindex+'" placeholder=" Title of Theorem"/>');
            
    var theoremContentWrapper = $('<div class="msm_theoremref_content_containers" id="msm_theoremref_content_container-'+idindex+'"></div>');
            
    var theoremStatementWrapper = $('<div class="msm_theoremref_statement_containers" id="msm_theoremref_statement_container-'+idindex+'-1"></div>');
    
    var theoremContentTitleContainer = $('<div class="msm_theoremref_statement_title_containers" id="msm_theoremref_statement_title_container-'+idindex+'-1"><b> Theorem Content </b></div>');
    var theoremContentTitleHidden = $('<span style="visibility: hidden;">Drag here to move this element.</span>');
            
    var theoremContentField = $('<textarea class="msm_unit_child_content" id="msm_theoremref_content_input-'+idindex+'-1" name="msm_theoremref_content_input-'+idindex+'-1"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-theoremrefcontent'+idindex+'-1"></div>');

    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-theoremrefcontent'+idindex+'-1"></div>');
            
    var theoremPartWrapper = $('<div class="msm_theoremref_part_dropareas" id="msm_theoremref_part_droparea-'+idindex+'-1"></div>');
    var partDndDiv = $("<div class='msm_dnd_containers' id='msm_dnd_container-"+idindex+"-1'>Drag additional content to here.\n\
                            <p>Valid child Elements: Part of a Theorem</p>\n\
                        </div>");   
            
    var theoremDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_theoremref_description_label-"+idindex+"' for='msm_theoremref_description_input-"+idindex+"'>Description: </label>");
    
    var theoremDescriptionField = '';
    
    if((dbId != '') && (typeof dbId !== "undefined"))
    {
        theoremDescriptionField = $("<input class='msm_child_description_inputs' id='msm_theoremref_description_input-"+idindex+"' name='msm_theoremref_description_input-"+idindex+"__"+dbId+"' placeholder='Insert description to search this element in future. '/>");
    }
    else
    {
        theoremDescriptionField = $("<input class='msm_child_description_inputs' id='msm_theoremref_description_input-"+idindex+"' name='msm_theoremref_description_input-"+idindex+"' placeholder='Insert description to search this element in future. '/>");
    }    
    
    var dndDiv = $("<div class='msm_dnd_containers' id='msm_dnd_container-"+idindex+"'>Drag additional content to here.\n\
                        <p>Valid child Elements: Extra Contents</p>\n\
                    </div>");     
            
    clonedCurrentElement.attr("id", "copied_msm_theoremref-"+idindex);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
    theoremPartWrapper.append(partDndDiv);
    theoremContentTitleContainer.append(theoremContentTitleHidden);
            
            
    theoremStatementWrapper.append(theoremContentTitleContainer);
    theoremStatementWrapper.append(theoremContentField);
    theoremStatementWrapper.append(subordinateContainer);
    theoremStatementWrapper.append(subordinateResult);
    theoremStatementWrapper.append(theoremPartWrapper);
            
    theoremContentWrapper.append(theoremStatementWrapper);
    
    selectAndTitleDiv.append(theoremSelectMenu);
    selectAndTitleDiv.append(theoremTitleField);
            
    clonedCurrentElement.append(theoremTitle);
    clonedCurrentElement.append(selectAndTitleDiv);
    clonedCurrentElement.append(theoremContentWrapper);
    clonedCurrentElement.append(dndDiv);
    clonedCurrentElement.append(theoremDescriptionLabel);
    clonedCurrentElement.append(theoremDescriptionField);
            
    return clonedCurrentElement;
}

/**
 * This method creates the form for making the comment reference materials.
 * 
 * @param {string} idindex                        string to attach to end of HTML ID to make this element unique
 * @param {int} dbId                              if user added internal/external reference, this value flags this reference
 *                                                when the values are passed to the server side to be inserted into db
 *                                                (if dbId is not an empty value, then the data already exists so do not insert data to msm_comment table)
 * @return {object} clonedCurrentElement          comment form container to be inserted below associate info element forms
 */
function makeRefComment(idindex, dbId)
{
    var clonedCurrentElement = $("<div></div>");
    
    var selectAndTitleDiv = $("<div class='msm_select_title_containers'></div>");
    var commentSelectMenu = $('<select name="msm_commentref_type_dropdown-'+idindex+'" class="msm_unit_child_dropdown" id="msm_commentref_type_dropdown-'+idindex+'">\n\
                                    <option value="Comment">Comment</option>\n\
                                    <option value="Remark">Remark</option>\n\
                                    <option value="Information">Information</option>\n\
                               </select>');
    var commentTitle = $("<span class='msm_element_title'><b style='margin-left: 40%;'> COMMENT </b></span>");
    var commentTitleField = $('<input class="msm_unit_child_title" id="msm_commentref_title_input-'+idindex+'" name="msm_commentref_title_input-'+idindex+'" placeholder=" Title of Comment"/>');
          
    var commentContentField = $('<textarea class="msm_unit_child_content" id="msm_commentref_content_input-'+idindex+'" name="msm_commentref_content_input-'+idindex+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-commentrefcontent'+idindex+'"></div>');

    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-commentrefcontent'+idindex+'"></div>');
    var commentDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_commentref_description_label-"+idindex+"' for='msm_commentref_description_input-"+idindex+"'>Description: </label>");
    
    var commentDescriptionField = null;
  
    if((dbId != '') && (typeof dbId !== "undefined"))
    {
        commentDescriptionField = $("<input class='msm_child_description_inputs' id='msm_commentref_description_input-"+idindex+"' name='msm_commentref_description_input-"+idindex+"__"+dbId+"' placeholder='Insert description to search this element in future. '/>");
    }
    else
    {
        commentDescriptionField = $("<input class='msm_child_description_inputs' id='msm_commentref_description_input-"+idindex+"' name='msm_commentref_description_input-"+idindex+"' placeholder='Insert description to search this element in future. '/>");
    }           
    
    clonedCurrentElement.attr("id", "copied_msm_commentref-"+idindex);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
    
    selectAndTitleDiv.append(commentSelectMenu);
    selectAndTitleDiv.append(commentTitleField);
            
    clonedCurrentElement.append(commentTitle);
    clonedCurrentElement.append(selectAndTitleDiv);
    clonedCurrentElement.append(commentContentField);
    clonedCurrentElement.append(subordinateContainer);
    clonedCurrentElement.append(subordinateResult);
    clonedCurrentElement.append(commentDescriptionLabel);
    clonedCurrentElement.append(commentDescriptionField);
    
    return clonedCurrentElement;
}

/**
 * This method creates the form for additional contents in theorem element.  It is triggered when an 
 * "Extra Content" element is dragged and dropped to a droppable container in theorem.
 * 
 * @param {eventObject} event         event object triggered from item being dropped into a designated droppable container
 */
function addrefTheoremContent(event)
{
    var newId = 1;
   
    var buttonIdInfo = event.target.id.split("-");
    
    // getting HTML id endings added to parent containers
    var idNumber = '';
    for(var i = 1; i < buttonIdInfo.length-1; i++)
    {
        idNumber += buttonIdInfo[i]+"-";
    }
    idNumber += buttonIdInfo[buttonIdInfo.length-1];
        
    // generating a number to be added at the end of HTML ID to make it unique
    if($("#msm_theoremref_content_container-"+idNumber).children("div").length > 0)
    {
        while(document.getElementById('msm_theoremref_statement_container-'+idNumber+'-'+newId) != null)
        {
            newId++;
        }
    }
    var theoremStatementWrapper = $('<div class="msm_theoremref_statement_containers" id="msm_theoremref_statement_container-'+idNumber+'-'+newId+'"></div>');
    var theoremCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
    
    var theoremContentTitleContainer = $('<div class="msm_theoremref_statement_title_containers" id="msm_theoremref_statement_title_container-'+idNumber+'-'+newId+'"><b> Theorem Content </b></div>');
    var theoremContentTitleHidden = $('<span style="visibility: hidden;">Drag here to move this element.</span>');
    var theoremContentField = $('<textarea class="msm_unit_child_content msm_theorem_content" id="msm_theoremref_content_input-'+idNumber+'-'+newId+'" name="msm_theoremref_content_input-'+idNumber+'-'+newId+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-theoremrefcontent'+idNumber+'-'+newId+'"></div>');

    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-theoremrefcontent'+idNumber+'-'+newId+'"></div>');
   
    var param = idNumber+"-"+newId;
   
    var partDndDiv = $("<div class='msm_dnd_containers' id='msm_dnd_container-"+idNumber+"-"+newId+"'>Drag additional content to here.\n\
                        <p>Valid child Elements: Part of a Theorem</p>\n\
                    </div>"); 
    var theoremPartWrapper = $('<div class="msm_theoremref_part_dropareas" id="msm_theoremref_part_droparea-'+idNumber+'-'+newId+'"></div>');
            
    theoremPartWrapper.append(partDndDiv);
    
    theoremContentTitleContainer.append(theoremContentTitleHidden);
            
    theoremStatementWrapper.append(theoremCloseButton);
    theoremStatementWrapper.append(theoremContentTitleContainer);
    theoremStatementWrapper.append(theoremContentField);
    theoremStatementWrapper.append(subordinateContainer);
    theoremStatementWrapper.append(subordinateResult);
   
    theoremStatementWrapper.append(theoremPartWrapper);
    
    $("#msm_theoremref_content_container-"+idNumber).append(theoremStatementWrapper);    
    
    initEditor("msm_theoremref_content_input-"+idNumber+'-'+newId);    
     
    $("#msm_dnd_container-"+idNumber+"-"+newId).droppable({
        accept: "#msm_component_tabs-2 > div",
        hoverClass: "ui-state-hover",
        tolerance: "pointer",
        drop: function( event, ui ) { 
            processAdditionalChild(event, ui.draggable.context.id);      
            allowDragnDrop();  
        }
    });
    
    $("#msm_theoremref_content_container-"+idNumber).sortable({
        appendTo: "msm_theoremref_content_container-"+idNumber,
        connectWith: "msm_theoremref_content_container-"+idNumber,
        cursor: "move",
        tolerance: "pointer",
        placeholder: "msm_sortable_placeholder",
        handle: ".msm_theoremref_statement_title_containers",
        start: function(event,ui)
        {
            $(".msm_sortable_placeholder").width(ui.item.context.offsetWidth);
            $(".msm_sortable_placeholder").height(ui.item.context.offsetHeight/2);
            $(".msm_sortable_placeholder").css("background-color","#DC143C");
            $(".msm_sortable_placeholder").css("opacity","0.5");
            $("#"+ui.item.context.id).css("background-color", "#F1EDC2");
            
            // this code along with the one in stop is needed for enabling sortable on the div containing
            // the tinymce editor so the iframe part of the editor doesn't become disabled
            $(this).find('.msm_unit_child_content').each(function() {
                tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));
                tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
            });
            
            $(this).find('.msm_theorem_part_title').each(function() {
                tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));
                tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
            });
            
        },
        stop: function(event, ui)
        {
            // if the css is not set, then it remains yellow as when it was being dragged
            $("#"+ui.item.context.id).css("background-color", "#EDEDED");
            
            // if there are children in intro element, need to refresh the ifram of its editors
            $(this).find('.msm_unit_child_content').each(function() {
                initEditor(this.id);
                $(this).sortable("refresh");
            });
            
            $(this).find('.msm_theorem_content').each(function() {
                initEditor(this.id);
                $(this).sortable("refresh");
            });
            
            $(this).find('.msm_theorem_part_title').each(function() {
                initTitleEditor(this.id, "85%");
                $(this).sortable("refresh");
            });
        }
    });
    
    $("#msm_theoremref_statement_title_container-"+idNumber+'-'+newId).mouseover(function () {
        $(this).children("span").css({
            "visibility": "visible",
            "color": "#4e6632",
            "opacity": "0.5",
            "cursor": "move"
        });
    });
    $("#msm_theoremref_statement_title_container-"+idNumber+'-'+newId).mouseout(function () {
        $(this).children("span").css("visibility", "hidden");
    });
    $("#msm_theoremref_statement_title_container-"+idNumber+'-'+newId).mouseup(function () {
        $(this).children("span").css("visibility", "hidden");
    });
}

/**
 * This method creates the form for additional parts in theorem element.  It is triggered when an 
 * "Parts of a Theorem" element is dragged and dropped to a droppable container in theorem content.
 * 
 * @param {eventObject} event         event object triggered from item being dropped into a designated droppable container
 */
function addrefTheoremPart(event)
{    
    var newId = 1;
    
    var buttonIdInfo = event.target.id.split("-");
    
    var idNumber = '';
    for(var i = 1; i < buttonIdInfo.length-1; i++)
    {
        idNumber += buttonIdInfo[i]+"-";
    }
    idNumber += buttonIdInfo[buttonIdInfo.length-1];
        
    if($("#msm_theoremref_part_droparea-"+idNumber).children("div").length > 0)
    {
        while(document.getElementById('msm_theoremref_part_container-'+idNumber+'-'+newId) != null)
        {
            newId++;
        }
    }
    
    var theoremPartContainer = $('<div class="msm_theorem_child" id="msm_theoremref_part_container-'+idNumber+'-'+newId+'"></div>');
    
    var theoremCloseButton = $('<a style="margin-bottom:1%;" class="msm_element_close" onclick="deleteElement(event)">x</a>');
    
    var theoremPartTitleContainer = $('<div class="msm_theoremref_part_title_containers" id="msm_theoremref_part_title_container-'+idNumber+'-'+newId+'"></div>');
    var theoremPartTitleHidden = $('<span style="visibility: hidden;">Drag here to move this element.</span>');
    
    var theoremPartLabel = $('<label class="msm_theorem_part_tlabel" for="msm_theoremref_part_title-'+idNumber+'-'+newId+'">Part Theorem title: </label>');
    var theoremPartTitle = $('<input class="msm_theorem_part_title" id="msm_theoremref_part_title-'+idNumber+'-'+newId+'" name="msm_theoremref_part_title-'+idNumber+'-'+newId+'" placeholder=" Title for this part of the theorem."/>');
    var theoremPartContentField = $('<textarea class="msm_theorem_content" id="msm_theoremref_part_content-'+idNumber+'-'+newId+'" name="msm_theoremref_part_content-'+idNumber+'-'+newId+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-theoremrefpart'+idNumber+'-'+newId+'"></div>');

    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-theoremrefpart'+idNumber+'-'+newId+'"></div>');
            
    theoremPartTitleContainer.append(theoremPartTitleHidden);
            
    theoremPartContainer.append(theoremCloseButton);
    theoremPartContainer.append(theoremPartTitleContainer);
    theoremPartContainer.append(theoremPartLabel);
    theoremPartContainer.append(theoremPartTitle);
    theoremPartContainer.append(theoremPartContentField);
    theoremPartContainer.append(subordinateContainer);
    theoremPartContainer.append(subordinateResult);
    
    $(theoremPartContainer).insertBefore("#"+event.target.id);
    
    if(tinymce.getInstanceById("msm_theoremref_part_content-"+idNumber+"-"+newId))
    {
        tinyMCE.execCommand('mceFocus', false, "msm_theoremref_part_content-"+idNumber+"-"+newId);
        tinymce.execCommand('mceRemoveControl', true, "msm_theoremref_part_content-"+idNumber+"-"+newId);
    }
    
    if(tinymce.getInstanceById("msm_theoremref_part_title-"+idNumber+"-"+newId))
    {
        tinyMCE.execCommand('mceFocus', false, "msm_theoremref_part_title-"+idNumber+"-"+newId);
        tinymce.execCommand('mceRemoveControl', true, "msm_theoremref_part_title-"+idNumber+"-"+newId);
    }
    
    initTitleEditor("msm_theoremref_part_title-"+idNumber+"-"+newId, "85%");
    initEditor("msm_theoremref_part_content-"+idNumber+"-"+newId);
    
    $("#msm_theoremref_part_droparea-"+idNumber).sortable({
        appendTo: "msm_theoremref_part_droparea-"+idNumber,
        connectWith: "msm_theoremref_part_droparea-"+idNumber,
        cursor: "move",
        tolerance: "pointer",
        placeholder: "msm_sortable_placeholder",
        handle: ".msm_theoremref_part_title_containers",
        start: function(event,ui)
        {
            $(".msm_sortable_placeholder").width(ui.item.context.offsetWidth);
            $(".msm_sortable_placeholder").height(ui.item.context.offsetHeight/2);
            $(".msm_sortable_placeholder").css("background-color","#DC143C");
            $(".msm_sortable_placeholder").css("opacity","0.5");
            $("#"+ui.item.context.id).css("background-color", "#F1EDC2");
            
            // this code along with the one in stop is needed for enabling sortable on the div containing
            // the tinymce editor so the iframe part of the editor doesn't become disabled
            $(this).find(".msm_theorem_part_title").each(function() {
                tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));
                tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
            });
            
            $(this).find('.msm_theorem_content').each(function() {
                tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));
                tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
            });
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#EDEDED");
            
            // if there are children in intro element, need to refresh the ifram of its editors
            $(this).find('.msm_theorem_part_title').each(function() {
                initTitleEditor(this.id, "85%");
                $(this).sortable("refresh");
            });
            
            $(this).find('.msm_theorem_content').each(function() {
                initEditor(this.id);
                $(this).sortable("refresh");
            });
        }
    });
    
    $("#msm_theoremref_part_title_container-"+idNumber+'-'+newId).mouseover(function () {
        $(this).children("span").css({
            "visibility": "visible",
            "color": "#4e6632",
            "opacity": "0.5",
            "cursor": "move"
        });
    });
    $("#msm_theoremref_part_title_container-"+idNumber+'-'+newId).mouseout(function () {
        $(this).children("span").css("visibility", "hidden");
    });
    $("#msm_theoremref_part_title_container-"+idNumber+'-'+newId).mouseup(function () {
        $(this).children("span").css("visibility", "hidden");
    });
                
}