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
    var selectedReftype = e.target.selectedIndex;
    
    var selectedId = e.target.id;    
        
    var element;
    
    var idString = "#"+e.target.parentElement.id + " > div";
   
    
    // removes any previously added forms for previous choices made by the author
    $(idString).each(function() {
        $(this).empty().remove();   
    })
    var currentId;    
    var associateInfo = selectedId.split("-");
    
    var indexNumber = associateInfo[1]+"-"+associateInfo[2];
        
    switch(selectedReftype)
    {
        case 0:
            break;
        case 1: //comment
            element = makeRefComment(indexNumber);
            $(element).insertAfter("#"+selectedId); 
            currentId = 'msm_commentref_content_input-'+indexNumber;
            break;
        case 2: //def
            element = makeRefDefinition(indexNumber);
            $(element).insertAfter("#"+selectedId); 
            currentId = 'msm_defref_content_input-'+indexNumber;
            break;
        case 3: //theorem
            element = makeRefTheorem(indexNumber);
            $(element).insertAfter("#"+selectedId);
            currentId = 'msm_theoremref_content_input-'+indexNumber+"-1";
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
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        skin : "o2k7",
        skin_variant : "silver"
    });
    
    $("#msm_theoremref_title_container-"+indexNumber).mouseover(function () {
        $(this).children("span").css({
            "visibility": "visible", 
            "color": "#4e6632", 
            "opacity": "0.5"
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
            "opacity": "0.5"
        });
    });
    $("#msm_theoremref_statement_title_container-"+indexNumber+'-1').mouseout(function () {
        $(this).children("span").css("visibility", "hidden");
    });
    $("#msm_theoremref_statement_title_container-"+indexNumber+'-1').mouseup(function () {
        $(this).children("span").css("visibility", "hidden");
    });
   
}


