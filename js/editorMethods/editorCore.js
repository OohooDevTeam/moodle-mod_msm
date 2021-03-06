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

/**
 * This function is activated when user drags one of the structural elememts on the very left side of the panel to middle panel.
 * It adds appropriate fields for the users to fill out for def/theorem/comments/info/content/media and images.
 * 
 * @param {eventObject} e               event object triggered by changes made to the selection of the dropdown menu 
 * @param {string} droppedId            tablename of element that was dropped into middle panel of the editor
 */
function processDroppedChild(e, droppedId)
{ 
    _index++;
    var currentContentid = 0;    
    var currenttheoremPart = 0;
    var currentTitleId = '';
    
    var element;
   
    switch(droppedId)
    {
        case "msm_def":
            element = makeDefinition();
            element.appendTo('#msm_child_appending_area');       
            currentTitleId = 'msm_def_title_input-'+_index;
            currentContentid = 'msm_def_content_input-'+_index;
            break;
        
        case "msm_theorem":
            element = makeTheorem();
            element.appendTo('#msm_child_appending_area');  
            currentTitleId = 'msm_theorem_title_input-'+_index
            currentContentid = 'msm_theorem_content_input-'+_index+'-1';
            break;
            
        case "msm_comment":
            element = makeComment();
            element.appendTo('#msm_child_appending_area'); 
            currentTitleId = 'msm_comment_title_input-'+_index
            currentContentid = 'msm_comment_content_input-'+_index;
            break;
            
        case "msm_extra_info":
            element = makeExtraInfo();
            element.appendTo("#msm_child_appending_area");     
            currentTitleId = 'msm_extra_title_input-'+_index;
            currentContentid = 'msm_extra_content_input-'+_index;
            break;
            
        case "msm_intro":
            checkIndexNumber("copied_msm_intro-"+_index);
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
                
                var overlayMenu = $('<div class="msm_element_overlays" id="msm_element_overlay-'+_index+'" style="display: none;"></div>');
            
                var overlayButtonEdit = $('<a class="msm_overlayButtons" id="msm_overlayButton_edit-'+_index+'" onclick="editUnit(event);"> Edit </a>');
                var overlayButtonDelete = $('<a class="msm_overlayButtons" id="msm_overlayButton_delete-'+_index+'" onclick="deleteOverlayElement(event);"> Delete </a>');
                
                var introTitle = $('<div class="msm_element_title_containers" id="msm_element_title_container-'+_index+'"><b style="margin-left: 43%; margin-right: 0%; margin-top: 2%; margin-bottom: 2%;"> INTRODUCTION </b></div>'); 
                var introTitlehidden = $('<span style="visibility: hidden;">     Drag here to move this element.</span>');

                var introTitleContainer = $("<div style='margin-top: 2%;'></div>")
                var introTitleLabel = $('<label class="msm_unit_intro_title_labels" id="msm_intro_title_label-'+_index+'" for="msm_intro_title_input-'+_index+'">Title:</label>');
                var introTitleField = $('<input class="msm_unit_intro_title" id="msm_intro_title_input-'+_index+'" name="msm_intro_title_input-'+_index+'" placeholder="Optional Title for the introduction"/>');     

                var introContentField = $('<textarea class="msm_unit_child_content" id="msm_intro_content_input-'+_index+'" name="msm_intro_content_input-'+_index+'" placeholder=" Need to add moodle form here?"/>');
                var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-introcontent'+_index+'"></div>');
                var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-introcontent'+_index+'"></div>');
            
                var introChildContainer = $("<div id='msm_intro_child_container'></div>");
                var dndDiv = $("<div class='msm_dnd_containers' id='msm_dnd_container-"+_index+"'>Drag additional content to here.\n\
                                     <p>Valid child Elements: Extra Contents</p>\n\
                                 </div>"); 
            
                clonedCurrentElement.attr("id", "copied_msm_intro-"+_index);
                clonedCurrentElement.attr("class", "copied_msm_structural_element");
                clonedCurrentElement.attr("style", "padding-top: 2%;");
                
                introTitleContainer.append(introTitleLabel);
                introTitleContainer.append(introTitleField);
                
                overlayMenu.append(overlayButtonDelete);                
                overlayMenu.append(overlayButtonEdit);               
                
                introTitle.append(introTitlehidden);
            
                clonedCurrentElement.append(introCloseButton);
                clonedCurrentElement.append(overlayMenu);
                clonedCurrentElement.append(introTitle); 
                clonedCurrentElement.append(introTitleContainer);
                clonedCurrentElement.append(introContentField);
                clonedCurrentElement.append(subordinateContainer);
                clonedCurrentElement.append(subordinateResult);
                clonedCurrentElement.append(introChildContainer);
                clonedCurrentElement.append(dndDiv);
                clonedCurrentElement.appendTo('#msm_child_appending_area');
            
                currentTitleId = "msm_intro_title_input-"+_index;
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
            checkIndexNumber("copied_msm_body-"+_index);
            var clonedCurrentElement = $("<div></div>");
            var bodyCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
            var overlayMenu = $('<div class="msm_element_overlays" id="msm_element_overlay-'+_index+'" style="display: none;"></div>');
            
            var overlayButtonEdit = $('<a class="msm_overlayButtons" id="msm_overlayButton_edit-'+_index+'" onclick=editUnit(event)> Edit </a>');
            var overlayButtonDelete = $('<a class="msm_overlayButtons" id="msm_overlayButton_delete-'+_index+'" onclick="deleteOverlayElement(event);"> Delete </a>');
            
            var bodyTitle = $('<div class="msm_element_title_containers" id="msm_element_title_container-'+_index+'"><b style="margin-left: 45%; margin-right: 0%; margin-top: 2%; margin-bottom: 2%;"> CONTENT </b></div>'); 
            var bodyTitlehidden = $('<span style="visibility: hidden;">     Drag here to move this element.</span>');
            var bodyTitleContainer = $("<div style='margin-top: 2%;'></div>");
            var bodyTitleLabel = $('<label class="msm_unit_body_title_labels" id="msm_body_title_label-'+_index+'" for="msm_body_title_input-'+_index+'">Title:</label>');
            var bodyTitleField = $('<input class="msm_unit_body_title" id="msm_body_title_input-'+_index+'" name="msm_body_title_input-'+_index+'" placeholder="Optional Title for this content"/>');  
            var bodyContentField = $('<textarea class="msm_unit_child_content" id="msm_body_content_input-'+_index+'" name="msm_body_content_input-'+_index+'" placeholder=" Need to add moodle form here?"/>');
            var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-bodycontent'+_index+'"></div>');
            var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-bodycontent'+_index+'"></div>');
            
            var imagemappingContainer = $('<div class="msm_imagemapping_containers" id="msm_imagemapping_container-bodycontent'+_index+'"></div>');
            var imagemappingResult = $('<div class="msm_imagemapping_result_containers" id="msm_imagemapping_result_container-bodycontent'+_index+'"></div>');

            bodyTitleContainer.append(bodyTitleLabel);
            bodyTitleContainer.append(bodyTitleField);
            
            bodyTitle.append(bodyTitlehidden);   
            
            overlayMenu.append(overlayButtonDelete);                
            overlayMenu.append(overlayButtonEdit);        
            
            clonedCurrentElement.attr("id", "copied_msm_body-"+_index);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            clonedCurrentElement.attr("style", "padding-top: 2%;");
            clonedCurrentElement.append(bodyCloseButton);
            clonedCurrentElement.append(overlayMenu);
            clonedCurrentElement.append(bodyTitle);
            clonedCurrentElement.append(bodyTitleContainer);
            clonedCurrentElement.append(bodyContentField);
            clonedCurrentElement.append(subordinateContainer);
            clonedCurrentElement.append(subordinateResult);
            clonedCurrentElement.append(imagemappingContainer);
            clonedCurrentElement.append(imagemappingResult);
            clonedCurrentElement.appendTo('#msm_child_appending_area');
            
            currentTitleId = "msm_body_title_input-"+_index;
            currentContentid = 'msm_body_content_input-'+_index;
            break;    
    }
    
    $(".msm_dnd_containers").droppable({
        accept: "#msm_component_tabs-2 > div",
        hoverClass: "ui-state-hover",
        tolerance: "pointer",
        drop: function( event, ui ) { 
            processAdditionalChild(event, ui.draggable.context.id);      
            allowDragnDrop();  
        }
    });    
    
    if($('#msm_editor_save').attr("disabled"))
    {
        $('#msm_editor_save').removeAttr('disabled');
    }
    
    // has to be exact mode b/c if it is initiated twice, the editor function gives it a random id and breaks the save method
    
    if((currentTitleId.match(/def/)) || (currentTitleId.match(/theorem/)) || (currentTitleId.match(/comment/)) || currentTitleId.match(/extra/))
    {
        initTitleEditor(currentTitleId, "26%");
    }
    else
    {
        initTitleEditor(currentTitleId, "96%");
    }
    
    initEditor(currentContentid);
    initEditor(currenttheoremPart);
    
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
                    tinymce.execCommand('mceFocus', false, $(this).attr("id")); 
                    tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
                }
            });
            
            $(this).find(".msm_unit_child_title").each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) != null)
                {
                    tinymce.execCommand('mceFocus', false, $(this).attr("id")); 
                    tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
                }
            });
            
            $(this).find(".msm_unit_intro_title").each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) != null)
                {
                    tinymce.execCommand('mceFocus', false, $(this).attr("id")); 
                    tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
                }
            });
            
            $(this).find(".msm_unit_body_title").each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) != null)
                {
                    tinymce.execCommand('mceFocus', false, $(this).attr("id")); 
                    tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
                }
            });
            
            $(this).find('.msm_theorem_part_title').each(function() {
                tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));
                tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
            });
             
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#EDEDED");    
                 
            var id = $(this).attr("id");
            
            $("#"+id+" textarea").each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) == null)
                {
                    if(this.className == "msm_info_titles")
                    {
                        noSubInitEditor(this.id);
                    }
                    else
                    {
                        initEditor(this.id); 
                    }      
                }
            });
            $(this).find(".msm_unit_child_title").each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) == null)
                {
                    if((this.id.match(/def/)) || (this.id.match(/theorem/)) || (this.id.match(/comment/)) || this.id.match(/extra/))
                    {
                        initTitleEditor(this.id, "26%");
                    }
                    else
                    {
                        initTitleEditor(this.id, "96%");
                    }
                }
            });
            
            $(this).find(".msm_unit_intro_title").each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) == null)
                {
                    initTitleEditor(this.id, "96%");       
                }
            });
            
            $(this).find(".msm_unit_body_title").each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) == null)
                {
                    initTitleEditor(this.id, "96%");       
                }
            });
            
            $(this).find('.msm_theorem_part_title').each(function() {
                initTitleEditor(this.id, "85%");
                $(this).sortable("refresh");
            });
        }
    });    
    $("#msm_child_appending_area").sortable("refresh");
    
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
 * Thie method is used to initiate tinyMCE for the title elements, such as for unit.
 * It is separated from the initEditor due to title editors having a simpler layout
 * that does not need any advanced function in tinyMCE.
 *
 * @param {string} elId                   HTML element ID
 * @param {string} width                  width % for CSS of editor
 */
