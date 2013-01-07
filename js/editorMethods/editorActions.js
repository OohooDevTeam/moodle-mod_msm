/**
 **************************************************************************
 **                              MSM                                     **
 **************************************************************************
 * @package     mod                                                      **
 * @subpackage  msm                                                      **
 * @name        msm                                                      **
 * @copyright   University of Alberta                                    **
 * @link        http://ualberta.ca                                       **
 * @author      Ga Young Kim                                             **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 **************************************************************************
 **************************************************************************/

// e = event
function processReftype(e)
{
    //    _index++;
    var selectedReftype = e.target.selectedIndex;
    
    var selectedId = e.target.id;    
        
    var element;
    
    var idString = "#"+e.target.parentElement.id + " > div";
   
    
    // removes any previously added forms for previous choices made by the author
    $(idString).each(function() {
        $(this).empty().remove();   
    })
        
    var currentId = 0;
    var indexnumber = 1;
        
    switch(selectedReftype)
    {
        case 0:
            break;
        case 1: //comment
            element = makeRefComment();
            $(element).insertAfter("#"+selectedId); 
            indexnumber= element[0].id.split("-");
            currentId = 'msm_commentref_content_input-'+indexnumber[1];
            break;
        case 2: //def
            element = makeRefDefinition();
            $(element).insertAfter("#"+selectedId);           
            indexnumber= element[0].id.split("-");
            currentId = 'msm_defref_content_input-'+indexnumber[1];
            break;
        case 3: //theorem
            element = makeRefTheorem();
            $(element).insertAfter("#"+selectedId);
            indexnumber= element[0].id.split("-");
            currentId = 'msm_theoremref_content_input-'+indexnumber[1];
            break;            
        case 4:
            selectedtext = "Example";
            break;
        case 5:
            selectedtext = "Section of this Composition";
            break; 
    }
    
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
        //        theme_advanced_toolbar_location : "external",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        //        file_browser_callback: "myFileBrowser",
        skin : "o2k7",
        skin_variant : "silver"
    });
   
}


