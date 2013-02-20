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
    _index++;
    var currentContentid = 0;    
    var currenttheoremPart = 0;
    
    var element;
   
    switch(droppedId)
    {
        case "msm_def":
            element = makeDefinition();
            element.appendTo('#msm_child_appending_area');
            
            currentContentid = 'msm_def_content_input-'+_index;
            break;
        
        case "msm_theorem":
            element = makeTheorem();
            element.appendTo('#msm_child_appending_area');
            
            currentContentid = 'msm_theorem_content_input-'+_index+'-1';
            break;
            
        case "msm_comment":
            element = makeComment();
            element.appendTo('#msm_child_appending_area');
            
            currentContentid = 'msm_comment_content_input-'+_index;
            break;
            
        case "msm_intro":
            var clonedCurrentElement = $("<div></div>");
            // additional code to only allow for one intro in unit
            var isPresent = false;
            
            $("#msm_child_appending_area > div").each(function() {
                var match = this.id.match(/copied_msm_intro-/); 
                if(match)
                {
                    isPresent = true;
                    return (false);
                }
            });
            
            if(!isPresent)
            {
                var introCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
                var introTitle = $('<div class="msm_element_title_containers" id="msm_element_title_container-'+_index+'"><b style="margin-left: 43%; margin-right: 0%; margin-top: 2%; margin-bottom: 2%;"> INTRODUCTION </b></div>'); 
                var introTitlehidden = $('<span style="visibility: hidden;">     Drag here to move this element.</span>');

                var introTitleContainer = $("<div style='margin-top: 2%;'></div>")
                var introTitleLabel = $('<label class="msm_unit_intro_title_labels" id="msm_intro_title_label-'+_index+'" for="msm_intro_title_input-'+_index+'">Title:</label>');
                var introTitleField = $('<input class="msm_unit_intro_title" id="msm_intro_title_input-'+_index+'" name="msm_intro_title_input-'+_index+'" placeholder="Optional Title for the introduction"/>');     

                var introContentField = $('<textarea class="msm_unit_child_content" id="msm_intro_content_input-'+_index+'" name="msm_intro_content_input-'+_index+'" placeholder=" Need to add moodle form here?"/>');
                var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-introcontent'+_index+'"></div>');
                var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-introcontent'+_index+'"></div>');
            
                var introChildContainer = $("<div id='msm_intro_child_container'></div>");
                var introChildButton = $('<input class="msm_intro_child_buttons" id="msm_intro_child_button-'+_index+'" type="button" onclick="addIntroContent('+_index+')" value="Add additional content"/>');
            
                clonedCurrentElement.attr("id", "copied_msm_intro-"+_index);
                clonedCurrentElement.attr("class", "copied_msm_structural_element");
                clonedCurrentElement.attr("style", "padding-top: 2%;");
                
                introTitleContainer.append(introTitleLabel);
                introTitleContainer.append(introTitleField);
                
                introTitle.append(introTitlehidden);
            
                clonedCurrentElement.append(introCloseButton);
                clonedCurrentElement.append(introTitle); 
                clonedCurrentElement.append(introTitleContainer);
                clonedCurrentElement.append(introContentField);
                clonedCurrentElement.append(subordinateContainer);
                clonedCurrentElement.append(subordinateResult);
                clonedCurrentElement.append(introChildContainer);
                clonedCurrentElement.append(introChildButton);
                clonedCurrentElement.appendTo('#msm_child_appending_area');
            
                currentContentid = 'msm_intro_content_input-'+_index;
            }
            else
            {
                $("<div class='dialogs' id='msm_presentIntro' style='display:none;'> There is already an introduction present in current section. </div>").appendTo("#msm_child_appending_area");
                
                $("#msm_presentIntro").dialog({
                    modal: true,
                    buttons: {
                        Ok: function() {
                            $(this).dialog("close");
                        }
                    }
                }); 
            }
           
            break;
            
        case "msm_body":
            var clonedCurrentElement = $("<div></div>");
            var bodyCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
            var bodyTitle = $('<div class="msm_element_title_containers" id="msm_element_title_container-'+_index+'"><b style="margin-left: 45%; margin-right: 0%; margin-top: 2%; margin-bottom: 2%;"> CONTENT </b></div>'); 
            var bodyTitlehidden = $('<span style="visibility: hidden;">     Drag here to move this element.</span>');
            var bodyTitleContainer = $("<div style='margin-top: 2%;'></div>");
            var bodyTitleLabel = $('<label class="msm_unit_body_title_labels" id="msm_body_title_label-'+_index+'" for="msm_body_title_input-'+_index+'">Title:</label>');
            var bodyTitleField = $('<input class="msm_unit_body_title" id="msm_body_title_input-'+_index+'" name="msm_body_title_input-'+_index+'" placeholder="Optional Title for this content"/>');  
            var bodyContentField = $('<textarea class="msm_unit_child_content" id="msm_body_content_input-'+_index+'" name="msm_body_content_input-'+_index+'" placeholder=" Need to add moodle form here?"/>');
            var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-bodycontent'+_index+'"></div>');
            var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-bodycontent'+_index+'"></div>');

            bodyTitleContainer.append(bodyTitleLabel);
            bodyTitleContainer.append(bodyTitleField);
            
            bodyTitle.append(bodyTitlehidden);

            clonedCurrentElement.attr("id", "copied_msm_body-"+_index);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            clonedCurrentElement.attr("style", "padding-top: 2%;");
            clonedCurrentElement.append(bodyCloseButton);
            clonedCurrentElement.append(bodyTitle);
            clonedCurrentElement.append(bodyTitleContainer);
            clonedCurrentElement.append(bodyContentField);
            clonedCurrentElement.append(subordinateContainer);
            clonedCurrentElement.append(subordinateResult);
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
        plugins : "subordinate,autolink,lists,advlist,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width: "100%",
        height: "70%",
        theme: "advanced",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,advhr,|,ltr,rtl,|,subordinate",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,forecolor,backcolor",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        skin : "o2k7",
        skin_variant : "silver"
    });
    
    tinyMCE.init({
        mode:"exact",
        elements: currenttheoremPart,
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
            
            $(this).find('.msm_unit_child_content').each(function() {                         
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    tinymce.execCommand("mceAddControl", false, $(this).attr("id")); 
                    $(this).sortable("refresh");
                } 
            });
                            
            //             if there are children in intro element, need to refresh the ifram of its editors
            $(this).find('.msm_intro_child_contents').each(function() {
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    tinymce.execCommand("mceAddControl", false, $(this).attr("id")); 
                    $(this).sortable("refresh");
                }
            });
                            
            $(this).find('.msm_info_titles').each(function() {
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    tinymce.execCommand("mceAddControl", false, $(this).attr("id")); 
                    $(this).sortable("refresh");
                }
            });
            $(this).find('.msm_info_contents').each(function() {
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    tinymce.execCommand("mceAddControl", false, $(this).attr("id")); 
                    $(this).sortable("refresh");
                }
            });
        }
    });    
    
    $("#msm_element_title_container-"+_index).mouseover(function () {
        $(this).children("span").css({
            "visibility": "visible", 
            "color": "#4e6632", 
            "opacity": "0.5",
            "cursor": "move"
        });
    });
    $("#msm_element_title_container-"+_index).mouseout(function () {
        $(this).children("span").css("visibility", "hidden");
    });
    $("#msm_element_title_container-"+_index).mouseup(function () {
        $(this).children("span").css("visibility", "hidden");
    });
    
    $("#msm_theorem_statement_title_container-"+_index+"-1").mouseover(function () {
        $(this).children("span").css({
            "visibility": "visible", 
            "color": "#4e6632", 
            "opacity": "0.5",
            "cursor": "move"
        });
    });
    $("#msm_theorem_statement_title_container-"+_index+"-1").mouseout(function () {
        $(this).children("span").css("visibility", "hidden");
    });
    $("#msm_theorem_statement_title_container-"+_index+"-1").mouseup(function () {
        $(this).children("span").css("visibility", "hidden");
    });               
                
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
    
    if(currentElement == '')
    {
        // in firefox, sometimes the target ends up being the textnode "x" instead of the <a> element
        currentElement = e.target.parentElement.parentElement.id;
    }
    
    $("#"+currentElement+" textarea").each(function() {
        if(tinymce.getInstanceById($(this).attr("id")) != null)
        {
            tinymce.execCommand('mceFocus', false, $(this).attr("id")); 
            tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
        }
    });
    //    
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
                $("#"+currentElement+" textarea").each(function() {
                    if(tinymce.getInstanceById($(this).attr("id")) == null)
                    {
                        tinymce.execCommand('mceAddControl', true, $(this).attr("id"));
                    }
                });
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
    
    var dragArea =document.createElement("div");
    var dragText = document.createElement("span");
    var dragTextNode = document.createTextNode("Drag here to move the element");
    
    dragText.setAttribute("style", "visibility: hidden");
    
    dragArea.className = "msm_intro_child_dragareas";
    dragArea.id = "msm_intro_child_dragarea-"+newId;
    dragText.appendChild(dragTextNode);
    dragArea.appendChild(dragText);
        
    var titleContainer = document.createElement("div");
    
    var titleLabel = document.createTextNode("Title:");
    introChildTitleLabel.appendChild(titleLabel);
    
    var introChildTitle = document.createElement("input");
    introChildTitle.id = "msm_intro_child_title-"+newId;
    introChildTitle.className = "msm_intro_child_titles";
    introChildTitle.name = "msm_intro_child_title-"+newId;
    introChildTitle.setAttribute("placeholder", "Optional Title for the Content");
    
    titleContainer.appendChild(titleLabel);
    titleContainer.appendChild(introChildTitle);
    
    var introChildContent = document.createElement("textarea");
    introChildContent.id = "msm_intro_child_content-"+newId;
    introChildContent.className = "msm_intro_child_contents";
    introChildContent.name = "msm_intro_child_content-"+newId;
    
    var subordinateContainer = document.createElement("div");
    subordinateContainer.id = "msm_subordinate_container-introchild"+newId;
    subordinateContainer.className = "msm_subordinate_containers";
    
    var subordinateResult = document.createElement("div");
    subordinateResult.id = "msm_subordinate_result_container-introchild"+newId;
    subordinateResult.className = "msm_subordiante_result_containers";
    
    introChildDiv.appendChild(introCloseButton);
    introChildDiv.appendChild(dragArea);

    introChildDiv.appendChild(titleContainer);
    introChildDiv.appendChild(introChildContent);
    introChildDiv.appendChild(subordinateContainer);
    introChildDiv.appendChild(subordinateResult);
    
    $(introChildDiv).appendTo("#msm_intro_child_container");    
    
    tinyMCE.init({
        mode:"exact",
        elements: "msm_intro_child_content-"+newId,   
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
            $(this).find('.msm_intro_child_contents').each(function() {
                tinyMCE.execCommand("mceAddControl", false, $(this).attr("id")); 
                $(this).sortable("refresh");
            });
        }
    });    
    
    $("#msm_intro_child_dragarea-"+newId).mouseover(function () {
        $(this).children("span").css({
            "visibility": "visible", 
            "color": "#4e6632", 
            "opacity": "0.5",
            "cursor": "move"
        });
    });
    $("#msm_intro_child_dragarea-"+newId).mouseout(function () {
        $(this).children("span").css("visibility", "hidden");
    });
    $("#msm_intro_child_dragarea-"+newId).mouseup(function () {
        $(this).children("span").css("visibility", "hidden");
    });
     
}

