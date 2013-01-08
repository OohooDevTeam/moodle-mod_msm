//tinyMCEPopup.requireLangPack();

var _subIndex = 1;

//var SubordinateDialog = {
function init(content, id){
    var selectedText;
   
    selectedText= document.getElementById('msm_subordinate_highlighted-'+id);
   
    selectedText.value = content;     
}
    
        
function changeForm(e, id) {
    var container = document.getElementById("msm_subordinate_content_form_container-"+id);
    var selectVal;
    
    // default setting when the subordinate window is first shown
    if(e == '')
    {
        selectVal = 0
    }
    else
    {
        selectVal = e.target.selectedIndex;
    }
            
    var fieldset = makeInfoForm(id);    
    
    //-----------------------------end of information form---------------------------------//
    
    //-----------------------------start of external url form-----------------------------//
    var urlfieldset = document.createElement("fieldset");
    urlfieldset.setAttribute("style", "border:1px solid black; padding: 2%; margin-top: 1%;");
    
    var urlLegend = document.createElement("legend");
    var urllegendText = document.createTextNode("External URL Form");
    urlLegend.appendChild(urllegendText);
    
    var urlLabel = document.createElement("label");
    var urlLabelText = document.createTextNode("External URL: ");
    urlLabel.setAttribute("for", "msm_subordinate_url-"+id);
    
    urlLabel.appendChild(urlLabelText);
    
    var urlInput = document.createElement("input");
    urlInput.setAttribute("type", "text"); //type url is not compatible with IE and safari
    urlInput.className = "msm_subordinate_urls";
    urlInput.id = "msm_subordinate_url-"+id;
    urlInput.name = "msm_subordinate_url-"+id;
    urlInput.setAttribute("style", "min-width: 83%;");
    
    urlfieldset.appendChild(urlLegend);
    urlfieldset.appendChild(urlLabel);
    urlfieldset.appendChild(urlInput);
    
    //-----------------------------end of external url form---------------------------------//
        
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
            container.appendChild(fieldset);
            break;
        case 1:
            container.appendChild(urlfieldset);
            container.appendChild(fieldset);
            break;
        case 2:
            alert("internal ref");
            break;
        case 3:
            alert("external ref");
            break;           
    }   
    
    initInfoEditor(id);
    
}

