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

/**
 * This JS file contains all the functions that "extra" features of the editor such as navigating the 
 * menu at the top, giving function to settings window...etc
 */

/**
 *  This function is triggered by the click on Settings navigation bar.  It opens the modal dialog box that has all the 
 *  forms responsible for changing the settings of the editor tool.
 *
 */
function openNavDialog()
{
    $('#msm_nav_setting').ready(function() {
        $('#msm_setting_dialog').dialog({
            // disabling the close button 
            open: function(event, ui) {
                $(".ui-dialog-titlebar-close").hide();
            },
            modal:true,
            autoOpen: false,
            height: 500,
            width: 605,
            closeOnEscape: false
        });
        $('#msm_setting_dialog').dialog('open').css('display', 'block');
        
        // this condition is to prevent code from adding child units whenever window is cancelled then opened again
        if(($('#msm_element_names').children('.msm_structure_names').length == 0) &&($('#msm_element_names').children('.msm_setting_form').length == 0))
        {
            var allUnitNames = $('#msm_unit_name_input').val().split(",");
            
            switch(allUnitNames[1])
            {
                case "Lecture":
                    $('#msm_type_lecture').attr("checked", "true");
                    $('#msm_structure_input_top').attr("value", allUnitNames[1]);
                    $('#msm_structure_input_alone').attr("value", allUnitNames[0]);
                    break;
                case "Book":
                    $('#msm_type_book').attr("checked", "true");
                    $('#msm_structure_input_top').attr("value", allUnitNames[1]);
                    $('#msm_structure_input_alone').attr("value", allUnitNames[0]);
                    break;
                case "Work Book":
                    $('#msm_type_wbook').attr("checked", "true");
                    $('#msm_structure_input_top').attr("value", allUnitNames[1]);
                    $('#msm_structure_input_alone').attr("value", allUnitNames[0]);
                    break;
                case "Others":
                    $('#msm_type_others').attr("checked", "true");
                    $('#msm_structure_input_top').attr("value", allUnitNames[1]);
                    $('#msm_structure_input_alone').attr("value", allUnitNames[0]);
                    break;
            } 
            for(var i=2; i < allUnitNames.length-1; i++)
            {
                if(allUnitNames[i] != '')
                {
                    var inputLabel = document.createElement('label');
                    inputLabel.htmlFor = "msm_structure_input_child-"+(i-1);
                    
                    var labelText = document.createTextNode("Child Unit : ");
                    
                    inputLabel.appendChild(labelText);   
                    
                    var inputField = document.createElement('input');
                    inputField.className = "msm_structure_input";
                    inputField.id = "msm_structure_input_child-"+(i-1);
                    inputField.name = "msm_structure_input_child-"+(i-1);
                    
                    $(inputField).val(allUnitNames[i]);
                    
                    var settingContainer = document.createElement('div');
                    settingContainer.className = "msm_setting_form";
                    
                    settingContainer.appendChild(inputLabel);
                    settingContainer.appendChild(inputField);
                    $(settingContainer).append('<span style="color: red;">*</span>');
                    
                    $(settingContainer).insertBefore('#msm_child_add');
                }               
            
            }
            var msmInstanceid = document.createElement('input');
            msmInstanceid.id = "msm_instance_id";
            msmInstanceid.name = "msm_instance_id";
            msmInstanceid.style.visibility = "hidden";
            msmInstanceid.style.display = "none";
            
            $(msmInstanceid).val(allUnitNames[allUnitNames.length-1]);
            
            $(msmInstanceid).insertAfter('#msm_child_add');
        }
    
    });
}

/**
*  This function is activated when the add Children button is clicked in settings form.
*  It adds more input fields for the users to fill out.
*
*/
function addChildUnit()
{
    $('#msm_child_add').ready(function () {
        
        var lastId = $(".msm_setting_form").last().children('.msm_structure_input').attr("id");
        
        //        var prevChildid = $('#msm_child_add').prev().prev().attr("id");     
        var childidNumber = lastId.split('-');
        
        var newidNumber = parseInt(childidNumber[1])+1;
        
        var inputLabel = document.createElement('label');
        inputLabel.htmlFor = "msm_structure_input_child-"+newidNumber;
        
        var labelText = document.createTextNode("Child Unit : ");
        
        inputLabel.appendChild(labelText);   
        
        var inputField = document.createElement('input');
        inputField.className = "msm_structure_input";
        inputField.id = "msm_structure_input_child-"+newidNumber;
        inputField.name = "msm_structure_input_child-"+newidNumber;
        
        $(inputField).attr("placeholder","Please specify the name of the child element of this composition.");
        
        var settingContainer = document.createElement('div');
        settingContainer.className = "msm_setting_form";
        
        settingContainer.appendChild(inputLabel);
        settingContainer.appendChild(inputField);
        
        $(settingContainer).insertBefore('#msm_child_add');        
    });
}