function initTitleEditor(elId, width)
{
    YUI().add("editor_tinymce");
    YUI().use('editor_tinymce', function(Y) {
        M.editor_tinymce.init_editor(Y, elId, {
            mode:"exact",
            inline: true,
            elements: elId,
            body_class: "msm_tinymce_title_bodies",
            content_css: "css/msmAuthoring.css",
            plugins:"matheditor,paste,contextmenu,insertdatetime,save,iespell",
            width: width,
            min_height: "20",
            theme_advanced_font_sizes:"1,2,3,4,5,6,7",
            theme_advanced_layout_manager:"SimpleLayout",
            theme_advanced_toolbar_align:"left",
            theme_advanced_statusbar_location: "none",
            theme_advanced_fonts:"Trebuchet=Trebuchet MS,Verdana,Arial,Helvetica,sans-serif;Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;Georgia=georgia,times new roman,times,serif;Tahoma=tahoma,arial,helvetica,sans-serif;Times New Roman=times new roman,times,serif;Verdana=verdana,arial,helvetica,sans-serif;Impact=impact;Wingdings=wingdings",
            theme_advanced_toolbar_location:"external",
            language_load:false,
            langrev:-1,
            theme_advanced_buttons1:"fontselect,fontsizeselect,|,undo,redo,|,bold,italic,underline,strikethrough,sub,sup,|,matheditor",
            moodle_init_plugins:"matheditor:loader.php/matheditor/-1/editor_plugin.js",
            moodle_plugin_base: M.cfg.wwwroot+"/lib/editor/tinymce/plugins/"
        });
    });   
}

