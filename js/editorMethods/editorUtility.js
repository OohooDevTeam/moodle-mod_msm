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
 * This method is called when the unit is saved and it adds a node to jsTree when the unit is added. * 
 *
 * @param {string} dbId                       string with information about saved unit
 */
function insertUnitStructure(dbId)
{
    var ajaxInfo = dbId.split("|");

    var unitInfo = ajaxInfo[0].split("-");
    var unitName = null;
    var idPair = null; 
    
    if(unitInfo.length > 2)
    {
        unitName = unitInfo[0];
        idPair = unitInfo[1]+"-"+unitInfo[2];
    }
    else
    {
        idPair = ajaxInfo[0];
    }    
    
    var linkText = null;  
        
    // from being called after edit was done
    if(ajaxInfo.length > 1)
    {
        // ajaxInfo[1] contains the id information for editted unit
        // it is used to find the old unit to replace with new unit when the
        // unit is editted
        var currentUnitInfo = ajaxInfo[1].split("-");
        var currentidPair = null;
        
        if(currentUnitInfo.length > 2)
        {
            currentidPair = currentUnitInfo[1]+"-"+currentUnitInfo[2]; 
        }
        else
        {
            currentidPair = currentUnitInfo[0]+"-"+currentUnitInfo[1]; 
        }
        
        //update the tree when the existing unit is editted
        $("#msm_composition_default").find("li").each(function() {
            var targetId = "msm_unit-"+currentidPair;
            var currentId = '';            
            var match = this.id.match(/msm_unit-.+/);
            
            if(!match)
            {
                currentId = "msm_unit-"+this.id;
            }
            else
            {
                currentId = this.id;
            }
            if(currentId == targetId)
            {                
                var parent = $(this).parents("ul").eq(0);
                var copyofCurrent = $(this).clone();
                
                // to replace the unit name if it had been changed from edit
                if(unitName != null)
                {
                    linkText = document.createTextNode(unitName);
                    $(copyofCurrent).children("a").each(function() {
                        $(this).empty();
                        $(this).append(linkText);
                    })
                }
                
                $(this).empty().remove();
                $(copyofCurrent).appendTo(parent);                
            }                
        });
    }
    else // creating the tree for the first time
    {
        var treetopli = $("#msm_composition_default").children("ul");
    
        var listChild = $("<li></li>");
        $(listChild).attr("id", "msm_unit-"+idPair);
    
        var linkElement = $("<a href='#'></a>");
        
        if(unitName != null)
        {
            linkText = document.createTextNode(unitName);
        }
        else
        {
            linkText = document.createTextNode(idPair);
        }
    
        $(linkElement).append(linkText);    
        $(listChild).append(linkElement);
    
        var rootul = $("<ul></ul>");
    
        var treeChildren = $("#msm_composition_default > ul").first().children("li");    
    
        if(treetopli.length == 0)
        { 
            rootul.append(listChild);
            $("#msm_composition_default").append(rootul);
        }
        else if(treeChildren.length == 0)
        {
            $("#msm_composition_default > ul").append(listChild);
        }
        else
        {
            var childUls = $("#msm_composition_default > ul > li").find("ul");        
        
            if(childUls.length == 0)  // no ul appended to top unit node to add more nested node to
            {
                rootul.append(listChild);
                $("#msm_composition_default > ul > li").first().append(rootul);
            }
            else // already there is subunit attached to the top unit so just append another item to the list
            {
                $("#msm_composition_default > ul > li").first().children("ul").append(listChild);          
            }
        }    
    }
        
    var currentUnit = document.getElementById('msm_currentUnit_id');
   
    if((currentUnit == null)||(currentUnit == "undefined"))
    {
        var newInputField = document.createElement("input");
        newInputField.id = "msm_currentUnit_id";
        newInputField.name = "msm_currentUnit_id";
        newInputField.setAttribute("style", "display:none;");
        newInputField.value = idPair;
        
        var form = document.getElementById("msm_unit_form");
                        
        form.appendChild(newInputField);
    }
    else
    {
        $("#msm_currentUnit_id").val(idPair);
    } 
    
    var parentOfCurrent = $("#msm_unit-"+idPair).parents("li").eq(0).attr("id");
    
    initTrees(idPair, parentOfCurrent);
    
}

/**
 * This method is used to initialize the tree built by the above method with jsTree jquery plugin.
 * 
 * @param {string} idPair             string of unit-comp_id-unit-unit_Id
 * @param {string} parent             HTML ID of the parent node of current unit node
 */
function initTrees(idPair, parent)
{
    $("#msm_unit_tree")
    .jstree({
        "plugins": ["themes", "html_data", "ui", "dnd"],
        "core":{
            "initially_open":[parent]
        }
    })
    .bind("load.jstree", function(){
        // select current unit as a default
        $("#msm_unit_tree").jstree("select_node", "msm_unit-"+idPair).trigger("select_node.jstree");
        $(".copied_msm_structural_element").unbind();
        // activate the overlay to give user buttons for edit/delete
        $("#msm_child_appending_area > .copied_msm_structural_element").mouseenter(
            function() {                
                var idNumber = $(this).attr("id").split("-");
                var overlayheight = $(this).height();

                $("#msm_element_overlay-"+idNumber[1]).css("top", this.offsetTop);

                $("#msm_element_overlay-"+idNumber[1]).css("display", "block");

                $("#msm_element_overlay-"+idNumber[1]).stop(true, true).animate({
                    height: overlayheight+50
                }, 700);                      
            });
        $("#msm_child_appending_area > .copied_msm_structural_element").mouseleave(function() {   
            var idNumber = $(this).attr("id").split("-");

            $("#msm_element_overlay-"+idNumber[1]).stop(true, true).animate({
                height: "30px"
            }, 300);
            $("#msm_element_overlay-"+idNumber[1]).css("display", "none");
        });
        
    })
    .bind("select_node.jstree", function(event, data) {
        // when a node is selected, use AJAX call to retrieve contents associated with this unit
        // and display the unit in middle panel
        $("#msm_standalone_tree").jstree("deselect_all");
        var dbInfo = [];         

        $(".msm_editor_buttons").remove();
        
        var nodeId = data.rslt.obj.attr("id");      
        var match = nodeId.match(/msm_unit-.+/);
        var nodeInfo = "";
        if(match)
        {
            $("#msm_editor_new").removeAttr("disabled");
            var tempInfo = nodeId.split("-");
            nodeInfo = tempInfo[1]+"-"+tempInfo[2];
        }
        else if(nodeId != "msm_composition_default")
        {
            $("#msm_editor_new").removeAttr("disabled");
            nodeInfo = nodeId;
        }
        else if(nodeId == "msm_composition_default")
        {
            $("#msm_editor_new").attr("disabled", "disabled");
            $("<input class=\"msm_editor_buttons\" id=\"msm_editor_reset\" type=\"button\" onclick=\"resetUnit()\" value=\"Reset\"/> ").appendTo("#msm_unit_form");
            $("<input type=\"submit\" name=\"msm_editor_save\" class=\"msm_editor_buttons\" id=\"msm_editor_save\" disabled=\"disabled\" value=\"Save\"/>").appendTo("#msm_unit_form");
            $("#msm_child_appending_area").empty();
        }

        if(nodeInfo != '')
        {
            $.ajax({
                type: "POST",
                url: "editorCreation/msmLoadUnit.php",
                data: {
                    "id": "msm_unit-"+nodeInfo
                },
                success: function(data)
                {
                    dbInfo = JSON.parse(data);  
                    processUnitData(dbInfo); 
                    $("#msm_currentUnit_id").val(nodeInfo);
                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);    

                },
                error: function(data)
                {
                    alert("ajax error in loading unit");
                }
            })
        }
    })
    .delegate("a", "click", function(event, data){
        event.preventDefault();
    });    
    
    $("#msm_standalone_tree")
    .jstree({
        "plugins": ["themes", "html_data", "ui", "dnd"],
        "core":{
            "initially_open":["msm_standalone_root"]
        }
    })
    .bind("select_node.jstree", function(event, data) {
        $("#msm_unit_tree").jstree("deselect_all");
        var dbInfo = [];         

        $(".msm_editor_buttons").remove();
        
        var nodeId = data.rslt.obj.attr("id");      
        var match = nodeId.match(/msm_unit-.+/);
        var nodeInfo = "";
        if(match)
        {
            var tempInfo = nodeId.split("-");
            nodeInfo = tempInfo[1]+"-"+tempInfo[2];
        }
        else if(nodeId != "msm_standalone_root")
        {
            nodeInfo = nodeId;
        }
        else if(nodeId == "msm_standalone_root")
        {
            $("#msm_editor_new").attr("disabled", "disabled");
            $("<input class=\"msm_editor_buttons\" id=\"msm_editor_reset\" type=\"button\" onclick=\"resetUnit()\" value=\"Reset\"/> ").appendTo("#msm_unit_form");
            $("<input type=\"submit\" name=\"msm_editor_save\" class=\"msm_editor_buttons\" id=\"msm_editor_save\" disabled=\"disabled\" value=\"Save\"/>").appendTo("#msm_unit_form");
            $("#msm_child_appending_area").empty();
        }

        if(nodeInfo != '')
        {
            $.ajax({
                type: "POST",
                url: "editorCreation/msmLoadUnit.php",
                data: {
                    "id": "msm_unit-"+nodeInfo
                },
                success: function(data)
                {
                    dbInfo = JSON.parse(data);  
                    processUnitData(dbInfo); 
                    $("#msm_currentUnit_id").val(nodeInfo);
                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);    

                },
                error: function(data)
                {
                    alert("ajax error in loading unit");
                }
            }); 
        }      

    })
    .delegate("a", "click", function(event, data){
        event.preventDefault();
    });   
}

