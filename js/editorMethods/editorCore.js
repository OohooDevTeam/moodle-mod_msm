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


/* 
 * This JS file contains the core functions that allows the editor to operate the way it does.
 * It is responsible for the addition of the structural elements that makes up the composition, saving of each section of composition,
 * editing of each section, reset the editor and empty the content area...etc
 */

// variable that gives an unique id number for all the structural element in the editor
var _index = 0;

// variable that gives unique id number to each image of the editor


/**
 * This function is activated when user drags one of the structural elememts on the very left side of the panel to middle panel.
 * It adds appropriate fields for the users to fill out for def/theorem/comments/info/content/media and images.
 */
function processDroppedChild(e, droppedId)
{ 
    var clonedCurrentElement = $("<div></div>");
    
    _index++;
    var currentContentid = 0;
    
    switch(droppedId)
    {
        case "msm_def":
            var defCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event);">x</a>');
            var defSelectMenu = $('<select name="msm_def_type_dropdown-'+_index+'" class="msm_unit_child_dropdown" id="msm_def_type_dropdown-'+_index+'">\n\
                                <option value="Notation">Notation</option>\n\
                                <option value="Definition">Definition</option>\n\
                                <option value="Agreement">Agreement</option>\n\
                                <option value="Convention">Convention</option>\n\
                                <option value="Axiom">Axiom</option>\n\
                                <option value="Terminology">Terminology</option>\n\
                            </select>');
            var defTitle = $("<span class='msm_element_title'><b> DEFINITION </b></span>");
            var defTitleField = $('<input class="msm_unit_child_title" id="msm_def_title_input-'+_index+'" name="msm_def_title_input-'+_index+'" placeholder=" Title of Definition"/>');
          
            var defContentField = $('<textarea class="msm_unit_child_content" id="msm_def_content_input-'+_index+'" name="msm_def_content_input-'+_index+'"/>');
            var defDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_def_description_label-"+_index+"' for='msm_def_descripton_input-"+_index+"'>Description: </label>");
            var defDescriptionField = $("<input class='msm_child_description_inputs' id='msm_def_descripton_input-"+_index+"' name='msm_def_descripton_input-"+_index+"' placeholder='Insert description to search this element in future. '/>");
            var defAssoMenu = $('<div class="msm_associate_optionarea"><b> Choose an associated information: </b>\n\
                            <select name="msm_def_associate_dropdown-'+_index+'" class="msm_associated_dropdown" id="msm_def_associate_dropdown-'+_index+'" onchange="processAssociate(event);">\n\
                                <option value="None">None</option>\n\
                                <option value="Info">Quick Info</option>\n\
                                <option value="Comment">Comment</option>\n\
                                <option value="Explanation">Explanation</option>\n\
                                <option value="Example">Example</option>\n\
                                <option value="Illustration">Illustration</option>\n\
                            </select></div>');        
            
            clonedCurrentElement.attr("id", "copied_msm_def-"+_index);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
            clonedCurrentElement.append(defCloseButton);
            clonedCurrentElement.append(defSelectMenu);
            clonedCurrentElement.append(defTitle);
            clonedCurrentElement.append(defTitleField);
            clonedCurrentElement.append(defContentField);
            clonedCurrentElement.append(defDescriptionLabel);
            clonedCurrentElement.append(defDescriptionField);
            clonedCurrentElement.append(defAssoMenu);
            clonedCurrentElement.appendTo('#msm_child_appending_area');
            
            currentContentid = 'msm_def_content_input-'+_index;
            break;
        
        case "msm_theorem":
            var theoremCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');

            var theoremSelectMenu = $('<select name="msm_theorem_type_dropdown-'+_index+'" class="msm_unit_child_dropdown" id="msm_theorem_type_dropdown-'+_index+'">\n\
                                <option value="Theorem">Theorem</option>\n\
                                <option value="Proposition">Proposition</option>\n\
                                <option value="Lemma">Lemma</option>\n\
                                <option value="Corollary">Corollary</option>\n\
                            </select>');
                
            var theoremTitle = $("<span class='msm_element_title'><b> THEOREM </b></span>");
            var theoremTitleField = $('<input class="msm_unit_child_title" id="msm_theorem_title_input-'+_index+'" name="msm_theorem_title_input-'+_index+'" placeholder=" Title of Theorem"/>');
            var theoremContentField = $('<textarea class="msm_unit_child_content" id="msm_theorem_content_input-'+_index+'" name="msm_theorem_content_input-'+_index+'"/>');
            var theoremDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_theorem_description_label-"+_index+"' for='msm_theorem_descripton_input-"+_index+"'>Description: </label>");
            var theoremDescriptionField = $("<input class='msm_child_description_inputs' id='msm_theorem_descripton_input-"+_index+"' name='msm_theorem_descripton_input-"+_index+"' placeholder='Insert description to search this element in future. '/>");
            var theoremAssoMenu = $('<div class="msm_associate_optionarea"><b> Choose an associated information: </b>\n\
                            <select name="msm_theorem_associate_dropdown-'+_index+'" class="msm_associated_dropdown" id="msm_theorem_associate_dropdown-'+_index+'" onchange="processAssociate(event);">\n\
                                <option value="None">None</option>\n\
                                <option value="Info">Quick Info</option>\n\
                                <option value="Comment">Comment</option>\n\
                                <option value="Explanation">Explanation</option>\n\
                                <option value="Example">Example</option>\n\
                                <option value="Illustration">Illustration</option>\n\
                                <option value="Proof">Proof</option>\n\
                            </select></div>');
        
            clonedCurrentElement.attr("id", "copied_msm_theorem-"+_index);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
            clonedCurrentElement.append(theoremCloseButton);
            clonedCurrentElement.append(theoremSelectMenu);
            clonedCurrentElement.append(theoremTitle);
            clonedCurrentElement.append(theoremTitleField);
            clonedCurrentElement.append(theoremContentField);
            clonedCurrentElement.append(theoremDescriptionLabel);
            clonedCurrentElement.append(theoremDescriptionField);
            clonedCurrentElement.append(theoremAssoMenu);
            clonedCurrentElement.appendTo('#msm_child_appending_area');
            
            currentContentid = 'msm_theorem_content_input-'+_index;
            break;
            
        case "msm_comment":
            var commentCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event);">x</a>');
            var commentSelectMenu = $('<select name="msm_comment_type_dropdown-'+_index+'" class="msm_unit_child_dropdown" id="msm_comment_type_dropdown-'+_index+'">\n\
                                <option value="Comment">Comment</option>\n\
                                <option value="Remark">Remark</option>\n\
                                <option value="Information">Information</option>\n\
                            </select>');
            var commentTitle = $("<span class='msm_element_title'><b> COMMENT </b></span>");
            var commentTitleField = $('<input class="msm_unit_child_title" id="msm_comment_title_input-'+_index+'" name="msm_comment_title_input-'+_index+'" placeholder=" Title of Comment"/>');
          
            var commentContentField = $('<textarea class="msm_unit_child_content" id="msm_comment_content_input-'+_index+'" name="msm_comment_content_input-'+_index+'"/>');
            var commentDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_comment_description_label-"+_index+"' for='msm_comment_descripton_input-"+_index+"'>Description: </label>");
            var commentDescriptionField = $("<input class='msm_child_description_inputs' id='msm_comment_descripton_input-"+_index+"' name='msm_comment_descripton_input-"+_index+"' placeholder='Insert description to search this element in future. '/>");
            var commentAssoMenu = $('<div class="msm_associate_optionarea"><b> Choose an associated information: </b>\n\
                            <select name="msm_comment_associate_dropdown-'+_index+'" class="msm_associated_dropdown" id="msm_def_associate_dropdown-'+_index+'" onchange="processAssociate(event);">\n\
                                <option value="None">None</option>\n\
                                <option value="Info">Quick Info</option>\n\
                                <option value="Comment">Comment</option>\n\
                                <option value="Explanation">Explanation</option>\n\
                                <option value="Example">Example</option>\n\
                                <option value="Illustration">Illustration</option>\n\
                            </select></div>');        
            
            clonedCurrentElement.attr("id", "copied_msm_comment-"+_index);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
            clonedCurrentElement.append(commentCloseButton);
            clonedCurrentElement.append(commentSelectMenu);
            clonedCurrentElement.append(commentTitle);
            clonedCurrentElement.append(commentTitleField);
            clonedCurrentElement.append(commentContentField);
            clonedCurrentElement.append(commentDescriptionLabel);
            clonedCurrentElement.append(commentDescriptionField);
            clonedCurrentElement.append(commentAssoMenu);
            clonedCurrentElement.appendTo('#msm_child_appending_area');
            
            currentContentid = 'msm_comment_content_input-'+_index;
            break;
            
        case "msm_intro":
            var introCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
            var introTitle = $("<span class='msm_element_title'><b> INTRODUCTION </b></span><br><br>");        
            
            var introTitleLabel = $('<label class="msm_unit_intro_title_labels" id="msm_intro_title_label-'+_index+'" for="msm_intro_title_input-'+_index+'">Title:</label>');
            var introTitleField = $('<input class="msm_unit_intro_title" id="msm_intro_title_input-'+_index+'" name="msm_intro_title_input-'+_index+'" placeholder="Optional Title for the introduction"/>');     

            var introContentField = $('<textarea class="msm_unit_child_content" id="msm_intro_content_input-'+_index+'" name="msm_intro_content_input-'+_index+'" placeholder=" Need to add moodle form here?"/>');
            
            var introChildContainer = $("<div id='msm_intro_child_container'></div>");
            var introChildButton = $('<input class="msm_intro_child_buttons" id="msm_intro_child_button-'+_index+'" type="button" onclick="addIntroContent('+_index+')" value="Add additional content"/>');
            
            clonedCurrentElement.attr("id", "copied_msm_intro-"+_index);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
            clonedCurrentElement.append(introCloseButton);
            clonedCurrentElement.append(introTitle); 
            clonedCurrentElement.append(introTitleLabel);
            clonedCurrentElement.append(introTitleField);
            clonedCurrentElement.append(introContentField);
            clonedCurrentElement.append(introChildContainer);
            clonedCurrentElement.append(introChildButton);
            clonedCurrentElement.appendTo('#msm_child_appending_area');
            
            currentContentid = 'msm_intro_content_input-'+_index;
            break;
            
        case "msm_body":
            var bodyCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
            var bodyTitle = $("<span class='msm_element_title'><b> CONTENT </b></span><br><br>");
            var bodyTitleLabel = $('<label class="msm_unit_body_title_labels" id="msm_body_title_label-'+_index+'" for="msm_body_title_input-'+_index+'">Title:</label>');
            var bodyTitleField = $('<input class="msm_unit_body_title" id="msm_body_title_input-'+_index+'" name="msm_body_title_input-'+_index+'" placeholder="Optional Title for this content"/>');  
            var bodyContentField = $('<textarea class="msm_unit_child_content" id="msm_body_content_input-'+_index+'" name="msm_body_content_input-'+_index+'" placeholder=" Need to add moodle form here?"/>');

            clonedCurrentElement.attr("id", "copied_msm_body-"+_index);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            clonedCurrentElement.append(bodyCloseButton);
            clonedCurrentElement.append(bodyTitle);
            clonedCurrentElement.append(bodyTitleLabel);
            clonedCurrentElement.append(bodyTitleField);
            clonedCurrentElement.append(bodyContentField);
            clonedCurrentElement.appendTo('#msm_child_appending_area');
            
            currentContentid = 'msm_body_content_input-'+_index;
            break;       
            
    }
    
    
    if($('#msm_editor_save').attr("disabled"))
    {
        $('#msm_editor_save').removeAttr('disabled');
    }
    
    // has to be exact mode b/c if it is initiated twice, the editor function gives it a random id and breaks the save method
    tinyMCE.init({
        mode:"exact",
        elements: currentContentid,
        plugins : "autolink,lists,advlist,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width: "96%",
        height: "70%",
        theme: "advanced",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,cleanup,help,code,|,insertdate,inserttime,preview",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,advhr,|,ltr,rtl",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,forecolor,backcolor",
        //        theme_advanced_toolbar_location : "external",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        skin : "o2k7",
        skin_variant : "silver"
    });
    
    
    $("#msm_child_appending_area").sortable({
        appendTo: "#msm_child_appending_area",
        connectWith: "#msm_child_appending_area",
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
            
            $(this).find('.msm_intro_child_contents').each(function() {
                tinyMCE.execCommand("mceRemoveControl", false, $(this).attr("id")); 
            });
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#FFFFFF");
            
            $(this).find('.msm_unit_child_content').each(function() {
                tinyMCE.execCommand("mceAddControl", false, $(this).attr("id")); 
                $(this).sortable("refresh");
            });
            
            // if there are children in intro element, need to refresh the ifram of its editors
            $(this).find('.msm_intro_child_contents').each(function() {
                tinyMCE.execCommand("mceAddControl", false, $(this).attr("id")); 
                $(this).sortable("refresh");
            });
        }
    });    
                
    $("#msm_child_appending_area").disableSelection();
}