/**
 * Thie method is used to initiate tinyMCE for the textarea elements.
 * 
 * @param {string} elId                   HTML element ID
 */
function initEditor(elId)
{ 
    YUI().add("editor_tinymce");
    YUI().use('editor_tinymce', function(Y) {
        M.editor_tinymce.init_editor(Y, elId, {
            mode:"exact",
            elements: elId,
            plugins:"matheditor,subordinate,moodlesubnolink,safari,table,style,layer,advhr,advlist,emotions,inlinepopups,searchreplace,paste,directionality,fullscreen,nonbreaking,contextmenu,insertdatetime,save,iespell,preview,print,noneditable,visualchars,xhtmlxtras,template,pagebreak,-dragmath,-moodlenolink,-spellchecker,-moodleimage,-moodlemedia",
            width: "100%",
            height: "70%",
            theme_advanced_font_sizes:"1,2,3,4,5,6,7",
            theme_advanced_layout_manager:"SimpleLayout",
            theme_advanced_toolbar_align:"left",
            theme_advanced_fonts:"Trebuchet=Trebuchet MS,Verdana,Arial,Helvetica,sans-serif;Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;Georgia=georgia,times new roman,times,serif;Tahoma=tahoma,arial,helvetica,sans-serif;Times New Roman=times new roman,times,serif;Verdana=verdana,arial,helvetica,sans-serif;Impact=impact;Wingdings=wingdings",
            theme_advanced_resize_horizontal:true,
            theme_advanced_resizing:true,
            theme_advanced_resizing_min_height:30,
            min_height:30,
            theme_advanced_toolbar_location:"top",
            theme_advanced_statusbar_location:"bottom",
            language_load:false,
            langrev:-1,
            theme_advanced_buttons1:"fontselect,fontsizeselect,formatselect,|,undo,redo,|,search,replace,|,fullscreen",
            theme_advanced_buttons2:"bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,|,cleanup,removeformat,pastetext,pasteword,|,forecolor,backcolor,|,ltr,rtl",
            theme_advanced_buttons3:"bullist,numlist,outdent,indent,|,subordinate,moodlesubnolink,|,image,moodlemedia,matheditor,nonbreaking,charmap,table,|,code,spellchecker",
            moodle_plugin_base: M.cfg.wwwroot+"/lib/editor/tinymce/plugins/",
            moodle_init_plugins:"dragmath:loader.php/dragmath/-1/editor_plugin.js,moodlenolink:loader.php/moodlenolink/-1/editor_plugin.js,spellchecker:loader.php/spellchecker/-1/editor_plugin.js,moodleimage:loader.php/moodleimage/-1/editor_plugin.js,moodlemedia:loader.php/moodlemedia/-1/editor_plugin.js,matheditor:loader.php/matheditor/-1/editor_plugin.js,subordinate:loader.php/subordinate/-1/editor_plugin.js,moodlesubnolink:loader.php/moodlesubnolink/-1/editor_plugin.js",
            file_browser_callback:"M.editor_tinymce.filepicker"
        })
        M.editor_tinymce.init_filepicker(Y, elId, tinymce_filepicker_options);
    });
}

/**
 * This method is used to initiate information title textarea which cannot have subordinate
 * functionality in the tinyMCE editor.
 * 
 * @param {string} elId               HTML element ID (for information element)
 */
