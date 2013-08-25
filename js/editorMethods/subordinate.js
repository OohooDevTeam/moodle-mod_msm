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

/* This js file contains all the function used by subordinate tinyMCE plugin to create the plugin UI and to process the input properly*/

// global variable to keep track of subordinates created and to avoid having duplicate HTML IDs by
// adding this number to end of HTML IDs
var _subIndex = 1;

/**
 * This method is called everytime the dropdown menu in the subordinate dialog is changed to vary the type of the subordinate.
 * It erases the child components of the form and creates new form elements that is appropriate for chosen type.
 *
 * @param {eventObject} e               event object for when the dropdown menu changes to specify the type of subordinate
 * @param {tinymce.Editor} ed           current editor that the subordinate plugin was triggered from
 * @param {string} id                   ending of this subordinat dialog("ending of parent element ID - number that is incremented per subordinate)
 */
function changeForm(e, ed, id) {
    var container = document.getElementById("msm_subordinate_content_form_container-"+id);
    var selectVal;
        
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
    
    var prevValues = [];
    
    // between changes of type of subordinate, the information element is common between them
    // so whenever the form component is chaned, keep the information element values and carry it over
    // to the new information form.
    $("#msm_subordinate_form-"+id).find(".msm_subordinate_textareas").each(function() {
        if(this.id == "msm_subordinate_infoTitle-"+id)
        {
            prevValues["msm_subordinate_infoTitle"] = tinymce.get(this.id).getContent({
                format: "html"
            });
        }
        else if(this.id == "msm_subordinate_infoContent-"+id)
        {
            prevValues["msm_subordinate_infoContent"] = tinymce.get(this.id).getContent({
                format: "html"
            });
        }
    });
    
    // create new information element forms
    var fieldset = makeInfoForm(ed, id, false);
    
    //-----------------------------start of external url form-----------------------------//
    var urlFieldSet = appendUrlForm(id);
    
    if(container.hasChildNodes())
    {
        while(container.firstChild)
        {
            container.removeChild(container.firstChild);
        }
    }
    // remove URL form if already attached
    if($("#msm_subordinate_url-"+id))
    {
        $("#msm_subordinate_url-"+id).parent().empty().remove();
    }
    
    var msmIdInfo = window.location.search.split("=");
    var msmId = msmIdInfo[1];
    
    var accordionContainer = document.createElement("div");

    var refString = '';    
    
    $("#msm_subordinate_select-"+id).prop("selectedIndex", selectVal);
     
    switch(selectVal)
    {
        case 0: // information selected
            container.appendChild(fieldset);
            break;
        case 1: // external link selected
            container.appendChild(urlFieldSet);
            container.appendChild(fieldset);
            break;
        case 2: // internal reference selected
            accordionContainer.id = "msm_subordinate_accordion-"+id;
            container.appendChild(accordionContainer);
            makeRefForm(ed, id, '');
            refString = "Internal References";
            break;
        case 3: // external reference selected
            accordionContainer.id = "msm_subordinate_accordion-"+id;
            container.appendChild(accordionContainer);
            makeRefForm(ed, id, '');
            refString = "External References";
            break;
        
    }
    
    // for jQuery UI accordion plugin used for external/internal referencing
    $("#msm_subordinate_accordion-"+id).accordion({
        heightStyle: "content",
        beforeActivate: function(e, ui) {
            if(ui.newPanel[0].id.match(/msm_info_accordion/))
            {
                $(".msm_subordinate_textareas").each(function() {
                    if(typeof tinymce.getInstanceById(this.id) === "undefined")
                    {
                        tinymce.execCommand("mceAddControl", true, this.id);
                    }                    
                });
            }
        }
    });
    // for submitting the search param for internal/external referencing
    $("#msm_search_submit").click(function(e) {
        submitAjax(refString, msmId, id, "subordinate");
    });
    
    // insert the values already in the previous information form before change trigger for
    // dropdown menu into new information element form
    $("#msm_subordinate_form-"+id).find(".msm_subordinate_textareas").each(function() {
        if(this.id == "msm_subordinate_infoTitle-"+id)
        {
            $(this).val(prevValues["msm_subordinate_infoTitle"]);
        }
        else if(this.id == "msm_subordinate_infoContent-"+id)
        {
            $(this).val(prevValues["msm_subordinate_infoContent"]);
        }
    });
    
    // initialize the tinyMCE editor
    initInfoEditor(id);
}

// existingRefId is given by msm_subordinate_ref-id result div and is a
// compositor ID for existing reference
/**
 * This method creates forms needed to search the existing materials to be added as a reference material.
 * The container div for internal/external reference forms are displayed using jQuery UI accordion plugin.
 *
 * @param {tinymce.Editor} ed           current editor that the subordinate plugin was triggered from
 * @param {string} id                   ending of this subordinat dialog("ending of parent element ID - number that is incremented per subordinate)
 * @param {string} existingRefId        database ID of the internal/external reference that was already saved into db(used to load existing subordinate)
 *                                      or if there are no existing subordinate and reference form is being created for the first time, then value is empty string
 */