/**
 * to save contents created in middle editor panel
 *  --> diplay change: from filled in content --> maintain same content in middle but with edit button instead of  & thumbnail of composed unit added to tree in right panel
 *  --> disable drag&drop/resize...etc
 */
function saveUnit()
{
    $("#msm_editor_save").ready(function() {
        var children =  document.getElementById('msm_child_appending_area').childNodes;
        
        var idString = "";
        for(var i=0; i<children.length-1; i++)
        {
            if(children[i].tagName == "DIV")
            {
                idString += children[i].id + ",";
            }           
        }
        if(children[children.length-1].tagName == "DIV")
        {
            idString += children[children.length-1].id;
        }
        
                    
        $("#msm_child_order").val(idString);

        var titleinput = $("#msm_unit_title").val();

        if((titleinput == null)||(titleinput == ""))
        {
            $("<div class='dialogs' id='msm_emptyMsmTitle'> Please specify the title of this unit. </div>").appendTo("#msm_unit_title");

            $("#msm_emptyMsmTitle").dialog({
                modal: true,
                buttons: {
                    Ok: function() {
                        $("#msm_unit_title").css("border-color", "#FFA500");
                        $(this).dialog("close");
                    }
                }
            });   
        }
        else
        {
            $("#msm_editor_save").remove();
            $("<button class='msm_editor_buttons' id='msm_editor_edit' type='button' onclick='editUnit()'> Edit </button>").appendTo("#msm_editor_middle");
            $("#msm_editor_reset").attr("disabled", "disabled");
        }

    });
//AJAX call to php file/function that will put appropriate info into db tables   
}

