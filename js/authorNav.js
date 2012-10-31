/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function openNavDialog()
{
    $('#msm_nav_setting').ready(function() {
        $('#msm_setting_dialog').dialog({
            modal:true,
            autoOpen: false,
            height: 500,
            width: 605
        });
        $('#msm_setting_dialog').dialog('open').css('display', 'block');
    //        $('#msm_setting_type').tabs().css('display', 'block');
    });
}

function addChildUnit()
{
    $('#msm_child_add').ready(function () {
        $('<span class="msm_structure_names">Child Unit :  </span>').insertBefore('#msm_child_add');
        $('<input class="msm_structure_input" name="msm_top" placeholder=" Please specify the name of the child element of this composition."/><br />').insertBefore('#msm_child_add');
    });
}

function closeSetting()
{
    $('#msm_setting_cancel').ready(function() {
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