function makeRefForm(ed, id, existingRefId)
{
    var infoAccordionHeader = $("<h3> Information Form </h3>");
    var infoFormFieldset = makeInfoForm(ed, id, true);
    var infoDiv = $("<div id='msm_info_accordion_container-"+id+"'></div>");
    
    $(infoDiv).append(infoFormFieldset);
    
    $("#msm_subordinate_accordion-"+id).append(infoAccordionHeader);
    $("#msm_subordinate_accordion-"+id).append(infoDiv);
    
    // subordinate does not have existing references --> so load the search form
    if(existingRefId == '')
    {
        var searchAccordionHeader = $("<h3> Search Form </h3>");
        var searchDiv = $("<div>\n\
                                <form id='msm_search_form'>\n\
                                    <label for='msm_search_type'>Type: </label>\n\
                                    <select id='msm_search_type' name='msm_search_type'>\n\
                                        <option value='definition'>Definition</option>\n\
                                        <option value='theorem'>Theorem</option>\n\
                                        <option value='comment'>Comment</option>\n\
                                    </select>\n\
                                    <br /><br />\n\
                                    <label for='msm_search_word'>Search: </label>\n\
                                    <input id='msm_search_word' name='msm_search_word' style='width: 74.5%;'/>\n\
                                    <select id='msm_search_word_type' name='msm_search_word_type' style='margin-left: 1%;'>\n\
                                        <option value='title'>Title</option>\n\
                                        <option value='content'>Content</option>\n\
                                        <option value='description'>Description</option>\n\
                                        <option value='all'>Title/Content/Description</option>\n\
                                    </select>\n\
                                    <br /><br />\n\
                                    <input type='button' value='Search' id='msm_search_submit' class='msm_search_buttons'/>\n\
                                </form>\n\
                            </div>");
        
        $("#msm_subordinate_accordion-"+id).append(searchAccordionHeader);
        $("#msm_subordinate_accordion-"+id).append(searchDiv);
    
        var searchResultAccordionHeader = $("<h3> Search Results </h3>");
        var searchResultAccordionDiv = $("<div id='msm_search_result'></div>");
    
        $("#msm_subordinate_accordion-"+id).append(searchResultAccordionHeader);
        $("#msm_subordinate_accordion-"+id).append(searchResultAccordionDiv);
    
        $("input#msm_search_word").css("width", "74.5%");
    }
    // the subordinate have already exisitng internal/external references
    // --> so load the previously chosen reference material
    else
    {
        $("#msm_subordinate_select-"+id).attr("disabled", "disabled");
        
        var overlayButtonEdit = $('<a class="msm_overlayButtons" id="msm_search_again_button-'+id+'")> Search Again </a>');
        var overlayButtonDelete = $('<a class="msm_overlayButtons" id="msm_delete_search-'+id+'"> Delete </a>');
                
        var displayHeader = $("<h3> Current Reference Material </h3>");
        var displayDiv = $("<div id='msm_subordinate_ref_display'></div>");      
        
        $("#msm_subordinate_accordion-"+id).append(displayHeader);
        $("#msm_subordinate_accordion-"+id).append(displayDiv);
        
        // AJAX call to load and display the exisitng reference
        $.ajax({
            type: 'POST',
            url:"editorCreation/msmLoadUnit.php",
            data: {
                loadRefId: existingRefId
            },
            success: function(data)
            {
                var htmlString = JSON.parse(data);              
                
                $("#msm_subordinate_ref_display").append(htmlString);
                
                $("#msm_subordinate_ref_display").append(overlayButtonDelete);
                $("#msm_subordinate_ref_display").append(overlayButtonEdit);
                
                // this code is needed to view dialog popup windows associated with subordinates
                // in the existing reference material
                $("#msm_subordinate_ref_display .msm_info_dialogs").dialog({
                    autoOpen: false,
                    height: "auto",
                    modal: false,
                    width: 605
                });
                                
                $("#msm_subordinate_ref_display").find(".msm_subordinate_hotwords").each(function(i, element) {
                    var idInfo = this.id.split("-");
                    var newid = idInfo[1];                                                    
                    for(var i = 2; i < idInfo.length; i++)
                    {
                        newid += "-"+idInfo[i];
                    }                                                    
                                                                           
                    previewInfo(this.id, "dialog-"+newid);
                   
                });
                
                // user chooses to search for a different existing material to be referenced
                // --> remove all display data and replace with search param again
                $("#msm_search_again_button-"+id).click(function() {
                    $("#msm_subordinate_select-"+id).removeAttr("disabled");
                    $("#msm_subordinate_accordion-"+id).accordion("destroy");
                    
                    $(displayHeader).empty().remove();
                    $(displayDiv).empty().remove();
                   
                    var searchAccordionHeader = $("<h3> Search Form </h3>");
                    var searchDiv = $("<div>\n\
                                <form id='msm_search_form'>\n\
                                    <label for='msm_search_type'>Type: </label>\n\
                                    <select id='msm_search_type' name='msm_search_type'>\n\
                                        <option value='definition'>Definition</option>\n\
                                        <option value='theorem'>Theorem</option>\n\
                                        <option value='comment'>Comment</option>\n\
                                    </select>\n\
                                    <br /><br />\n\
                                    <label for='msm_search_word'>Search: </label>\n\
                                    <input id='msm_search_word' name='msm_search_word' style='width: 74.5%;'/>\n\
                                    <select id='msm_search_word_type' name='msm_search_word_type' style='margin-left: 1%;'>\n\
                                        <option value='title'>Title</option>\n\
                                        <option value='content'>Content</option>\n\
                                        <option value='description'>Description</option>\n\
                                        <option value='all'>Title/Content/Description</option>\n\
                                    </select>\n\
                                    <br /><br />\n\
                                    <input type='button' value='Search' id='msm_search_submit' class='msm_search_buttons'/>\n\
                                </form>\n\
                            </div>");
        
                    $("#msm_subordinate_accordion-"+id).append(searchAccordionHeader);
                    $("#msm_subordinate_accordion-"+id).append(searchDiv);
    
                    var searchResultAccordionHeader = $("<h3> Search Results </h3>");
                    var searchResultAccordionDiv = $("<div id='msm_search_result'></div>");
    
                    $("#msm_subordinate_accordion-"+id).append(searchResultAccordionHeader);
                    $("#msm_subordinate_accordion-"+id).append(searchResultAccordionDiv);
    
                    $("input#msm_search_word").css("width", "74.5%");
                    
                    $("#msm_subordinate_accordion-"+id).accordion({
                        heightStyle: "content",
                        beforeActivate: function(e, ui) {
                            if(ui.newPanel[0].id.match(/msm_info_accordion/))
                            {
                                $(".msm_subordinate_textareas").each(function() {
                                    if(typeof tinymce.getInstanceById(this.id) === "undefined")
                                    {
                                        tinymce.execCommand("mceAddControl", true, this.id);
                                    }                    
                                });
                            }
                        }
                    });
                    
                    var refString = '';
                    
                    if($("#msm_subordinate_select-"+id).prop("selectedIndex") == 2)
                    {
                        refString = "Internal Reference";
                    }
                    else if($("#msm_subordinate_select-"+id).prop("selectedIndex") == 3)
                    {
                        refString = "External Reference";
                    }
                    
                    var msmIdInfo = window.location.search.split("=");
                    var msmId = msmIdInfo[1];
    
                    // for submitting the search param for internal/external referencing
                    $("#msm_search_submit").click(function(e) {
                        submitAjax(refString, msmId, id, "subordinate");
                    });
                   
                });
                
                // user chooses to delete the reference
                // --> change the default form to information form --> so just information title/content textareas
                //      with tinyMCE enabled
                $("#msm_delete_search-"+id).click(function() {
                    $("#msm_subordinate_select-"+id).removeAttr("disabled");
                    $("#msm_subordinate_select-"+id).prop("selectedIndex", 0); // change dropdown value to information
                    
                    var prevValues = [];
                    
                    $("#msm_subordinate_form-"+id).find(".msm_subordinate_textareas").each(function() {
                        if(this.id == "msm_subordinate_infoTitle-"+id)
                        {
                            prevValues["msm_subordinate_infoTitle"] = tinymce.get(this.id).getContent({
                                format: "html"
                            });
                        }
                        else if(this.id == "msm_subordinate_infoContent-"+id)
                        {
                            prevValues["msm_subordinate_infoContent"] = tinymce.get(this.id).getContent({
                                format: "html"
                            });
                        }
                    });
                    
                    $('#msm_subordinate_content_form_container-'+id+" textarea").each(function() {
                        if(tinymce.getInstanceById($(this).attr("id")) != null)
                        {
                            tinymce.execCommand('mceFocus', false, $(this).attr("id"));
                            tinymce.execCommand('mceRemoveControl', false, $(this).attr("id"));
                        }
                    });
                    
                    $("#msm_subordinate_content_form_container-"+id).empty();
                    
                    var infoForm = makeInfoForm(ed, id, false);
                    $("#msm_subordinate_content_form_container-"+id).append(infoForm);
                    
                    $("#msm_subordinate_form-"+id).find(".msm_subordinate_textareas").each(function() {
                        if(this.id == "msm_subordinate_infoTitle-"+id)
                        {
                            $(this).val(prevValues["msm_subordinate_infoTitle"]);
                        }
                        else if(this.id == "msm_subordinate_infoContent-"+id)
                        {
                            $(this).val(prevValues["msm_subordinate_infoContent"]);
                        }
                    });
                    
                    initInfoEditor(id);
                });
            }
        });
    }
}