/**
 *  This method is used to create blank unit form when the user triggers the "New Unit" button after save.
 */
function newUnit()
{
    $("#msm_child_appending_area").empty();
    
    if(tinymce.getInstanceById("msm_unit_title") != null)
    {
        tinyMCE.execCommand("mceRemoveControl", true, "msm_unit_title");        
    }
    
    // need to switch display only div to input field so when the form is submitted, then the data can be passed as POST object
    $("div#msm_unit_title").replaceWith('<input class="msm_title_input" id="msm_unit_title" name="msm_unit_title" onkeypress="validateBorder()"/>');
    
    $("#msm_unit_title").val('');
    $("#msm_unit_short_title").val('');
    $("#msm_unit_description_input").val('');
    
    $("#msm_child_order").val('');
    $("#msm_currentUnit_id").val('');
    
    var fileoption = $('<input id="msm_file_options" name="msm_file_options" style="display:none;"/>');
    $(fileoption).val(JSON.stringify(tinymce_filepicker_options));
    $(fileoption).appendTo("#msm_unit_form");
    
   
    initTitleEditor("msm_unit_title", "80%");
    $("#msm_unit_short_title").removeAttr("readonly");
    $("#msm_unit_description_input").removeAttr("readonly");
    
    $(".msm_editor_buttons").remove();    
    $("<input class=\"msm_editor_buttons\" id=\"msm_editor_reset\" type=\"button\" onclick=\"resetUnit()\" value=\"Reset\"/> ").appendTo("#msm_unit_form");
    $("<input type=\"submit\" name=\"msm_editor_save\" class=\"msm_editor_buttons\" id=\"msm_editor_save\" disabled=\"disabled\" value=\"Save\"/>").appendTo("#msm_unit_form");
    
    $("#msm_editor_new").attr("disabled", "disabled");
    
    $(".msm_structural_element").draggable({
        appendTo: "msm_editor_middle_droparea",
        containment: "msm_editor_middle_droparea",
        scroll: true,
        cursor: "move",
        helper: "clone"                   
    });       
    
    $(".msm_child_element").draggable({
        appendTo: "msm_editor_middle_droparea",
        containment: "msm_editor_middle_droparea",
        scroll: true,
        cursor: "move",
        helper: "clone"
    }); 
        
    $("#msm_editor_middle_droparea").droppable({
        accept: "#msm_component_tabs-1 > div",
        hoverClass: "ui-state-hover",
        tolerance: "pointer",
        drop: function( event, ui ) { 
            processDroppedChild(event, ui.draggable.context.id);
            allowDragnDrop();  
        }
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
            // this code along with the one in stop is needed for enabling sortable on the div containing
            // the tinymce editor so the iframe part of the editor doesn't become disabled
            $(this).find('.msm_unit_child_content').each(function() {
                tinyMCE.execCommand("mceRemoveControl", false, $(this).attr("id")); 
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
        },
        stop: function(event, ui)
        {
            $("#"+ui.item.context.id).css("background-color", "#EDEDED");
            
            $(this).find('.msm_unit_child_content').each(function() {
                if(tinymce.getInstanceById($(this).attr("id"))==null)
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
                    initTitleEditor(this.id, "96%");       
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
        }
    });         
    $("#msm_child_appending_area").sortable("refresh");
    
    $("#msm_editor_save").click(function(event) { 
        //prevents navigation to msmUnitForm.php
        event.preventDefault();
        
        $("#msm_unit_short_title").removeAttr("readonly");
        $("#msm_unit_description_input").removeAttr("readonly");
        $(".copied_msm_structural_element select").removeAttr("disabled");
        $(".copied_msm_structural_element input").removeAttr("disabled");
        $("#msm_child_appending_area").find(".msm_editor_content").each(function() {
            $(this).removeClass("msm_editor_content");
            var newdata = document.createElement("textarea");
            newdata.id = this.id;
            newdata.name = this.id;
            newdata.className = this.className;
                    
            // remove all mathjax HTML code in content before
            // putting the content into tinyMCE
            $(this).find("span.matheditor").each(function() {
                var newspan = document.createElement("span");
                newspan.className = "matheditor";
    
                var scriptChild = $(this).find("script");
        
                var scriptWithMath = scriptChild[scriptChild.length-1];
                var mathContent = "\\("+$(scriptWithMath).text()+"\\)"; 
                $(newspan).append(mathContent);
                
                $(this).replaceWith(newspan);
            });
            
            newdata.value = $(this).html();            
            $(this).replaceWith(newdata);               
        });              
        submitForm();            
    });
}

/**
 * This method appends the content of the selected unit to the main editor
 * and displays the information.  Also it sets the editor up for any edit that 
 * maybe made by the user and disables all editor until the edit is triggered.
 *
 * @param {string} htmlData                HTML code to display the unit
 */
function processUnitData(htmlData)
{    
    if(tinymce.getInstanceById("msm_unit_title") != null)
    {
        tinyMCE.execCommand("mceRemoveControl", true, "msm_unit_title");        
    }
    
    $(".msm_unit_intro_title").each(function() {
        if(tinymce.getInstanceById(this.id) != null)
        {
            tinyMCE.execCommand("mceRemoveControl", true, this.id);        
        }
    })
    $('#msm_unit_form').empty();
    
    $('#msm_unit_form').append(htmlData);
    
    var currentUnit = document.getElementById('msm_currentUnit_id');
    var form = document.getElementById("msm_unit_form");
   
    // if the currentUnit hidden input field is missing, readd it
    if((currentUnit == null)||(currentUnit == "undefined"))
    {
        var newInputField = document.createElement("input");
        newInputField.id = "msm_currentUnit_id";
        newInputField.name = "msm_currentUnit_id";
        newInputField.setAttribute("style", "display:none;");      
                        
        form.appendChild(newInputField);
    }
    
    // activate the overlay
    $(".copied_msm_structural_element").unbind();
    $("#msm_child_appending_area > .copied_msm_structural_element").mouseenter(
        function() {
            var idNumber = $(this).attr("id").split("-");
            var overlayheight = $(this).height();

            $("#msm_element_overlay-"+idNumber[1]).css("top", this.offsetTop);

            $("#msm_element_overlay-"+idNumber[1]).css("display", "block");

            $("#msm_element_overlay-"+idNumber[1]).stop(true, true).animate({
                height: overlayheight+50
            }, 700);                      
        });
    $("#msm_child_appending_area > .copied_msm_structural_element").mouseleave(function() {       
        var idNumber = $(this).attr("id").split("-");

        $("#msm_element_overlay-"+idNumber[1]).stop(true, true).animate({
            height: "30px"
        }, 300);
        $("#msm_element_overlay-"+idNumber[1]).css("display", "none");
    });
        
    // enable all unit title/description input fields to be able to edit upon double click trigger
    $("#msm_unit_title").dblclick(function(){
        processMathContent(this.id);
        initTitleEditor(this.id, "80%");
        allowDragnDrop();
    });
    $("#msm_unit_short_title").dblclick(function(){
        $(this).removeAttr("readonly");
        $(this).addClass("msm_add_border");
        allowDragnDrop();
    });
    $("#msm_unit_description_input").dblclick(function(){
        $(this).removeAttr("readonly");
        $(this).addClass("msm_add_border");
        allowDragnDrop();
    });
    $(".msm_structural_element").draggable({
        appendTo: "msm_editor_middle_droparea",
        containment: "msm_editor_middle_droparea",
        scroll: true,
        cursor: "move",
        helper: "clone"                   
    }); 
    
    $(".msm_child_element").draggable({
        appendTo: "msm_editor_middle_droparea",
        containment: "msm_editor_middle_droparea",
        scroll: true,
        cursor: "move",
        helper: "clone"
    }); 

    $("#msm_editor_middle_droparea").droppable({
        accept: "#msm_component_tabs-1 > div",
        hoverClass: "ui-state-hover",
        tolerance: "pointer",
        drop: function( event, ui ) { 
            processDroppedChild(event, ui.draggable.context.id);      
            allowDragnDrop();  
        }
    }); 
    $(".msm_dnd_containers").droppable({
        accept: "#msm_component_tabs-2 > div",
        hoverClass: "ui-state-hover",
        tolerance: "pointer",
        drop: function( event, ui ) { 
            processAdditionalChild(event, ui.draggable.context.id);      
            allowDragnDrop();  
        }
    });    
}

/**
 * This method is triggered by the "Done" button and it starts the methods to update the unit information
 * to reflect the relationship between each unit in the jsTree into msm_compositor table.  After done button
 * is pressed, the page navigates to the view.php for displaying the unit.
 *
 * @param {eventObject} e             click trigger event object
 */
function saveComp(e)
{
    e.preventDefault();
    
    var exsitingSaveButton = $("#msm_editor_middle").find("#msm_editor_save");
    
    if(exsitingSaveButton.length > 0)
    {
        $("<div class='dialogs' id='msm_save_comp'> <span class='ui-icon ui-icon-alert' style='float: left; margin: 0 7px 20px 0;'></span>Please save the current unit first. </div>").appendTo('#msm_editor_middle');
        $( "#msm_save_comp" ).dialog({
            resizable: false,
            height:180,
            modal: true,
            buttons: {
                "Ok": function() {
                    $(this).dialog("close");
                }
            }
        });
    }
    else
    {
        var treeInnerHTML = $("#msm_composition_default > ul").html();    
    
        var standaloneTree = '';
        $("#msm_standalone_root").find("li").each(function() {
            standaloneTree += $(this).attr("id") + ",";  
        });
   
        var params = {
            tree_content: treeInnerHTML,
            standalone_content: standaloneTree
        };
        var ids = [];
        $.ajax({
            type: 'POST',
            url:"editorCreation/msmLoadUnit.php",
            data: params,
            success: function(data)
            {
                ids = JSON.parse(data);
                var idInfos = ids.split("-");
                if(ids != '')
                {
                    window.location = "view.php?msmid="+idInfos[0]+"&unitid="+idInfos[1];                        
                }
            },
            error: function(data)
            {
                alert("ajax error in loading unit");
            }
        }); 
    }    
}

// triggered by edit button when either saved after making the unit, or when edit button is clicked after returning to edit mode from display mode
/**
 * This method is triggered by the user when the "Edit" button on the overlay is pressed or when other elements are dragged to designated areas to be added.
 * This method essentially enables the tinyMCE for each div with class name msm_editor_content and for unit title input field.  Also all the input fields
 * and the select menus are enabled.
 *
 * @param {eventObject} e                 click event object from the edit button in overlay
 */
function editUnit(e)
{   
    reinitAllTitles();   
    
    $("#msm_editor_new").attr("disabled", "disabled");
    
    if($("#msm_unit_short_title").attr("readonly"))
    {
        $("#msm_unit_short_title").removeAttr("readonly");
    }
    if($("#msm_unit_description_input").attr("readonly"))
    {
        $("#msm_unit_description_input").removeAttr("readonly");
    }

    var targetElement = '';
    
    
    // when editUnit is called from editChildCompnents.js when child elements are dragged
    // to edit the content without edit button trigger
    if($.type(e) === "string")
    {
        if(e.match(/theorem/))
        {
            var idInfo = e.split("-");
            var theoremId = idInfo[1];
            if(e.match(/theoremref/))
            {
                targetElement = "copied_msm_theoremref-"+theoremId;
            }
            targetElement = "copied_msm_theorem-"+theoremId;                
        }
        else
        {
            targetElement = e;
        }
    }
    else
    {
        if(e.target.tagName == "A")
        {
            targetElement = e.target.parentElement.parentElement.id;
        }
        else 
        {      
            targetElement = e.target.parentElement.parentElement.parentElement.id;
        }
    }
       
    var elementInfo = [];
    
    $.ajax({
        type: "POST",
        url: "editorCreation/msmLoadUnit.php",
        data:{
            "mode":"edit",
            "childOrder": $("#msm_child_order").val(),
            "currentElement": targetElement,
            "currentUnit": $("#msm_currentUnit_id").val()
        },
        success: function(data)
        {
            // elementInfo is an instance of EditorUnit class
            elementInfo = JSON.parse(data);
            enableContentEditors(elementInfo, targetElement);  
            enableEditorFunction();   
            
            var closeButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');      
            //        
            var intromatch = targetElement.match(/copied_msm_intro-.+/);
            var bodymatch = targetElement.match(/copied_msm_body-.+/);
        
            if((!intromatch)&&(!bodymatch))
            {
                $(closeButton).attr("style", "margin-top: 2%;");
            }
            
            $("#"+targetElement).prepend(closeButton); //can't use insertBefore since the reference element to insert before can change (eg. intro is header while def is select)
            
            $("#msm_editor_new").attr("disabled", "disabled");
            
            var saveButton = document.getElementById("msm_editor_save");
            
            if(saveButton == null)
            {
                $(".msm_editor_buttons").remove();
                $("<input type='submit' class='msm_editor_buttons' id='msm_editor_save' value='Save'/>").appendTo("#msm_editor_middle");          
                $('<button class="msm_editor_buttons" id="msm_editor_cancel" onclick="cancelUnit(event)"> Cancel </button>').appendTo("#msm_editor_middle");  
            } 
            
            // when new child elements are editted in after save, if the close button is not removed, the code below adds multiple close buttons
            $("#"+targetElement).find(".msm_element_close").each(function() {
                $(this).remove(); 
            });
            
            $("#"+targetElement).find(".msm_theorem_statement_title_containers").each(function() {
                var closeButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');      
                $(closeButton).insertBefore($(this));
            });
    
            $("#"+targetElement).find(".msm_theorem_part_title_containers").each(function() {
                var closeButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');      
                $(closeButton).insertBefore($(this));
            });

            $("#"+targetElement).find(".msm_theoremref_statement_title_containers").each(function() {
                var closeButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');      
                $(closeButton).insertBefore($(this));
            });
    
            $("#"+targetElement).find(".msm_theoremref_part_title_containers").each(function() {
                var closeButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');      
                $(closeButton).insertBefore($(this));
            });
    
    
            $("#"+targetElement).find(".msm_associate_info_headers").each(function() {
                var closeButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
                $(closeButton).insertBefore($(this));
            });
    
            $("#"+targetElement).find(".msm_intro_child_dragareas").each(function() {
                var closeButton = $('<a class="msm_element_close" onclick="deleteElement(event)">x</a>');
                $(closeButton).insertBefore($(this));
            });
    
            $("#msm_editor_save").unbind("click");
            $("#msm_editor_save").click(function(event) { 
                //         prevents navigation to msmUnitForm.php
                event.preventDefault();
                // enabling all input that was disabled to submit the form             
                $("#msm_unit_short_title").removeAttr("readonly");
                $("#msm_unit_description_input").removeAttr("readonly");
                $(".copied_msm_structural_element select").removeAttr("disabled");
                $(".copied_msm_structural_element input").removeAttr("disabled");
                $("#msm_child_appending_area").find(".msm_editor_content").each(function() {
                    $(this).removeClass("msm_editor_content");
                    var newdata = document.createElement("textarea");
                    newdata.id = this.id;
                    newdata.name = this.id;
                    newdata.className = this.className;
                    
                    // replace mathjax HTML code with <span class='matheditor'>\(math_content\)</span>
                    $(this).find("span.matheditor").each(function() {
                        var newspan = document.createElement("span");
                        newspan.className = "matheditor";
    
                        var scriptChild = $(this).find("script");
        
                        var scriptWithMath = scriptChild[scriptChild.length-1];
                        var mathContent = "\\("+$(scriptWithMath).text()+"\\)"; 
                        $(newspan).append(mathContent);
                
                        $(this).replaceWith(newspan);
                    });
            
                    newdata.value = $(this).html();            
                    $(this).replaceWith(newdata);               
                });
                submitForm();
            
            });    
    
        }
    });
}

/**
 * This method is called by editUnit method above to reinitiate tinyMCE editor to all the title input fields.
 */
function reinitAllTitles() 
{
    var inputUnitTitle = $('<input class="msm_title_input" id="msm_unit_title" name="msm_unit_title" onkeypress="validateBorder()"/>');
    processMathContent("msm_unit_title");
    var currentTitleContent = $("div#msm_unit_title").html();
    
    console.log("content of unit title?");
    console.log(currentTitleContent);
    
    $(inputUnitTitle).val(currentTitleContent);
    
    $("div#msm_unit_title").replaceWith(inputUnitTitle);   
    
    $("div.msm_editor_titles").each(function() {
        var currentIntroId = this.id;
        
        $(this).removeClass("msm_editor_titles");
        var currentClassName = this.className;
        
        var inputIntroTitle = $('<input class="'+currentClassName+'" id="'+currentIntroId+'" name="'+currentIntroId+'"/>');
        
        processMathContent(this.id);
        var currentIntroContent = $(this).html();
        
        console.log("content of intro title?");
        console.log(currentIntroContent);
        
        $(inputIntroTitle).val(currentIntroContent);
        
        $(this).replaceWith(inputIntroTitle);    
        initTitleEditor(this.id, "96%");
    });
    
    initTitleEditor("msm_unit_title", "80%");
}



/**
 * This method undoes all the actions done by above method(disableEditorFunction) and enables all input and selection fields,
 * reinitalizes all jquery actions, and reattaches close buttons for deletion of each structural element.
 */
function enableEditorFunction()
{
    // reinitalize all jquery actions
    $(".msm_structural_element").draggable({
        appendTo: "msm_editor_middle_droparea",
        containment: "msm_editor_middle_droparea",
        scroll: true,
        cursor: "move",
        helper: "clone"                   
    });      
    
    $(".msm_child_element").draggable({
        appendTo: "msm_editor_middle_droparea",
        containment: "msm_editor_middle_droparea",
        scroll: true,
        cursor: "move",
        helper: "clone"
    }); 
            
    $("#msm_editor_middle_droparea").droppable({
        accept: "#msm_component_tabs-1 > div",
        hoverClass: "ui-state-hover",
        tolerance: "pointer",
        drop: function( event, ui ) { 
            processDroppedChild(event, ui.draggable.context.id); 
            allowDragnDrop();    
        }
    }); 
    $(".msm_dnd_containers").droppable({
        accept: "#msm_component_tabs-2 > div",
        hoverClass: "ui-state-hover",
        tolerance: "pointer",
        drop: function( event, ui ) { 
            processAdditionalChild(event, ui.draggable.context.id);      
            allowDragnDrop();  
        }
    });
    
    moveElements();
    
    enableDragTitleToggle();
}

/**
 * This method initializes all the sortable jquery plugins to each draggable elements
 * for drag and drop sorting.
 */
function moveElements() 
{
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
                    initTitleEditor(this.id, "96%");       
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
        }
    });
    $("#msm_child_appending_area").sortable("refresh");
    
    $(".msm_associate_containers").each(function() {
        $("#"+this.id).sortable({
            appendTo: this.id,
            connectWith: this.id,
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
                var id = $(this).attr("id");            
            
                $("#"+id+" textarea").each(function() {
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
            }
        });   
        $("#"+this.id).sortable("refresh");
    });   
    
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
                        
            $(this).find(".msm_intro_child_titles").each(function() {
                tinyMCE.execCommand('mceFocus', false, $(this).attr("id"));          
                tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
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
            
            $(this).find(".msm_intro_child_titles").each(function() {
                if(tinymce.getInstanceById($(this).attr("id"))==null)
                {
                    initTitleEditor(this.id, "96%");                    
                }
            });
        }
    });
    $("#msm_intro_child_container").sortable("refresh");


    $(".msm_theorem_content_containers").each(function() {
        $("#"+this.id).sortable({
            appendTo: this.id,
            connectWith: this.id,
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
            }
        });
        $("#"+this.id).sortable("refresh");

    });
    $(".msm_theorem_part_dropareas").each(function(){
        $("#"+this.id).sortable({
            appendTo: this.id,
            connectWith: this.id,
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
            }
        });
        $("#"+this.id).sortable("refresh");
    });
    
    $(".msm_theoremref_content_containers").each(function() {
        $("#"+this.id).sortable({
            appendTo: this.id,
            connectWith: this.id,
            cursor: "move",
            tolerance: "pointer",
            placeholder: "msm_sortable_placeholder",   
            handle: ".msm_theoremref_statement_title_containers",
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
            }
        });
        $("#"+this.id).sortable("refresh");
    });
    $(".msm_theoremref_part_dropareas").each(function(){
        $("#"+this.id).sortable({
            appendTo: this.id,
            connectWith: this.id,
            cursor: "move",
            tolerance: "pointer",
            placeholder: "msm_sortable_placeholder",    
            handle: ".msm_theoremref_part_title_containers",
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
            }
        });
        $("#"+this.id).sortable("refresh");
    });
}

