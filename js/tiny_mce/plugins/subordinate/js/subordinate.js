//tinyMCEPopup.requireLangPack();

//var SubordinateDialog = {
function init(content, id1, id2){
    var selectedText;
    if(id2 != '')
    {
        selectedText= document.getElementById('msm_subordinate_highlighted-'+id1+'-'+id2);
    }
    else
    {
        selectedText= document.getElementById('msm_subordinate_highlighted-'+id1);
    }     
        
    selectedText.value = content; 
}
    
        
function changeForm(e, id1, id2) {
    var container;
    var selectVal = e.target.selectedIndex;
            
    // making a fieldset element for the info form (all selection will be using it
    // so make it available to all switch cases)    
    var fieldset = document.createElement("fieldset");
    fieldset.setAttribute("style", "border:1px solid black; padding: 2%; margin-top: 1%;");
        
    var infoLegend = document.createElement("legend");
    var legendText = document.createTextNode("Subordinate Information Form");
    infoLegend.appendChild(legendText);
        
    var infotitlediv = document.createElement("div");
    var infocontentdiv = document.createElement("div");
    
    var infoTitleLabel = document.createElement("label");
    var infoContentLabel = document.createElement("label");
        
    var infoTitle = document.createTextNode("Information Title: ");
    var infoContent = document.createTextNode("Information Content: ");
    
    var infoTitleInput = document.createElement("textarea");
    var infoContentInput = document.createElement("textarea");
    if(id2 != '')
    {
        container= document.getElementById("msm_subordinate_content_form_container-"+id1+"-"+id2);
        
        infoTitleLabel.setAttribute("for", "msm_subordinate_infoTitle-"+id1+"-"+id2);        
        infoTitleLabel.appendChild(infoTitle);
        
        infoTitleInput.id = "msm_subordinate_infoTitle-"+id1+"-"+id2;
        infoTitleInput.name = "msm_subordinate_infoTitle-"+id1+"-"+id2;
        infoTitleInput.className = "msm_subordinate_textareas";        
        
        infoContentLabel.setAttribute("for", "msm_subordinate_infoContent-"+id1+"-"+id2);        
        infoContentLabel.appendChild(infoContent);
    
        infoContentInput.id = "msm_subordinate_infoContent-"+id1+"-"+id2;
        infoContentInput.name = "msm_subordinate_infoContent-"+id1+"-"+id2;
        infoContentInput.className = "msm_subordinate_textareas";
    }
    else
    {
        container= document.getElementById("msm_subordinate_content_form_container-"+id1);
        
        infoTitleLabel.setAttribute("for", "msm_subordinate_infoTitle-"+id1);        
        infoTitleLabel.appendChild(infoTitle);
        
        infoTitleInput.id = "msm_subordinate_infoTitle-"+id1;
        infoTitleInput.name = "msm_subordinate_infoTitle-"+id1;
        infoTitleInput.className = "msm_subordinate_textareas";     
        
        infoContentLabel.setAttribute("for", "msm_subordinate_infoContent-"+id1);        
        infoContentLabel.appendChild(infoContent);
    
        infoContentInput.id = "msm_subordinate_infoContent-"+id1;
        infoContentInput.name = "msm_subordinate_infoContent-"+id1;
        infoContentInput.className = "msm_subordinate_textareas";
    }
       
    fieldset.appendChild(infoLegend);
    infotitlediv.appendChild(infoTitleLabel);
    infotitlediv.appendChild(infoTitleInput);
    fieldset.appendChild(infotitlediv);        
        
    fieldset.appendChild(document.createElement("br"));
        
    infocontentdiv.appendChild(infoContentLabel);        
    infocontentdiv.appendChild(infoContentInput);
    fieldset.appendChild(infocontentdiv);
        
    // erasing any previously appended children from previous choices made by the user
    if(container.hasChildNodes())
    {
        while(container.firstChild)
        {
            container.removeChild(container.firstChild);
        }
    }
    
        
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
    
    if(id2 != '')
    {
        tinyMCE.init({            
            mode:"exact",
            elements: "msm_subordinate_infoTitle-"+id1+"-"+id2,
            plugins : "subordinate,autolink,lists,advlist,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
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
            skin : "o2k7",
            skin_variant : "silver"
        });
        tinyMCE.init({            
            mode:"exact",
            elements: "msm_subordinate_infoContent-"+id1+"-"+id2,
            plugins : "subordinate,autolink,lists,advlist,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
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
            skin : "o2k7",
            skin_variant : "silver"
        }); 
    }
    else
    {
        tinyMCE.init({            
            mode:"exact",
            elements: "msm_subordinate_infoTitle-"+id1,
            plugins : "subordinate,autolink,lists,advlist,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
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
            skin : "o2k7",
            skin_variant : "silver"
        });
        tinyMCE.init({            
            mode:"exact",
            elements: "msm_subordinate_infoContent-"+id1,
            plugins : "subordinate,autolink,lists,advlist,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
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
            skin : "o2k7",
            skin_variant : "silver"
        });
    }
   
}

function closeSubFormDialog(id1, id2)
{
    if (id2 != '')
    {
        $('#msm_subordinate_container-'+id1+'-'+id2).dialog("close");

    }
    else
    {
        $('#msm_subordinate_container-'+id1).dialog("close");

    }
}
//
//function insert() {
//    // Insert the contents from the input into the document
//    tinyMCEPopup.editor.execCommand('mceInsertContent', false, document.forms[0].someval.value);
//    tinyMCEPopup.close();
//}


//tinyMCEPopup.onInit.add(SubordinateDialog.init, SubordinateDialog);