/**
* This function is activated when the cancel button is pressed in the settings form.
* It prompts the user for verification on their choice to close the settings form without saving.
*
*/
function closeSetting()
{
    $('#msm_setting_cancel').ready(function() {
        $("#msm_setting_cancelled > p").css("display", "block");
        $( "#msm_setting_cancelled" ).dialog({
            resizable: false,
            height:180,
            modal: true,
            buttons: {
                "Yes": function() {
                    $( this ).dialog( "close" );
                    $('#msm_setting_dialog').dialog("close");
                },
                "No": function() {
                    $( this ).dialog( "close" );                   
                }
            }
        });
    });
}



/**
*  This method checks if the user tried to save without filling in required field (ie, different border color for field)
*  then changes the border color when user fills in the field. (color change = orange --> green)
*
*/
function validateBorder()
{
    if(document.getElementById('msm_type_specifiedType').style.borderColor == 'rgb(255, 165, 0)')
    {
        if($('#msm_type_specifiedType').val())
        {
            $('#msm_type_specifiedType').css('border-color', '#228B22');
        }
    }
    else if(document.getElementById('msm_unit_title').style.borderColor == 'rgb(255, 165, 0)')
    {
        if($('#msm_unit_title').val())
        {
            $('#msm_unit_title').css('border-color', '#228B22');
        }
    }
}

/**
* This function is activated when radio buttons are triggered.  When the selection of the radio buttons are changed, then update the settings composition name
* input area to be updated accordingly.
*/
function processChange(e)
{
    $(".msm_setting_form").empty().remove();  
    
    var child1Value;
    var child2Value;
    var child3Value;
    var child4Value;
    
    
    switch(e.target.id)
    {
        case "msm_type_lecture":
            $("#msm_structure_input_top").val("Lecture");
            
            child1Value = "Part";
            child2Value = "Topic";
            child3Value = "Section";
            child4Value = "Subsection";           
            break;
        
        case "msm_type_book":
            $("#msm_structure_input_top").val("Book");
            
            child1Value = "Book Part";
            child2Value = "Chapter";
            child3Value = "Section";
            child4Value = "Subsection";
            break;
        
        case "msm_type_wbook":
            $("#msm_structure_input_top").val("Work Book");
            child1Value = "Book Part";
            child2Value = "Chapter";
            child3Value = "Section";
            child4Value = "Subsection";
            
            break;
        
        case "msm_type_others":
            // need to read the input field later
            $("#msm_structure_input_top").val("Others");
            break;
    
    }
    
    var container1 = document.createElement("div");
    container1.className = "msm_setting_form";    
    var container2 = document.createElement("div");
    container2.className = "msm_setting_form";    
    var container3 = document.createElement("div");
    container3.className = "msm_setting_form";    
    var container4 = document.createElement("div");
    container4.className = "msm_setting_form";
    
    var labelText1 = document.createTextNode("Child Unit : ");
    var labelText2 = document.createTextNode("Child Unit : ");
    var labelText3 = document.createTextNode("Child Unit : ");
    var labelText4 = document.createTextNode("Child Unit : ");
    
    var label1 = document.createElement("label");
    label1.htmlFor = "msm_structure_input_child-0"; 
    label1.appendChild(labelText1);    
    var inputfield1 = document.createElement("input");
    inputfield1.className = "msm_structure_input";
    inputfield1.id = "msm_structure_input_child-0";
    inputfield1.name = "msm_structure_input_child-0";
    $(inputfield1).val(child1Value);
    
    var label2 = document.createElement("label");
    label2.htmlFor = "msm_structure_input_child-1";   
    label2.appendChild(labelText2);    
    var inputfield2 = document.createElement("input");
    inputfield2.className = "msm_structure_input";
    inputfield2.id = "msm_structure_input_child-1";
    inputfield2.name = "msm_structure_input_child-1";
    $(inputfield2).val(child2Value);
    
    var label3 = document.createElement("label");
    label3.htmlFor = "msm_structure_input_child-2";    
    label3.appendChild(labelText3);    
    var inputfield3 = document.createElement("input");
    inputfield3.className = "msm_structure_input";
    inputfield3.id = "msm_structure_input_child-2";
    inputfield3.name = "msm_structure_input_child-2";
    $(inputfield3).val(child3Value);
    
    var label4 = document.createElement("label");
    label4.htmlFor = "msm_structure_input_child-3";  
    label4.appendChild(labelText4);    
    var inputfield4 = document.createElement("input");
    inputfield4.className = "msm_structure_input";
    inputfield4.id = "msm_structure_input_child-3";
    inputfield4.name = "msm_structure_input_child-3";
    $(inputfield4).val(child4Value);
    
    // first child element
    container1.appendChild(label1);
    container1.appendChild(inputfield1);
    $(container1).append('<span style="color: red;">*</span>');
    
    // second child element
    container2.appendChild(label2);
    container2.appendChild(inputfield2);
    $(container2).append('<span style="color: red;">*</span>');
    
    // third child element
    container3.appendChild(label3);
    container3.appendChild(inputfield3);
    $(container3).append('<span style="color: red;">*</span>');
    
    // fourth child element
    container4.appendChild(label4);
    container4.appendChild(inputfield4);
    $(container4).append('<span style="color: red;">*</span>');
    
    $(container1).insertBefore('#msm_child_add');
    $(container2).insertBefore('#msm_child_add');
    $(container3).insertBefore('#msm_child_add');
    $(container4).insertBefore('#msm_child_add');


}