/**
 * This method enables the draggable area for each draggable element.  It enables the toggling of 
 * text to indicate draggable area.
 */
function enableDragTitleToggle()
{   
    $(".msm_element_title_containers").each(function() {
        $(this).mouseover(function() {
            $(this).children("span").each(function(){
                $(this).css({
                    "visibility": "visible",
                    "color": "#4e6632",
                    "opacity": "0.5",
                    "cursor": "move",
                    "display": "inline"
                });
            });
        });
        $(this).mouseout(function() {
            $(this).children("span").css("visibility", "hidden");
        });
        $(this).mouseup(function() {
            $(this).children("span").css("visibility", "hidden");
        });
    });
    
    $(".msm_intro_child_dragareas").each(function() {
        $(this).children("span").css("display", "inline");
        $(this).mouseover(function() {
            $(this).children("span").each(function(){
                $(this).css({
                    "visibility": "visible",
                    "color": "#4e6632",
                    "opacity": "0.5",
                    "cursor": "move",
                    "display": "inline"
                });
            });
        });
        $(this).mouseout(function() {
            $(this).children("span").css("visibility", "hidden");
        });
        $(this).mouseup(function() {
            $(this).children("span").css("visibility", "hidden");
        });
    });
    
    $(".msm_associate_info_headers").each(function() {
        $(this).mouseover(function() {
            $(this).children("span").css({
                "visibility": "visible",
                "color": "#4e6632",
                "opacity": "0.5",
                "cursor": "move",
                "display": "inline"
            });
        });
        $(this).mouseout(function() {
            $(this).children("span").css({
                "visibility": "hidden",
                "display": "none"
            });
        });
        $(this).mouseup(function() {
            $(this).children("span").css({
                "visibility": "hidden",
                "display": "none"
            });
        });
    });     
    
    $(".msm_theorem_statement_title_containers").each(function(){
        $(this).mouseover(function() {
            $(this).children("span").css({
                "visibility": "visible",
                "color": "#4e6632",
                "opacity": "0.5",
                "cursor": "move",
                "display": "inline"
            });
        });
        $(this).mouseout(function() {
            $(this).children("span").css("visibility", "hidden");
        });
        $(this).mouseup(function() {
            $(this).children("span").css("visibility", "hidden");
        });
    });
    
    $(".msm_theorem_part_title_containers").each(function() { 
        $(this).children("span").css("display", "inline");
        $(this).mouseover(function() {
            $(this).children("span").css({
                "visibility": "visible",
                "color": "#4e6632",
                "opacity": "0.5",
                "cursor": "move",
                "display": "inline"
            });
        });
        $(this).mouseout(function() {
            $(this).children("span").css("visibility", "hidden");
        });
        $(this).mouseup(function() {
            $(this).children("span").css("visibility", "hidden");
        });
    });
    
    $(".msm_theoremref_statement_title_containers").each(function(){
        $(this).mouseover(function() {
            $(this).children("span").css({
                "visibility": "visible",
                "color": "#4e6632",
                "opacity": "0.5",
                "cursor": "move",
                "display": "inline"
            });
        });
        $(this).mouseout(function() {
            $(this).children("span").css("visibility", "hidden");
        });
        $(this).mouseup(function() {
            $(this).children("span").css("visibility", "hidden");
        });
    });
    
    $(".msm_theoremref_part_title_containers").each(function() {
        $(this).children("span").css("display", "inline");
        $(this).mouseover(function() {
            $(this).children("span").css({
                "visibility": "visible",
                "color": "#4e6632",
                "opacity": "0.5",
                "cursor": "move",
                "display": "inline"
            });
        });
        $(this).mouseout(function() {
            $(this).children("span").css("visibility", "hidden");
        });
        $(this).mouseup(function() {
            $(this).children("span").css("visibility", "hidden");
        });
    });
}