/**
 * need to enable drag/drop/resize again... and also enable edit button
 *
 */
function editUnit()
{
    
}

/**
 * needs to ask if the user wants to save the content if save button has not been pressed, if was saved, then call saveUnit and close dialog,
 * else if not save then empty out content in middle, save button disabled while reset is abled
 */
function resetUnit()
{    
    $('#msm_editor_reset').ready(function() {
        $("<div class='dialogs' id='msm_resetComposition'> <span class='ui-icon ui-icon-alert' style='float: left; margin: 0 7px 20px 0;'></span>Are you sure you wish to discard the current composition? </div>").appendTo('#msm_editor_middle');
        $( "#msm_resetComposition" ).dialog({
            resizable: false,
            height:180,
            modal: true,
            buttons: {
                "Yes": function() {
                    // empty out the contents in the middle editting area and diable the save button
                    $('#msm_child_appending_area').empty();
                    $('#msm_editor_save').attr("disabled", "disabled");
                    $( this ).dialog( "close" );
                },
                "No": function() {
                    $( this ).dialog( "close" );                   
                }
            }
        });
    });
}

/**
 * This method is used to delete elements that were added to the middle panel and is triggered by msm_element_close button in each of the 
 * structural elements dragged from the left column.
 * 
 */
function deleteElement(e)
{
    var currentElement = e.target.parentElement.id;
    $("<div class='dialogs' id='msm_deleteComposition'> <span class='ui-icon ui-icon-alert' style='float: left; margin: 0 7px 20px 0;'></span>Are you sure you wish to delete this element from the composition? </div>").appendTo('#'+currentElement);
    $( "#msm_deleteComposition" ).dialog({
        resizable: false,
        height:180,
        modal: true,
        buttons: {
            "Yes": function() {
                $('#'+currentElement).empty().remove();
                    
                // if deleted the last item then disable the save button
                if($("#msm_child_appending_area").children().length < 1)
                {
                    $("#msm_editor_save").attr("disabled", "disabled");
                }
                $( this ).dialog( "close" );
            },
            "No": function() {
                $( this ).dialog( "close" );                   
            }
        }
    });
}