/**
 * This method is used to initialize the tinyMCE editor for each of textareas associated with information element.
 * 
 * @param {string} id            ending of this subordinat dialog("ending of parent element ID - number that is incremented per subordinate)
 *                               used to uniquely identify the textareas for information title and content elements
 */
function initInfoEditor(id)
{
    var titleid = "msm_subordinate_infoTitle-"+id;
    var contentid = "msm_subordinate_infoContent-"+id;
    
    // initialize tinymce for title textarea
    YUI().use('editor_tinymce', function(Y) {
        M.editor_tinymce.init_editor(Y, titleid, {
            mode:"exact",
            elements: titleid,
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
            theme_advanced_buttons3:"bullist,numlist,outdent,indent,|,image,moodlemedia,matheditor,nonbreaking,charmap,table,|,code,spellchecker",
            moodle_init_plugins:"dragmath:loader.php/dragmath/-1/editor_plugin.js,moodlenolink:loader.php/moodlenolink/-1/editor_plugin.js,spellchecker:loader.php/spellchecker/-1/editor_plugin.js,moodleimage:loader.php/moodleimage/-1/editor_plugin.js,moodlemedia:loader.php/moodlemedia/-1/editor_plugin.js,matheditor:loader.php/matheditor/-1/editor_plugin.js",
            file_browser_callback:"M.editor_tinymce.filepicker",
            moodle_plugin_base: M.cfg.wwwroot+"/lib/editor/tinymce/plugins/"
        })
        
        M.editor_tinymce.init_filepicker(Y, id, tinymce_filepicker_options);
    });
    
    // initialize tinymce for content textarea --> allow the subordinate plugin to be added to get nested subordinates
    YUI().use('editor_tinymce', function(Y) {
        M.editor_tinymce.init_editor(Y, contentid, {
            mode:"exact",
            elements: contentid,
            plugins:"matheditor,safari,table,style,layer,advhr,advlist,emotions,inlinepopups,subordinate,moodlesubnolink,searchreplace,paste,directionality,fullscreen,nonbreaking,contextmenu,insertdatetime,save,iespell,preview,print,noneditable,visualchars,xhtmlxtras,template,pagebreak,-dragmath,-moodlenolink,-spellchecker,-moodleimage,-moodlemedia",
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
            moodle_init_plugins:"dragmath:loader.php/dragmath/-1/editor_plugin.js,moodlenolink:loader.php/moodlenolink/-1/editor_plugin.js,spellchecker:loader.php/spellchecker/-1/editor_plugin.js,moodleimage:loader.php/moodleimage/-1/editor_plugin.js,moodlemedia:loader.php/moodlemedia/-1/editor_plugin.js,matheditor:loader.php/matheditor/-1/editor_plugin.js,subordinate:loader.php/subordinate/-1/editor_plugin.js,moodlesubnolink:loader.php/moodlesubnolink/-1/editor_plugin.js",
            file_browser_callback:"M.editor_tinymce.filepicker",
            moodle_plugin_base: M.cfg.wwwroot+"/lib/editor/tinymce/plugins/"
        })
        
        M.editor_tinymce.init_filepicker(Y, contentid, tinymce_filepicker_options);
    });
}

/**
 * This method creates form for the information elements in the subordinate dialog.  
 *
 * @param {tinymce.Editor} ed      current editor that the subordinate plugin was triggered from     
 * @param {string} id              ending of this subordinat dialog("ending of parent element ID - number that is incremented per subordinate)
 * @param {bool}   refFlag         a flag used to determine if the information form is needed for internal/external reference forms or not
 *                                  (if this info form is part of reference form, then do not need legends in fieldset)
 */
function makeInfoForm(ed, id, refFlag)
{
    // making a fieldset element for the info form (all selection will be using it
    // so make it available to all switch cases)
    var fieldset = document.createElement("fieldset");
    if(!refFlag)
    {
        fieldset.setAttribute("style", "border:1px solid black; padding: 2%; margin-top: 1%;");

        var infoLegend = document.createElement("legend");
        var legendText = document.createTextNode("Subordinate Information Form");
        infoLegend.appendChild(legendText);
    }
        
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
   
    if(!refFlag)
    {
        fieldset.appendChild(infoLegend);
    }
    infotitlediv.appendChild(infoTitleLabel);
    infotitlediv.appendChild(infoTitleInput);
    fieldset.appendChild(infotitlediv);
        
    fieldset.appendChild(document.createElement("br"));
        
    infocontentdiv.appendChild(infoContentLabel);
    infocontentdiv.appendChild(infoContentInput);
    fieldset.appendChild(infocontentdiv);
    
    return fieldset;
}

/**
 * This method is used to load previous subordinate data given by the hidden divs. The hidden divs contains all the
 * data to create the subordinate dialog with all the saved data.  This function loads all of them into correct
 * form and display it for user to edit.
 *
 * @param {tinymce.Editor} editor        current editor that the subordinate plugin was triggered from     
 * @param {string} id                    ending of this subordinat dialog("ending of parent element ID - number that is incremented per subordinate)
 */