function addTheoremContent(event, idNumber)
{
    
    var newId = 1;
        
    if($("#msm_theorem_content_container-"+idNumber).children("div").length > 0)
    {
        while(document.getElementById('msm_theorem_statement_container-'+idNumber+'-'+newId) != null)
        {
            newId++;
        }
    }
    
    var theoremStatementWrapper = $('<div class="msm_theorem_statement_containers" id="msm_theorem_statement_container-'+idNumber+'-'+newId+'"></div>');
    var theoremCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
    var theoremContentTitleContainer = $('<div class="msm_theorem_statement_title_containers" id="msm_theorem_statement_title_container-'+idNumber+'-'+newId+'"><b> Theorem Content </b></div>');
    var theoremContentTitleHidden = $('<span style="visibility: hidden;">Drag here to move this element.</span>');
    var theoremContentField = $('<textarea class="msm_unit_child_content msm_theorem_content" id="msm_theorem_content_input-'+idNumber+'-'+newId+'" name="msm_theorem_content_input-'+idNumber+'-'+newId+'"/>');    
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-theoremcontent'+idNumber+'-'+newId+'"></div>');

    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-theoremcontent'+idNumber+'-'+newId+'"></div>');
   
       
    var theoremPartButton = $('<input class="msm_theorem_part_buttons" id="msm_theorem_part_button-'+idNumber+'-'+newId+'" type="button" onclick="addTheoremPart(event, \''+idNumber+'-'+newId+'\')" value="Add more parts"/>');
    var theoremPartWrapper = $('<div class="msm_theorem_part_dropareas" id="msm_theorem_part_droparea-'+idNumber+'-'+newId+'"></div>');
            
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
        elements: "msm_theorem_content_input-"+idNumber+'-'+newId,   
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
    });
    
    $("#msm_theorem_content_container-"+idNumber).sortable({
        appendTo: "msm_theorem_content_container-"+idNumber,
        connectWith: "msm_theorem_content_container-"+idNumber,
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
            
            var id = $(this).attr("id");
            
            $("#"+id+" textarea").each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) == null)
                {
                    tinymce.execCommand('mceAddControl', true, $(this).attr("id"));
                    $(this).sortable("refresh");
                }
            });
        }
    });    
    
    $("#msm_theorem_statement_title_container-"+idNumber+'-'+newId).mouseover(function () {
        $(this).children("span").css({
            "visibility": "visible", 
            "color": "#4e6632", 
            "opacity": "0.5",
            "cursor": "move"
        });
    });
    $("#msm_theorem_statement_title_container-"+idNumber+'-'+newId).mouseout(function () {
        $(this).children("span").css("visibility", "hidden");
    });
    $("#msm_theorem_statement_title_container-"+idNumber+'-'+newId).mouseup(function () {
        $(this).children("span").css("visibility", "hidden");
    });
                
}

