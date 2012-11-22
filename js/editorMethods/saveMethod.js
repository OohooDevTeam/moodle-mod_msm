/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(function(){
    $(".msm_form_error").hide();
    $("#msm_editor_save").click(function() {        
       var editorChildren = $("#msm_child_appending_area").children("div");
       console.log(editorChildren);
    });
});
