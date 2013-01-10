//tinyMCEPopup.requireLangPack();

var _subIndex = 1;

//var SubordinateDialog = {
function init(content, id){
    //    alert("init");
    var selectedText;
   
    selectedText= document.getElementById('msm_subordinate_highlighted-'+id);
   
    selectedText.value = content;     
}
    
        
function changeForm(e, id) {
    //    alert("changeForm");
    var container = document.getElementById("msm_subordinate_content_form_container-"+id);
    var selectVal;
    
    // default setting when the subordinate window is first shown
    
    switch(e)
    {
        case '':
            selectVal = 0;
            break;
        case "info":
            selectVal = 0;
            break;
        case "url":
            selectVal = 1;
            break;
        case "ir":
            selectVal = 2;
            break;
        case "er":
            selectVal = 3;
            break;
        default:
            selectVal = e.target.selectedIndex;
            break;
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
    console.log("id?: "+id);
    
    console.log(tinymce.editors);
    
    var titleid = "msm_subordinate_infoTitle-"+id;
    var contentid = "msm_subordinate_infoContent-"+id;
   
    // must remove the instance of editor that is being reinitialized, otherwise it creates multiple instances of the editor with the same id
    tinymce.execCommand('mceRemoveControl', false, titleid);
    tinymce.execCommand('mceRemoveControl', false, contentid);
    
    tinymce.settings = 
    {
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
    
    console.log(titleid);
    console.log(contentid);
    
    tinymce.execCommand('mceAddControl', false, titleid);
    tinymce.execCommand('mceAddControl', false, contentid);
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
    var selected = ed.selection.getNode();
    
    // checking if this selected text has already been submitted once 
    // if so, find the existing storage div and update the data with new ones
    // if not, then proceed to create a new storage div for the new subordinate data
    if(selected.tagName == "A")
    {
        var foundElement = findSubordinateResult(selected, id);
        
        if(foundElement)
        {    
            var oldIndex = foundElement.id.split('-');
            var oldId;
            var oldsId;
            
            if(oldIndex.length > 3)
            {
                oldId = oldIndex[1] + "-" + oldIndex[2];
                oldsId = oldIndex[3];
            }
            else
            {
                oldId = oldIndex[1];
                oldsId = oldIndex[2];
            }
            
            var resultContainer = document.getElementById(foundElement.id);
            
            createSubordinateData(oldId, oldsId, ed, resultContainer);
        }
    }
    else
    {   
        var subResultContainer = document.createElement("div");
        // id defines which editor the subordinate is from and _subIndex is related to the hot tagged word that this subordinate is associated with
        subResultContainer.id = "msm_subordinate_result-"+id+"-"+_subIndex;
        subResultContainer.setAttribute("style","display:none;");
    
        createSubordinateData(id, _subIndex, ed, subResultContainer);
        
        _subIndex++;
    }  
}

function createSubordinateData(id, sId, ed, subResultContainer)
{   
    var hasError;
    var errorArray;
    
    $("#msm_subordinate-"+id+" textarea").each(function(){ 
        $(this).val(tinymce.get(this.id).getContent({
            format: "text"
        }));
    
    });    
    
    var subSelectVal = $("#msm_subordinate_select-"+id).val();
    var infoTitleVal = $("#msm_subordinate_infoTitle-"+id).val();
    var infoContentVal = $("#msm_subordinate_infoContent-"+id).val();
    
    $("#"+subResultContainer.id).empty();
    
    var selectChoiceContainer = document.createElement("div");
    selectChoiceContainer.id = "msm_subordinate_select-"+id+"-"+sId;
 
    var selectChoiceContainerText = document.createTextNode(subSelectVal);
    selectChoiceContainer.appendChild(selectChoiceContainerText);
            
    var infoTitleContainer = document.createElement("div");
    infoTitleContainer.id = "msm_subordinate_infoTitle-"+id+"-"+sId;
            
    var infoTitleContainerText = document.createTextNode(infoTitleVal);
    infoTitleContainer.appendChild(infoTitleContainerText);
            
    var infoContentContainer = document.createElement("div");
    infoContentContainer.id = "msm_subordinate_infoContent-"+id+"-"+sId;
    
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
                urlContainer.id = "msm_subordinate_url-"+id+"-"+sId;
                        
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
        console.log(id);
        console.log(resultcontainer);
        
        resultcontainer.appendChild(subResultContainer);
        
        // swapping selected text as anchor element 
        var selectedText = ed.selection.getContent();
        
        if(ed.selection.getNode().tagName != "A")
        {
            ed.selection.setContent("<a href='#' class='msm_subordinate_hotwords' id='msm_subordinate_hotword-"+id+"-"+sId+"'>"+selectedText+"</a>");
        }      
        
        $('#msm_subordinate_container-'+id).dialog("close");
    }
    // null values are present
    else
    {          
        nullErrorWarning(errorArray, id);
    }
   
}

function nullErrorWarning(errorArray, id)
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

function loadValues(ed, id)
{
    var matchedElement;
    
    var selected = ed.selection.getNode();
    
    // previous value only exists if the node is already anchor element
    // if it's just a plain text element, then there are no existing values to be considered
    if(selected.tagName == 'A')
    {
        matchedElement = findSubordinateResult(selected, id);
        
        $("#"+matchedElement.id+" > div").each(function() {
            var divid = this.id.split("-");
            var formid;
            
            // eliminate the last number in id that indicates which anchored element it belongs to
            // b/c the editor id does not reflect this
            if(divid.length > 3)
            {
                formid = divid[0]+"-"+divid[1]+"-"+divid[2];
            }
            else
            {
                formid = divid[0]+"-"+divid[1];
            }
            
            var formData = this.innerHTML;            
            var editor = tinymce.get(formid);
            
            if(formid.match(/select/))
            {
                switch(formData)
                {
                    case "Information":
                        changeForm("info", id);                        
                        document.getElementById(formid).selectedIndex = 0;
                        break;
                    case "External Link":
                        changeForm("url", id);
                        document.getElementById(formid).selectedIndex = 1;
                        break;
                    case "Internal Reference":
                        changeForm("ir", id);
                        document.getElementById(formid).selectedIndex = 2;
                        break;
                    case "External Reference":
                        changeForm("er", id);
                        document.getElementById(formid).selectedIndex = 3;
                        break;
                }
            }
            
            if(typeof editor != "undefined")
            {
                editor.setContent(formData);
            }
            
            if(formid.match(/url/))
            {
                document.getElementById(formid).value = formData;
            }
        });
        
    }
    // the element is not an anchor element --> empty out the form so user can fill it in again
    else
    {
        // TODO need a function to eliminate nested anchor elements in the selected text
        // cases:
        // 1. user can have 2 separate words with one nested <a>
        // 2. user cna have 3+ separate words highlighted together with 1+ nested <a> in any order
        //        --> can also have middle of a word to middle of next word...etc
        //        --> therefore no assumption on spacing...
                
                
        //        var anchorElements = selected.getElementsByTagName("a");
        //        
        //        for(var i=0; i<anchorElements.length; i++)
        //        {
        //            var text = anchorElements[i].innerHTML;
        //            ed.selection.replaceChild(text, anchorElements[i]);
        //        }
        //        //        // nested anchored element
        //        //        if(ed.selection.getEnd().tagName == "A")
        //        //        {            
        //        //           
        //        //        }
        //        console.log("not anchor: "+ selected.tagName);
        
        var container = document.getElementById('msm_subordinate_content_form_container-'+id);
        
        // to prevent resetting the info form when it just was created in makeSubordinateDialog function
        if(container.hasChildNodes())
        {
            $('#msm_subordinate_content_form_container-'+id).empty();
            //            alert("form container emptied");
            container.appendChild(makeInfoForm(id))
        }
        
    }
    
}

/**
 *  This function finds the subordinate data submitted stored in the div and returns the 
 *  HTML element itself. Used to load the information that were submitted by the user already and
 *  allows for an editting function.
 *  
 *  @param selected    the element with highlighted text in editor
 *  @param id          the id number attached to the div to specify the subordinate data in question
 */
function findSubordinateResult(selected, id)
{
    var resultNumber;
    var selectedNumber;
    var matchedElement;
    
    var selectedId = selected.id.split("-");   
        
    $("#msm_subordinate_result_container-"+id + " > div").each(function() {
        var resultid = this.id.split("-"); 
            
        if(resultid.length > 3)
        {
            resultNumber = resultid[1] + "-" + resultid[2] + "-" + resultid[3];
            selectedNumber = selectedId[1] + "-" + selectedId[2] + "-" + selectedId[3];
        }
        else
        {
            resultNumber = resultid[1] + "-" + resultid[2];
            selectedNumber = selectedId[1] + "-" + selectedId[2];
        }
            
        if(resultNumber == selectedNumber)
        {
            matchedElement = this;
        }
    });
        
    return matchedElement;
}