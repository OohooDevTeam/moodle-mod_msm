/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*for possibly implementing activation of tinymce only when that specific text area is under focus...
 * suceeded in making this happen but once the tinymce was diabled, couldn't reactivate it when the textarea is selected again...*/
function showtinyMce(event)
{
    var elementid = event.target.id;  
    var currentEditorId = 0;
    var configArray = [{
        mode:"exact",
        elements: elementid,
        plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width: "96%",
        height: "70%",
        theme: "advanced",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,ltr,rtl",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,forecolor,backcolor",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        readonly: false,
        skin : "o2k7",
        skin_variant : "silver"
    },{
        mode : "textareas",
        theme : "advanced",
        readonly : true        
    }];
    
    for (var i = 0; i < tinymce.editors.length; i++)
    {        
        if((tinymce.editors[i].id != elementid) &&(!tinymce.editors[i].settings.readonly))
        { 
            currentEditorId = tinymce.editors[i].id;
            var idparts = currentEditorId.split("-");
            //            tinyMCE.execCommand("mceRemoveControl", true, currentEditorId);
            tinyMCE.settings = configArray[1];
            tinyMCE.execCommand('mceToggleEditor', true, currentEditorId);
            
            if((tinymce.editors[i].settings.readonly) && (document.getElementById('msm_content_edit-'+idparts[1])== null))
            {                
                var newButton = "<input type='button' class='msm_content_edit_buttons' id='msm_content_edit-"+idparts[1]+"' name='msm_content_edit_button' value='Edit Content' onclick='activateTinymce(event)'/>";               
                var typeofElement = idparts[0].split("_");                
                var spanID = typeofElement[0] + "_" + typeofElement[1] +"_edit_button_location-"+idparts[1];                
                document.getElementById(spanID).innerHTML = newButton; 
            }            
        }
    }  

    tinyMCE.settings = configArray[0];
    tinyMCE.execCommand('mceToggleEditor', true, elementid);
}

/**
 * need to close activated TinyMCE and activate tinyMCE in selected div
 * (attempt at reversing the process done from prev function)
 *
 */
function activateTinymce(e)
{
    alert("activated");
    var parentElementComponents = e.target.parentElement.id.split("_");
    var partOfParentId = parentElementComponents[0]+"_"+parentElementComponents[1];
    var idParts = parentElementComponents[4].split("-");
    var targetEditor = partOfParentId+"_content_input-"+idParts[1];
    // span of where the new edit button will be included
    var spanID = partOfParentId + "_edit_button_location-"+idParts[1];
    //    console.log(targetEditor);
    var configArray = [{
        mode:"exact",
        elements:  targetEditor,
        plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width: "96%",
        height: "70%",
        theme: "advanced",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,ltr,rtl",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,forecolor,backcolor",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        readonly: false,
        skin : "o2k7",
        skin_variant : "silver"
    },{
        mode : "textareas",
        theme : "advanced",
        readonly : true        
    }];

    
    for(var i=0; i < tinymce.editors.length; i++)
    {     
        //         console.log(tinymce.editors);
        var currentEditorId = tinymce.editors[i].id;
        
        console.log("editor #"+i+": "+currentEditorId);
       
        if(tinymce.editors[i].settings.readonly)
        {
            if(currentEditorId == targetEditor)
            {
                //                console.log("readonly:" +tinymce.editors[i].id);
                console.log("hey");
                                
                //                tinyMCE.execCommand("mceRemoveControl", true, currentEditorId);
                tinyMCE.settings = configArray[0];
                tinyMCE.execCommand('mceToggleEditor', true, currentEditorId);                
            //                document.getElementById(spanID).innerHTML = '';
            }
             
        }
    //        else
    //        {      
    //            //            console.log("not readonly: "+currentEditorId);
    //            tinyMCE.execCommand("mceRemoveControl", true, currentEditorId);
    //            tinyMCE.settings = configArray[1];
    //            tinyMCE.execCommand('mceAddControl', true, currentEditorId);
    //            
    //            var idNumber = currentEditorId.split("-");
    //            var selectedElement = currentEditorId.split("_");
    //            
    //            var spantoAddButton = selectedElement[0] + "_" + selectedElement[1] + "_edit_button_location-" + idNumber[1];
    //            
    //            var newButton = "<input type='button' class='msm_content_edit_buttons' id='msm_content_edit-"+idNumber[1]+"' name='msm_content_edit_button' value='Edit Content' onclick='activateTinymce(event)'/>";           
    //            document.getElementById(spantoAddButton).innerHTML = newButton;            
    //        }
    }
}

/**
 * This method is activated when the dragged item in the middle panel is double clicked.
 * It allows for the div to be resized by calling on jQuery UI resizable.
 * 
 * was developed when the def/theorem ...etc were gonna be resizable --> now only images to start off with
 */