function showUnitPreview()
{
    makePreviewDialog();
    
    var indicatorField = document.createElement("input");
    indicatorField.id = "msm_mode_info";
    indicatorField.name = "msm_mode_info";
    indicatorField.value = "preview";    
    
    $("#msm_unit_form").append(indicatorField);
    
    $("#msm_mode_info").css({
        "visibility": "hidden", 
        "display":"none"
    });
    
    // update the child order values incase some were rearranged
    var childOrderString = '';
    $('#msm_child_appending_area').children("div").each(function() {
        childOrderString += this.id +",";
    });
    
    var urlParam = window.location.search;       
    var urlParamInfo = urlParam.split("=");
    
    $("#msm_child_order").val(childOrderString+urlParamInfo[1]);
    
    var editorDivs = $("#msm_unit_form").find(".msm_editor_content");
      
    if(editorDivs.length > 0)  // editor is in display mode
    {  
        var dataArray  = getDisabledData();
        
        var subordinates = [];
        
        $("#msm_child_appending_area").find(".msm_subordinate_results").each(function() {
            $(this).children("div").each(function() {
                subordinates.push(this.id+"||"+$(this).html());
            });
        });
        
        var subordinateString = '';
        for(var i = 0; i < subordinates.length-1; i++)
        {
            subordinateString += subordinates[i] + "//|";
        }            
        subordinateString += subordinates[subordinates.length-1];
        dataArray["msm_unit_subordinate_container"] = subordinateString;       
        
        dataArray["msm_file_options"] = JSON.stringify(tinymce_filepicker_options);
                
        var ids = [];
        $.ajax({
            type: "POST",
            url: "editorCreation/msmUnitForm.php",
            data: $.param(dataArray),
           
            success: function(data) { 
                // this section of the code is for detecting empty contents and it gives the user 
                // a warning dialog box and highlights the contents that are empty
                ids = JSON.parse(data);
                $(".msm_info_dialogs").dialog("destroy");
                $(".msm_info_dialogs").empty().remove();
                $("#msm_preview_dialog .leftbox").append(ids); 
                               
                var wWidth = $(window).width();
                var wHeight = $(window).height();
                        
                var dWidth = wWidth*0.8;
                var dHeight = wHeight*0.8;
                $( "#msm_preview_dialog" ).dialog({
                    resizable: false,
                    modal: true,
                    height: dHeight,
                    width: dWidth,
                    open: function() {
                        $('#MySplitter').split({
                            orientation: 'vertical',
                            position: '50%'
                        });  
                        $(".msm_info_dialogs").dialog({
                            autoOpen: false,
                            height: "auto",
                            modal: false,
                            width: 605
                        });  
                        MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                    },
                    close: function() {
                        $(".msm_info_dialogs").dialog("destroy");
                        $("#msm_mode_info").empty().remove();
                    }
                });   
                                
                $("#msm_preview_dialog .leftbox").find(".msm_subordinate_hotwords").each(function(i, element) {
                    var idInfo = this.id.split("-");
                    var newid = '';
                        
                    for(var i=1; i < idInfo.length-1; i++)
                    {
                        newid += idInfo[i]+"-";
                    }
                            
                    newid += idInfo[idInfo.length-1];
                    
                    $("#dialog-"+newid).dialog({
                        autoOpen: false,
                        height: "auto",
                        modal: false,
                        width: 605
                    });  
                        
                    previewInfo(this.id, "dialog-"+newid);
                });
                
                $(".msm_info_dialogs").find(".msm_subordinate_hotwords").each(function() {
                    var idInfo = this.id.split("-");
                    var newid = '';
                        
                    for(var i=1; i < idInfo.length-1; i++)
                    {
                        newid += idInfo[i]+"-";
                    }
                            
                    newid += idInfo[idInfo.length-1];
                        
                    $("#dialog-"+newid).dialog({
                        autoOpen: false,
                        height: "auto",
                        modal: false,
                        width: 605
                    }); 
                        
                    previewInfo(this.id, "dialog-"+newid);
                });
                               
            }
        });
    // parse HTML to get needed contents... but still need to put in ajax to can use PHP class functions
    }
    else // editor is in edit mode
    {
        $("#msm_unit_form").trigger("submit");             
    }   
}