/**
 * This method call the appropriate method to change from view mode to 
 * edit mode with all the editors, inputs, dropdown menus...etc activated.
 * The appropriate method is chosen according to the ID of div containers that 
 * the overlay is over which has the edit button.
 *
 * @param {array} unitArray               instance of the EditorUnit children class that was passed by JSON
 * @param {string} currentElement         HTML ID of the container with overlay that the edit button was triggere from
 */
function enableContentEditors(unitArray, currentElement)
{ 
    var intromatch = /^copied_msm_intro-\d+$/;
    var bodymatch = /^copied_msm_body-\d+$/;
    var defmatch = /^copied_msm_def-\d+$/;
    var commentmatch = /^copied_msm_comment-\d+$/;
    var theoremmatch = /^copied_msm_theorem-\d+$/;  
    var extrainfomatch = /^copied_msm_extra_info-\d+$/;
    
    if(currentElement.match(bodymatch))
    {
        createBodyText(currentElement, unitArray);    
    }
    else if(currentElement.match(intromatch))
    {
        createIntroText(currentElement, unitArray);
    }
    else if(currentElement.match(defmatch))
    {
        createDefText(currentElement, unitArray)
    }
    else if(currentElement.match(commentmatch))
    {
        createCommentText(currentElement, unitArray)
    }
    else if(currentElement.match(theoremmatch))
    {
        createTheoremText(currentElement, unitArray)
    }
    else if(currentElement.match(extrainfomatch))
    {
        createExtraInfoText(currentElement, unitArray);
    }    
}