function loadPreviousData(editor, id)
{
    var selectedAnchorIdInfo = null;
    
    var indexId = '';
    
    // responsible for loading already existing subordinate values after view.php is triggered
    $(document).find(".msm_subordinate_results").each(function() {
        $(this).find(".msm_subordinate_hotword_matchs").each(function() {
            if(editor.selection.getNode().id == $(this).text())
            {
                selectedAnchorIdInfo = this.id.split("-");
                
                for(var i=1; i < selectedAnchorIdInfo.length-1; i++)
                {
                    indexId += selectedAnchorIdInfo[i] + "-";
                }
                indexId += selectedAnchorIdInfo[selectedAnchorIdInfo.length-1];
            }
        });
    });
    
    if(indexId == '')
    {
        selectedAnchorIdInfo = editor.selection.getNode().id.split("-");
            
        for(var i=1; i < selectedAnchorIdInfo.length-1; i++)
        {
            indexId += selectedAnchorIdInfo[i] + "-";
        }
        indexId += selectedAnchorIdInfo[selectedAnchorIdInfo.length-1];
    }
    
    var prevSelectValue = null;
    var prevUrlValue = null;
    var prevInfoTitleValue = null;
    var prevInfoContentValue = null;
    var prevRefId = null;
   
    // load all the previous values into correct area
    $('#msm_subordinate_result-'+indexId).children('div').each(function() {
        if(this.id == 'msm_subordinate_select-'+indexId)
        {
            prevSelectValue = $(this).text();
        }
        else if(this.id == 'msm_subordinate_url-'+indexId)
        {
            prevUrlValue = $(this).text();
        }
        else if(this.id == 'msm_subordinate_infoTitle-'+indexId)
        {
            prevInfoTitleValue = $(this).html();
        }
        else if(this.id == 'msm_subordinate_infoContent-'+indexId)
        {
            // to process the math elements to load it properly
            $(this).find(".matheditor").each(function() {
                var newcontent = '';
                $(this).find("script").each(function() {
                    newcontent = $(this).html();
                });
          
                $(this).empty();
                $(this).append(newcontent);
            });
            prevInfoContentValue = $(this).html();
        }
        else if(this.id == 'msm_subordinate_ref-'+indexId)
        {
            prevRefId = $(this).text();
        }
    });           
        
    // selecting the dropdown menu to correct type
    switch(prevSelectValue)
    {
        case "Information":
            $("#msm_subordinate_select-"+id).prop("selectedIndex", 0);
            break;
        case "External Link":
            $("#msm_subordinate_select-"+id).prop("selectedIndex", 1);
            break;
        case "Internal Reference":
            $("#msm_subordinate_select-"+id).prop("selectedIndex", 2);
            break;
        case "External Reference":
            $("#msm_subordinate_select-"+id).prop("selectedIndex", 3);
            break;
    }
  
    if((prevUrlValue != '')&&(prevUrlValue != 'undefined')&&(prevUrlValue !== null))
    {
        var urlFieldSet = appendUrlForm(id);
        $("#msm_subordinate_content_form_container-"+id).prepend(urlFieldSet);
        $("#msm_subordinate_form-"+id+ " #msm_subordinate_url-"+id).val(prevUrlValue);
    }
    
    // there are reference materials saved previously
    if((prevRefId != '')&&(prevRefId != 'undefined')&&(prevRefId !== null))
    {
        var container = $("#msm_subordinate_content_form_container-"+id);
        
        $('#msm_subordinate_content_form_container-'+id+" textarea").each(function() {
            if(tinymce.getInstanceById($(this).attr("id")) != null)
            {
                tinymce.execCommand('mceFocus', false, $(this).attr("id"));
                tinymce.execCommand('mceRemoveControl', false, $(this).attr("id"));
            }
        });
        
        $(container).empty(); // has info title/content textarea already appended
        
        var accordionContainer = document.createElement("div");
        accordionContainer.id = "msm_subordinate_accordion-"+id;
        $(container).append(accordionContainer)
        makeRefForm(editor, id, prevRefId);

        var msmIdInfo = window.location.search.split("=");
        var msmId = msmIdInfo[1];
        $("#msm_search_submit").click(function(e) {
            submitAjax(prevSelectValue, msmId, id, "subordinate");
        });
    }
   
    $(".msm_subordinate_textareas").each(function() {
        if(this.id == "msm_subordinate_infoTitle-"+id)
        {
            this.value = prevInfoTitleValue;
        }
        else if(this.id == "msm_subordinate_infoContent-"+id)
        {
            this.value = prevInfoContentValue;
        }
    });
     
}

/**
 * This method is used when the type of subordinate triggered is "External Link" which needs 
 * additional form components for adding the URL for the link.
 *
 * @param {string} id       ending of this subordinat dialog("ending of parent element ID - number that is incremented per subordinate)
 */
function appendUrlForm(id)
{
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
    
    var urlErrorSpan = document.createElement("span");
    urlErrorSpan.id = "msm_invalid_url_span";
    urlErrorSpan.setAttribute("style", "color: red; display: none; margin-left: 85px;");
    
    var urlErrorSpanText = document.createTextNode("Please enter a valid URL.");
    urlErrorSpan.appendChild(urlErrorSpanText);
    
    urlfieldset.appendChild(urlLegend);
    urlfieldset.appendChild(urlLabel);
    urlfieldset.appendChild(urlInput);
    urlfieldset.appendChild(urlErrorSpan);
    
    return urlfieldset;
}

/**
 * This method is called to disable all tinyMCE editor and then closing the 
 * subordinate dialog window.
 *
 * @param {string} id           ending of this subordinat dialog("ending of parent element ID - number that is incremented per subordinate)
 */
function closeSubFormDialog(id)
{
    $('#msm_subordinate_container-'+id+" textarea").each(function() {
        if(tinymce.getInstanceById($(this).attr("id")) != null)
        {
            tinymce.EditorManager.execCommand('mceFocus', false, $(this).attr("id"));
            tinymce.EditorManager.execCommand('mceRemoveControl', false, $(this).attr("id"));
        }
    });
    
    $('#msm_subordinate_container-'+id).empty();
    $('#msm_subordinate_container-'+id).dialog("close");
}

/**
 * This method is triggered by the save button in the dialog window and it extracts all the information 
 * in the dialog form and organizes them into hidden divs that can later be used to load these data back
 * when user wants to edit the content.
 *
 * @param {tinymce.Editor} ed       current editor that the subordinate plugin was triggered from     
 * @param {string} id               ending of this subordinat dialog("ending of parent element ID - number that is incremented per subordinate)
 * @param {string} subId            a string flag for nested subordinates     
 */