function noSubInitEditor(elId)
{
    YUI().add("editor_tinymce");
    YUI().use('editor_tinymce', function(Y) {
        M.editor_tinymce.init_editor(Y, elId, {
            mode:"exact",
            elements: elId,
            plugins:"matheditor,safari,table,style,layer,advhr,advlist,emotions,inlinepopups,searchreplace,paste,directionality,fullscreen,nonbreaking,contextmenu,insertdatetime,save,iespell,preview,print,noneditable,visualchars,xhtmlxtras,template,pagebreak,-dragmath,-moodlenolink,-spellchecker,-moodleimage,-moodlemedia",
            width: "100%",
            height: "70%",
            theme_advanced_font_sizes:"1,2,3,4,5,6,7",
            theme_advanced_layout_manager:"SimpleLayout",
            theme_advanced_toolbar_align:"left",
            theme_advanced_fonts:"Trebuchet=Trebuchet MS,Verdana,Arial,Helvetica,sans-serif;Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;Georgia=georgia,times new roman,times,serif;Tahoma=tahoma,arial,helvetica,sans-serif;Times New Roman=times new roman,times,serif;Verdana=verdana,arial,helvetica,sans-serif;Impact=impact;Wingdings=wingdings",
            theme_advanced_resize_horizontal:true,
            theme_advanced_resizing:true,
            theme_advanced_resizing_min_height:30,
            min_height:30,
            theme_advanced_toolbar_location:"top",
            theme_advanced_statusbar_location:"bottom",
            language_load:false,
            langrev:-1,
            theme_advanced_buttons1:"fontselect,fontsizeselect,formatselect,|,undo,redo,|,search,replace,|,fullscreen",
            theme_advanced_buttons2:"bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,|,cleanup,removeformat,pastetext,pasteword,|,forecolor,backcolor,|,ltr,rtl",
            theme_advanced_buttons3:"bullist,numlist,outdent,indent,|,matheditor,nonbreaking,charmap,table,|,code,spellchecker",
            moodle_init_plugins:"dragmath:loader.php/dragmath/-1/editor_plugin.js,moodlenolink:loader.php/moodlenolink/-1/editor_plugin.js,spellchecker:loader.php/spellchecker/-1/editor_plugin.js,moodleimage:loader.php/moodleimage/-1/editor_plugin.js,moodlemedia:loader.php/moodlemedia/-1/editor_plugin.js,matheditor:loader.php/matheditor/-1/editor_plugin.js",
            file_browser_callback:"M.editor_tinymce.filepicker",
            moodle_plugin_base: M.cfg.wwwroot+"/lib/editor/tinymce/plugins/"
        })
        
        M.editor_tinymce.init_filepicker(Y, elId, tinymce_filepicker_options);
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
 * @param {eventObject} e         event object from clicking the close buttons on each of draggable elements
 */
function deleteElement(e)
{
    
    var currentElement = e.target.parentElement.id;
    
    if(currentElement == '')
    {
        // in firefox, sometimes the target ends up being the textnode "x" instead of the <a> element
        currentElement = e.target.parentElement.parentElement.id;       
    }
    
    // may need to insert code to remove tinyMCE Editor from each title input fields that this element has
    // --> did not implement since no error was encountered... 
    // --> if later some problem with tinyMCE reinitialization for title fields, then insert the code to 
    //      reinitialize the tinyMCE here
    $("#"+currentElement+" textarea").each(function() {
        if(tinymce.getInstanceById($(this).attr("id")) != null)
        {
            tinymce.execCommand('mceFocus', false, $(this).attr("id")); 
            tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
        }
    });
        
    $("#"+currentElement+" input").each(function() {
        if(($(this).hasClass("msm_unit_child_title")) || ($(this).hasClass("msm_unit_intro_title")) ||
            ($(this).hasClass("msm_intro_child_titles")) || $(this).hasClass("msm_unit_body_title"))
            {
            if(tinymce.getInstanceById($(this).attr("id")) != null)
            {
                tinymce.execCommand('mceFocus', false, $(this).attr("id")); 
                tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
            }
        }
    })
        
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
                        initEditor(this.id); 
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
 * 
 * @param {string} idNumer                string added to main intro div to make it unique
 */
function addIntroContent(idNumber)
{
    // no need for parent ID to be preserved as there are only one intros allows in an unit
    var newId = 1;
    
    $(".msm_intro_child").each(function() {
        newId++;
    });    
    
    // preventing duplicate ID from being created...so check if the ID already exists
    $(".msm_intro_child").each(function() {
        if("msm_intro_child_div-"+newId == this.id)
        {
            newId++;
        }
    });
    
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
    
    var childTitleLabel = document.createElement("label");
    childTitleLabel.className = "msm_intro_child_title_labels";
    var childTitleLabelText = document.createTextNode("Title:");
    
    childTitleLabel.appendChild(childTitleLabelText);
    
    var introChildTitle = document.createElement("input");
    introChildTitle.id = "msm_intro_child_title-"+newId;
    introChildTitle.className = "msm_intro_child_titles";
    introChildTitle.name = "msm_intro_child_title-"+newId;
    introChildTitle.setAttribute("placeholder", "Optional Title for the Content");
    
    var introChildContent = document.createElement("textarea");
    introChildContent.id = "msm_intro_child_content-"+newId;
    introChildContent.className = "msm_intro_child_contents";
    introChildContent.name = "msm_intro_child_content-"+newId;
    
    var subordinateContainer = document.createElement("div");
    subordinateContainer.id = "msm_subordinate_container-introchild"+newId;
    subordinateContainer.className = "msm_subordinate_containers";
    
    var subordinateResult = document.createElement("div");
    subordinateResult.id = "msm_subordinate_result_container-introchild"+newId;
    subordinateResult.className = "msm_subordinate_result_containers";
    
    introChildDiv.appendChild(introCloseButton);
    introChildDiv.appendChild(dragArea);

    introChildDiv.appendChild(childTitleLabel);
    introChildDiv.appendChild(introChildTitle);
    introChildDiv.appendChild(introChildContent);
    introChildDiv.appendChild(subordinateContainer);
    introChildDiv.appendChild(subordinateResult);
    
    $(introChildDiv).appendTo("#msm_intro_child_container"); 
    
    initTitleEditor("msm_intro_child_title-"+newId, "96%");
    initEditor("msm_intro_child_content-"+newId);
    
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
            
            $(this).find('.msm_intro_child_titles').each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) != null)
                {
                    tinymce.execCommand('mceFocus', false, $(this).attr("id")); 
                    tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
                }
            });
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#EDEDED");
            
            // if there are children in intro element, need to refresh the ifram of its editors
            $(this).find('.msm_intro_child_contents').each(function() {
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    initEditor(this.id);                    
                }
            });
            
            $(this).find('.msm_intro_child_titles').each(function() {
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    initTitleEditor(this.id, "96%");                    
                }
            });
        }
    });    
    $("#msm_intro_child_container").sortable("refresh");

    // enables the toggling fuctions for indicating draggable area
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

/**
 * This method creates the form for additional contents in theorem element.  It is triggered when an 
 * "Extra Content" element is dragged and dropped to a droppable container in theorem.
 * 
 * @param {eventObject} event         event triggered from dropping an "Extra Content" element to theorem
 */
function addTheoremContent(event)
{    
    var newId = 1;
    
    var buttonIdInfo = event.target.id.split("-");
    
    var idNumber = '';
    for(var i=1; i < buttonIdInfo.length-1; i++)
    {
        idNumber += buttonIdInfo[i]+"-";
    }
    idNumber += buttonIdInfo[buttonIdInfo.length-1];
        
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
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-statementtheoremcontent'+idNumber+'-'+newId+'"></div>');

    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-statementtheoremcontent'+idNumber+'-'+newId+'"></div>'); 

    var theoremPartWrapper = $('<div class="msm_theorem_part_dropareas" id="msm_theorem_part_droparea-'+idNumber+'-'+newId+'"></div>');
    var partDndDiv = $("<div class='msm_dnd_containers' id='msm_dnd_container-"+idNumber+'-'+newId+"'>Drag additional content to here.\n\
                        <p>Valid child Elements: Part of a Theorem</p>\n\
                    </div>");
            
    theoremPartWrapper.append(partDndDiv);
    
    theoremContentTitleContainer.append(theoremContentTitleHidden);
            
    theoremStatementWrapper.append(theoremCloseButton);
    theoremStatementWrapper.append(theoremContentTitleContainer);
    theoremStatementWrapper.append(theoremContentField);
    theoremStatementWrapper.append(subordinateContainer);
    theoremStatementWrapper.append(subordinateResult);
   
    theoremStatementWrapper.append(theoremPartWrapper);
    
    $("#msm_theorem_content_container-"+idNumber).append(theoremStatementWrapper);    
    
    initEditor("msm_theorem_content_input-"+idNumber+'-'+newId); 
    
    $("#msm_dnd_container-"+idNumber+"-"+newId).droppable({
        accept: "#msm_component_tabs-2 > div",
        hoverClass: "ui-state-hover",
        tolerance: "pointer",
        drop: function( event, ui ) { 
            processAdditionalChild(event, ui.draggable.context.id);      
            allowDragnDrop();  
        }
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
            
            $(this).find('.msm_theorem_part_title').each(function() {
                tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));
                tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
            });
            
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#EDEDED");
            
            var id = $(this).attr("id");
            
            $("#"+id+" textarea").each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) == null)
                {
                    initEditor(this.id);                    
                }
            });
            
            $(this).find('.msm_theorem_part_title').each(function() {
                initTitleEditor(this.id, "85%");
            });
        }
    });    
    $("#msm_theorem_content_container-"+idNumber).sortable("refresh");

    // enables the toggling fuctions for indicating draggable area
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

