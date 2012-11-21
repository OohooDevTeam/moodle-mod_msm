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
var _imageIndex = 0;

/**
 * This function is activated when user drags one of the structural elememts on the very left side of the panel to middle panel.
 * It adds appropriate fields for the users to fill out for def/theorem/comments/info/content/media and images.
 */
function processDroppedChild(e, droppedId)
{ 
    var clonedCurrentElement = $("<div></div>");
    
    _index++;
    
    switch(droppedId)
    {
        case "msm_def":
            var defCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event);">x</a>');
            var defSelectMenu = $('<select class="msm_unit_child_dropdown" id="msm_def_type_dropdown-'+_index+'" onchange="processType(event);">\n\
                                <option value="Notation">Notation</option>\n\
                                <option value="Definition">Definition</option>\n\
                                <option value="Agreement">Agreement</option>\n\
                                <option value="Convention">Convention</option>\n\
                                <option value="Axiom">Axiom</option>\n\
                                <option value="Terminology">Terminology</option>\n\
                            </select>');
            var defTitleField = $('<input class="msm_unit_child_title" id="msm_def_title_input-'+_index+'" name="msm_def_title" placeholder=" Title of Definition"/>');
          
            var defContentField = $('<textarea class="msm_unit_child_content" id="msm_def_content_input-'+_index+'" name="msm_def_content" placeholder="Need to add moodle form here?"/>');
            
            var defAssoMenu = $('<div class="msm_associate_optionarea"><b> Choose an associated information: </b>\n\
                            <select class="msm_associated_dropdown" id="msm_def_associate_dropdown-'+_index+'" onchange="processAssociate(event);">\n\
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
            clonedCurrentElement.append(defTitleField);
            clonedCurrentElement.append(defContentField);
            clonedCurrentElement.append(defAssoMenu);
            clonedCurrentElement.appendTo('#msm_child_appending_area');
            break;
        
        case "msm_theorem":
            var theoremCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');

            var theoremSelectMenu = $('<select class="msm_unit_child_dropdown" id="msm_theorem_type_dropdown-'+_index+'" onchange="processType(event);">\n\
                                <option value="Theorem">Theorem</option>\n\
                                <option value="Proposition">Proposition</option>\n\
                                <option value="Lemma">Lemma</option>\n\
                                <option value="Corollary">Corollary</option>\n\
                            </select>');
            var theoremTitleField = $('<input class="msm_unit_child_title" id="msm_theorem_title_input-'+_index+'" name="msm_theorem_title" placeholder=" Title of Theorem"/>');
            var theoremContentField = $('<textarea class="msm_unit_child_content" id="msm_theorem_content_input-'+_index+'" name="msm_theorem_content" placeholder=" Need to add moodle form here?"/>');

            var theoremAssoMenu = $('<div class="msm_associate_optionarea"><b> Choose an associated information: </b>\n\
                            <select class="msm_associated_dropdown" id="msm_theorem_associate_dropdown-'+_index+'" onchange="processAssociate(event);">\n\
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
            clonedCurrentElement.append(theoremTitleField);
            clonedCurrentElement.append(theoremContentField);
            clonedCurrentElement.append(theoremAssoMenu);
            clonedCurrentElement.appendTo('#msm_child_appending_area');
            break;
            
        case "msm_pic":
            var picCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
            var picTitleField = $('<input class="msm_unit_pic_title" id="msm_pic_title_input-'+_index+'" name="msm_pic_title" placeholder="Optional Title for the Image"/>');
            var picFilePicker = $('<br> <input type="file" class="msm_pic_filepicker" id="msm_pic_filepicker-'+_index+'" name="msm_pic_files[]" multiple onchange="showImagePreview(event)"/>\n\
                                        <br><output class="msm_file_lists" id="msm_file_list-'+_index+'"></output>');
            var picCaptionField = $('<textarea class="msm_unit_child_content" id="msm_pic_content-'+_index+'" name="msm_pic_content" placeholder="Caption for the image"/>');
            
            clonedCurrentElement.attr("id", "copied_msm_pic-"+_index);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
            clonedCurrentElement.append(picCloseButton);
            clonedCurrentElement.append(picTitleField);
            clonedCurrentElement.append(picFilePicker);
            clonedCurrentElement.append(picCaptionField);
            
            clonedCurrentElement.appendTo("#msm_child_appending_area");
            break;
            
        case "msm_intro":
            var introCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
            var introTitle = $('<h3> Introduction </h3>');           
            var introContentField = $('<textarea class="msm_unit_child_content" id="msm_intro_content_input-'+_index+'" name="msm_intro_content" placeholder=" Need to add moodle form here?"/>');

            
            clonedCurrentElement.attr("id", "copied_msm_intro-"+_index);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            
            clonedCurrentElement.append(introCloseButton);
            clonedCurrentElement.append(introTitle);           
            clonedCurrentElement.append(introContentField);
            clonedCurrentElement.appendTo('#msm_child_appending_area');
            break;
            
        case "msm_body":
            var bodyCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
            var bodyContentField = $('<textarea class="msm_unit_child_content" id="msm_body_content_input-'+_index+'" name="msm_body_content" placeholder=" Need to add moodle form here?"/>');

            clonedCurrentElement.attr("id", "copied_msm_body-"+_index);
            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            clonedCurrentElement.append(bodyCloseButton);
            clonedCurrentElement.append(bodyContentField);
            clonedCurrentElement.appendTo('#msm_child_appending_area');
            break;
            
        case "msm_media":
            alert("media");
            break;       
            
    }
    if($('#msm_editor_save').attr("disabled"))
    {
        $('#msm_editor_save').removeAttr('disabled');
    }
    
    tinyMCE.init({
        mode:"textareas",
        plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width: "96%",
        height: "70%",
        theme: "advanced",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,cleanup,help,code,|,insertdate,inserttime,preview",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,advhr,|,ltr,rtl",
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
        start: function(event, ui)
        {
            $(".msm_sortable_placeholder").width(ui.item.context.offsetWidth);
        }
    });
                
    $("#msm_child_appending_area").disableSelection();
}

/**
 *  This function is responsible for displaying the preview of the image chosen by the user
 *  in the editor.
 *
 */
function showImagePreview(evt)
{
    var filepickerId = evt.target.id.split("-")
    var files = evt.target.files; // FileList object
    
    _imageIndex++;
    
    var outputElement = document.getElementById('msm_file_list-'+filepickerId[1]);
    
    // remove image inserted before --> according to XML schema, only one image allowed
    if(outputElement.hasChildNodes())
    {
        $('#'+outputElement.firstChild.id).empty().remove();
    }
    
    // Only process image files
    if(files.length != 0) // condition to deal with canceled transaction in browse window
    {
        if (files[0].type.match('image.*')) {
            var reader = new FileReader();

            // Closure to capture the file information.
            reader.onload = (function(theFile) {
                return function(e) {
                    // Render thumbnail.
                    var imageDiv = document.createElement('div');
                    imageDiv.className = "msm_img_previews";
                    imageDiv.id = "msm_img_preview-"+filepickerId[1]+"-"+_imageIndex;
                
                    var image = document.createElement("img");
                    image.id = "msm_img_thumbnail-"+filepickerId[1]+"-"+_imageIndex;
                    image.className = "msm_thumbnails";
                    image.src = e.target.result;
                    image.title = escape(theFile.name);            
                   
                    //                    var isDown = false;
                    image.onclick = function(){
                        $(this).resizable
                        ({
                            ghost: true,
                            create: function(event, ui)
                            {
                                var maxw = $('#msm_img_preview-'+filepickerId[1]+'-'+_imageIndex).width();
                                $(this).resizable("option", "maxWidth", maxw);
                            }
                        //                            resize: function(event, ui)
                        //                            {
                        //                                
                        //                                $("table.mceLayout > *").mouseup(function(){
                        //                                    $(".msm_thumbnails").resizable("widget").trigger("mouseup");
                        //                                    if(isDown)
                        //                                    {
                        //                                        alert("trigger2");
                        //                                        $(".msm_thumbnails").resizable("widget").trigger("mouseup");
                        //                                    }
                        //                                })
                        //                                                      
                        //                            }
                        });                        
                    };

                    imageDiv.appendChild(image);
                    document.getElementById('msm_file_list-'+filepickerId[1]).insertBefore(imageDiv, null);
                    
                    //resizing the image so it fits into the div                    
                    var imageWidth = document.getElementById('msm_img_thumbnail-'+filepickerId[1]+'-'+_imageIndex).width;
                    var divWidth = imageDiv.offsetWidth;
                    
                    if(imageWidth >= divWidth)
                    {
                        image.width = divWidth;
                    }
                };
            })(files[0]);

            // Read in the image file as a data URL.
            reader.readAsDataURL(files[0]);
        }
        else
        {
            // alert dialog to notify the user to select only image file
            $("<div class='dialogs' id='msm_wrongFileType'> Please select an image file. </div>").appendTo('#msm_pic_filepicker-'+_index);
            
            $( "#msm_wrongFileType" ).dialog({
                modal: true,
                buttons: {
                    Ok: function(event, ui) {
                        //removing the wrong file from event object
                        document.getElementById('msm_pic_filepicker-'+_index).value = '';
                        $( this ).dialog( "close" );
                    }
                }
            });
        }
    }
    
    // reinitialize tinyMCE 
    tinyMCE.init({
        mode:"textareas",
        plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width: "96%",
        height: "70%",
        theme: "advanced",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,cleanup,help,code,|,insertdate,inserttime,preview",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,advhr,|,ltr,rtl",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,forecolor,backcolor",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        skin : "o2k7",
        skin_variant : "silver"
    });
    
}

/**
 * to save contents created in middle editor panel
 *  --> diplay change: from filled in content --> maintain same content in middle but with edit button instead of  & thumbnail of composed unit added to tree in right panel
 *  --> disable drag&drop/resize...etc
 */
function saveUnit()
{
    $('#msm_editor_save').ready(function() {
        var titleinput = $('#msm_unit_title').val();
        console.log(titleinput);
        if((titleinput == null)||(titleinput == ''))
        {
            $("<div class='dialogs' id='msm_emptyMsmTitle'> Please specify the title of this unit. </div>").appendTo('#msm_unit_title');
            
            $( "#msm_emptyMsmTitle" ).dialog({
                modal: true,
                buttons: {
                    Ok: function() {
                        $('#msm_unit_title').css('border-color', '#FFA500');
                        $( this ).dialog( "close" );
                    }
                }
            });   
        }
        else
        {
            $('#msm_editor_save').remove();
            $('<button class="msm_editor_buttons" id="msm_editor_edit" type="button" onclick="editUnit()"> Edit </button>').appendTo('#msm_editor_middle');
            $('#msm_editor_reset').attr("disabled", "disabled");
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