function getDisabledData()
{
    var dataArray = {};    
    dataArray["msm_child_order"] = $("#msm_child_order").val();
    dataArray["msm_mode_info"] = "preview";
    dataArray["msm_unit_title"] = $("#msm_unit_title").val();
    dataArray["msm_unit_description_input"] = $("#msm_unit_description_input").val();
    dataArray["msm_unit_short_title"] = $("#msm_unit_short_title").val();
    
    $("#msm_child_appending_area").find(".msm_unit_child_dropdown").each(function() {
        dataArray[this.id] = $(this).val(); 
    });
    $("#msm_child_appending_area").find(".msm_unit_child_title").each(function() {
        dataArray[this.id] = $(this).val(); 
    });
    
    $("#msm_child_appending_area").find(".msm_unit_intro_title").each(function() {
        dataArray[this.id] = $(this).val(); 
    });
        
    $("#msm_child_appending_area").find(".msm_unit_body_title").each(function() {
        dataArray[this.id] = $(this).val(); 
    });
        
    $("#msm_child_appending_area").find(".msm_intro_child_titles").each(function() {
        dataArray[this.id] = $(this).val(); 
    });
    
    $("#msm_child_appending_area").find(".msm_editor_content").each(function() {
        // need to deal with mathjax code before sending it off to server side to be
        // processed
        var currentContent = $(this).clone();
        
        $(currentContent).find(".MathJax_Preview").each(function() {
            $(this).empty().remove();
        });
        $(currentContent).find(".MathJax_Display").each(function() {
            $(this).empty().remove();
        });
        
        $(currentContent).find("script").each(function() {
            var content = $(this).text();
            var typeInfo = $(this).attr("type").split(";");
            
            // if the mathjax is defined as display then there is an additional part to 
            // type attribute in form of type='math/tex;mode=display'
            if(typeInfo.length >1)
            {
                $(this).replaceWith("\\["+content+"\\]");
            }
            else // inline mathjax
            {
                $(this).replaceWith("\\("+content+"\\)");
            }
        });
        
        dataArray[this.id] = $(currentContent).html();
    });
    
    $("#msm_child_appending_area").find(".msm_child_description_inputs").each(function() {
        dataArray[this.id] = $(this).val(); 
    });
        
    $("#msm_child_appending_area").find(".msm_theorem_part_title").each(function() {
        dataArray[this.id] = $(this).val(); 
    });   
        
    $("#msm_child_appending_area").find(".msm_associated_dropdown").each(function() {
        dataArray[this.id] = $(this).val(); 
    }); 
        
    $("#msm_child_appending_area").find(".msm_associate_reftype_dropdown").each(function() {
        dataArray[this.id] = $(this).val(); 
    }); 
        
    $("#msm_child_appending_area").find(".msm_theoremref_part_title").each(function() {
        dataArray[this.id] = $(this).val(); 
    });  
    // for any tinymces that is active
    $("textarea").each(function(){        
        if(typeof tinymce.get(this.id) !== "undefined")
        {
            dataArray[this.id] = tinymce.get(this.id).getContent({
                format: "html"
            });     
        }
    });  
        
    return dataArray;
}