function submitSubForm(ed, id, subId)
{
    var selectedText = ed.selection.getContent({
        format: 'text'
    });
            
    var selectedNode = null;
        
    if($.browser.msie) // for firefox the strucutre of ed object is a bit different
    {
        selectedNode = ed.selection.getNode().childNodes[0].tagName;
    }
    else
    {
        selectedNode = ed.selection.getNode().tagName;
    }
    
    var newSubordinateDiv = null;
    
    // if this subordinate is new, then add the hidden divs to the result container div
    // otherwise, delete the old data and the associated hidden divs and add new ones
    if(selectedNode != 'A')
    {
        newSubordinateDiv = createSubordinateDiv(id, id+"-"+_subIndex, '');
    }
    else
    {
        var textIdInfo = ed.selection.getNode().id.split("-");
        
        var textId = '';
        for(var i=1; i < textIdInfo.length-1; i++)
        {
            textId += textIdInfo[i]+"-";
        }
        textId+= textIdInfo[textIdInfo.length-1];
        
        newSubordinateDiv = replaceSubordinateDiv(id, textId, subId);
    }
       
    // when empty contents are submitted
    if(newSubordinateDiv instanceof Array)
    {
        nullErrorWarning(newSubordinateDiv, id);
    }
    else // no error
    {
        var subordinateDivIndexInfo = newSubordinateDiv.id.split("-");
    
        var subIndex = '';
        for(var i=1; i < subordinateDivIndexInfo.length-1; i++)
        {
            subIndex+= subordinateDivIndexInfo[i]+"-";
        }
        subIndex += subordinateDivIndexInfo[subordinateDivIndexInfo.length-1];
        
        if(subId != '')
        {
            var subparent = findParentDiv(subId);
            $(subparent).find(".msm_subordinate_result_containers").eq(0).append(newSubordinateDiv);

        }
        else
        {
            $("#msm_subordinate_result_container-"+id).append(newSubordinateDiv);
        }
    
        
        var urltext = '';
        if($("#msm_subordinate_select-"+id).val() == 'External Link')
        {
            urltext = $("#msm_subordinate_url-"+id).val();
        }
        
        var newContent = '';
        if(selectedNode != "A")
        {
            // wrap the highlighted text with <a>
            if((urltext != '') &&(urltext != null) &&(typeof urltext !== "undefined"))
            {
                newContent = "<a href='"+$.trim(urltext)+"' target='_blank' class='msm_subordinate_hotwords' id='msm_subordinate_hotword-"+subIndex+"'>"+$.trim(selectedText)+"</a> ";
            }
            else
            {
                newContent = "<a href='#' class='msm_subordinate_hotwords' id='msm_subordinate_hotword-"+subIndex+"'>"+$.trim(selectedText)+"</a> ";
            }
           
            ed.selection.setContent(newContent);
        }
        closeSubFormDialog(id);
    }
   
}

/**
 * This method deletes the associated hidden divs with existing subordinate and inserts new hidden divs with 
 * newly updated/editted data associated with the same subordinate when the user edits its contents.
 * 
 * @param {string} index        string that was added to an HTML element ID to make them unique --> top level subordinate
 * @param {string} hotId        string that was added to an HTML element ID to make them unique --> id ending of current anchor element
 * @param {string} subId        string that was added to an HTML element ID to make them unique --> nested subordinate
 */
function replaceSubordinateDiv(index, hotId, subId)
{
    var subparent = null;
    
    // top subordinate with nested subordinates do not have subId defined
    // but can use its id to get the parent(def/theorem...etc) Div id
    if(subId == '')
    {
        subparent = findParentDiv(index);
    }
    else
    {
        subparent = findParentDiv(subId);
    }
    // after the subordinate is saved the hotId ending is previous values before the HTML IDs for the subordinate divs were replaced
    // with the database ID values.  So if that's the case then this function will not be finding the old
    // subordinate values to replace.
    var foundDiv = false;
    // need grab the parent to get the id of the result container
    // (this generic code allows the plugin to have nested subordinates)
    $(subparent).find(".msm_subordinate_result_containers").eq(0).children("div").each(function() {
        if(this.id == "msm_subordinate_result-"+hotId)
        {
            foundDiv = true;
            $(this).empty().remove();
        }
    });
    
    // possibility of result container HTML ID ending is pre-database ID
    // before save into db, incrementing numbers were added to keep the div unique
    // after save into db, it contains the database ID
    if(!foundDiv)
    {
        var newId = '';
        $(subparent).find(".msm_subordinate_result_containers").eq(0).children("div").each(function() {           
            $(this).find(".msm_subordinate_hotword_matchs").each(function() {
                var oldmatch = $(this).text();   
                var oldmatchInfo = oldmatch.split("-");                    
                var oldId = oldmatchInfo[1];
                for(var i=2; i < oldmatchInfo.length; i++)
                {
                    oldId += "-"+oldmatchInfo[i];
                }    
                
                if(oldId == hotId)
                {
                    var newIdInfo = this.id.split("-");
                    
                    newId = newIdInfo[1];
                    for(var i=2; i < newIdInfo.length; i++)
                    {
                        newId += "-"+newIdInfo[i];
                    }
                }
            });
            
            if(this.id == "msm_subordinate_result-"+newId)
            {
                $(this).empty().remove();
            }
            
        });
    }
   
    var subordinateResultContainer = createSubordinateDiv(index, hotId, "replace");
    
    return subordinateResultContainer;
}

/**
 * This method is used to create the hidden divs for submitted subordinate form data.
 * These hidden divs with data are used to load the existing subordinate data when
 * user triggers an edit.
 * 
 * @param {string} index            string that was added to an HTML element ID to make them unique --> top level subordinate  
 * @param {string} oldidString      string that was added to an HTML element ID to make them unique --> id ending of current anchor element
 * @param {string} flag             string flag to indicate if this method was called from replaceSubordinateDiv or not (if it is, has "replace" value)
 */