function resizeElement(e)
{   
    var currentid = e.target.id;
    var match = currentid.match(/copied_/);
    var splittedType = e.target.id.split("_");
    var selectedID = splittedType[1]+"_"+splittedType[2]+"_"+splittedType[3];
    
    // to prevent textarea from being trigger to resize when double clicked
    if(match != null)
    {
        var originalWidth = $('#'+currentid).width();
        var currentWidth = 0;
        var xdifference = 0;
        var editorWidth = 0;
        var newEditorWidth = 0;
        
        var originalHeight = $('#'+currentid).height();
        var currentHeight = 0;
        var ydifference = 0;
        var editorHeight = 0;
        var newEditorHeight = 0;
        $('#'+currentid).resizable({
            containement: "#msm_editor_middle_droparea",
            //            ghost: true,
            disabled: false,
            helper: "ui-resizable-helper",
            minHeight: 330,
            minWidth: 605,            
            stop: function(event, ui)
            {
                // adjusting editor width and height accordingly when 
                // the parent div is resized
                currentWidth = $('#'+currentid).width();
                xdifference = originalWidth-currentWidth;
                
                editorWidth = $('#'+selectedID+'tbl').width();
                newEditorWidth = editorWidth-xdifference;
                
                $('#'+selectedID+'tbl').width(newEditorWidth);
                
                currentHeight = $('#'+currentid).height();
                ydifference = originalHeight-currentHeight;
                
                editorHeight = $('#'+selectedID+'tbl').height();
                newEditorHeight = editorHeight-ydifference;
                
                $('#'+selectedID+'tbl').height(newEditorHeight);   
            }
        });  
        
        $("#msm_editor_middle_droparea>*").not("#"+currentid).click(function() {
            $('#'+currentid).resizable("disable");
        });
        
        $("#"+currentid).click(function() {
            $('#'+currentid).resizable("enable");
        });
    }
}

/**
 * to get the choice of type drop down menu --> index for identifying html ID and childtype for distiguishing if
 * it's from def/theorem
 */
function processType(e)
{
    //    console.log(e);
    var selectedType = e.target.selectedIndex;
    var selectedChildElement = e.target.id;
    var selectedtext = null;
    
    var splitResult = selectedChildElement.split('_');
    
    if(splitResult[1] == "def")
    {
        switch(selectedType)
        {
            case 0:
                selectedtext = "Notation";
                break;
            case 1:
                selectedtext = "Definition";
                break;
            case 2:
                selectedtext = "Agreement";
                break;
            case 3:
                selectedtext = "Convention";
                break;
            case 4:
                selectedtext = "Axiom";
                break;
            case 5:
                selectedtext = "Terminology";
                break;
        }
    }
    else
    {
        switch(selectedType)
        {
            case 0:
                selectedtext = "Theorem";
                break;
            case 1:
                selectedtext = "Proposition";
                break;
            case 2:
                selectedtext = "Lemma";
                break;
            case 3:
                selectedtext = "Corollary";
                break;
        }
    }
    
    
    alert(selectedtext);
}

$("#msm_editor_save").click(function() {
    var children =  document.getElementById("msm_child_appending_area").childNodes;

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
        $("<div class=\"dialogs\" id=\"msm_emptyMsmTitle\"> Please specify the title of this unit. </div>").appendTo("#msm_unit_title");

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
        $("<button class=\"msm_editor_buttons\" id=\"msm_editor_edit\" type=\"button\" onclick=\"editUnit()\"> Edit </button>").appendTo("#msm_editor_middle");
        $("#msm_editor_reset").attr("disabled", "disabled");
    }

});

$(document).ready(function() {
     // for disabling the resize when focus is out of the picture 
               $("#msm_editor_container").click(function(e){
                    var matches = e.target.className.match(/msm_thumbnails/);
                    if(!matches)
                    {
                        $(".msm_thumbnails").resizable("destroy");
                    }
               });
});

// // case for processChild
// // need these variables
// var _imageIndex = 0;
//var _mediaIndex = 0;
//case "msm_pic":
//            var picCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
//            var picTitle = $("<span class='msm_element_title'><b> IMAGE </b></span><br><br>");
//            var picTitleLabel = $('<label class="msm_unit_pic_title_labels" id="msm_pic_title_label-'+_index+'" for="msm_pic_title_input-'+_index+'">Title of Image:</label>');
//            var picTitleField = $('<input class="msm_unit_pic_title" id="msm_pic_title_input-'+_index+'" name="msm_pic_title_input-'+_index+'" placeholder="Optional Title for the Image"/>');            
//            var picFilePicker = $('<br> <input type="file" class="msm_pic_filepicker" id="msm_pic_filepicker-'+_index+'" name="msm_pic_filepicker-'+_index+'" onchange="showImagePreview(event)"/>\n\
//                                        <br><output class="msm_file_lists" id="msm_file_list-//'+_index+'" name="msm_file_list-'+_index+'"></output>');
//            var picDescriptionLabel = $("<label class='msm_child_description_labels' id='msm_pic_description_label-"+_index+"' for='msm_pic_descripton_input-"+_index+"'>Description: </label>");
//            var picDescriptionField = $("<input class='msm_child_description_inputs' id='msm_pic_descripton_input-"+_index+"' name='msm_pic_descripton_input-"+_index+"' placeholder='Insert description to search this element in future. '/>");
//            var picCaptionField = $('<textarea class="msm_unit_child_content" id="msm_pic_content-'+_index+'" name="msm_pic_content-'+_index+'" placeholder="Caption for the image"/>');
//            
//            clonedCurrentElement.attr("id", "copied_msm_pic-"+_index);
//            clonedCurrentElement.attr("class", "copied_msm_structural_element");
//            
//            clonedCurrentElement.append(picCloseButton);
//            clonedCurrentElement.append(picTitle);
//            clonedCurrentElement.append(picTitleLabel);
//            clonedCurrentElement.append(picTitleField);
//            clonedCurrentElement.append(picFilePicker);                    
//            clonedCurrentElement.append(picCaptionField);   
//            clonedCurrentElement.append(picDescriptionLabel);
//            clonedCurrentElement.append(picDescriptionField);     
//            clonedCurrentElement.appendTo("#msm_child_appending_area");
//            
//            currentContentid = 'msm_pic_content-'+_index;
//            break;