function addTheoremPart(event, idNumber)
{
    var newId = 1;    
        
    if($("#msm_theorem_part_droparea-"+idNumber).children("div").length > 0)
    {
        while(document.getElementById('msm_theorem_part_container-'+idNumber+'-'+newId) != null)
        {
            newId++;
        }
    }
    
    var theoremPartContainer = $('<div class="msm_theorem_child" id="msm_theorem_part_container-'+idNumber+'-'+newId+'"></div>');
    
    var theoremCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
    
    var theoremPartTitleContainer = $('<div class="msm_theorem_part_title_containers" id="msm_theorem_part_title_container-'+idNumber+'-'+newId+'"></div>');
    var theoremPartTitleHidden = $('<span style="visibility: hidden;">Drag here to move this element.</span>');
    
    var theoremPartLabel = $('<label class="msm_theorem_part_tlabel" for="msm_theorem_part_title-'+idNumber+'-'+newId+'">Part Theorem title: </label>');
    var theoremPartTitle = $('<input class="msm_theorem_part_title" id="msm_theorem_part_title-'+idNumber+'-'+newId+'" name="msm_theorem_part_title-'+idNumber+'-'+newId+'" placeholder=" Title for this part of the theorem."/>');
    var theoremPartContentField = $('<textarea class="msm_theorem_content" id="msm_theorem_part_content-'+idNumber+'-'+newId+'" name="msm_theorem_part_content-'+idNumber+'-'+newId+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-theorempart'+idNumber+'-'+newId+'"></div>');

    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-theorempart'+idNumber+'-'+newId+'"></div>');
            
    theoremPartTitleContainer.append(theoremPartTitleHidden);
            
    theoremPartContainer.append(theoremCloseButton);
    theoremPartContainer.append(theoremPartTitleContainer)
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
    
    if(tinymce.getInstanceById("msm_theorem_part_content-"+idNumber+"-"+newId) == null)
    {
        tinymce.execCommand("mceAddControl", false, "msm_theorem_part_content-"+idNumber+"-"+newId);
 
    }
    
    $("#msm_theorem_part_droparea-"+idNumber).sortable({
        appendTo: "msm_theorem_part_droparea-"+idNumber,
        connectWith: "msm_theorem_part_droparea-"+idNumber,
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
                tinyMCE.execCommand("mceAddControl", false, $(this).attr("id"));
                $(this).sortable("refresh");
            });
        }
    });    
    
    $("#msm_theorem_part_title_container-"+idNumber+'-'+newId).mouseover(function () {
        $(this).children("span").css({
            "visibility": "visible", 
            "color": "#4e6632", 
            "opacity": "0.5",
            "cursor": "move"
        });
    });
    $("#msm_theorem_part_title_container-"+idNumber+'-'+newId).mouseout(function () {
        $(this).children("span").css("visibility", "hidden");
    });
    $("#msm_theorem_part_title_container-"+idNumber+'-'+newId).mouseup(function () {
        $(this).children("span").css("visibility", "hidden");
    });
                
}