function createSubordinateDiv(index, oldidString, flag)
{
    var idString = '';
    var errorArray = [];
    
    // no need to check for duplicate id if replacing already existing one...
    if(flag == '')
    {
        var oldidStringInfo = oldidString.split("-");
    
        idString = checkForExistence(oldidString) + "-" + oldidStringInfo[oldidStringInfo.length-1];
    }
    else if(flag == "replace")
    {
        idString = oldidString;
    }
        
    var resultContainer = document.createElement("div");
    resultContainer.id = "msm_subordinate_result-"+idString;
    resultContainer.className = "msm_subordinate_results";
    
    $("#msm_subordinate-"+index+" textarea").each(function(){
        this.value = tinymce.get(this.id).getContent({
            format: "html"
        });
       
        // if no match is found, indexOf returns -1
        var isInfoTitle = this.id.indexOf("infoTitle");
       
        if((this.value == '') && (isInfoTitle == -1))
        {
            errorArray.push(this.id+"_ifr");
        }
    });
    
    var selectedBox = $("#msm_search_result_table input").filter(":checked");
    // no selection made in search result --> meaning only infotitle/info contents are filled.
    // so switch select value to information
    if(errorArray.length == 0)
    {
        var resultSelectDiv = document.createElement("div");
        resultSelectDiv.id = "msm_subordinate_select-"+idString;
        
        var selectedValue= $("#msm_subordinate_select-"+index).val();
        
        if((selectedBox.length == 0) && ((selectedValue == "Internal Reference") || (selectedValue == "External Reference")))
        {
            selectedValue == "information";
        }
        
        var resultSelectText = document.createTextNode(selectedValue);
            
        resultSelectDiv.appendChild(resultSelectText);
        
        var resultUrlDiv = null;
        
        if($("#msm_subordinate_select-"+index).val() == 'External Link')
        {
            resultUrlDiv = document.createElement("div");
            resultUrlDiv.id = "msm_subordinate_url-"+idString
            var resultUrlText = document.createTextNode($("#msm_subordinate_url-"+index).val());
            resultUrlDiv.appendChild(resultUrlText);
        }
    
        var resultTitleDiv = document.createElement("div");
        resultTitleDiv.id = "msm_subordinate_infoTitle-"+idString;
    
        $(resultTitleDiv).append($("#msm_subordinate_infoTitle-"+index).val());
    
        var resultContentDiv = document.createElement("div");
        resultContentDiv.id = "msm_subordinate_infoContent-"+idString;
       
        // replace all math elements in the infocontent to span with text value of \(mathtext\)
        $("#msm_subordinate_infoContent-"+index).find(".matheditor").each(function() {
            var newcontent = '';
            $(this).find("script").each(function() {
                newcontent = $(this).html();
            });
          
            $(this).empty();
            $(this).append("\("+newcontent+"\)");
        });
        $(resultContentDiv).append($("#msm_subordinate_infoContent-"+index).val());
        
        // for internal/external reference values to be stored
        
        var refValueDiv='';
        
        if(selectedBox.length > 0)
        {
            refValueDiv = document.createElement("div");
            refValueDiv.id = "msm_subordinate_ref-"+idString;
        
            var selectedRow = $(selectedBox).closest("tr");
            var selectedCells = $(selectedRow).find(".msm_search_result_table_cells");
            var selectedCheckbox = $(selectedCells[0]).find("input");
                    
            var selectedId = $(selectedCheckbox[0]).attr("id").split("-");
            
            var refValueText = document.createTextNode(selectedId[1]);
            $(refValueDiv).append(refValueText);
        }
        
        // inserting all parts of the subordinate result values into the container div
        resultContainer.appendChild(resultSelectDiv);
        if(resultUrlDiv != null)
        {
            resultContainer.appendChild(resultUrlDiv);
        }
        resultContainer.appendChild(resultTitleDiv);
        resultContainer.appendChild(resultContentDiv);
        
        if(selectedBox.length > 0)
        {
            resultContainer.appendChild(refValueDiv);
        }
        
        _subIndex++;
    
        return resultContainer;
    }
    else
    {
        return errorArray;
    }
}

/**
 * 
 * @param {array} errorArray        array of HTML IDs of empty required fields that is used to change border color to indicate an error for user
 * @param {string} id               HTML ID ending of elements associated with the subordinate dialog window
 */
function nullErrorWarning(errorArray, id)
{
    for(var i=0; i < errorArray.length; i++)
    {
        var match = errorArray[i].match(/subordinate.url./);
            
        if(match)
        {
            var invalidornull = errorArray[i].split("|");
            
            if(invalidornull.length > 1)
            {
                $("#msm_invalid_url_span").css("display","block");
                $(invalidornull[0]).css("border", "solid 4px #FFA500");
            }
            $("#"+errorArray[i]).css("border", "solid 4px #FFA500");
            
        }
        else
        {
            $("#"+errorArray[i]).css("border", "solid 4px #FFA500");
        }
    }
                
    $("<div class=\"dialogs\" id=\"msm_emptySubContent\"> Please fill out the highlighted areas with appropriate information to complete the form. </div>").appendTo('#msm_subordinate_container-'+id);

    $("#msm_emptySubContent").dialog({
        modal: true,
        buttons: {
            Ok: function() {
                errorArray = null;
                $(this).dialog("close");
            }
        }
    });
}

/**
 * This recurisve method is used to create new HTML ID for the HTML elements in the
 * subordinate dialog window.  For jquery Dialog and tinyMCE editor for each textarea to be
 * initiated properly, the HTML IDs have to be unique.
 * 
 * @param {string} oldtestId        HTML ID of previously created subordinate dialog window.
 */
function checkForExistence(oldtestId)
{
    var newTestId = '';
    
    var testIdInfo = oldtestId.split("-");
    var testId = testIdInfo[0];
         
    for(var i = 1; i <= testIdInfo.length-2; i++)
    {
        testId += "-"+testIdInfo[i] ;
    }
    
    newTestId = testId;
        
    $("#msm_child_appending_area").find(".msm_subordinate_results").each(function() {
        var existingIdInfo = this.id.split("-");
        var existingId = '';
         
        for(var i = 1; i < existingIdInfo.length-1; i++)
        {
            existingId += existingIdInfo[i] + "-";
        }
        existingId += existingIdInfo[existingIdInfo.length-1];
             
        if(oldtestId == existingId)
        {
            var lastChar = testId.charAt(testId.length-1);
            var newlastchar = parseInt(lastChar)+1;
            newTestId = testId.substring(0, testId.length-1) + newlastchar;
            checkForExistence(newTestId);
        }
    });
    
    return newTestId;
}

/**
 *  This method is used to activate the subordinate dialog window and all the needed
 *  jquery plugins for display.
 *  
 *  @param {tinymce.Editor} ed          current editor that the subordinate plugin was triggered from  
 *  @param {string} idNumber            HTML ID ending of elements associated with the subordinate dialog window
 *  @param {string} subId               a string flag for nested subordinates  
 */