function initInfoEditor(id)
{
    tinyMCE.init({            
        mode:"exact",
        elements: "msm_subordinate_infoTitle-"+id,
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
        elements: "msm_subordinate_infoContent-"+id,
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

function makeInfoForm(id)
{
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
        
    infoTitleLabel.setAttribute("for", "msm_subordinate_infoTitle-"+id);        
    infoTitleLabel.appendChild(infoTitle);
        
    infoTitleInput.id = "msm_subordinate_infoTitle-"+id;
    infoTitleInput.name = "msm_subordinate_infoTitle-"+id;
    infoTitleInput.className = "msm_subordinate_textareas";        
        
    infoContentLabel.setAttribute("for", "msm_subordinate_infoContent-"+id);        
    infoContentLabel.appendChild(infoContent);
    
    infoContentInput.id = "msm_subordinate_infoContent-"+id;
    infoContentInput.name = "msm_subordinate_infoContent-"+id;
    infoContentInput.className = "msm_subordinate_textareas";
   
       
    fieldset.appendChild(infoLegend);
    infotitlediv.appendChild(infoTitleLabel);
    infotitlediv.appendChild(infoTitleInput);
    fieldset.appendChild(infotitlediv);        
        
    fieldset.appendChild(document.createElement("br"));
        
    infocontentdiv.appendChild(infoContentLabel);        
    infocontentdiv.appendChild(infoContentInput);
    fieldset.appendChild(infocontentdiv);
    
    return fieldset;
}

function closeSubFormDialog(id)
{
    $('#msm_subordinate_container-'+id).dialog("close");
}

// ed --> current editor that the plugin was triggered from
function submitSubForm(ed, id)
{
    var hasError = false;
    var errorArray = [];
    
    var subSelectVal = $("#msm_subordinate_select-"+id).val();
    $("textarea").each(function(){ 
        this.value = tinymce.get(this.id).getContent();
    });
    var infoTitleVal = $("#msm_subordinate_infoTitle-"+id).val();
    var infoContentVal = $("#msm_subordinate_infoContent-"+id).val();
    
    // TODO need validation methods
    
    var subResultContainer = document.createElement("div");
    // id defines which editor the subordinate is from and _subIndex is related to the hot tagged word that this subordinate is associated with
    subResultContainer.id = "msm_subordinate_result-"+id+"-"+_subIndex;
    subResultContainer.setAttribute("style","display:none;");
    
    //----All data associated with subordinate information(common for all select choices) ----//
    var selectChoiceContainer = document.createElement("div");
    selectChoiceContainer.id = "msm_subordinate_select-"+id+"-"+_subIndex;
 
    var selectChoiceContainerText = document.createTextNode(subSelectVal);
    selectChoiceContainer.appendChild(selectChoiceContainerText);
            
    var infoTitleContainer = document.createElement("div");
    infoTitleContainer.id = "msm_subordinate_infoTitle-"+id+"-"+_subIndex;
            
    var infoTitleContainerText = document.createTextNode(infoTitleVal);
    infoTitleContainer.appendChild(infoTitleContainerText);
            
    var infoContentContainer = document.createElement("div");
    infoContentContainer.id = "msm_subordinate_infoContent-"+id+"-"+_subIndex;
    
    if(infoContentVal != '')
    {
        var infoContentContainerText = document.createTextNode(infoContentVal);
        infoContentContainer.appendChild(infoContentContainerText);  
    }
    else
    {
        hasError = true;
        errorArray.push("#msm_subordinate_infoContent-"+id+"_ifr");
    }
      
    //-----------------------------------------------------------------------------------//
    switch(subSelectVal)
    {
        case "Information":
            subResultContainer.appendChild(selectChoiceContainer);
            subResultContainer.appendChild(infoTitleContainer);
            subResultContainer.appendChild(infoContentContainer);
            break;
            
        case "External Link":
            var urlInputValue = $("#msm_subordinate_url-"+id).val();
            
            if(urlInputValue != '')
            {
                var urlContainer = document.createElement("div");
                urlContainer.id = "msm_subordinate_url"+id+"-"+_subIndex;
                        
                var urlContainerText = document.createTextNode(urlInputValue);
                urlContainer.appendChild(urlContainerText);
                        
                subResultContainer.appendChild(selectChoiceContainer);    
                subResultContainer.appendChild(urlContainer);                   
                subResultContainer.appendChild(infoTitleContainer);
                subResultContainer.appendChild(infoContentContainer);
            }
            else
            {
                hasError = true;
                errorArray.push("#msm_subordinate_url-"+id);
            }
            break;
            
              
        case "Internal Reference":
            break;
        case "External Reference":
            break;          
          
    }
        
    if(!hasError)
    {
        // insert the div storing the result of subordinate form as nextsibling of textarea that triggered the subordinate plugin
        var resultcontainer = document.getElementById("msm_subordinate_result_container-"+id);
        resultcontainer.appendChild(subResultContainer);
        
        // swapping selected text as anchor element 
        var selectedText = ed.selection.getContent();
        ed.selection.setContent("<a href='#' class='msm_subordinate_hotwords' id='msm_subordinate_hotword-"+id+"-"+_subIndex+"'>"+selectedText+"</a>");
        
        $('#msm_subordinate_container-'+id).dialog("close");
        _subIndex++;
    }
    // null values are present
    else
    {
        for(var i=0; i < errorArray.length; i++)
        {
            var match = errorArray[i].match(/subordinate.url./);
            
            if(match)
            {
                $(errorArray[i]).css("border", "solid 4px #FFA500");
            }
            else
            {
                $(errorArray[i]).parent().css("border", "solid 4px #FFA500");
            }
        }
                
        $("<div class=\"dialogs\" id=\"msm_emptySubContent\"> Please fill out the highlighted areas to complete the form. </div>").appendTo('#msm_subordinate_container-'+id);

        $("#msm_emptySubContent").dialog({
            modal: true,
            buttons: {
                Ok: function() {
                    $(this).dialog("close");
                }
            }
        }); 
    }
}