/**
 * This method creates the form for additional parts in theorem element.  It is triggered when an 
 * "Parts of a Theorem" element is dragged and dropped to a droppable container in theorem content.
 * 
 * @param {eventObject} event         event object triggered from item being dropped into a designated droppable container
 */
function addTheoremPart(event)
{
    var newId = 1;   
    
    var buttonIdInfo = event.target.id.split("-");
    
    var idNumber = '';
    for(var i=1; i < buttonIdInfo.length-1; i++)
    {
        idNumber += buttonIdInfo[i]+"-";
    }
    idNumber += buttonIdInfo[buttonIdInfo.length-1];
        
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
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-parttheoremcontent'+idNumber+'-'+newId+'"></div>');

    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-parttheoremcontent'+idNumber+'-'+newId+'"></div>');
            
    theoremPartTitleContainer.append(theoremPartTitleHidden);
            
    theoremPartContainer.append(theoremCloseButton);
    theoremPartContainer.append(theoremPartTitleContainer)
    theoremPartContainer.append(theoremPartLabel);
    theoremPartContainer.append(theoremPartTitle);
    theoremPartContainer.append(theoremPartContentField);
    theoremPartContainer.append(subordinateContainer);
    theoremPartContainer.append(subordinateResult);
    
    $(theoremPartContainer).insertBefore("#"+event.target.id);
    
    if(tinymce.getInstanceById("msm_theorem_part_content-"+idNumber+"-"+newId) == null)
    {
        initEditor("msm_theorem_part_content-"+idNumber+"-"+newId);  
    }
    
    if(tinymce.getInstanceById("msm_theorem_part_title-"+idNumber+"-"+newId) == null)
    {
        initTitleEditor("msm_theorem_part_title-"+idNumber+"-"+newId, "85%");  
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
        
            $(this).find('.msm_theorem_part_title').each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) != null)
                {
                    tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));          
                    tinymce.execCommand('mceRemoveControl', true, $(this).attr("id")); 
                }                
            });
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#EDEDED");
            
            // if there are children in intro element, need to refresh the ifram of its editors
            $(this).find('.msm_theorem_content').each(function() {
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    initEditor(this.id);                    
                }
            });
            
            $(this).find('.msm_theorem_part_title').each(function() {
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    initTitleEditor(this.id, "85%");                    
                }
            });
        }
    });    
    $("#msm_theorem_part_droparea-"+idNumber).sortable("refresh");

    // enables the toggling fuctions for indicating draggable area
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

/**
 * This method add the form components needed to create associate element in the editor and also initializes all the plugins needed.
 * 
 * @param {int} index                     ending id number for associate to be attached to the HTML ID of the associate
 * @param {string} type                   the parent of the associate (def/comment/theorem)
 */
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
    var infoheadertexthidden = $('<span style="visibility: hidden; display:none;"> Drag here to move this element.</span>');
    
    var infoTitleLabel = $('<label for="msm_info_title-'+index + '-' + newId+'-1">title: </label>');
    // title input area needs to be a textarea due to the need for math equation editor
    var infoTitleInput = $('<textarea class="msm_info_titles" id="msm_info_title-'+index + '-' + newId+'-1" name="msm_info_title-'+index + '-' + newId+'-1"/>');    
    
    var infoContentLabel = $('<label for="msm_info_content-'+index + '-' + newId+'-1">content: </label>');
    var infoContentInput = $('<textarea class="msm_info_contents" id="msm_info_content-'+index + '-' + newId+'-1" name="msm_info_content-'+index + '-' + newId+'-1"/>');
    var subordinateContentContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-infocontent'+index + '-' + newId+'-1"></div>');

    var subordinateContentResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-infocontent'+index + '-' + newId+'-1"></div>');

    var refTypeDiv = $("<div class='msm_associate_reftype_optionarea' id='msm_associate_reftype_option-"+index+"-"+newId+"-1'></div>"); // area where ref form gets appended to
    //    var refTypeDropdown = $("<div class='msm_associate_reftype_optionarea' id='msm_associate_reftype_option-"+index + "-" + newId+"-1'><span class='msm_associate_reftype_label'>Type of reference to add: </span>\n\
    //                                <select name='msm_associate_reftype-//"+index + "-" + newId+"-1' class='msm_associate_reftype_dropdown' id='msm_associate_reftype-"+index + "-" + newId+"-1' onchange='processReftype(event);'>\n\
    //                                    <option value='None'>None</option>\n\
    //                                    <option value='Comment'>Comment</option>\n\
    //                                    <option value='Definition'>Definition</option>\n\
    //                                    <option value='Theorem'>Theorem</option>\n\
    //                                    <option value='Example'>Example</option> \n\
    //                                    <option value='Section of this Composition'>Section of this Composition</option>\n\
    //                                </select></div>//");
    
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
    associateInfoDiv.append(subordinateContentContainer);
    associateInfoDiv.append(subordinateContentResult);
    associateInfoDiv.append(refTypeDiv);
     
    $(associateInfoDiv).insertBefore("#msm_dnd_container-"+index);
        
    if(tinymce.getInstanceById("msm_info_content-"+index+"-"+newId+"-1") != null)
    {
        tinyMCE.execCommand('mceFocus', false,"msm_info_content-"+index+"-"+newId+"-1");
        tinymce.execCommand('mceRemoveControl', true, "msm_info_content-"+index+"-"+newId+"-1");
    }
        
    if(tinymce.getInstanceById("msm_info_title-"+index+"-"+newId+"-1") != null)
    {
        tinyMCE.execCommand('mceFocus', false, "msm_info_title-"+index+"-"+newId+"-1");
        tinymce.execCommand('mceRemoveControl', true, "msm_info_title-"+index+"-"+newId+"-1");
    }
    
    initEditor("msm_info_content-"+index+"-"+newId+"-1");
    noSubInitEditor("msm_info_title-"+index+"-"+newId+"-1");    
       
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
                    $(this).find('.msm_theorem_part_title').each(function() {
                        tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));
                        tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
                    });
                });
            });
            
            // should only find referenced materials
            $(this).find(".msm_unit_child_title").each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) != null)
                {
                    tinymce.execCommand('mceFocus', false, $(this).attr("id")); 
                    tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
                }                 
            });
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#EDEDED");
            
            // if there are children in intro element, need to refresh the ifram of its editors
            $(this).find('.msm_info_titles').each(function() {
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    noSubInitEditor(this.id);
                }
            });
            $(this).find('.msm_info_contents').each(function() {
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    initEditor(this.id);
                }
            });
           
            $(this).find('.msm_associate_reftype_optionarea').each(function() {
                $(this).find('.copied_msm_structural_element').each(function() {
                    $(this).find('.msm_unit_child_content').each(function()
                    {
                        if(tinymce.getInstanceById($(this).attr("id"))==null)
                        {
                            initEditor(this.id);
                        }
                    });
                });
            });

            $(this).find(".msm_unit_child_title").each(function() {
                if(tinymce.getInstanceById($(this).attr("id")) == null)
                {
                    initTitleEditor(this.id, "26%");
                }
                
            });
            
            $(this).find('.msm_theorem_part_title').each(function() {
                initTitleEditor(this.id, "85%");
                $(this).sortable("refresh");
            });
        }
    });
    
    $("#msm_associate_container-"+index).sortable("refresh");
    
    // enables the toggling fuctions for indicating draggable area
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
            "visibility":"hidden"
        });
    });
    $("#msm_associate_info_header-"+index+"-"+newId).mouseup(function () {
        $(this).children("span").css({
            "visibility":"hidden"
        });
    });     
}