function createDialog(ed, idNumber, subId)
{
    // to fix the dialog window size to 80% of window size
    var wWidth = $(window).width();
    var wHeight = $(window).height();
                
    var dWidth = wWidth*0.6;
    var dHeight = wHeight*0.8;
                
    $('#msm_subordinate_container-'+idNumber).dialog({       
        modal:true,
        autoOpen: false,
        height: dHeight,
        width: dWidth,
        closeOnEscape: false,
        open: function(event, ui) {
            $(".ui-dialog-titlebar-close").hide(); // disabling the close button
            $("#msm_subordinate_highlighted-"+idNumber).val(ed.selection.getContent({
                format : 'text'
            }));
            $("#msm_subordinate_accordion-"+idNumber).accordion({
                heightStyle: "content",
                beforeActivate: function(e, ui) {
                    if(ui.newPanel[0].id.match(/msm_info_accordion/))
                    {
                        $(".msm_subordinate_textareas").each(function() {
                            if(typeof tinymce.getInstanceById(this.id) === "undefined")
                            {
                                tinymce.execCommand("mceAddControl", true, this.id);
                            }                    
                        });
                    }
                }
            });
            initInfoEditor(idNumber);
        },
        buttons: {
            "Save": function() {
                submitSubForm(ed, idNumber, subId);
            },
            "Cancel": function() {
                closeSubFormDialog(idNumber);
            }
        }
    });
    //    $('#msm_subordinate_container-'+idNumber).css('display', 'block');
    $('#msm_subordinate_container-'+idNumber).dialog('open').css('display', 'block');
}

/**
 * This method is used to find the main component of unit that triggered this subordinate plugin.
 * (essentially looking for the div that contained the tinyMCE editor that triggered the subordinate plugin)
 * 
 * @param {string} idEnding         HTML ID ending of elements associated with the subordinate dialog window
 */
function findParentDiv(idEnding)
{   
    var parent = null;
    var matchInfo = null;
    var typeId = null;
    
    var defPattern = /^\S*(defcontent\d+\S*)$/;
    var defrefPattern = /^\S*(defrefcontent\d+\S*)$/;
    var statementTheoremPattern = /^\S*(statementtheoremcontent\d+\S*)$/;
    var statementTheoremRefPattern = /^\S*(theoremrefcontent\d+\S*)$/;
    var partTheoremPattern = /^\S*(parttheoremcontent\d+\S*)$/;
    var partTheoremRefPattern = /^\S*(theoremrefpart\d+\S*)$/;
    var commentPattern = /^\S*(commentcontent\d+\S*)$/;
    var commentrefPattern = /^\S*(commentrefcontent\d+\S*)$/;
    var bodyPattern = /^\S*(bodycontent\d+\S*)$/;
    var introPattern = /^\S*(introcontent\d+\S*)$/;
    var introChildPattern = /^\S*(introchild\d+\S*)$/;
    var extraInfoPattern = /^\S*(extracontent\d+\S*)$/;
    var associatePattern = /^\S*(infocontent\d+\S*)$/;
    
    var defmatch = idEnding.match(defPattern);
    var defrefmatch = idEnding.match(defrefPattern);
    var statementmatch = idEnding.match(statementTheoremPattern);
    var statementrefmatch = idEnding.match(statementTheoremRefPattern);
    var partmatch = idEnding.match(partTheoremPattern);
    var partrefmatch = idEnding.match(partTheoremRefPattern);
    var commentmatch = idEnding.match(commentPattern);
    var commentrefmatch = idEnding.match(commentrefPattern);
    var bodymatch = idEnding.match(bodyPattern);
    var intromatch = idEnding.match(introPattern);
    var introchildmatch = idEnding.match(introChildPattern);
    var extracontentmatch = idEnding.match(extraInfoPattern);
    var associatematch = idEnding.match(associatePattern);
    
    // parent needs to be whatever div contains the object in
    // msm_subordinate_result_containers class (usually the
    // copied_msm_structural_elements class)
    
    if(defmatch)
    {
        matchInfo = defmatch[0].split("-");
        typeId = matchInfo[0].replace(/([A-Za-z]*?)(\d+)/, "$2");
        parent = document.getElementById("copied_msm_def-"+typeId);
    }
    if(defrefmatch)
    {
        matchInfo = defrefmatch[0].split("-");
        typeId = matchInfo[0].replace(/([A-Za-z]*?)(\d+)/, "$2");
        typeId += "-"+matchInfo[1]+"-"+matchInfo[2];
        
        parent = document.getElementById("copied_msm_defref-"+typeId);
    }
    else if(commentmatch)
    {
        matchInfo = commentmatch[0].split("-");
        typeId = matchInfo[0].replace(/([A-Za-z]*?)(\d+)/, "$2");
        parent = document.getElementById("copied_msm_comment-"+typeId);
    }
    else if(commentrefmatch)
    {
        matchInfo = commentrefmatch[0].split("-");
        typeId = matchInfo[0].replace(/([A-Za-z]*-?)(\d+)/, "$2");
        typeId += "-"+matchInfo[1]+"-"+matchInfo[2];
        
        parent = document.getElementById("copied_msm_commentref-"+typeId);
    }
    else if(bodymatch)
    {
        matchInfo = bodymatch[0].split("-");
        typeId = matchInfo[0].replace(/([A-Za-z]*?)(\d+)/, "$2");
        parent = document.getElementById("copied_msm_body-"+typeId);
    }
    else if(intromatch)
    {
        matchInfo = intromatch[0].split("-");
        typeId = matchInfo[0].replace(/([A-Za-z]*?)(\d+)/, "$2");
        parent = document.getElementById("copied_msm_intro-"+typeId);
    }
    else if(introchildmatch)
    {
        matchInfo = introchildmatch[0].split("-");
        typeId = matchInfo[0].replace(/([A-Za-z]*?)(\d+)/, "$2");
        parent = document.getElementById("msm_intro_child_div-"+typeId);
    }
    else if(extracontentmatch)
    {
        matchInfo = extracontentmatch[0].split("-");
        typeId = matchInfo[0].replace(/([A-Za-z]*?)(\d+)/, "$2");
        parent = document.getElementById("copied_msm_extra_info-"+typeId);
    }
    else if(associatematch)
    {
        matchInfo = associatematch[0].split("-");
        typeId = matchInfo[0].replace(/([A-Za-z]*?)(\d+)/, "$2");
        typeId += "-"+matchInfo[1];
        
        parent = document.getElementById("msm_associate_childs-"+typeId);
    }
    else if (statementmatch)
    {
        matchInfo = statementmatch[0].split("-");
        typeId = matchInfo[0].replace(/([A-Za-z]*?)(\d+)/, "$2");
        
        $(".copied_msm_structural_element").each(function() {
            var currentIdInfo = this.id.split("-");
            $(this).find(".msm_subordinate_result_containers").each(function() {
                var resultIdInfo = this.id.split("-");
                
                var resultIdEnding = resultIdInfo[1].replace(/(statementtheoremcontent)(\d+)/, "$2");
                
                if(typeId == resultIdInfo[2])
                {
                    if(currentIdInfo[1] == resultIdEnding)
                    {
                        typeId = resultIdEnding;
                    }
                }
            });
        });
        
        parent = document.getElementById("copied_msm_theorem-"+typeId);
    }
    else if(statementrefmatch)
    {
        matchInfo = statementrefmatch[0].split("-");
        typeId = matchInfo[0].replace(/([A-Za-z]*?)(\d+)/, "$2");
        typeId += "-"+matchInfo[1]+"-"+matchInfo[2];
        parent = document.getElementById("copied_msm_theoremref-"+typeId);
    }
    else if(partmatch)
    {
        matchInfo = partmatch[0].split("-");
        typeId = matchInfo[0].replace(/([A-Za-z]*?)(\d+)/, "$2");
        typeId += "-"+matchInfo[1];
        parent = document.getElementById("msm_theorem_statement_container-"+typeId);
    }
    else if(partrefmatch)
    {
        matchInfo = partrefmatch[0].split("-");
        typeId = matchInfo[0].replace(/([A-Za-z]*?)(\d+)/, "$2");
        typeId += "-"+matchInfo[1]+"-"+matchInfo[2]+"-"+matchInfo[3];
        
        parent = document.getElementById("msm_theoremref_statement_container-"+typeId);
    }
    
    return parent;
}