//case "msm_media":
            //            var mediaCloseButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
            //            var mediaTitle = $("<span class='msm_element_title'><b> MEDIA </b></span><br><br>");
            //            var mediaTitleField = $('<input class="msm_unit_pic_title" id="msm_media_title_input-'+_index+'" name="msm_media_title" placeholder="Optional Title for the media element"/>');
            //            var mediaFilePicker = $('<br> <input type="file" class="msm_pic_filepicker" id="msm_media_filepicker-'+_index+'" name="msm_media_files[]" multiple onchange="showMediaPreview(event)"/>\n\
            //                                        <br><output class="msm_file_lists" id="msm_mediafile_list-//'+_index+'"></output>');
            //            var mediaCaptionField = $('<textarea class="msm_unit_child_content" id="msm_media_content-'+_index+'" name="msm_media_content" placeholder="Caption for the media element"/>');
            //            
            //            clonedCurrentElement.attr("id", "copied_msm_media-"+_index);
            //            clonedCurrentElement.attr("class", "copied_msm_structural_element");
            //            
            //            clonedCurrentElement.append(mediaCloseButton);
            //            clonedCurrentElement.append(mediaTitle);
            //            clonedCurrentElement.append(mediaTitleField);
            //            clonedCurrentElement.append(mediaFilePicker);
            //            clonedCurrentElement.append(mediaCaptionField);
            //            
            //            clonedCurrentElement.appendTo("#msm_child_appending_area");
//            break;   

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
                    image.onclick = function(){
                        $(this).resizable
                        ({
                            ghost: true,
                            create: function(event, ui)
                            {
                                var maxw = $('#msm_img_preview-'+filepickerId[1]+'-'+_imageIndex).width();
                                $(this).resizable("option", "maxWidth", maxw);
                            }
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
    tinyMCE.execCommand("mceAddControl", false, tinymce.Editor.id);    
}

/**
 * The method to show previews of video/audio...etc files to be added to the composition
 *
 */
//function showMediaPreview(evt)
//{
//    var filepickerId = evt.target.id.split("-")
//    var files = evt.target.files; // FileList object
//    
//    console.log(evt);
//    
//    _mediaIndex++;
//    
//    //    var outputElement = document.getElementById('msm_file_list-'+filepickerId[1]);
//    
//    // remove image inserted before --> according to XML schema, only one image allowed
//    //    if(outputElement.hasChildNodes())
//    //    {
//    //        $('#'+outputElement.firstChild.id).empty().remove();
//    //    }
//    
//    // Only process image files
//    if(files.length != 0) // condition to deal with canceled transaction in browse window
//    {
//        if (files[0].type.match('.mp4')) {
//            
//            var videoreader = new FileReader();
//            
//            videoreader.onload = (function(theMedia)
//            {
//                return function(e){
//                    var videoDiv = document.createElement("div");
//                    videoDiv.className = "flowplayer";
//                    videoDiv.id = "msm_media_preview_container-" + filepickerId[1] + "-" + _mediaIndex;
//                    
//                    var videoElement = document.createElement("video");
//                    videoElement.src = e.target.result;
//                    videoElement.id = "msm_video_clip-" + filepickerId[1] + "-" + _mediaIndex;
//                    videoElement.className = "msm_video_preview";
//                    videoElement.title = escape(theMedia.name);  
//                    
//                    videoDiv.appendChild(videoElement);
//                    document.getElementById('msm_mediafile_list-'+filepickerId[1]).insertBefore(videoDiv, null);
//                };
//            })(files[0]);
//            
//            videoreader.readAsDataURL(files[0]);
//        }
//        else if(files[0].type.match('audio.*'))
//        {
//            alert("audio!");
//        }
//        else
//        {
//            alert("wrong type of media");
//        }
//    }
//    
//}