/**
 * This method creates the "editable" theorem with all the input and dropdown
 * menus and tinyMCE editors enabled and disables the overlay.
 *
 * @param {string} element        HTML ID of the container with overlay that the edit button was triggere from
 * @param {array} unitInfo        instance of the EditorTheorem class that was passed by JSON
 */
function createTheoremText(element, unitInfo)
{
    var elementIdInfo = element.split("-");
    
    $("#"+element).find(".msm_theorem_part_title").each(function() {
        $(this).removeAttr("disabled");
    });
    $("#msm_element_overlay-"+elementIdInfo[1]).css("display", "none");
    $("#msm_theorem_type_dropdown-"+elementIdInfo[1]).removeAttr("disabled");
    $("#msm_theorem_title_input-"+elementIdInfo[1]).removeAttr("disabled");
    $("#msm_theorem_description_input-"+elementIdInfo[1]).removeAttr("disabled");
    $("#msm_theorem_child_button-"+elementIdInfo[1]).removeAttr("disabled");
    $("#msm_associate_button-"+elementIdInfo[1]).removeAttr("disabled");
    
    
    var theoremStatementInfo = $("#"+element).find(".msm_theorem_statement_containers");
    
    for(var i = 0; i < theoremStatementInfo.length; i++)
    {
        if(typeof unitInfo["contents"][i] !== "undefined")
        {           
            // get theorem content from the unit object
            var theoremcontent = unitInfo["contents"][i]["content"];
            var statementidInfo = theoremStatementInfo[i].id.split("-");
            
            var statementid = '';
            for(var j = 1; j < statementidInfo.length-1; j++)
            {
                statementid += statementidInfo[j]+"-";
            }
                
            statementid += statementidInfo[statementidInfo.length-1]; // now containering theoremid-statementid pair for first content then for rest it's theoremid-topstatementid-statementid
        
            var theoremStatementTextArea = document.createElement("textarea");
            theoremStatementTextArea.id = "msm_theorem_content_input-"+statementid;
            theoremStatementTextArea.name = "msm_theorem_content_input-"+statementid;
            theoremStatementTextArea.className = "msm_unit_child_content";
            
            $(theoremStatementTextArea).val(theoremcontent);
        
            $("#"+theoremStatementInfo[i].id).children(".msm_editor_content").each(function(index, element){
                $(this).replaceWith(theoremStatementTextArea);
            });
        
            initEditor(theoremStatementTextArea.id);
        
            // replace displayed theorem part to "editable" theorem part form
            var theoremPartInfo = $("#"+theoremStatementInfo[i].id).find(".msm_theorem_child");
     
            for(var k=0; k < theoremPartInfo.length; k++)
            {
                // when new part theorem is added by dragging before triggering the editUnit,
                // there are more part theorems than given in unitInfo object
                if(typeof unitInfo["contents"][i]["children"][k] !== "undefined")
                {
                    var theoremPartContent = unitInfo["contents"][i]["children"][k]["content"];            
           
                    var partidInfo = theoremPartInfo[k].id.split("-");
             
                    var partid = '';
                    for(var m = 1; m < partidInfo.length-1; m++)
                    {
                        partid += partidInfo[m]+"-";
                    }
                
                    partid += partidInfo[partidInfo.length-1];
        
                    var theoremPartTextArea = document.createElement("textarea");
                    theoremPartTextArea.id = "msm_theorem_part_content-"+partid;
                    theoremPartTextArea.name = "msm_theorem_part_content-"+partid;
                    theoremPartTextArea.className = "msm_theorem_content";
            
                    $(theoremPartTextArea).val(theoremPartContent);
        
                    $("#"+theoremPartInfo[k].id).children(".msm_editor_content").each(function(){
                        var elementidInfo = this.id.split("-");
                
                        var elementid = '';
                        for(var n=1; n < elementidInfo.length-1; n++)
                        {
                            elementid += elementidInfo[n] + "-";
                        }
                        elementid += elementidInfo[elementidInfo.length-1];
                        if(elementid == partid)
                        {
                            $(this).replaceWith(theoremPartTextArea);
                        }
                    });
                    initEditor(theoremPartTextArea.id);
                }            
            }
        }
    }
    $("#"+element).unbind();
    createAssociateText(element, unitInfo);
    enableEditorFunction();
}

/**
 * This method is used to create associate form that user can edit. It enables all 
 * tinyMCE edtiros and input fields/dropdown menus and disables the overlay.
 *
 * @param {string} mainElement            HTML ID of the container with overlay that the edit button was triggere from
 * @param {array} aArray                  instance of the EditorUnit children class that was passed by JSON
 */
function createAssociateText(mainElement, aArray)
{
    $("#"+mainElement).find(".msm_associated_dropdown").each(function() {
        $(this).removeAttr("disabled");
    });
    
    $("#"+mainElement).find(".msm_associate_reftype_dropdown").each(function() {
        $(this).removeAttr("disabled");
    });
    
    var associateArray = aArray["children"];
    var associateIds = $("#"+mainElement).find(".msm_associate_childs");
    
    for(var i = 0; i < associateIds.length; i++)
    {
        var infos = $("#"+associateIds[i].id).find(".msm_editor_content"); // includes the reference
        
        // can have associate that do not have msm_editor_content (ie.when the associate is editted in after save)
        if(infos.length > 0)
        {
            var currentInfo = infos[0].id.split("-");
        
            var infoid = '';
            for(var j=1; j < currentInfo.length-1; j++)
            {
                infoid += currentInfo[j]+"-";
            }
            infoid += currentInfo[currentInfo.length-1];
        
            //-------------------processing info----------------------
        
            var associateTitle = associateArray[i]["infos"][0]["caption"];
            var associateContent = associateArray[i]["infos"][0]["content"];
  
            var infoTitleArea = document.createElement("textarea");
            infoTitleArea.id = "msm_info_title-"+infoid;
            infoTitleArea.name = "msm_info_title-"+infoid;
            infoTitleArea.className = "msm_info_titles";
        
            var infoContentArea = document.createElement("textarea");
            infoContentArea.id = "msm_info_content-"+infoid;
            infoContentArea.name = "msm_info_content-"+infoid;
            infoContentArea.className = "msm_info_contents";
        
            $(infoTitleArea).val(associateTitle);
    
            $("#"+infos[0].id).replaceWith(infoTitleArea);
        
            noSubInitEditor(infoTitleArea.id);
        
            $(infoContentArea).val(associateContent);
    
            $("#"+infos[1].id).replaceWith(infoContentArea);
        
            initEditor(infoContentArea.id);
        }       
        
        //-------------------processing references----------------------
        
        if(infos.length >2)
        {
            var refIdInfo = infos[2].id.split("-");
        
            var refid = '';
            for(var n=1; n < refIdInfo.length-1; n++)
            {
                refid += refIdInfo[n]+"-";
            }
            refid += refIdInfo[refIdInfo.length-1];
        
            var refContent = associateArray[i]["infos"][0]["ref"]["content"];
            var refTableName = associateArray[i]["infos"][0]["ref"]["tablename"];
            switch(refTableName)
            {
                case "msm_def":
                    $("#msm_defref_type_dropdown-"+refid).removeAttr("disabled");
                    $("#msm_defref_title_input-"+refid).removeAttr("disabled");
                    $("#msm_defref_description_input-"+refid).removeAttr("disabled");
                    
                    var defrefTextArea = document.createElement("textarea");
                    defrefTextArea.id = "msm_defref_content_input-"+refid;
                    defrefTextArea.name = "msm_defref_content_input-"+refid;
                    defrefTextArea.className = "msm_unit_child_content";
                    
                    $(defrefTextArea).val(refContent);
                    $("#"+infos[2].id).replaceWith(defrefTextArea);
                    initEditor(defrefTextArea.id);
                    break;
                case "msm_theorem":
                    createTheoremRefText(mainElement, associateArray, i, infoid);   
                    break;
                case "msm_comment":
                    $("#msm_commentref_type_dropdown-"+refid).removeAttr("disabled");
                    $("#msm_commentref_title_input-"+refid).removeAttr("disabled");
                    $("#msm_commentref_description_input-"+refid).removeAttr("disabled");
                    
                    var commentrefTextArea = document.createElement("textarea");
                    commentrefTextArea.id = "msm_commentref_content_input-"+refid;
                    commentrefTextArea.name = "msm_commentref_content_input-"+refid;
                    commentrefTextArea.className = "msm_unit_child_content";
                    
                    $(commentrefTextArea).val(refContent);
                    $("#"+infos[2].id).replaceWith(commentrefTextArea);
                    initEditor(commentrefTextArea.id);
                    break;
            }
        }      
    }
    enableEditorFunction();
}

/**
 * This method creates a form for theorem reference material for the user to edit
 * the existing content.  
 *
 * @param {string} element                HTML ID of the container with overlay that the edit button was triggere from
 * @param {array} aArray                  instance of the EditorAssociate class that was passed by JSON
 * @param {integer} index                     index number for which associate this reference is relevant
 * @param {string} infoId                 string added to end of info element textareas to make them unique
 */