function makePreviewDialog()
{    
    var jsTree = document.getElementById("msm_unit_tree");
    var middleEditor = document.getElementById("msm_editor_middle_droparea");
    if((jsTree.hasChildNodes()) || (middleEditor.hasChildNodes()))
    {
        $("#msm_preview_dialog").empty().remove();
    
        var dialogBox = $("<div class='dialogs' id='msm_preview_dialog'></div>");
    
        var splitterBox = $("<div id='MySplitter'></div>");
        var leftCol = $("<div id='leftcol'></div>");
        var leftBox = $("<div class='leftbox'></div>");    
    
        var rightCol = $("<div id='rightcol'></div>");
        var rightBox = $("<div class='rightbox'></div>");
    
        $(leftCol).append(leftBox);
        $(rightCol).append(rightBox);
    
        $(splitterBox).append(leftCol);
        $(splitterBox).append(rightCol);
    
        $(dialogBox).append(splitterBox);
    
        $(dialogBox).insertAfter($("#msm_nav_preview"));         
        
    }
    else
    {
        $("<div class='dialogs' id='msm_preview_nounit'> <span class='ui-icon ui-icon-alert' style='float: left; margin: 0 7px 20px 0;'></span>There are no contents to preview.</div>").appendTo('#msm_editor_middle');
        $( "#msm_preview_nounit" ).dialog({
            resizable: false,
            height:180,
            modal: true,
            buttons: {
                "OK": function() {
                    $(this).dialog("close");
                }
            }
        });
        
        
    }
    
}

function previewinfoopen(triggerId, idEnding)
{    
    $("#"+triggerId).unbind('click');
    $("#"+triggerId).click(function() {
        $('.rightbox').empty();
        var cloned = $('#refcontent-'+idEnding).clone();
        cloned.find('*').each(function(){
            var currentid = $(this).attr('id');
            if(typeof currentid != 'undefined')
            {
                $(this).attr('id', 'copy'+currentid);
            }
        });
        cloned.appendTo($('.rightbox')).css('display', 'block');
        MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
        
        $("#msm_preview_dialog .rightbox").find(".msm_subordinate_hotwords").each(function(i, element) {
            var idInfo = this.id.split("-");
            var newid = '';
                        
            for(var i=1; i < idInfo.length-1; i++)
            {
                newid += idInfo[i]+"-";
            }
                            
            newid += idInfo[idInfo.length-1];
                    
            $("#dialog-"+newid).dialog({
                autoOpen: false,
                height: "auto",
                modal: false,
                width: 605
            });  
            previewInfo(this.id, "dialog-"+newid);
        });
    });
    
    $("#"+triggerId).ready(function(e){        
        $("#"+triggerId).mousemove(function (e) {
            $('#dialog-'+idEnding).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });
            $('#dialog-'+idEnding).dialog('open');
        });
         
        $("#"+triggerId).mouseout(function(){
            $('#dialog-'+idEnding).dialog('close');
        });
   
    });
    
    
}

function exportComposition(event)
{
    event.preventDefault();      
    var unitnames = $("#msm_unit_name_input").val();
    
    var unitInfo = unitnames.split(",");
    
    var msmid = unitInfo[unitInfo.length-1];
    var issuccess = false;
    var ids = null;
    $.ajax({
        type: "POST",
        url: "XMLExporter/beginExport.php",
        data: {
            msm_id: msmid
        },           
        success: function(data) { 
            ids = JSON.parse(data);
            if(ids instanceof Object)
            {
                issuccess = true;
                ids["flag"] = issuccess;
            //               document.location = "XMLExporter/forceDownload.php";
                
            //                var exportConfirm = $("<div id='msm_export_confirm' class='dialogs' title='Export Complete'><p> Your composition has been successfully exported to designated folder. </p></div>");
            //                    
            //                $("#msm_editor_middle").append(exportConfirm);
            //                    
            //                $("#msm_export_confirm").dialog({
            //                    modal:false,
            //                    buttons: {
            //                        Ok: function(){
            //                            $(this).dialog("close");
            //                        }
            //                    }
            //                });                
            }
            else
            {
                var exportError = $("<div id='msm_export_error' class='dialogs' title='Export Error'><p> The export process was not able to finish successfully. </p></div>");
                    
                $("#msm_editor_middle").append(exportError);
                    
                $("#msm_export_error").dialog({
                    modal:false,
                    buttons: {
                        Ok: function(){
                            $(this).dialog("close");
                        }
                    }
                });
            }
            
        },
        error: function() {}
    }).done(function() {
        if(ids)
        {
            $.get(
                'XMLExporter/forceDownload.php', 
                {
                    zipfilename: ids["zipfilename"]
                });
        }
    }); 
}