function makeRefDefinition()
{
    var clonedCurrentElement = $("<div></div>");
    
    var defCloseButton = $('<a class="msm_element_close" id="msm_def_element_closebutton-'+_index+'" onclick="deleteRefElement(event);">x</a>');
    var defSelectMenu = $('<select name="msm_defref_type_dropdown-'+_index+'" class="msm_unit_child_dropdown" id="msm_defref_type_dropdown-'+_index+'">\n\
                                <option value="Notation">Notation</option>\n\
                                <option value="Definition">Definition</option>\n\
                                <option value="Agreement">Agreement</option>\n\
                                <option value="Convention">Convention</option>\n\
                                <option value="Axiom">Axiom</option>\n\
                                <option value="Terminology">Terminology</option>\n\
                            </select>');
    var defTitle = $("<span class='msm_element_title'><b> DEFINITION </b></span>");
    var defTitleField = $('<input class="msm_unit_child_title" id="msm_defref_title_input-'+_index+'" name="msm_defref_title_input-'+_index+'" placeholder=" Title of Definition"/>');
          
    var defContentField = $('<textarea class="msm_unit_child_content" id="msm_defref_content_input-'+_index+'" name="msm_defref_content_input-'+_index+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-'+_index+'"></div>');
    var defDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_defref_description_label-"+_index+"' for='msm_defref_description_input-"+_index+"'>Description: </label>");
    var defDescriptionField = $("<input class='msm_child_description_inputs' id='msm_defref_description_input-"+_index+"' name='msm_defref_description_input-"+_index+"' placeholder='Insert description to search this element in future. '/>");
               
    clonedCurrentElement.attr("id", "copied_msm_defref-"+_index);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
   
    clonedCurrentElement.append(defCloseButton);
    clonedCurrentElement.append(defSelectMenu);
    clonedCurrentElement.append(defTitle);
    clonedCurrentElement.append(defTitleField);
    clonedCurrentElement.append(defContentField);
    clonedCurrentElement.append(subordinateContainer);
    clonedCurrentElement.append(defDescriptionLabel);
    clonedCurrentElement.append(defDescriptionField);
    
    return clonedCurrentElement;
}

function makeRefTheorem()
{
    var clonedCurrentElement = $("<div></div>");
    var theoremCloseButton = $('<a class="msm_element_close" id="msm_theorem_element_closebutton-'+_index+'" onclick="deleteRefElement(event)">x</a>');

    var theoremSelectMenu = $('<select name="msm_theoremref_type_dropdown-'+_index+'" class="msm_unit_child_dropdown" id="msm_theoremref_type_dropdown-'+_index+'">\n\
                                <option value="Theorem">Theorem</option>\n\
                                <option value="Proposition">Proposition</option>\n\
                                <option value="Lemma">Lemma</option>\n\
                                <option value="Corollary">Corollary</option>\n\
                            </select>');
                
    var theoremTitle = $("<span class='msm_element_title'><b> THEOREM </b></span>");
    var theoremTitleField = $('<input class="msm_unit_child_title" id="msm_theoremref_title_input-'+_index+'" name="msm_theoremref_title_input-'+_index+'" placeholder=" Title of Theorem"/>');
            
    var theoremContentWrapper = $('<div class="msm_theoremref_content_containers" id="msm_theoremref_content_container-'+_index+'"></div>');
            
    var theoremStatementWrapper = $('<div class="msm_theoremref_statement_containers" id="msm_theoremref_statement_container-'+_index+'"></div>');
            
    var theoremContentHeader = $('<span class="msm_theorem_content_header"><b>Theorem Content</b></span>');
    var theoremContentField = $('<textarea class="msm_unit_child_content" id="msm_theoremref_content_input-'+_index+'" name="msm_theoremref_content_input-'+_index+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-ref'+_index+'"></div>');
            
    var theoremPartWrapper = $('<div class="msm_theoremref_part_dropareas" id="msm_theoremref_part_droparea-'+_index+'"></div>');
            
    var theoremChildButton = $('<input class="msm_theorem_child_buttons" id="msm_theoremref_child_button-'+_index+'" type="button" onclick="addrefTheoremContent(event, '+_index+')" value="Add content"/>');
    var theoremPartButton = $('<input class="msm_theorem_part_buttons" id="msm_theoremref_part_button-'+_index+'" type="button" onclick="addrefTheoremPart(event, '+_index+')" value="Add more parts"/>');
    var theoremDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_theoremref_description_label-"+_index+"' for='msm_theoremref_description_input-"+_index+"'>Description: </label>");
    var theoremDescriptionField = $("<input class='msm_child_description_inputs' id='msm_theoremref_description_input-"+_index+"' name='msm_theoremref_description_input-"+_index+"' placeholder='Insert description to search this element in future. '/>");
            
    clonedCurrentElement.attr("id", "copied_msm_theoremref-"+_index);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
    theoremPartWrapper.append(theoremPartButton);
            
    theoremStatementWrapper.append(theoremContentHeader);
    theoremStatementWrapper.append(theoremContentField); 
    theoremStatementWrapper.append(subordinateContainer);
    theoremStatementWrapper.append(theoremPartWrapper);
            
    theoremContentWrapper.append(theoremStatementWrapper);
    theoremContentWrapper.append(theoremChildButton);
            
    clonedCurrentElement.append(theoremCloseButton);
    clonedCurrentElement.append(theoremSelectMenu);
    clonedCurrentElement.append(theoremTitle);
    clonedCurrentElement.append(theoremTitleField);
    clonedCurrentElement.append(theoremContentWrapper);
    clonedCurrentElement.append(theoremDescriptionLabel);
    clonedCurrentElement.append(theoremDescriptionField);
            
    return clonedCurrentElement;
}

function makeRefComment()
{
    var clonedCurrentElement = $("<div></div>");
    var commentCloseButton = $('<a class="msm_element_close" id="msm_comment_element_closebutton-'+_index+'" onclick="deleteRefElement(event);">x</a>');
    var commentSelectMenu = $('<select name="msm_commentref_type_dropdown-'+_index+'" class="msm_unit_child_dropdown" id="msm_commentref_type_dropdown-'+_index+'">\n\
                                <option value="Comment">Comment</option>\n\
                                <option value="Remark">Remark</option>\n\
                                <option value="Information">Information</option>\n\
                            </select>');
    var commentTitle = $("<span class='msm_element_title'><b> COMMENT </b></span>");
    var commentTitleField = $('<input class="msm_unit_child_title" id="msm_commentref_title_input-'+_index+'" name="msm_commentref_title_input-'+_index+'" placeholder=" Title of Comment"/>');
          
    var commentContentField = $('<textarea class="msm_unit_child_content" id="msm_commentref_content_input-'+_index+'" name="msm_commentref_content_input-'+_index+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-ref'+_index+'"></div>');
    var commentDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_commentref_description_label-"+_index+"' for='msm_commentref_description_input-"+_index+"'>Description: </label>");
    var commentDescriptionField = $("<input class='msm_child_description_inputs' id='msm_commentref_description_input-"+_index+"' name='msm_commentref_description_input-"+_index+"' placeholder='Insert description to search this element in future. '/>");
            
    clonedCurrentElement.attr("id", "copied_msm_commentref-"+_index);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
    clonedCurrentElement.append(commentCloseButton);
    clonedCurrentElement.append(commentSelectMenu);
    clonedCurrentElement.append(commentTitle);
    clonedCurrentElement.append(commentTitleField);
    clonedCurrentElement.append(commentContentField);
    clonedCurrentElement.append(subordinateContainer);
    clonedCurrentElement.append(commentDescriptionLabel);
    clonedCurrentElement.append(commentDescriptionField);
    
    return clonedCurrentElement;
}

function deleteRefElement(e)
{
    var currentElementType = e.target.id.split("_");
    var currentElementInfo = e.target.id.split("-");
    
    var currentElement = "copied_msm_"+currentElementType[1]+"ref-"+currentElementInfo[1]+"-"+currentElementInfo[2];
    
    $("<div class='dialogs' id='msm_deleteRefComposition'> <span class='ui-icon ui-icon-alert' style='float: left; margin: 0 7px 20px 0;'></span>Are you sure you wish to delete this element from the composition? </div>").appendTo('#'+currentElement);
    $( "#msm_deleteRefComposition" ).dialog({
        resizable: false,
        height:180,
        modal: true,
        buttons: {
            "Yes": function() {
                $('#'+currentElement).empty().remove();
                
                if($('#msm_associate_reftype_option-'+currentElementInfo[1]+"-"+currentElementInfo[2]).children().length < 1)
                {
                    $('#msm_associate_reftype-'+currentElementInfo[1]+'-1').val("None");
                }   
                
                $( this ).dialog( "close" );
            },
            "No": function() {
                $( this ).dialog( "close" );                   
            }
        }
    });
}

function addrefTheoremContent(event, idNumber)
{
    var newId = idNumber;
    
    $(".msm_theoremref_content_containers > div").each(function(index) {
        newId++;
    })
    
    var theoremStatementWrapper = $('<div class="msm_theoremref_statement_containers" id="msm_theoremref_statement_container-'+newId+'"></div>');
    var theoremCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
    var theoremContentHeader = $('<span class="msm_theorem_content_header"><b>Theorem Content</b></span>');
    var theoremContentField = $('<textarea class="msm_unit_child_content msm_theorem_content" id="msm_theoremref_content_input-'+newId+'" name="msm_theoremref_content_input-'+newId+'"/>');    
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-ref'+newId+'"></div>');
   
    var theoremPartButton = $('<input class="msm_theorem_part_buttons" id="msm_theoremref_part_button-'+newId+'" type="button" onclick="addrefTheoremPart(event, '+newId+')" value="Add more parts"/>');
    var theoremPartWrapper = $('<div class="msm_theorem_part_dropareas" id="msm_theoremref_part_droparea-'+newId+'"></div>');
            
    theoremPartWrapper.append(theoremPartButton);
            
    theoremStatementWrapper.append(theoremCloseButton);
    theoremStatementWrapper.append(theoremContentHeader);
    theoremStatementWrapper.append(theoremContentField);
    theoremStatementWrapper.append(subordinateContainer);
   
    theoremStatementWrapper.append(theoremPartWrapper);
    
    $(theoremStatementWrapper).insertBefore("#"+event.target.id);    
    
    tinyMCE.init({
        mode:"exact",
        elements: "msm_theoremref_content_input-"+newId,        
        plugins : "subordinate,autolink,lists,advlist,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width: "100%",
        height: "70%",
        theme: "advanced",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,cleanup,help,code,|,insertdate,inserttime,preview",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,advhr,|,ltr,rtl,|,subordinate",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,forecolor,backcolor",
        //        theme_advanced_toolbar_location : "external",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        skin : "o2k7",
        skin_variant : "silver"
    });
    
    $("#msm_theoremref_content_container-"+idNumber).sortable({
        appendTo: "msm_theoremref_content_container-"+idNumber,
        connectWith: "msm_theoremref_content_container-"+idNumber,
        cursor: "move",
        tolerance: "pointer",
        placeholder: "msm_sortable_placeholder",        
        start: function(event,ui)
        {
            $(".msm_sortable_placeholder").width(ui.item.context.offsetWidth);
            $(".msm_sortable_placeholder").height(ui.item.context.offsetHeight/2);
            $(".msm_sortable_placeholder").css("background-color","#DC143C");
            $(".msm_sortable_placeholder").css("opacity","0.5");
            $("#"+ui.item.context.id).css("background-color", "#F1EDC2");
        },
        beforeStop: function(event, ui)
        {
            // this code along with the one in stop is needed for enabling sortable on the div containing
            // the tinymce editor so the iframe part of the editor doesn't become disabled
            $(this).find('.msm_unit_child_content').each(function() {
                tinyMCE.execCommand("mceRemoveControl", false, $(this).attr("id")); 
            });
            
            $(this).find('.msm_theorem_content').each(function() {
                tinyMCE.execCommand("mceRemoveControl", false, $(this).attr("id")); 
            });
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#FFFFFF");
            
            // if there are children in intro element, need to refresh the ifram of its editors
            $(this).find('.msm_unit_child_content').each(function() {
                tinyMCE.execCommand("mceAddControl", false, $(this).attr("id")); 
                $(this).sortable("refresh");
            });
            
            $(this).find('.msm_theorem_content').each(function() {
                tinyMCE.execCommand("mceAddControl", false, $(this).attr("id")); 
                $(this).sortable("refresh");
            });
        }
    });    
                
    $("#msm_theoremref_content_container-"+idNumber).disableSelection();
}

function addrefTheoremPart(event, idNumber)
{
    var newId = idNumber;
    
    $("#msm_theoremref_part_droparea-"+idNumber+" > div").each(function(index, element) {
        newId++;
    });    
    
    var theoremPartContainer = $('<div class="msm_theorem_child" id="msm_theoremref_part_container-'+newId+'"></div>');
    
    var theoremCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
    
    var theoremPartLabel = $('<label class="msm_theorem_part_tlabel" for="msm_theoremref_part_title-0">Part Theorem title: </label>');
    var theoremPartTitle = $('<input class="msm_theorem_part_title" id="msm_theoremref_part_title-'+idNumber+'-'+newId+'" name="msm_theoremref_part_title-'+idNumber+'-'+newId+'" placeholder=" Title for this part of the theorem."/>');
    var theoremPartContentField = $('<textarea class="msm_theorem_content" id="msm_theoremref_part_content-'+idNumber+'-'+newId+'" name="msm_theoremref_part_content-'+idNumber+'-'+newId+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-ref'+idNumber+'-'+newId+'"></div>');
            
    theoremPartContainer.append(theoremCloseButton);
    theoremPartContainer.append(theoremPartLabel);
    theoremPartContainer.append(theoremPartTitle);
    theoremPartContainer.append(theoremPartContentField);
    theoremPartContainer.append(subordinateContainer);
    
    $(theoremPartContainer).insertBefore("#"+event.target.id);
    
    tinyMCE.init({
        mode:"exact",
        elements: "msm_theoremref_part_content-"+idNumber+"-"+newId,               
        plugins : "subordinate,autolink,lists,advlist,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width: "100%",
        height: "70%",
        theme: "advanced",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,cleanup,help,code,|,insertdate,inserttime,preview",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,advhr,|,ltr,rtl,|,subordinate",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,forecolor,backcolor",
        //        theme_advanced_toolbar_location : "external",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        skin : "o2k7",
        skin_variant : "silver"
    });
    
    $("#msm_theoremref_part_droparea-"+idNumber).sortable({
        appendTo: "msm_theoremref_part_droparea-"+idNumber,
        connectWith: "msm_theoremref_part_droparea-"+idNumber,
        cursor: "move",
        tolerance: "pointer",
        placeholder: "msm_sortable_placeholder",        
        start: function(event,ui)
        {
            $(".msm_sortable_placeholder").width(ui.item.context.offsetWidth);
            $(".msm_sortable_placeholder").height(ui.item.context.offsetHeight/2);
            $(".msm_sortable_placeholder").css("background-color","#DC143C");
            $(".msm_sortable_placeholder").css("opacity","0.5");
            $("#"+ui.item.context.id).css("background-color", "#F1EDC2");
        },
        beforeStop: function(event, ui)
        {
            // this code along with the one in stop is needed for enabling sortable on the div containing
            // the tinymce editor so the iframe part of the editor doesn't become disabled
            $(this).find('.msm_theorem_content').each(function() {
                tinyMCE.execCommand("mceRemoveControl", true, $(this).attr("id")); 
            });
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#FFFFFF");
            
            // if there are children in intro element, need to refresh the ifram of its editors
            $(this).find('.msm_theorem_content').each(function() {                 
//                tinyMCE.execCommand("mceSubordinate", false, $(this).attr("id"));
                tinyMCE.execCommand("mceAddControl", true, $(this).attr("id"));
                $(this).sortable("refresh");
            });
        }
    });    
                
    $("#msm_theoremref_content_container-"+idNumber).disableSelection();
}