// index is the ending id number for associate

// ** make sure that the form input areas have both index number and their own unique idnumber so can order them according to their parents
function addAssociateForm(index, type)
{
    var newId = 1;
        
    if($("#msm_associate_container-"+index).children("div").length > 0)
    {
        while(document.getElementById('msm_associate_childs-'+index+'-'+newId) != null)
        {
            newId++;
        }
    }
    
    var associateInfoDiv = $('<div class="msm_associate_childs" id="msm_associate_childs-' + index + '-' + newId + '"></div>');
 
    var typeDropdown;
    
    if((type == 'def')||(type == 'comment'))
    {
        typeDropdown = $('<div class="msm_associate_optionarea"><span class="msm_associate_option_label"> Type of information: </span>\n\
                            <select name="msm_associate_dropdown-'+index + '-' + newId+'" class="msm_associated_dropdown" id="msm_associate_dropdown-'+index + '-' + newId+'">\n\
                                <option value="Comment">Comment</option>\n\
                                <option value="Explanation">Explanation</option>\n\
                                <option value="Example">Example</option>\n\
                                <option value="Illustration">Illustration</option>\n\
                                <option value="Remark">Remark</option>\n\
                                <option value="Exploration">Exploration</option>\n\
                            </select></div>');    
    }
    else if(type == "theorem")
    {
        typeDropdown = $('<div class="msm_associate_optionarea"><span class="msm_associate_option_label"> Type of information: </span>\n\
                           <select name="msm_associate_dropdown-'+index + '-' + newId+'" class="msm_associated_dropdown" id="msm_associate_dropdown-'+index + '-' + newId+'">\n\
                                <option value="Comment">Comment</option>\n\
                                <option value="Explanation">Explanation</option>\n\
                                <option value="Example">Example</option>\n\
                                <option value="Illustration">Illustration</option>\n\
                                <option value="Remark">Remark</option>\n\
                                <option value="Exploration">Exploration</option>\n\
                                <option value="Proof">Proof</option>\n\
                            </select></div>');
    }
    
    var infoHeader = $('<div class="msm_associate_info_headers" id="msm_associate_info_header-' + index + '-' + newId + '"></div>');
    var infoheadertext = $('<b> ASSOCIATED INFORMATION </b>');
    var infoheadertexthidden = $('<span style="visibility: hidden; display:none;">     Drag here to move this element.</span>');
    
    var infoTitleLabel = $('<label for="msm_info_title-'+index + '-' + newId+'">title: </label>');
    // title input area needs to be a textarea due to the need for math equation editor
    var infoTitleInput = $('<textarea class="msm_info_titles" id="msm_info_title-'+index + '-' + newId+'" name="msm_info_title-'+index + '-' + newId+'"/>');    
    
    var infoContentLabel = $('<label for="msm_info_content-'+index + '-' + newId+'">content: </label>');
    var infoContentInput = $('<textarea class="msm_info_contents" id="msm_info_content-'+index + '-' + newId+'" name="msm_info_content-'+index + '-' + newId+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-infocontent'+index + '-' + newId+'"></div>');

    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-infocontent'+index + '-' + newId+'"></div>');

    var refTypeDropdown = $("<div class='msm_associate_reftype_optionarea' id='msm_associate_reftype_option-"+index + "-" + newId+"'><span class='msm_associate_reftype_label'>Type of reference to add: </span>\n\
                                    <select name='msm_associate_reftype-"+index + "-" + newId+"' class='msm_associate_reftype_dropdown' id='msm_associate_reftype-"+index + "-" + newId+"' onchange='processReftype(event);'>\n\
                                    <option value='None'>None</option>\n\
                                    <option value='Comment'>Comment</option>\n\
                                    <option value='Definition'>Definition</option>\n\
                                    <option value='Theorem'>Theorem</option>\n\
                                    <option value='Example'>Example</option> \n\
                                    <option value='Section of this Composition'>Section of this Composition</option>\n\
                               </select></div>");
    
    var associateCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event);">x</a>');
  
    infoHeader.append(infoheadertext);
    infoHeader.append(infoheadertexthidden);
  
    associateInfoDiv.append(associateCloseButton);
    associateInfoDiv.append(infoHeader);
    associateInfoDiv.append(typeDropdown);
    associateInfoDiv.append(infoTitleLabel);
    associateInfoDiv.append(infoTitleInput);
    associateInfoDiv.append(infoContentLabel);
    associateInfoDiv.append(infoContentInput);
    associateInfoDiv.append(subordinateContainer);
    associateInfoDiv.append(subordinateResult);
    associateInfoDiv.append(refTypeDropdown);
     
    $(associateInfoDiv).insertBefore("#msm_associate_button-"+index);
    
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
        
    if(tinymce.getInstanceById("msm_info_content-"+index+"-"+newId) != null)
    {
        tinyMCE.execCommand('mceFocus', false,"msm_info_content-"+index+"-"+newId);          
        tinymce.execCommand('mceRemoveControl', true, "msm_info_content-"+index+"-"+newId);
    }
        
    if(tinymce.getInstanceById("msm_info_title-"+index+"-"+newId) != null) 
    {
        tinyMCE.execCommand('mceFocus', false, "msm_info_title-"+index+"-"+newId);          
        tinymce.execCommand('mceRemoveControl', true, "msm_info_title-"+index+"-"+newId);
    }
    
    tinyMCE.execCommand('mceAddControl', true, "msm_info_content-"+index+"-"+newId);
    tinyMCE.execCommand('mceAddControl', true, "msm_info_title-"+index+"-"+newId);
       
    $("#msm_associate_container-"+index).sortable({
        appendTo: "msm_associate_container-"+index,
        connectWith: "msm_associate_container-"+index,
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
            $(this).find('.msm_info_titles').each(function() {
                tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));          
                tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
            });
            $(this).find('.msm_info_contents').each(function() {
                tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));          
                tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
            });
            
            $(this).find('.msm_associate_reftype_optionarea').each(function() {
                $(this).find('.copied_msm_structural_element').each(function() {
                    $(this).find('.msm_unit_child_content').each(function()
                    {
                        tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));          
                        tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
                    });
                });
            });
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#FFFFFF");
            
            // if there are children in intro element, need to refresh the ifram of its editors
            $(this).find('.msm_info_titles').each(function() {
                tinyMCE.execCommand("mceAddControl", false, $(this).attr("id")); 
                $(this).sortable("refresh");
            });
            $(this).find('.msm_info_contents').each(function() {
                tinyMCE.execCommand("mceAddControl", false, $(this).attr("id")); 
                $(this).sortable("refresh");
            });
            
            $(this).find('.msm_associate_reftype_optionarea').each(function() {
                $(this).find('.copied_msm_structural_element').each(function() {
                    $(this).find('.msm_unit_child_content').each(function()
                    {
                        tinyMCE.execCommand("mceAddControl", false, $(this).attr("id")); 
                    });
                });
            });
            
        //            
        }
    });    
    
    $("#msm_associate_info_header-"+index+"-"+newId).mouseover(function () {
        $(this).children("span").css({
            "display": "inline",
            "visibility": "visible", 
            "color": "#4e6632", 
            "opacity": "0.5",
            "cursor": "move"
        });
    });
    $("#msm_associate_info_header-"+index+"-"+newId).mouseout(function () {
        $(this).children("span").css({
            "visibility":"hidden", 
            "display":"none"
        });
    });
    $("#msm_associate_info_header-"+index+"-"+newId).mouseup(function () {
        $(this).children("span").css({
            "visibility":"hidden", 
            "display":"none"
        });
    });           
     
}