/**
 * This method creates the form need to input the information for definition elements.
 * 
 * @return {object}               finished form in a container div
 */
function makeDefinition()
{
    checkIndexNumber("copied_msm_def-"+_index);
    var clonedCurrentElement = $("<div></div>");
    
    var defCloseButton = $('<a class="msm_element_close" style="margin-top: 2%;" onclick="deleteElement(event);">x</a>');
    
    var overlayMenu = $('<div class="msm_element_overlays" id="msm_element_overlay-'+_index+'" style="display: none;"></div>');
            
    var overlayButtonEdit = $('<a class="msm_overlayButtons" id="msm_overlayButton_edit-'+_index+'" onclick=editUnit(event)> Edit </a>');
    var overlayButtonDelete = $('<a class="msm_overlayButtons" id="msm_overlayButton_delete-'+_index+'" onclick="deleteOverlayElement(event);"> Delete </a>');
    
    var selectAndTitleDiv = $("<div class='msm_select_title_containers'></div>");
    var defSelectMenu = $('<select name="msm_def_type_dropdown-'+_index+'" class="msm_unit_child_dropdown" id="msm_def_type_dropdown-'+_index+'">\n\
                                    <option value="Notation">Notation</option>\n\
                                    <option value="Definition">Definition</option>\n\
                                    <option value="Agreement">Agreement</option>\n\
                                    <option value="Convention">Convention</option>\n\
                                    <option value="Axiom">Axiom</option>\n\
                                    <option value="Terminology">Terminology</option>\n\
                                </select>');
    var defTitleContainer = $('<div class="msm_element_title_containers" id="msm_element_title_container-'+_index+'"><b style="margin-left: 40%;"> DEFINITION </b></div>');
    var defTitleHidden = $('<span style="visibility: hidden;">Drag here to move this element.</span>');   
    var defTitleField = $('<input class="msm_unit_child_title" id="msm_def_title_input-'+_index+'" name="msm_def_title_input-'+_index+'" placeholder=" Title of Definition"/>');
          
    var defContentField = $('<textarea class="msm_unit_child_content" id="msm_def_content_input-'+_index+'" name="msm_def_content_input-'+_index+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-defcontent'+_index+'"></div>');
    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-defcontent'+_index+'"></div>');
    var defDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_def_description_label-"+_index+"' for='msm_def_description_input-"+_index+"'>Description: </label>");
    var defDescriptionField = $("<input class='msm_child_description_inputs' id='msm_def_description_input-"+_index+"' name='msm_def_description_input-"+_index+"' placeholder='Insert description to search this element in future. '/>");
    var defAssociateDiv = $("<div class='msm_associate_containers' id='msm_associate_container-"+_index+"'></div>");
    var dndDiv = $("<div class='msm_dnd_containers' id='msm_dnd_container-"+_index+"'>Drag additional content to here.\n\
                        <p>Valid child Elements: Associates, internal and/or external references</p>\n\
                    </div>");
            
    clonedCurrentElement.attr("id", "copied_msm_def-"+_index);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
    defAssociateDiv.append(dndDiv);
            
    defTitleContainer.append(defTitleHidden);
    
    selectAndTitleDiv.append(defSelectMenu);
    selectAndTitleDiv.append(defTitleField);
    
    overlayMenu.append(overlayButtonDelete);
    overlayMenu.append(overlayButtonEdit);
            
    clonedCurrentElement.append(defCloseButton);
    clonedCurrentElement.append(overlayMenu);
    clonedCurrentElement.append(defTitleContainer);

    clonedCurrentElement.append(selectAndTitleDiv);
    clonedCurrentElement.append(defContentField);
    clonedCurrentElement.append(subordinateContainer);
    clonedCurrentElement.append(subordinateResult);
    clonedCurrentElement.append(defDescriptionLabel);
    clonedCurrentElement.append(defDescriptionField);
    clonedCurrentElement.append(defAssociateDiv);
    
    return clonedCurrentElement;
}

/**
 * This method creates the form need to input the information for theorem elements.
 * 
 * @return {object}               finished form in a container div
 */
function makeTheorem()
{
    checkIndexNumber("copied_msm_theorem-"+_index);
    var clonedCurrentElement = $("<div></div>");
    var theoremCloseButton = $('<a class="msm_element_close" style="margin-top: 2%;" onclick="deleteElement(event)">x</a>');
    
    var overlayMenu = $('<div class="msm_element_overlays" id="msm_element_overlay-'+_index+'" style="display: none;"></div>');
            
    var overlayButtonEdit = $('<a class="msm_overlayButtons" id="msm_overlayButton_edit-'+_index+'" onclick=editUnit(event)> Edit </a>');
    var overlayButtonDelete = $('<a class="msm_overlayButtons" id="msm_overlayButton_delete-'+_index+'" onclick="deleteOverlayElement(event);"> Delete </a>');

    var selectAndTitleDiv = $("<div class='msm_select_title_containers'></div>");
    var theoremSelectMenu = $('<select name="msm_theorem_type_dropdown-'+_index+'" class="msm_unit_child_dropdown" id="msm_theorem_type_dropdown-'+_index+'">\n\
                                <option value="Theorem">Theorem</option>\n\
                                <option value="Proposition">Proposition</option>\n\
                                <option value="Lemma">Lemma</option>\n\
                                <option value="Corollary">Corollary</option>\n\
                            </select>');
    
    var theoremTitleContainer = $('<div class="msm_element_title_containers" id="msm_element_title_container-'+_index+'"><b style="margin-left: 41%;"> THEOREM </b></div>');   
    var theoremTitleHidden = $('<span style="visibility: hidden;">Drag here to move this element.</span>');       
    var theoremTitleField = $('<input class="msm_unit_child_title" id="msm_theorem_title_input-'+_index+'" name="msm_theorem_title_input-'+_index+'" placeholder=" Title of Theorem"/>');
            
    var theoremContentWrapper = $('<div class="msm_theorem_content_containers" id="msm_theorem_content_container-'+_index+'"></div>');
            
    var theoremStatementWrapper = $('<div class="msm_theorem_statement_containers" id="msm_theorem_statement_container-'+_index+'-1"></div>');
            
    var theoremContentTitleContainer = $('<div class="msm_theorem_statement_title_containers" id="msm_theorem_statement_title_container-'+_index+'-1"><b> Theorem Content </b></div>');
    var theoremContentTitleHidden = $('<span style="visibility: hidden;">Drag here to move this element.</span>');        
    
    var theoremContentField = $('<textarea class="msm_unit_child_content" id="msm_theorem_content_input-'+_index+'-1" name="msm_theorem_content_input-'+_index+'-1"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-statementtheoremcontent'+_index+'-1"></div>');
    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-statementtheoremcontent'+_index+'-1"></div>');
    var theoremPartWrapper = $('<div class="msm_theorem_part_dropareas" id="msm_theorem_part_droparea-'+_index+'-1"></div>');
    var partDndDiv = $("<div class='msm_dnd_containers' id='msm_dnd_container-"+_index+"-1'>Drag additional content to here.\n\
                        <p>Valid child Elements: Part of a Theorem</p>\n\
                    </div>");               
    var theoremDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_theorem_description_label-"+_index+"' for='msm_theorem_description_input-"+_index+"'>Description: </label>");
    var theoremDescriptionField = $("<input class='msm_child_description_inputs' id='msm_theorem_description_input-"+_index+"' name='msm_theorem_description_input-"+_index+"' placeholder='Insert description to search this element in future. '/>");
    var theoremAssociateDiv = $("<div class='msm_associate_containers' id='msm_associate_container-"+_index+"'></div>");
    var dndDiv = $("<div class='msm_dnd_containers' id='msm_dnd_container-"+_index+"'>Drag additional content to here.\n\
                        <p>Valid child Elements: Associates, Extra Contents, internal and/or external references</p>\n\
                    </div>");        
        
    clonedCurrentElement.attr("id", "copied_msm_theorem-"+_index);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
    theoremPartWrapper.append(partDndDiv);
    
    theoremAssociateDiv.append(dndDiv);
    
    theoremTitleContainer.append(theoremTitleHidden);
    
    theoremContentTitleContainer.append(theoremContentTitleHidden);
            
    theoremStatementWrapper.append(theoremContentTitleContainer);
    theoremStatementWrapper.append(theoremContentField); 
    theoremStatementWrapper.append(subordinateContainer); 
    theoremStatementWrapper.append(subordinateResult);
    theoremStatementWrapper.append(theoremPartWrapper);
            
    theoremContentWrapper.append(theoremStatementWrapper);
    
    overlayMenu.append(overlayButtonDelete);
    overlayMenu.append(overlayButtonEdit);
    
    selectAndTitleDiv.append(theoremSelectMenu);
    selectAndTitleDiv.append(theoremTitleField);
            
    clonedCurrentElement.append(theoremCloseButton);
    clonedCurrentElement.append(overlayMenu);
    clonedCurrentElement.append(theoremTitleContainer);
    clonedCurrentElement.append(selectAndTitleDiv)
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
            
            $(this).find('.msm_theorem_part_title').each(function() {
                tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));
                tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
            });
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#EDEDED");
            
            // if there are children in intro element, need to refresh the ifram of its editors
            $(this).find('.msm_unit_child_content').each(function() {
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    initEditor(this.id);                    
                }
            });
            
             $(this).find('.msm_theorem_part_title').each(function() {
                initTitleEditor(this.id, "85%");
                $(this).sortable("refresh");
            });
        }
    });    
    $("#msm_theorem_content_container-"+_index+"-1").sortable("refresh");

            
    return clonedCurrentElement;
}

