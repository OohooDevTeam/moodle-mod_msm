//tinyMCEPopup.requireLangPack();

//var SubordinateDialog = {
function init(){
        
    var selectedText = document.getElementById('msm_subordinate_highlighted');
        
    selectedText.value = tinyMCEPopup.editor.selection.getContent({
        format : 'text'
    });
        
//        var f = document.forms[0];
//        
//
//        // Get the selected contents as text and place it in the input
//        f.msm_subordinate_highlighted.value = tinyMCEPopup.editor.selection.getContent({
//            format : 'text'
//        });
}
    
        
function changeForm(e) {
    var selectVal = e.target.selectedIndex;
            
    // making a fieldset element for the info form (all selection will be using it
    // so make it available to all switch cases)    
    var fieldset = document.createElement("fieldset");
        
    var infoLegend = document.createElement("legend");
    var legendText = document.createTextNode("Subordinate Information Form");
    infoLegend.appendChild(legendText);
        
    var infotitlediv = document.createElement("div");
    var infocontentdiv = document.createElement("div");
        
    var infoTitle = document.createTextNode("Information Title: ");
    var infoContent = document.createTextNode("Information Content: ");
        
    var infoTitleInput = document.createElement("textarea");
    infoTitleInput.id = "msm_subordinate_infoTitle";
    infoTitleInput.name = "msm_subordinate_infoTitle";
    infoTitleInput.className = "msm_subordinate_textareas";
        
    var infoContentInput = document.createElement("textarea");
    infoContentInput.id = "msm_subordinate_infoContent";
    infoContentInput.name = "msm_subordinate_infoContent";
    infoContentInput.className = "msm_subordinate_textareas";
        
    fieldset.appendChild(infoLegend);
    infotitlediv.appendChild(infoTitle);
    infotitlediv.appendChild(infoTitleInput);
    fieldset.appendChild(infotitlediv);
        
        
    infocontentdiv.appendChild(infoContent);        
    infocontentdiv.appendChild(infoContentInput);
    fieldset.appendChild(infocontentdiv);
            
    var container = document.getElementById("msm_subordinate_content_form_container");
        
    switch(selectVal)
    {
        case 0:
            alert("none");
            break;
        case 1:
            container.appendChild(fieldset);
            break;
        case 2:
            alert("external link");
            break;
        case 3:
            alert("internal ref");
            break;
        case 4:
            alert("external ref");
            break;
    }
    tinyMCE.init({            
        mode:"exact",
        elements: "msm_subordinate_infoTitle",
        plugins : "subordinate,autolink,lists,advlist,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width: "96%",
        height: "70%",
        theme: "advanced",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,ltr,rtl,|,subordinate",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,forecolor,backcolor",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        skin : "o2k7",
        skin_variant : "silver"
    });
    tinyMCE.init({            
        mode:"exact",
        elements: "msm_subordinate_infoContent",
        plugins : "subordinate,autolink,lists,advlist,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width: "96%",
        height: "70%",
        theme: "advanced",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,ltr,rtl,|,subordinate",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,forecolor,backcolor",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        skin : "o2k7",
        skin_variant : "silver"
    });
}
//
//function insert() {
//    // Insert the contents from the input into the document
//    tinyMCEPopup.editor.execCommand('mceInsertContent', false, document.forms[0].someval.value);
//    tinyMCEPopup.close();
//}


//tinyMCEPopup.onInit.add(SubordinateDialog.init, SubordinateDialog);