/**
 * The anchored element that the subordinate is linked to is IDed differently when navigating from
 * view.php back to authoringTool.php than when created for the first time.  The initial ID is stored in 
 * div msm_subordiante_hotword-idEnding and it is used to retrieve the new html ending used for edit.
 * 
 * @param {string} oldid           HTML ID ending of intially created subordinate elements when it was first created
 */
function isExistingIndex(oldid)
{
    var newId = '';
    var existingDiv = document.getElementById("msm_subordinate_container-"+oldid);
    var existinghotword = document.getElementById("msm_subordinate_hotword-"+oldid);
   
    if((existingDiv)||(existinghotword))
    {
        var oldidInfo = oldid.split("-");
        
        var idInt = parseInt(oldidInfo[oldidInfo.length-1])+1;
           
        newId = oldidInfo[0]+"-"+idInt;
           
        newId = isExistingIndex(newId);
    }
    else
    {
        newId = oldid;
    }
    
    return newId;
}

/**
 * This method replaces temporary HTML id given before view.php with database id after view.php script is triggered.
 * 
 * @param {tinymce.Editor} ed               current tinyMCE editor that the subordinate plugin was triggered from
 * @param {array} edIdInfo                  HTML Id of the editor split at "-" delimiter
 */
function replaceIdEnding(ed, edIdInfo)
{
    var pattern = /([A-Za-z]*?)(\d+)((?:-\d+)*)/;
    var tempString = '';
    // nested subordinates in statement theorem needs to match the statement theorem id not theorem id
    var statementTheoremmatch = /^\S*(statementtheoremcontent\d+\S*)$/;
    var statementTheoremRefmatch = /^\S*(theoremrefcontent\d+\S*)$/;
    var partTheoremmatch = /^\S*(parttheoremcontent\d+\S*)$/;
    var partTheoremRefmatch = /^\S*(theoremrefpart\d+\S*)$/;
    var associatematch = /^\S*(infocontent\d+\S*)$/;
    var refmatch = /^\S*((defrefcontent|commentrefcontent)\d+\S*)/;
    var idReplacement = '';
    if(ed.editorId.match(statementTheoremmatch))
    {
        var subordinatematch = /^\S*(subordinateinfoContent)+(statementtheoremcontent\d+\S*)$/;
                
        if(ed.editorId.match(subordinatematch))
        {
            idReplacement = edIdInfo[1].replace(pattern, "$2");
        }
        else
        {
            for(var i = 1; i < edIdInfo.length-1; i++)
            {
                tempString += edIdInfo[i] + "-";
            }
            tempString += edIdInfo[edIdInfo.length-1];
                    
            var tempidReplacement = tempString.replace(pattern, "$3");
                    
            var tempStringInfo = tempidReplacement.split("-");
                    
            idReplacement = tempStringInfo[1];
        }
    }
    else if(ed.editorId.match(partTheoremmatch))
    {
        var partpattern = /([A-Za-z]*?)(\d+-\d+-\d+)((?:-\d+)*)/;
        for(var i = 1; i < edIdInfo.length-1; i++)
        {
            tempString += edIdInfo[i] + "-";
        }
        tempString += edIdInfo[edIdInfo.length-1];
                    
        idReplacement = tempString.replace(partpattern, "$2");
    }
    else if(ed.editorId.match(associatematch))
    {
        var assopattern = /([A-Za-z]*?)(\d+-\d+)((?:-\d+)*)/;
        for(var i = 1; i < edIdInfo.length-1; i++)
        {
            tempString += edIdInfo[i] + "-";
        }
        tempString += edIdInfo[edIdInfo.length-1];
                    
        idReplacement = tempString.replace(assopattern, "$2");
    }
    else if(ed.editorId.match(refmatch))
    {
        var refpattern = /([A-Za-z]*?)(\d+-\d+-\d+)((?:-\d+)*)/;
        for(var i = 1; i < edIdInfo.length-1; i++)
        {
            tempString += edIdInfo[i] + "-";
        }
        tempString += edIdInfo[edIdInfo.length-1];
                    
        idReplacement = tempString.replace(refpattern, "$2");
    }
    else if(ed.editorId.match(statementTheoremRefmatch))
    {
        var theoremcontentrefpattern = /([A-Za-z]*?)(\d+-\d+-\d+-\d+)((?:-\d+)*)/;
        for(var i = 1; i < edIdInfo.length-1; i++)
        {
            tempString += edIdInfo[i] + "-";
        }
        tempString += edIdInfo[edIdInfo.length-1];
                    
        idReplacement = tempString.replace(theoremcontentrefpattern, "$2");
    }
    else if(ed.editorId.match(partTheoremRefmatch))
    {
        var theorempartrefpattern = /([A-Za-z]*?)(\d+-\d+-\d+-\d+-\d+)((?:-\d+)*)/;
        for(var i = 1; i < edIdInfo.length-1; i++)
        {
            tempString += edIdInfo[i] + "-";
        }
        tempString += edIdInfo[edIdInfo.length-1];
                    
        idReplacement = tempString.replace(theorempartrefpattern, "$2");
    }
    else
    {
        idReplacement = edIdInfo[1].replace(pattern, "$2");
    }
            
    return idReplacement;
}