function createTheoremRefText(element, aArray, index, infoId)
{           
    $("#"+element).find(".msm_theorem_part_title").each(function() {
        $(this).removeAttr("disabled");
    });
   
    $("#"+element).find(".msm_child_description_inputs").each(function() {
        $(this).removeAttr("disabled");
    });
    
    $("#"+element).find(".msm_unit_child_title").each(function() {
        $(this).removeAttr("disabled");
    });
    
    $("#"+element).find(".msm_unit_child_dropdown").each(function() {
        $(this).removeAttr("disabled");
    });
    
    var theoremStatementInfo = $("#msm_associate_reftype_option-"+infoId).find(".msm_theoremref_statement_containers");
     
    for(var ind = 0; ind < theoremStatementInfo.length; ind++)
    {
        var theoremcontent = aArray[index]["infos"][0]["ref"]["contents"][ind]["content"];
        var statementidInfo = theoremStatementInfo[ind].id.split("-");
            
        var statementid = '';
        for(var j = 1; j < statementidInfo.length-1; j++)
        {
            statementid += statementidInfo[j]+"-";
        }
                
        statementid += statementidInfo[statementidInfo.length-1]; // now containing theoremid-statementid pair for first content then for rest it's theoremid-topstatementid-statementid
        
        var theoremStatementTextArea = document.createElement("textarea");
        theoremStatementTextArea.id = "msm_theoremref_content_input-"+statementid;
        theoremStatementTextArea.name = "msm_theoremref_content_input-"+statementid;
        theoremStatementTextArea.className = "msm_unit_child_content msm_theorem_content";
            
        $(theoremStatementTextArea).val(theoremcontent);
        
        $("#"+theoremStatementInfo[ind].id).children(".msm_editor_content").each(function(index, element){
            $(this).replaceWith(theoremStatementTextArea);
        });
        
        initEditor(theoremStatementTextArea.id);
        
        var theoremPartInfo = $("#"+theoremStatementInfo[ind].id).find(".msm_theorem_child");
     
        for(var k=0; k < theoremPartInfo.length; k++)
        {
            var theoremPartContent = aArray[index]["infos"][0]["ref"]["contents"][ind]["children"][k]["content"];
            var partidInfo = theoremPartInfo[k].id.split("-");
             
            var partid = '';
            for(var m = 1; m < partidInfo.length-1; m++)
            {
                partid += partidInfo[m]+"-";
            }
                
            partid += partidInfo[partidInfo.length-1];
        
            var theoremPartTextArea = document.createElement("textarea");
            theoremPartTextArea.id = "msm_theoremref_part_content-"+partid;
            theoremPartTextArea.name = "msm_theoremref_part_content-"+partid;
            theoremPartTextArea.className = "msm_theorem_content";
            
            $(theoremPartTextArea).val(theoremPartContent);
        
            $("#"+theoremPartInfo[k].id).children(".msm_editor_content").each(function(index, element){
                var elementidInfo = this.id.split("-");
                
                var elementid = '';
                for(var n=1; n < elementidInfo.length-1; n++)
                {
                    elementid += elementidInfo[n] + "-";
                }
                elementid += elementidInfo[elementidInfo.length-1];
                                
                if(elementid == partid)
                {
                    $(this).replaceWith(theoremPartTextArea);
                }
            });
            initEditor(theoremPartTextArea.id);
        }
    }
}

/**
 * This method creates the "editable" comment with all the input and dropdown
 * menus and tinyMCE editors enabled and disables the overlay.
 *
 * @param {string} element        HTML ID of the container with overlay that the edit button was triggere from
 * @param {array} unitInfo        instance of the EditorComment class that was passed by JSON
 */
function createCommentText(element, unitInfo)
{
    var elementIdInfo = element.split("-");
    
    $("#msm_element_overlay-"+elementIdInfo[1]).css("display", "none");
    $("#msm_comment_type_dropdown-"+elementIdInfo[1]).removeAttr("disabled");
    $("#msm_comment_title_input-"+elementIdInfo[1]).removeAttr("disabled");
    $("#msm_comment_description_input-"+elementIdInfo[1]).removeAttr("disabled");
    $("#msm_associate_button-"+elementIdInfo[1]).removeAttr("disabled");
    
    var commentcontent = unitInfo["content"];
    
    var currentId = $("#"+element).children(".msm_editor_content").first().attr("id");
    
    var commentInfo = currentId.split("-");
                
    var commentTextArea = document.createElement("textarea");
    commentTextArea.id = "msm_comment_content_input-"+commentInfo[1];
    commentTextArea.name = "msm_comment_content_input-"+commentInfo[1];
    commentTextArea.className = "msm_unit_child_content";
            
    $(commentTextArea).val(commentcontent);   
    
    $("#"+currentId).replaceWith(commentTextArea);    
        
    initEditor(commentTextArea.id);    
    $("#"+element).unbind();
    createAssociateText(element, unitInfo);
    enableEditorFunction();
}

/**
 * This method creates the "editable" definition with all the input and dropdown
 * menus and tinyMCE editors enabled and disables the overlay.
 *
 * @param {string} element        HTML ID of the container with overlay that the edit button was triggere from
 * @param {array} unitInfo        instance of the EditoDefinition class that was passed by JSON
 */
function createDefText(element, unitInfo)
{
    var elementIdInfo = element.split("-");
    
    $("#msm_element_overlay-"+elementIdInfo[1]).css("display", "none");
    $("#msm_def_type_dropdown-"+elementIdInfo[1]).removeAttr("disabled");
    $("#msm_def_title_input-"+elementIdInfo[1]).removeAttr("disabled");
    $("#msm_def_description_input-"+elementIdInfo[1]).removeAttr("disabled");
    $("#msm_associate_button-"+elementIdInfo[1]).removeAttr("disabled");
    
    var defcontent = unitInfo["content"];
    
    var currentId = $("#"+element).children(".msm_editor_content").first().attr("id");
    
    var defInfo = currentId.split("-");
                
    var defTextArea = document.createElement("textarea");
    defTextArea.id = "msm_def_content_input-"+defInfo[1];
    defTextArea.name = "msm_def_content_input-"+defInfo[1];
    defTextArea.className = "msm_unit_child_content";
            
    $(defTextArea).val(defcontent);   
    
    $("#"+currentId).replaceWith(defTextArea);   
    initEditor(defTextArea.id); 
    $("#"+element).unbind();
    createAssociateText(element, unitInfo);
    
    enableEditorFunction();
   
}

/**
 * This method creates the "editable" intro with all the input and dropdown
 * menus and tinyMCE editors enabled and disables the overlay.
 *
 * @param {string} element        HTML ID of the container with overlay that the edit button was triggere from
 * @param {array} unitInfo        instance of the EditorIntro class that was passed by JSON
 */
function createIntroText(element, unitInfo)
{
    var elementIdInfo = element.split("-");
    $("#msm_element_overlay-"+elementIdInfo[1]).css("display", "none");
    $("#msm_intro_title_input-"+elementIdInfo[1]).removeAttr("disabled");
        
    $("#"+element).find(".msm_intro_child_titles").each(function() {
        $(this).removeAttr("disabled");
    });
    
    $("#msm_intro_child_button-"+elementIdInfo[1]).removeAttr("disabled");
    
    var introcontent = '';
               
    if(unitInfo["blocks"] == '')
    {
        for(var index=0; index < unitInfo["children"].length; index++)
        {
            introcontent += unitInfo["children"][index]["content"];
        }
    }
    else
    {       
        for(var index=0; index < unitInfo["blocks"][0]["content"].length; index++)
        {
            introcontent += unitInfo["blocks"][0]["content"][index]["content"];
        }
    }
    
    var currentId = $("#"+element).children(".msm_editor_content").first().attr("id");
    
    var introInfo = currentId.split("-");
                
    var introTextArea = document.createElement("textarea");
    introTextArea.id = "msm_intro_content_input-"+introInfo[1];
    introTextArea.name = "msm_intro_content_input-"+introInfo[1];
    introTextArea.className = "msm_unit_child_content";
            
    $(introTextArea).val(introcontent);  
    
    $("#"+currentId).replaceWith(introTextArea);    
        
    initEditor(introTextArea.id);     
               
    // intro has children
    for(var j = 1; j < unitInfo["blocks"].length; j++)
    {
        var introchildcontent = '';

        for(var k=0; k < unitInfo["blocks"][j]["content"].length; k++)
        {
            introchildcontent += unitInfo["blocks"][j]["content"][k]["content"];
        }        
        
        var childArray = $("#msm_intro_child_container").children(".msm_intro_child");
        
        var childidInfo = childArray[j-1].id.split("-"); 
                
        var introChildTextArea = document.createElement("textarea");
        introChildTextArea.id = "msm_intro_child_content-"+childidInfo[1];
        introChildTextArea.name = "msm_intro_child_content-"+childidInfo[1];
        introChildTextArea.className = "msm_intro_child_contents";                
          
        $(introChildTextArea).val(introchildcontent);
        
        $("#msm_intro_child_content-"+childidInfo[1]).replaceWith(introChildTextArea); 
        
        initEditor(introChildTextArea.id);  
    }                   
    $("#"+element).unbind();
    enableEditorFunction();
}