/**
 * This method creates the form need to input the information for comment elements.
 * 
 * @return {object}               finished form in a container div
 */
function makeComment()
{
    checkIndexNumber("copied_msm_comment-"+_index);
    var clonedCurrentElement = $("<div></div>");
    var commentCloseButton = $('<a class="msm_element_close" style="margin-top: 2%;" onclick="deleteElement(event);">x</a>');
    
    var overlayMenu = $('<div class="msm_element_overlays" id="msm_element_overlay-'+_index+'" style="display: none;"></div>');
            
    var overlayButtonEdit = $('<a class="msm_overlayButtons" id="msm_overlayButton_edit-'+_index+'" onclick=editUnit(event)> Edit </a>');
    var overlayButtonDelete = $('<a class="msm_overlayButtons" id="msm_overlayButton_delete-'+_index+'" onclick="deleteOverlayElement(event);"> Delete </a>');
    
    var selectAndTitleDiv = $("<div class='msm_select_title_containers'></div>");
    var commentSelectMenu = $('<select name="msm_comment_type_dropdown-'+_index+'" class="msm_unit_child_dropdown" id="msm_comment_type_dropdown-'+_index+'">\n\
                                <option value="Comment">Comment</option>\n\
                                <option value="Remark">Remark</option>\n\
                                <option value="Information">Information</option>\n\
                            </select>');
    var commentTitleContainer = $('<div class="msm_element_title_containers" id="msm_element_title_container-'+_index+'"><b style="margin-left: 40%"> COMMENT </b></div>'); 
    var commentTitleHidden = $('<span style="visibility: hidden;">Drag here to move this element.</span>');
    var commentTitleField = $('<input class="msm_unit_child_title" id="msm_comment_title_input-'+_index+'" name="msm_comment_title_input-'+_index+'" placeholder=" Title of Comment"/>');
          
    var commentContentField = $('<textarea class="msm_unit_child_content" id="msm_comment_content_input-'+_index+'" name="msm_comment_content_input-'+_index+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-commentcontent'+_index+'"></div>');
    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-commentcontent'+_index+'"></div>');
    var commentDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_comment_description_label-"+_index+"' for='msm_comment_description_input-"+_index+"'>Description: </label>");
    var commentDescriptionField = $("<input class='msm_child_description_inputs' id='msm_comment_description_input-"+_index+"' name='msm_comment_description_input-"+_index+"' placeholder='Insert description to search this element in future. '/>");
    var commentAssociateDiv = $("<div class='msm_associate_containers' id='msm_associate_container-"+_index+"'></div>");
    var dndDiv = $("<div class='msm_dnd_containers' id='msm_dnd_container-"+_index+"'>Drag additional content to here.\n\
                        <p>Valid child Elements: Associates, internal and/or external references</p>\n\
                    </div>");
            
    clonedCurrentElement.attr("id", "copied_msm_comment-"+_index);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
    
    commentAssociateDiv.append(dndDiv);
    
    commentTitleContainer.append(commentTitleHidden);    
   
    overlayMenu.append(overlayButtonDelete);
    overlayMenu.append(overlayButtonEdit);
    
    selectAndTitleDiv.append(commentSelectMenu);
    selectAndTitleDiv.append(commentTitleField);
            
    clonedCurrentElement.append(commentCloseButton);
    clonedCurrentElement.append(overlayMenu);
    clonedCurrentElement.append(commentTitleContainer);

    clonedCurrentElement.append(selectAndTitleDiv);
    clonedCurrentElement.append(commentContentField);
    clonedCurrentElement.append(subordinateContainer);
    clonedCurrentElement.append(subordinateResult);
    clonedCurrentElement.append(commentDescriptionLabel);
    clonedCurrentElement.append(commentDescriptionField);
    clonedCurrentElement.append(commentAssociateDiv);
    
    return clonedCurrentElement;
}

/**
 * This method creates the form need to input the information for extra information elements.
 * 
 * @return {object}               finished form in a container div
 */
function makeExtraInfo()
{
    checkIndexNumber("copied_msm_extra_info-"+_index);
    
    var clonedCurrentElement = $("<div></div>");
    var extraInfoCloseButton = $('<a class="msm_element_close" style="margin-top: 2%;" onclick="deleteElement(event);">x</a>');
    
    var overlayMenu = $('<div class="msm_element_overlays" id="msm_element_overlay-'+_index+'" style="display: none;"></div>');
            
    var overlayButtonEdit = $('<a class="msm_overlayButtons" id="msm_overlayButton_edit-'+_index+'" onclick=editUnit(event)> Edit </a>');
    var overlayButtonDelete = $('<a class="msm_overlayButtons" id="msm_overlayButton_delete-'+_index+'" onclick="deleteOverlayElement(event);"> Delete </a>');
    
    var selectAndTitleDiv = $("<div class='msm_select_title_containers'></div>");
    var extraSelectMenu = $('<select name="msm_extra_type_dropdown-'+_index+'" class="msm_unit_child_dropdown" id="msm_extra_type_dropdown-'+_index+'">\n\
                                <option value="Summary">Summary</option>\n\
                                <option value="Historical">Historical Notes</option>\n\
                                <option value="Trailer">Trailer</option>\n\
                                <option value="Acknowledgements">Acknowledgements</option>\n\
                                <option value="Preface">Preface</option>\n\
                           </select>');
    var extraTitleContainer = $('<div class="msm_element_title_containers" id="msm_element_title_container-'+_index+'"><b style="margin-left: 30%;"> Extra Information </b></div>'); 
    var extraTitleHidden = $('<span style="visibility: hidden;">Drag here to move this element.</span>');
    var extraTitleField = $('<input class="msm_unit_child_title" id="msm_extra_title_input-'+_index+'" name="msm_extra_title_input-'+_index+'" placeholder=" Title of this extra information"/>');
          
    var extraContentField = $('<textarea class="msm_unit_child_content" id="msm_extra_content_input-'+_index+'" name="msm_extra_content_input-'+_index+'"/>');
    var subordinateContainer = $('<div class="msm_subordinate_containers" id="msm_subordinate_container-extracontent'+_index+'"></div>');
    var subordinateResult = $('<div class="msm_subordinate_result_containers" id="msm_subordinate_result_container-extracontent'+_index+'"></div>');
    
    clonedCurrentElement.attr("id", "copied_msm_extra_info-"+_index);
    clonedCurrentElement.attr("class", "copied_msm_structural_element");
    
    extraTitleContainer.append(extraTitleHidden);    
   
    overlayMenu.append(overlayButtonDelete);
    overlayMenu.append(overlayButtonEdit);
    
    selectAndTitleDiv.append(extraSelectMenu);
    selectAndTitleDiv.append(extraTitleField);
            
    clonedCurrentElement.append(extraInfoCloseButton);
    clonedCurrentElement.append(overlayMenu);
    clonedCurrentElement.append(extraTitleContainer);
    clonedCurrentElement.append(selectAndTitleDiv);
    clonedCurrentElement.append(extraContentField);
    clonedCurrentElement.append(subordinateContainer);
    clonedCurrentElement.append(subordinateResult);
    
    return clonedCurrentElement;
}

/**
 * This element check if the current element HTML ID exists in the form or not.
 * If the element ID already exists, then it increments the index number.
 * 
 * @param {string} oldid          current element id 
 */
function checkIndexNumber(oldid)
{
    $("#msm_child_appending_area").find(".copied_msm_structural_element").each(function() {
        if(this.id == oldid)
        {
            _index++;                
        }
    })
}