/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('subordinate');

    tinymce.create('tinymce.plugins.SubordinatePlugin', {
        /**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
        init : function(ed, url) {
            // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceSubordinate');
                       
            //            ed.addCommand('mceSubordinate', function() {   
            //                $('#test_tinymce').html(url+'/subordinate.htm').dialog({
            //                    // disabling the close button 
            //                    open: function(event, ui) {
            //                        alert("open dialog");
            //                        $(".ui-dialog-titlebar-close").hide();
            //                        $("#msm_subordinate_highlighted").val(ed.selection.getContent({
            //                            format : 'text'
            //                        }));
            //                    },
            //                    modal:true,
            //                    autoOpen: false,
            //                    height: 500,
            //                    width: 750,
            //                    closeOnEscape: false
            //                });
            //                $('#msm_subordinate').dialog('open').css('display', 'block');
            //            });  


            // Add a node change handler, when no content is selected, the button is disabled,
            // otherwise, the button is enabled.
            ed.onNodeChange.add(function(ed, cm, n) {
                if(ed.selection.getContent())
                {
                    cm.setDisabled('subordinate', false);  
                }
                else
                {                 
                    cm.setActive('subordinate', false); 
                    cm.setDisabled('subordinate', true);  
                }
            });
            
            // Register subordinate button
            ed.addButton('subordinate', {
                title : 'subordinate.desc',
                cmd : 'mceSubordinate',
                image : url + '/img/subordinate.png',
                onclick: function() {
                    var idNumber = ed.editorId.split("-");
                    
                    if(idNumber > 2)
                    {
                        makeSubordinateDialog(idNumber[1], idNumber[2]);
                         
                        init(ed.selection.getContent(), idNumber[1], idNumber[2]);
                        $('#msm_subordinate_container-'+idNumber[1]+'-'+idNumber[2]).dialog({
                            open: function(event, ui) {                               
                                $(".ui-dialog-titlebar-close").hide();  //  disabling the close button 
                                $("#msm_subordinate_highlighted-"+idNumber[1]+"-"+idNumber[2]).val(ed.selection.getContent({
                                    format : 'text'
                                }));
                            },
                            modal:true,
                            autoOpen: false,
                            height: 500,
                            width: 750,
                            closeOnEscape: false
                        });
                        $('#msm_subordinate_container-'+idNumber[1]+'-'+idNumber[2]).dialog('open').css('display', 'block');
                    }
                    else
                    {
                        makeSubordinateDialog(idNumber[1], '');
                         
                        init(ed.selection.getContent(), idNumber[1], '');
                        $('#msm_subordinate_container-'+idNumber[1]).dialog({
                            //                     disabling the close button 
                            open: function(event, ui) {
                                $(".ui-dialog-titlebar-close").hide();
                                $("#msm_subordinate_highlighted-"+idNumber[1]).val(ed.selection.getContent({
                                    format : 'text'
                                }));
                            },
                            modal:true,
                            autoOpen: false,
                            height: 500,
                            width: 750,
                            closeOnEscape: false
                        });
                        $('#msm_subordinate_container-'+idNumber[1]).dialog('open').css('display', 'block');
                    }
                    
                }
            });
        },

        /**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
        createControl : function(n, cm) {
            return null;
        },

        /**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
        getInfo : function() {
            return {
                longname : 'Subordinate plugin',
                author : 'Ga Young Kim',
                authorurl : 'http://ualberta.ca ',
                infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/subordinate',
                version : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('subordinate', tinymce.plugins.SubordinatePlugin);
})();

function makeSubordinateDialog(idNumber1, idNumber2)
{
    var container;
    var dialogwhole = document.createElement('div');
    var dialogForm = document.createElement('form');
    var dialogFormContainer = document.createElement('div');
    var selectedTextlabel = document.createElement('label');
    var selectedTextValue = document.createTextNode('Selected Text : ');
    var selectedTextInput = document.createElement('input');
    
    var selectTypeLabel = document.createElement('label');
    var selectTypeText = document.createTextNode('Subordinate Type : ');
    var selectTypeMenu = document.createElement('select');
    
    var selectTypeOption0 = document.createElement('option');
    selectTypeOption0.setAttribute("value", "None");
    var selectTypeOption0Value = document.createTextNode('None');
    var selectTypeOption1 = document.createElement('option');
    selectTypeOption1.setAttribute("value", "Information");
    var selectTypeOption1Value = document.createTextNode('Information');
    var selectTypeOption2 = document.createElement('option');
    selectTypeOption2.setAttribute("value", "External Link");
    var selectTypeOption2Value = document.createTextNode('External Link');
    var selectTypeOption3 = document.createElement('option');
    selectTypeOption3.setAttribute("value", "Internal Reference");
    var selectTypeOption3Value = document.createTextNode('Internal Reference');
    var selectTypeOption4 = document.createElement('option');
    selectTypeOption4.setAttribute("value", "External Reference");
    var selectTypeOption4Value = document.createTextNode('External Reference');
    
    selectTypeOption0.appendChild(selectTypeOption0Value);
    selectTypeOption1.appendChild(selectTypeOption1Value);
    selectTypeOption2.appendChild(selectTypeOption2Value);
    selectTypeOption3.appendChild(selectTypeOption3Value);
    selectTypeOption4.appendChild(selectTypeOption4Value);
    
    var dialogContentForm = document.createElement('div');
    var dialogButtonContainer = document.createElement('div');
    var saveButton = document.createElement('input');
    var cancelButton = document.createElement('input');  
    
    if(idNumber2 != '')
    {
        container = document.getElementById('msm_subordinate_container-'+idNumber1+'-'+idNumber2);        
        dialogwhole.id = 'msm_subordinate-'+idNumber1+'-'+idNumber2;
        container.setAttribute("title", "Create Subordinate");
        
        dialogForm.id = 'msm_subordinate_form-'+idNumber1+'-'+idNumber2;
        
        dialogFormContainer.className = "msm_subordinate_form_container";
        
        selectedTextlabel.setAttribute("for",'msm_subordinate_highlighted-'+idNumber1+'-'+idNumber2);
        selectedTextlabel.appendChild(selectedTextValue);
        
        selectedTextInput.id = 'msm_subordinate_highlighted-'+idNumber1+'-'+idNumber2;
        selectedTextInput.name = 'msm_subordinate_highlighted-'+idNumber1+'-'+idNumber2;
        selectedTextInput.setAttribute("disabled", "disabled");
        
        selectTypeLabel.setAttribute("for", 'msm_subordinate_select-'+idNumber1+'-'+idNumber2);
        selectTypeLabel.appendChild(selectTypeText);
        
        selectTypeMenu.id = 'msm_subordinate_select-'+idNumber1+'-'+idNumber2;
        selectTypeMenu.name = 'msm_subordinate_select-'+idNumber1+'-'+idNumber2;
        selectTypeMenu.onchange = function() {
            changeForm(event, idNumber1, idNumber2);
        };
        
        dialogContentForm.id = 'msm_subordinate_content_form_container-'+idNumber1+'-'+idNumber2;
        dialogContentForm.className = "msm_subordinate_content_form_containers";
        
        dialogButtonContainer.className = 'msm_subordinate_button_container';
        
        saveButton.setAttribute("type", "submit");
        saveButton.id = 'msm_subordinate_submit-'+idNumber1+'-'+idNumber2;
        saveButton.className = 'msm_subordinate_button';
        saveButton.setAttribute("value", "Save");
        
        cancelButton.setAttribute("type", "button");
        cancelButton.id = 'msm_subordinate_cancel-'+idNumber1+'-'+idNumber2;
        cancelButton.className = 'msm_subordinate_button';
        cancelButton.setAttribute("value", "Cancel");
        cancelButton.onclick = function() {
            closeSubFormDialog(idNumber1, idNumber2);
        };
        
       
        
    }
    else
    {
        container = document.getElementById('msm_subordinate_container-'+idNumber1);        
        dialogwhole.id = 'msm_subordinate-'+idNumber1;
        container.setAttribute("title", "Create Subordinate");
        
        dialogForm.id = 'msm_subordinate_form-'+idNumber1;
        
        dialogFormContainer.className = "msm_subordinate_form_container";
        
        selectedTextlabel.setAttribute("for",'msm_subordinate_highlighted-'+idNumber1);
        selectedTextlabel.appendChild(selectedTextValue);
        
        selectedTextInput.id = 'msm_subordinate_highlighted-'+idNumber1;
        selectedTextInput.name = 'msm_subordinate_highlighted-'+idNumber1;
        selectedTextInput.setAttribute("disabled", "disabled");
        
        selectTypeLabel.setAttribute("for", 'msm_subordinate_select-'+idNumber1);
        selectTypeLabel.appendChild(selectTypeText);
        
        selectTypeMenu.id = 'msm_subordinate_select-'+idNumber1;
        selectTypeMenu.name = 'msm_subordinate_select-'+idNumber1;
        selectTypeMenu.onchange = function() {
            changeForm(event, idNumber1, '');
        };
        
        dialogContentForm.id = 'msm_subordinate_content_form_container-'+idNumber1;
        dialogContentForm.className = "msm_subordinate_content_form_containers";
        
        dialogButtonContainer.className = 'msm_subordinate_button_container';
        
        saveButton.setAttribute("type", "submit");
        saveButton.id = 'msm_subordinate_submit-'+idNumber1;
        saveButton.className = 'msm_subordinate_button';
        saveButton.setAttribute("value", "Save");
        
        cancelButton.setAttribute("type", "button");
        cancelButton.id = 'msm_subordinate_cancel-'+idNumber1;
        cancelButton.className = 'msm_subordinate_button';
        cancelButton.setAttribute("value", "Cancel");
        cancelButton.onclick = function() {
            closeSubFormDialog(idNumber1, '');
        };
    }
    
    selectTypeMenu.appendChild(selectTypeOption0);
    selectTypeMenu.appendChild(selectTypeOption1);
    selectTypeMenu.appendChild(selectTypeOption2);
    selectTypeMenu.appendChild(selectTypeOption3);
    selectTypeMenu.appendChild(selectTypeOption4);
        
    dialogButtonContainer.appendChild(saveButton);
    dialogButtonContainer.appendChild(cancelButton);
        
    dialogFormContainer.appendChild(selectedTextlabel);
    dialogFormContainer.appendChild(selectedTextInput);
    dialogFormContainer.appendChild(document.createElement('br'));
    dialogFormContainer.appendChild(document.createElement('br'));
    dialogFormContainer.appendChild(selectTypeLabel);
    dialogFormContainer.appendChild(selectTypeMenu);
    dialogFormContainer.appendChild(dialogContentForm);
        
    dialogForm.appendChild(dialogFormContainer);
    dialogForm.appendChild(document.createElement('br'));
    dialogForm.appendChild(document.createElement('br'));
    dialogForm.appendChild(dialogButtonContainer);
        
    dialogwhole.appendChild(dialogForm);
   
    // only append the new dialog form to div when it hasn't already been done
    if(!container.hasChildNodes())
    {
        container.appendChild(dialogwhole);
    }
    
}