function makeDefinition()
{
    var clonedCurrentElement = $("<div></div>");
    
    var defCloseButton = $('<a class="msm_element_close" style="margin-top: 2%;" onclick="deleteElement(event);">x</a>');
    
    var defSelectMenu = $('<select name="msm_def_type_dropdown-'+_index+'" class="msm_unit_child_dropdown" id="msm_def_type_dropdown-'+_index+'">\n\
                                    <option value="Notation">Notation</option>\n\
                                    <option value="Definition">Definition</option>\n\
                                    <option value="Agreement">Agreement</option>\n\
                                    <option value="Convention">Convention</option>\n\
                                    <option value="Axiom">Axiom</option>\n\
                                    <option value="Terminology">Terminology</option>\n\
                                </select>');
    var defTitleContainer = $('<div class="msm_element_title_containers" id="msm_element_title_container-'+_index+'"><b style="margin-left: 30%;"> DEFINITION </b></div>');
    var defTitleHidden = $('<span style="visibility: hidden;">Drag here to move this element.</span>');   
    var defTitleField = $('<input class="msm_unit_child_title" id="msm_def_title_input-'+_index+'" name="msm_def_title_input-'+_index+'" placeholder=" Title of Definition"/>');
          
    var defContentField = $('<textarea class="msm_unit_child_content" id="msm_def_content_input-'+_index+'" name="msm_def_content_input-'+_index+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-defcontent'+_index+'"></div>');
    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-defcontent'+_index+'"></div>');
    var defDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_def_description_label-"+_index+"' for='msm_def_description_input-"+_index+"'>Description: </label>");
    var defDescriptionField = $("<input class='msm_child_description_inputs' id='msm_def_description_input-"+_index+"' name='msm_def_description_input-"+_index+"' placeholder='Insert description to search this element in future. '/>");
    var defAssociateDiv = $("<div class='msm_associate_containers' id='msm_associate_container-"+_index+"'></div>");
    var type = "def";
    var defAssociateButton = $("<input type='button' class='msm_associate_buttons' id='msm_associate_button-"+_index+"' onclick='addAssociateForm("+_index+", \""+type+"\")' value='Add Associated Information''/>")
            
    clonedCurrentElement.attr("id", "copied_msm_def-"+_index);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
    defAssociateDiv.append(defAssociateButton);
    
    defTitleContainer.append(defTitleHidden);
            
    clonedCurrentElement.append(defCloseButton);
    clonedCurrentElement.append(defSelectMenu);
    clonedCurrentElement.append(defTitleContainer);
    clonedCurrentElement.append(defTitleField);
    clonedCurrentElement.append(defContentField);
    clonedCurrentElement.append(subordinateContainer);
    clonedCurrentElement.append(subordinateResult);
    clonedCurrentElement.append(defDescriptionLabel);
    clonedCurrentElement.append(defDescriptionField);
    clonedCurrentElement.append(defAssociateDiv);
    
    return clonedCurrentElement;
}

function makeTheorem()
{
    var clonedCurrentElement = $("<div></div>");
    var theoremCloseButton = $('<a class="msm_element_close" style="margin-top: 2%;" onclick="deleteElement(event)">x</a>');

    var theoremSelectMenu = $('<select name="msm_theorem_type_dropdown-'+_index+'" class="msm_unit_child_dropdown" id="msm_theorem_type_dropdown-'+_index+'">\n\
                                <option value="Theorem">Theorem</option>\n\
                                <option value="Proposition">Proposition</option>\n\
                                <option value="Lemma">Lemma</option>\n\
                                <option value="Corollary">Corollary</option>\n\
                            </select>');
    
    var theoremTitleContainer = $('<div class="msm_element_title_containers" id="msm_element_title_container-'+_index+'"><b style="margin-left: 31%;"> THEOREM </b></div>');   
    var theoremTitleHidden = $('<span style="visibility: hidden;">Drag here to move this element.</span>');       
    var theoremTitleField = $('<input class="msm_unit_child_title" id="msm_theorem_title_input-'+_index+'" name="msm_theorem_title_input-'+_index+'" placeholder=" Title of Theorem"/>');
            
    var theoremContentWrapper = $('<div class="msm_theorem_content_containers" id="msm_theorem_content_container-'+_index+'-1"></div>');
            
    var theoremStatementWrapper = $('<div class="msm_theorem_statement_containers" id="msm_theorem_statement_container-'+_index+'-1"></div>');
            
    var theoremContentTitleContainer = $('<div class="msm_theorem_statement_title_containers" id="msm_theorem_statement_title_container-'+_index+'-1"><b> Theorem Content </b></div>');
    var theoremContentTitleHidden = $('<span style="visibility: hidden;">Drag here to move this element.</span>');        
    
    var theoremContentField = $('<textarea class="msm_unit_child_content" id="msm_theorem_content_input-'+_index+'-1" name="msm_theorem_content_input-'+_index+'-1"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-theoremcontent'+_index+'-1"></div>');
    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-theoremcontent'+_index+'-1"></div>');
    var theoremPartWrapper = $('<div class="msm_theorem_part_dropareas" id="msm_theorem_part_droparea-'+_index+'-1"></div>');
            
    var theoremChildButton = $('<input class="msm_theorem_child_buttons" id="msm_theorem_child_button-'+_index+'" type="button" onclick="addTheoremContent(event, \''+_index+'-1\')" value="Add content"/>');
    var theoremPartButton = $('<input class="msm_theorem_part_buttons" id="msm_theorem_part_button-'+_index+'" type="button" onclick="addTheoremPart(event,  \''+_index+'-1\')" value="Add more parts"/>');
    var theoremDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_theorem_description_label-"+_index+"' for='msm_theorem_description_input-"+_index+"'>Description: </label>");
    var theoremDescriptionField = $("<input class='msm_child_description_inputs' id='msm_theorem_description_input-"+_index+"' name='msm_theorem_description_input-"+_index+"' placeholder='Insert description to search this element in future. '/>");
    var theoremAssociateDiv = $("<div class='msm_associate_containers' id='msm_associate_container-"+_index+"'></div>");
    var type = "theorem";
    var theoremAssociateButton = $("<input class='msm_associate_buttons' id='msm_associate_button-"+_index+"' type='button' onclick='addAssociateForm("+_index+", \""+type+"\")' value='Add Associated Information''/>")
        
    clonedCurrentElement.attr("id", "copied_msm_theorem-"+_index);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
    theoremPartWrapper.append(theoremPartButton);
    theoremAssociateDiv.append(theoremAssociateButton);
    
    theoremTitleContainer.append(theoremTitleHidden);
    
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
    clonedCurrentElement.append(theoremTitleContainer);
    clonedCurrentElement.append(theoremTitleField);
    clonedCurrentElement.append(theoremContentWrapper);
    clonedCurrentElement.append(theoremDescriptionLabel);
    clonedCurrentElement.append(theoremDescriptionField);
    clonedCurrentElement.append(theoremAssociateDiv);
    
    $("#msm_theorem_content_container-"+_index+"-1").sortable({
        appendTo: "msm_theorem_content_container-"+_index+"-1",
        connectWith: "msm_theorem_content_container-"+_index+"-1",
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
            
            // this code along with the one in stop is needed for enabling sortable on the div containing
            // the tinymce editor so the iframe part of the editor doesn't become disabled
            $(this).find('.msm_unit_child_content').each(function() {    
                if(tinyMCE.getInstanceById($(this).attr("id")) != null)
                {
                    tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));          
                    tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
                }               
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
        }
    });    
            
    return clonedCurrentElement;
}