/**
 * This method creates the "editable" unit body with all the input and dropdown
 * menus and tinyMCE editors enabled and disables the overlay.
 *
 * @param {string} element        HTML ID of the container with overlay that the edit button was triggere from
 * @param {array} unitInfo        instance of the EditorBlock class that was passed by JSON
 */
function createBodyText(element, unitInfo)
{
    var elementIdInfo = element.split("-");
    
    $("#msm_body_title_input-"+elementIdInfo[1]).removeAttr("disabled");
    $("#msm_element_overlay-"+elementIdInfo[1]).css("display", "none");
    
    var bodycontent = '';
    for(var index=0; index < unitInfo["content"].length; index++)
    {
        bodycontent += unitInfo["content"][index]["content"];
    }
    
    var currentId = $("#"+element).children(".msm_editor_content").first().attr("id");
    var bodyInfo = currentId.split("-");
                
    var bodyTextArea = document.createElement("textarea");
    bodyTextArea.id = "msm_body_content_input-"+bodyInfo[1];
    bodyTextArea.name = "msm_body_content_input-"+bodyInfo[1];
    bodyTextArea.className = "msm_unit_child_content";
            
    $(bodyTextArea).val(bodycontent);
    
    $("#"+currentId).replaceWith(bodyTextArea);    
        
    initEditor(bodyTextArea.id); 
    $("#"+element).unbind();
    enableEditorFunction();    
}

/**
 * This method creates the "editable" extra information with all the input and dropdown
 * menus and tinyMCE editors enabled and disables the overlay.
 *
 * @param {string} element        HTML ID of the container with overlay that the edit button was triggere from
 * @param {array} unitInfo        instance of the EditorExtrInfo class that was passed by JSON
 */
function createExtraInfoText(element, unitInfo)
{
    var elementIdInfo = element.split("-");
    
    $("#msm_extra_type_dropdown-"+elementIdInfo[1]).removeAttr("disabled");
    $("#msm_extra_title_input-"+elementIdInfo[1]).removeAttr("disabled");
    $("#msm_element_overlay-"+elementIdInfo[1]).css("display", "none");
    
    var extracontent = '';
    for(var index=0; index < unitInfo["blocks"][0]["content"].length; index++)
    {
        extracontent += unitInfo["blocks"][0]["content"][index]["content"];
    }
    
    var currentId = $("#"+element).children(".msm_editor_content").first().attr("id");
    var extraInfo = currentId.split("-");
                
    var extraTextArea = document.createElement("textarea");
    extraTextArea.id = "msm_extra_content_input-"+extraInfo[1];
    extraTextArea.name = "msm_extra_content_input-"+extraInfo[1];
    extraTextArea.className = "msm_unit_child_content";
            
    $(extraTextArea).val(extracontent);
    
    $("#"+currentId).replaceWith(extraTextArea);    
        
    initEditor(extraTextArea.id); 
    $("#"+element).unbind();
    enableEditorFunction();    
}

/**
 * This method is triggered by "Remove this Unit" button after user saves the content.  It removes all data related to the
 * specified unit but if any component of the unit is used as a reference material or if it contains reference material that
 * is part of a content in another unit, it only removes the structural data in msm_compositor but does not remove the orignial data
 * in its respetive database table.
 * 
 * @param {eventObject} e             event object triggered from click to "Remove this Unit" button
 */
function removeUnit(e)
{
    e.preventDefault();
    
    $("<div class='dialogs' id='msm_removeUnit'> <span class='ui-icon ui-icon-alert' style='float: left; margin: 0 7px 20px 0;'></span>Are you sure that you would like to remove this unit? </div>").appendTo('#msm_editor_middle');
    $( "#msm_removeUnit" ).dialog({
        resizable: false,
        height:180,
        modal: true,
        buttons: {
            "Yes": function() {
                var currentUnitIdPair = $("#msm_currentUnit_id").val();
                var param = {
                    removeUnit:currentUnitIdPair
                };
    
                var ids = [];
    
                $.ajax({
                    type: 'POST',
                    url:"editorCreation/msmUnitForm.php",
                    data: param,
                    success: function(data)
                    {
                        ids = JSON.parse(data);
            
                        // show root Unit
                        processUnitData(ids); 
                        $("#msm_currentUnit_id").val(currentUnitIdPair);
            
                        $("#msm_unit_tree").find("li").each(function() {           
                            var stringid = "msm_unit-"+currentUnitIdPair;
                            var parent = $(this);
                            var match = this.id.match(/msm_unit-.+/);
                            var currentId = '';
                            if(!match)
                            {
                                currentId = "msm_unit-"+this.id;
                            }
                            else
                            {
                                currentId = this.id;
                            }
                            if(currentId == stringid)
                            {
                                $(this).children("ul").children("li").each(function() {
                                    $(this).insertBefore(parent);
                                });               
                                $(this).empty().remove();
                            }
                        });
            
                        var liElements = $("#msm_unit_tree").find("li");
            
                        if(liElements.length == 0)
                        {
                            $("#msm_editor_new").attr("disabled", "disabled");
                            $("#msm_comp_done").attr("disabled", "disabled");
                        }
            
                        newUnit();
            
                        MathJax.Hub.Queue(["Typeset",MathJax.Hub]); 
            
                    },
                    error: function(data)
                    {
                        alert("ajax error in loading unit");
                    }
                });
                $(this).dialog("close");
            },
            "No": function() {
                $(this).dialog("close");
            }
        }
    });   
}

/**
 * This method is triggered by the "Cancel" button during the edit mode before the save has been triggered.  Basically the rol of this ethod is to popup 
 * a warning message about losing unsaved content.  If user decides not to go with cancel, then it just closes the popup window but if the 
 * user agrees to go with cancel action, then it changes the editor content to ones in the database to erases all the changes.
 * 
 * @param {eventObject} e               event object triggered by click to "Cancel" button during edit process
 */
function cancelUnit(e)
{
    e.preventDefault();
    
    $("<div class='dialogs' id='msm_cancelUnit'> <span class='ui-icon ui-icon-alert' style='float: left; margin: 0 7px 20px 0;'></span>Are you sure that you would like cancel all the changes made? </div>").appendTo('#msm_editor_middle');
    $( "#msm_cancelUnit" ).dialog({
        resizable: false,
        height:180,
        modal: true,
        buttons: {
            "Yes": function() {
                var currentUnitIdPair = $("#msm_currentUnit_id").val();
    
                var param = {
                    cancelUnit:currentUnitIdPair
                };
    
                var htmlstring = '';
    
                // AJAX call to retrieve the unchanged data from the database
                $.ajax({
                    type: 'POST',
                    url:"editorCreation/msmLoadUnit.php",
                    data: param,
                    success: function(data)
                    {
                        htmlstring = JSON.parse(data);
            
                        $("#msm_unit_form").empty();
            
                        $("#msm_unit_form").html(htmlstring);
            
                        var currentUnitId = document.createElement("input");
                        currentUnitId.id = "msm_currentUnit_id";
                        currentUnitId.name = "msm_currentUnit_id";
                        currentUnitId.setAttribute("style", "display:none;");
                        currentUnitId.value = currentUnitIdPair;
       
                        $("#msm_unit_form").append($(currentUnitId));
                        disableEditorFunction();   
                        
                        $(".msm_structural_element").draggable({
                            appendTo: "msm_editor_middle_droparea",
                            containment: "msm_editor_middle_droparea",
                            scroll: true,
                            cursor: "move",
                            helper: "clone"                   
                        });    
                        
                        $(".msm_child_element").draggable({
                            appendTo: "msm_editor_middle_droparea",
                            containment: "msm_editor_middle_droparea",
                            scroll: true,
                            cursor: "move",
                            helper: "clone"
                        }); 
        
                        $("#msm_editor_middle_droparea").droppable({
                            accept: "#msm_component_tabs-1 > div",
                            hoverClass: "ui-state-hover",
                            tolerance: "pointer",
                            drop: function( event, ui ) { 
                                processDroppedChild(event, ui.draggable.context.id);
                                allowDragnDrop();        
                            }
                        });
                        $(".msm_dnd_containers").droppable({
                            accept: "#msm_component_tabs-2 > div",
                            hoverClass: "ui-state-hover",
                            tolerance: "pointer",
                            drop: function( event, ui ) { 
                                processAdditionalChild(event, ui.draggable.context.id);      
                                allowDragnDrop();  
                            }
                        });
                    
                        $("#msm_unit_title").dblclick(function(){
                            processMathContent(this.id);
                            initTitleEditor(this.id, "80%");
                            allowDragnDrop();
                        });
                        $("#msm_unit_short_title").dblclick(function(){
                            $(this).removeAttr("readonly");
                            $(this).addClass("msm_add_border");
                            allowDragnDrop();
                        });
                        $("#msm_unit_description_input").dblclick(function(){
                            $(this).removeAttr("readonly");
                            $(this).addClass("msm_add_border");
                            allowDragnDrop();
                        });
           
                        $(".msm_editor_buttons").remove();
                        $("<button class=\"msm_editor_buttons\" id=\"msm_editor_new\" type=\"button\" onclick=\"newUnit()\"> New Unit </button>").appendTo("#msm_editor_middle");        
                        $("<button class=\"msm_editor_buttons\" id=\"msm_editor_remove\" type=\"button\" onclick=\"removeUnit(event)\"> Remove this Unit </button>").appendTo("#msm_editor_middle");
                    },
                    error: function(data)
                    {
                        alert("ajax error in loading unit");
                    }
                });
                $(this).dialog("close");
            },
            "No": function() {
                $(this).dialog("close");
            }
        }
    });  
}

