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