function makeComment()
{
    var clonedCurrentElement = $("<div></div>");
    var commentCloseButton = $('<a class="msm_element_close" style="margin-top: 2%;" onclick="deleteElement(event);">x</a>');
    var commentSelectMenu = $('<select name="msm_comment_type_dropdown-'+_index+'" class="msm_unit_child_dropdown" id="msm_comment_type_dropdown-'+_index+'">\n\
                                <option value="Comment">Comment</option>\n\
                                <option value="Remark">Remark</option>\n\
                                <option value="Information">Information</option>\n\
                            </select>');
    var commentTitleContainer = $('<div class="msm_element_title_containers" id="msm_element_title_container-'+_index+'"><b style="margin-left: 30%"> COMMENT </b></div>'); 
    var commentTitleHidden = $('<span style="visibility: hidden;">Drag here to move this element.</span>');
    var commentTitleField = $('<input class="msm_unit_child_title" id="msm_comment_title_input-'+_index+'" name="msm_comment_title_input-'+_index+'" placeholder=" Title of Comment"/>');
          
    var commentContentField = $('<textarea class="msm_unit_child_content" id="msm_comment_content_input-'+_index+'" name="msm_comment_content_input-'+_index+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-commentcontent'+_index+'"></div>');
    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-commentcontent'+_index+'"></div>');
    var commentDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_comment_description_label-"+_index+"' for='msm_comment_description_input-"+_index+"'>Description: </label>");
    var commentDescriptionField = $("<input class='msm_child_description_inputs' id='msm_comment_description_input-"+_index+"' name='msm_comment_description_input-"+_index+"' placeholder='Insert description to search this element in future. '/>");
    var commentAssociateDiv = $("<div class='msm_associate_containers' id='msm_associate_container-"+_index+"'></div>");
    var type = "comment";
    var commentAssociateButton = $("<input class='msm_associate_buttons' id='msm_associate_button-"+_index+"' type='button' onclick='addAssociateForm("+_index+", \""+type+"\")' value='Add Associated Information''/>")
            
    clonedCurrentElement.attr("id", "copied_msm_comment-"+_index);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
    commentAssociateDiv.append(commentAssociateButton);
    
    commentTitleContainer.append(commentTitleHidden);
            
    clonedCurrentElement.append(commentCloseButton);
    clonedCurrentElement.append(commentSelectMenu);
    clonedCurrentElement.append(commentTitleContainer);
    clonedCurrentElement.append(commentTitleField);
    clonedCurrentElement.append(commentContentField);
    clonedCurrentElement.append(subordinateContainer);
    clonedCurrentElement.append(subordinateResult);
    clonedCurrentElement.append(commentDescriptionLabel);
    clonedCurrentElement.append(commentDescriptionField);
    clonedCurrentElement.append(commentAssociateDiv);
    
    return clonedCurrentElement;
}