/**
 * This method is called everytime a "Full Screen" or "Basic Window" is triggered
 * to toggle between two buttons and to toggle between full screeen and basic window modes.
 * 
 * @param {eventObject} e             click event object
 */
function swapButtons(e) {
    var target = e.target.id;
    
    var basicWindow = null;
    var text = null;
    var fullScreen = null;
    
    if(target == "msm_comp_fullscreen")
    {
        basicWindow = document.createElement("button");
        basicWindow.id = "msm_comp_basicwindow";
        text = document.createTextNode("Basic Window");
            
        basicWindow.appendChild(text);
            
        $(basicWindow).insertBefore($("#msm_comp_fullscreen"));
        $("#msm_comp_fullscreen").remove();
            
    }
    else
    {
        fullScreen = document.createElement("button");
        fullScreen.id = "msm_comp_fullscreen";
        text = document.createTextNode("Full Screen");
            
        fullScreen.appendChild(text);
            
        $(fullScreen).insertBefore($("#msm_comp_basicwindow"));
        $("#msm_comp_basicwindow").remove();
    }
    
    // when full screen is triggered, remove all blocks and other moodle menus
    $("#msm_comp_fullscreen").click(function(event) {
        $("#page-header").css("display", "none");
        $(".block").addClass("dock_on_load");
        $("#region-main").addClass("nomargin"); 
        $("#msm_editor_container").trigger("spliter.resize");
        $("#msm_editor_middleright").trigger("spliter.resize");
        swapButtons(event);
    });
            
    // when basic window button is triggered, undo what was done above
    $("#msm_comp_basicwindow").click(function(event) {
        $("#page-header").css("display", "block");
        $(".block").removeClass("dock_on_load");
        $("#region-main").removeClass("nomargin");   
        $("#msm_editor_container").trigger("spliter.resize");
        $("#msm_editor_middleright").trigger("spliter.resize");
        swapButtons(event);
    });     
}

/**
 * This method is triggered by the "Delete" button on the overlay of each core elements. 
 * It removes the core element with the overlay with the triggered button and all its child elements.
 *
 * @param {eventObject} e             click event object
 */
function deleteOverlayElement(e)
{
    var currentElement = e.target.parentElement.parentElement.id;
        
    $("#"+currentElement+" textarea").each(function() {
        if(tinymce.getInstanceById($(this).attr("id")) != null)
        {
            tinymce.execCommand('mceFocus', false, $(this).attr("id")); 
            tinymce.execCommand('mceRemoveControl', true, $(this).attr("id"));
        }
    });
        
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
                $(".msm_editor_buttons").remove();
                $("<input type='submit' class='msm_editor_buttons' id='msm_editor_save' value='Save'/>").appendTo("#msm_editor_middle");          
                $('<button class="msm_editor_buttons" id="msm_editor_cancel" onclick="cancelUnit(event)"> Cancel </button>').appendTo("#msm_editor_middle");
                
                $("#msm_editor_save").unbind("click");
                $("#msm_editor_save").click(function(event) { 
                    //         prevents navigation to msmUnitForm.php
                    event.preventDefault();
                    // enabling all input that was disabled to submit the form
                    $("#msm_unit_short_title").removeAttr("readonly");
                    $("#msm_unit_description_input").removeAttr("readonly");
                    $(".copied_msm_structural_element select").removeAttr("disabled");
                    $(".copied_msm_structural_element input").removeAttr("disabled");
                    $("#msm_child_appending_area").find(".msm_editor_content").each(function() {
                        $(this).removeClass("msm_editor_content");
                        var newdata = document.createElement("textarea");
                        newdata.id = this.id;
                        newdata.name = this.id;
                        newdata.className = this.className;
                    
                        $(this).find("span.matheditor").each(function() {
                            var newspan = document.createElement("span");
                            newspan.className = "matheditor";
    
                            var scriptChild = $(this).find("script");
        
                            var scriptWithMath = scriptChild[scriptChild.length-1];
                            var mathContent = "\\("+$(scriptWithMath).text()+"\\)"; 
                            $(newspan).append(mathContent);
                
                            $(this).replaceWith(newspan);
                        });
            
                        newdata.value = $(this).html();            
                        $(this).replaceWith(newdata);               
                    });
                    submitForm();            
                });  
            },
            "No": function() {
                $("#"+currentElement+" textarea").each(function() {
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
                $(this).dialog("close");                   
            }
        }
    });   
}

/**
 * This method is called by double click trigger in the title/description inputs in unit and it changes the 
 * buttons from New Unit/Remove this Unit to Save/Cancel buttons and also has code to retrieve data when the
 * save is triggered.
 * 
 */
function allowDragnDrop()
{
    var buttonPresent = $("#msm_editor_middle").find("#msm_editor_new");
            
    if(buttonPresent.length > 0)
    {
        $(".msm_editor_buttons").remove();
        $('<button class="msm_editor_buttons" id="msm_editor_cancel" onclick="cancelUnit(event)"> Cancel </button>').appendTo("#msm_editor_middle");
        $("<input type=\"submit\" name=\"msm_editor_save\" class=\"msm_editor_buttons\" id=\"msm_editor_save\" value=\"Save\"/>").appendTo("#msm_editor_middle");
                        
        $("#msm_editor_save").unbind("click");
        $("#msm_editor_save").click(function(event) { 
            //         prevents navigation to msmUnitForm.php
            event.preventDefault();
            
            $("#msm_unit_short_title").removeAttr("readonly");
            $("#msm_unit_description_input").removeAttr("readonly");
            $(".copied_msm_structural_element select").removeAttr("disabled");
            $(".copied_msm_structural_element input").removeAttr("disabled");
            
            if(typeof tinymce.getInstanceById("msm_unit_title") === "undefined")
            {
                var titlecontent = $("div#msm_unit_title").html();
                var inputTitle = $('<input class="msm_title_input" id="msm_unit_title" name="msm_unit_title" onkeypress="validateBorder()"/>');                    
                $(inputTitle).val(titlecontent);                    
                $("div#msm_unit_title").replaceWith(inputTitle);                       
            }
            else
            {
                tinymce.execCommand('mceFocus', false, "msm_unit_title"); 
                tinymce.execCommand('mceRemoveControl', true, "msm_unit_title");
            }
            initTitleEditor("msm_unit_title", "80%"); 
            
            $("#msm_child_appending_area").find(".msm_editor_content").each(function() {
                $(this).removeClass("msm_editor_content");
                var newdata = document.createElement("textarea");
                newdata.id = this.id;
                newdata.name = this.id;
                newdata.className = this.className;
                    
                $(this).find("span.matheditor").each(function() {
                    var newspan = document.createElement("span");
                    newspan.className = "matheditor";
    
                    var scriptChild = $(this).find("script");
        
                    var scriptWithMath = scriptChild[scriptChild.length-1];
                    var mathContent = "\\("+$(scriptWithMath).text()+"\\)"; 
                    $(newspan).append(mathContent);
                
                    $(this).replaceWith(newspan);
                });
            
                newdata.value = $(this).html();            
                $(this).replaceWith(newdata);               
            });
            
            submitForm();
            
        });  
    }      
}

/**
 * when the tinymce is activated again to edit already existing unit title, need to remove mathjax code
 * and only get the needed math content wrapped in <span class="matheditor">mathcontent</span> to allow
 * tinymce to recognize the math element.
 * 
 * @param {string} id                 HTML ID of the title element that the math is being processed from.
 */ 
function processMathContent(id)
{    
    $("#"+id).find("span.matheditor").each(function() {
        var newspan = document.createElement("span");
        newspan.className = "matheditor";
    
        var scriptChild = $(this).find("script");
        
        // if user triggers any edit function while the mathjax is loading, there will be no
        // script tag due to it not being converted by mathjax yet
        if(scriptChild.length != 0)
        {
            var scriptWithMath = scriptChild[scriptChild.length-1];
            var mathContent = "\\("+$(scriptWithMath).text()+"\\)"; 
            $(newspan).append(mathContent);
            $(this).replaceWith(newspan);
        }
        
    });
}