/*
 * This method is triggered by the add content button in introduction.  It adds a div identical to content in
 * structural element for user to be able to extend the intro into sections...etc with it's own subtitles to section them off. 
 * All contents added in here belongs to block in intro section.
 */
function addIntroContent(idNumber)
{
    var childNumber = 0;
    
    $(".msm_intro_child").each(function() {
        childNumber++;
    })
    
    var newId = idNumber + childNumber;
    
    var introChildDiv = document.createElement("div");
    introChildDiv.id = "msm_intro_child_div-"+newId;
    introChildDiv.className = "msm_intro_child";
    
    var introCloseButton = document.createElement("a");
    introCloseButton.className = "msm_element_close";
    introCloseButton.onclick = function(e) {
        var currentElement = e.target.parentElement.id;
        $("<div class='dialogs' id='msm_deleteIntroChild'> <span class='ui-icon ui-icon-alert' style='float: left; margin: 0 7px 20px 0;'></span>Are you sure you wish to delete this content from introduction? </div>").appendTo('#'+currentElement);
        $( "#msm_deleteIntroChild" ).dialog({
            resizable: false,
            height:180,
            modal: true,
            buttons: {
                "Yes": function() {
                    $('#'+currentElement).empty().remove();
                    $( this ).dialog( "close" );
                },
                "No": function() {
                    $( this ).dialog( "close" );                   
                }
            }
        });
    };
    
    var introCloseButtonText = document.createTextNode("x");
    introCloseButton.appendChild(introCloseButtonText);    
    
    var introChildTitleLabel = document.createElement("label");
    introChildTitleLabel.id = "msm_intro_child_title_label-"+newId;
    introChildTitleLabel.className = "msm_intro_child_title_labels";
    introChildTitleLabel.setAttribute("for", "msm_intro_child_title-"+newId);
    
    var titleLabel = document.createTextNode("Title:");
    introChildTitleLabel.appendChild(titleLabel);
    
    var introChildTitle = document.createElement("input");
    introChildTitle.id = "msm_intro_child_title-"+newId;
    introChildTitle.className = "msm_intro_child_titles";
    introChildTitle.name = "msm_intro_child_title-"+newId;
    introChildTitle.setAttribute("placeholder", "Optional Title for the Content");
    
    var introChildContent = document.createElement("textarea");
    introChildContent.id = "msm_intro_child_content-"+newId;
    introChildContent.className = "msm_intro_child_contents";
    introChildContent.name = "msm_intro_child_content-"+newId;
    
    introChildDiv.appendChild(introCloseButton);
    introChildDiv.appendChild(introChildTitleLabel);
    introChildDiv.appendChild(introChildTitle);
    introChildDiv.appendChild(introChildContent);
    
    $(introChildDiv).appendTo("#msm_intro_child_container");
    
    //    $(introChildDiv).insertBefore("#msm_intro_child_button-"+idNumber);
    
    tinyMCE.init({
        mode:"exact",
        elements: "msm_intro_child_content-"+newId,                    
        plugins : "autolink,lists,advlist,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width: "96%",
        height: "70%",
        theme: "advanced",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,cleanup,help,code,|,insertdate,inserttime,preview",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,advhr,|,ltr,rtl",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,forecolor,backcolor",
        //        theme_advanced_toolbar_location : "external",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        skin : "o2k7",
        skin_variant : "silver"
    });
    
    $("#msm_intro_child_container").sortable({
        appendTo: "msm_intro_child_container",
        connectWith: "msm_intro_child_container",
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
            $(this).find('.msm_intro_child_contents').each(function() {
                tinyMCE.execCommand("mceRemoveControl", false, $(this).attr("id")); 
            });
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#FFFFFF");
            
            // if there are children in intro element, need to refresh the ifram of its editors
            $(this).find('.msm_intro_child_contents').each(function() {
                tinyMCE.execCommand("mceAddControl", false, $(this).attr("id")); 
                $(this).sortable("refresh");
            });
        }
    });    
                
    $("msm_intro_child_container").disableSelection();
     
}