function makeRefDefinition(idindex)
{
    var clonedCurrentElement = $("<div></div>");
    
    var defCloseButton = $('<a class="msm_element_close" id="msm_def_element_closebutton-'+idindex+'" style="margin-top: 2%;" onclick="deleteRefElement(event);">x</a>');
    var defSelectMenu = $('<select name="msm_defref_type_dropdown-'+idindex+'" class="msm_unit_child_dropdown" id="msm_defref_type_dropdown-'+idindex+'">\n\
                                <option value="Notation">Notation</option>\n\
                                <option value="Definition">Definition</option>\n\
                                <option value="Agreement">Agreement</option>\n\
                                <option value="Convention">Convention</option>\n\
                                <option value="Axiom">Axiom</option>\n\
                                <option value="Terminology">Terminology</option>\n\
                            </select>');
    var defTitle = $("<span class='msm_element_title'><b style='margin-left: 30%;'> DEFINITION </b></span>");
    var defTitleField = $('<input class="msm_unit_child_title" id="msm_defref_title_input-'+idindex+'" name="msm_defref_title_input-'+idindex+'" placeholder=" Title of Definition"/>');
          
    var defContentField = $('<textarea class="msm_unit_child_content" id="msm_defref_content_input-'+idindex+'" name="msm_defref_content_input-'+idindex+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-defrefcontent'+idindex+'"></div>');
    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-defrefcontent'+idindex+'"></div>');
    var defDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_defref_description_label-"+idindex+"' for='msm_defref_description_input-"+idindex+"'>Description: </label>");
    var defDescriptionField = $("<input class='msm_child_description_inputs' id='msm_defref_description_input-"+idindex+"' name='msm_defref_description_input-"+idindex+"' placeholder='Insert description to search this element in future. '/>");
               
    clonedCurrentElement.attr("id", "copied_msm_defref-"+idindex);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
   
    clonedCurrentElement.append(defCloseButton);
    clonedCurrentElement.append(defSelectMenu);
    clonedCurrentElement.append(defTitle);
    clonedCurrentElement.append(defTitleField);
    clonedCurrentElement.append(defContentField);
    clonedCurrentElement.append(subordinateContainer);
    clonedCurrentElement.append(subordinateResult);
    clonedCurrentElement.append(defDescriptionLabel);
    clonedCurrentElement.append(defDescriptionField);
    
    return clonedCurrentElement;
}

function makeRefTheorem(idindex)
{
    var clonedCurrentElement = $("<div></div>");
    var theoremCloseButton = $('<a class="msm_element_close" id="msm_theorem_element_closebutton-'+idindex+'" style="margin-top: 2%;" onclick="deleteRefElement(event)">x</a>');

    var theoremSelectMenu = $('<select name="msm_theoremref_type_dropdown-'+idindex+'" class="msm_unit_child_dropdown" id="msm_theoremref_type_dropdown-'+idindex+'">\n\
                                <option value="Theorem">Theorem</option>\n\
                                <option value="Proposition">Proposition</option>\n\
                                <option value="Lemma">Lemma</option>\n\
                                <option value="Corollary">Corollary</option>\n\
                            </select>');
    
    var theoremTitleContainer = $('<div class="msm_element_title_containers" id="msm_theoremref_title_container-'+idindex+'"><b style="margin-left: 31%;"> THEOREM </b></div>');   
    var theoremTitleHidden = $('<span style="visibility: hidden; display:block;">Drag here to move this element.</span>');     
                
    var theoremTitleField = $('<input class="msm_unit_child_title" id="msm_theoremref_title_input-'+idindex+'" name="msm_theoremref_title_input-'+idindex+'" placeholder=" Title of Theorem"/>');
            
    var theoremContentWrapper = $('<div class="msm_theoremref_content_containers" id="msm_theoremref_content_container-'+idindex+'-1"></div>');
            
    var theoremStatementWrapper = $('<div class="msm_theoremref_statement_containers" id="msm_theoremref_statement_container-'+idindex+'-1"></div>');
    
    var theoremContentTitleContainer = $('<div class="msm_theoremref_statement_title_containers" id="msm_theoremref_statement_title_container-'+idindex+'-1"><b> Theorem Content </b></div>');
    var theoremContentTitleHidden = $('<span style="visibility: hidden;">Drag here to move this element.</span>');
            
    var theoremContentField = $('<textarea class="msm_unit_child_content" id="msm_theoremref_content_input-'+idindex+'-1" name="msm_theoremref_content_input-'+idindex+'-1"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-theoremrefcontent'+idindex+'-1"></div>');

    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-theoremrefcontent'+idindex+'-1"></div>');
            
    var theoremPartWrapper = $('<div class="msm_theoremref_part_dropareas" id="msm_theoremref_part_droparea-'+idindex+'-1"></div>');
            
    var theoremChildButton = $('<input class="msm_theorem_child_buttons" id="msm_theoremref_child_button-'+idindex+'" type="button" onclick="addrefTheoremContent(event, \''+idindex+'-1\')" value="Add content"/>');
    var theoremPartButton = $('<input class="msm_theorem_part_buttons" id="msm_theoremref_part_button-'+idindex+'" type="button" onclick="addrefTheoremPart(event, \''+idindex+'-1\')" value="Add more parts"/>');
    var theoremDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_theoremref_description_label-"+idindex+"' for='msm_theoremref_description_input-"+idindex+"'>Description: </label>");
    var theoremDescriptionField = $("<input class='msm_child_description_inputs' id='msm_theoremref_description_input-"+idindex+"' name='msm_theoremref_description_input-"+idindex+"' placeholder='Insert description to search this element in future. '/>");
            
    clonedCurrentElement.attr("id", "copied_msm_theoremref-"+idindex);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
    theoremPartWrapper.append(theoremPartButton);
    theoremTitleContainer.append(theoremTitleHidden)
    theoremContentTitleContainer.append(theoremContentTitleHidden);
            
    theoremStatementWrapper.append(theoremContentTitleContainer);
    theoremStatementWrapper.append(theoremContentField); 
    theoremStatementWrapper.append(subordinateContainer);
    theoremStatementWrapper.append(subordinateResult);
    theoremStatementWrapper.append(theoremPartWrapper);
            
    theoremContentWrapper.append(theoremStatementWrapper);
    theoremContentWrapper.append(theoremChildButton);
            
    clonedCurrentElement.append(theoremCloseButton);
    clonedCurrentElement.append(theoremSelectMenu);
    clonedCurrentElement.append(theoremTitleField);
    clonedCurrentElement.append(theoremTitleContainer);    
    clonedCurrentElement.append(theoremContentWrapper);
    clonedCurrentElement.append(theoremDescriptionLabel);
    clonedCurrentElement.append(theoremDescriptionField);
            
    return clonedCurrentElement;
}

function makeRefComment(idindex)
{
    var clonedCurrentElement = $("<div></div>");
    var commentCloseButton = $('<a class="msm_element_close" id="msm_comment_element_closebutton-'+idindex+'" style="margin-top: 2%;" onclick="deleteRefElement(event);">x</a>');
    var commentSelectMenu = $('<select name="msm_commentref_type_dropdown-'+idindex+'" class="msm_unit_child_dropdown" id="msm_commentref_type_dropdown-'+idindex+'">\n\
                                <option value="Comment">Comment</option>\n\
                                <option value="Remark">Remark</option>\n\
                                <option value="Information">Information</option>\n\
                            </select>');
    var commentTitle = $("<span class='msm_element_title'><b style='margin-left: 30%;'> COMMENT </b></span>");
    var commentTitleField = $('<input class="msm_unit_child_title" id="msm_commentref_title_input-'+idindex+'" name="msm_commentref_title_input-'+idindex+'" placeholder=" Title of Comment"/>');
          
    var commentContentField = $('<textarea class="msm_unit_child_content" id="msm_commentref_content_input-'+idindex+'" name="msm_commentref_content_input-'+idindex+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-commentrefcontent'+idindex+'"></div>');

    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-commentrefcontent'+idindex+'"></div>');
    var commentDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_commentref_description_label-"+idindex+"' for='msm_commentref_description_input-"+idindex+"'>Description: </label>");
    var commentDescriptionField = $("<input class='msm_child_description_inputs' id='msm_commentref_description_input-"+idindex+"' name='msm_commentref_description_input-"+idindex+"' placeholder='Insert description to search this element in future. '/>");
            
    clonedCurrentElement.attr("id", "copied_msm_commentref-"+idindex);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
    clonedCurrentElement.append(commentCloseButton);
    clonedCurrentElement.append(commentSelectMenu);
    clonedCurrentElement.append(commentTitle);
    clonedCurrentElement.append(commentTitleField);
    clonedCurrentElement.append(commentContentField);
    clonedCurrentElement.append(subordinateContainer);
    clonedCurrentElement.append(subordinateResult);
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
    var newId = 1;
        
    if($("#msm_theoremref_content_container-"+idNumber).children("div").length > 0)
    {
        while(document.getElementById('msm_theoremref_statement_container-'+idNumber+'-'+newId) != null)
        {
            newId++;
        }
    }
    
    var theoremStatementWrapper = $('<div class="msm_theoremref_statement_containers" id="msm_theoremref_statement_container-'+idNumber+'-'+newId+'"></div>');
    var theoremCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
    
    var theoremContentTitleContainer = $('<div class="msm_theoremref_statement_title_containers" id="msm_theoremref_statement_title_container-'+idNumber+'-'+newId+'"><b style="margin-left: 30%;"> Theorem Content </b></div>');
    var theoremContentTitleHidden = $('<span style="visibility: hidden;">Drag here to move this element.</span>');
    var theoremContentField = $('<textarea class="msm_unit_child_content msm_theorem_content" id="msm_theoremref_content_input-'+idNumber+'-'+newId+'" name="msm_theoremref_content_input-'+idNumber+'-'+newId+'"/>');    
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-theoremrefcontent'+idNumber+'-'+newId+'"></div>');

    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-theoremrefcontent'+idNumber+'-'+newId+'"></div>');
   
    var param = idNumber+"-"+newId;
   
    var theoremPartButton = $('<input class="msm_theorem_part_buttons" id="msm_theoremref_part_button-'+idNumber+'-'+newId+'" type="button" onclick="addrefTheoremPart(event, \''+param+'\')" value="Add more parts"/>');
    var theoremPartWrapper = $('<div class="msm_theorem_part_dropareas" id="msm_theoremref_part_droparea-'+idNumber+'-'+newId+'"></div>');
            
    theoremPartWrapper.append(theoremPartButton);
    
    theoremContentTitleContainer.append(theoremContentTitleHidden);
            
    theoremStatementWrapper.append(theoremCloseButton);
    theoremStatementWrapper.append(theoremContentTitleContainer);
    theoremStatementWrapper.append(theoremContentField);
    theoremStatementWrapper.append(subordinateContainer);
    theoremStatementWrapper.append(subordinateResult);
   
    theoremStatementWrapper.append(theoremPartWrapper);
    
    $(theoremStatementWrapper).insertBefore("#"+event.target.id);    
    
    tinyMCE.init({
        mode:"exact",
        elements: "msm_theoremref_content_input-"+idNumber+'-'+newId,        
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
            
            $(this).find('.msm_theorem_content').each(function() {
                tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));          
                tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
            });
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#FFFFFF");
            
            tinyMCE.settings = {
                mode:"none",   
                plugins : "subordinate,autolink,lists,advlist,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
                width: "100%",
                height: "70%",
                theme: "advanced",
                theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
                theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,cleanup,help,code,|,insertdate,inserttime,preview",
                theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,advhr,|,ltr,rtl,|,subordinate",
                theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,forecolor,backcolor",
                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left",
                theme_advanced_statusbar_location : "bottom",
                skin : "o2k7",
                skin_variant : "silver"     
            };
            
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
    
    $("#msm_theoremref_statement_title_container-"+idNumber+'-'+newId).mouseover(function () {
        $(this).children("span").css({
            "visibility": "visible", 
            "color": "#4e6632", 
            "opacity": "0.5"
        });
    });
    $("#msm_theoremref_statement_title_container-"+idNumber+'-'+newId).mouseout(function () {
        $(this).children("span").css("visibility", "hidden");
    });
    $("#msm_theoremref_statement_title_container-"+idNumber+'-'+newId).mouseup(function () {
        $(this).children("span").css("visibility", "hidden");
    });
                
//    $("#msm_theoremref_content_container-"+idNumber).disableSelection();
}

function addrefTheoremPart(event, idNumber)
{  
    
    var newId = 1;
        
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
    
    tinyMCE.settings = {
        mode: "none",
        plugins : "subordinate,autolink,lists,advlist,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width: "100%",
        height: "70%",
        theme: "advanced",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,cleanup,help,code,|,insertdate,inserttime,preview",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,advhr,|,ltr,rtl,|,subordinate",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,forecolor,backcolor",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        skin : "o2k7",
        skin_variant : "silver"
    };
    
    if(tinymce.getInstanceById("msm_theoremref_part_content-"+idNumber+"-"+newId))
    {
        tinyMCE.execCommand('mceFocus', false, "msm_theoremref_part_content-"+idNumber+"-"+newId);          
        tinymce.execCommand('mceRemoveControl', true, "msm_theoremref_part_content-"+idNumber+"-"+newId);
    }
        
    tinymce.execCommand("mceAddControl", false, "msm_theoremref_part_content-"+idNumber+"-"+newId);
    
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
            $(this).find('.msm_theorem_content').each(function() {
                tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));          
                tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
            });
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#FFFFFF");
            
            tinyMCE.settings = {
                mode:"none",   
                plugins : "subordinate,autolink,lists,advlist,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
                width: "100%",
                height: "70%",
                theme: "advanced",
                theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
                theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,cleanup,help,code,|,insertdate,inserttime,preview",
                theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,advhr,|,ltr,rtl,|,subordinate",
                theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,forecolor,backcolor",
                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left",
                theme_advanced_statusbar_location : "bottom",
                skin : "o2k7",
                skin_variant : "silver"     
            };
            
            // if there are children in intro element, need to refresh the ifram of its editors
            $(this).find('.msm_theorem_content').each(function() {                 
                tinyMCE.execCommand("mceAddControl", true, $(this).attr("id"));
                $(this).sortable("refresh");
            });
        }
    });   
    
    $("#msm_theoremref_part_title_container-"+idNumber+'-'+newId).mouseover(function () {
        $(this).children("span").css({
            "visibility": "visible", 
            "color": "#4e6632", 
            "opacity": "0.5"
        });
    });
    $("#msm_theoremref_part_title_container-"+idNumber+'-'+newId).mouseout(function () {
        $(this).children("span").css("visibility", "hidden");
    });
    $("#msm_theoremref_part_title_container-"+idNumber+'-'+newId).mouseup(function () {
        $(this).children("span").css("visibility", "hidden");
    });
                
}