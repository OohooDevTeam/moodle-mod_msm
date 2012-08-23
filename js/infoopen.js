
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


/* 
 * This javascript function is similar to the one in popup.js but this code does not 
 * deal with dialog box when the buttons are clicked.  It is to display the related
 * reference materials to the right side of the panel instead of sticking the dialog box 
 * to the frame.
 */
var x = 0; // stores the x-axis position of the mouse
var y = 0; // stores the y-axis position of the mouse

/* variable i is passed by each time the defminibutton/minibutton in definition.php/theorem.php has a mouse hovering over and 
 * it tracks the unique ID number of the buttons to call the correct dialog windows.
 */

function infoopen(i) {
    $('#defminibutton-'+i).unbind('click');
    $('#defminibutton-'+i).click(function() {
        $('.rightbox').empty();
        $('.rightbox').append($('#refcontent-'+i));    
        $('#refcontent-'+i).css('display', 'block');
//        $('#refcontent-'+i).toggleClass('refcontent', 'shownrefcontent');
    });
    
    $('#defminibutton-'+i).ready(function(e){
        $('#dialog-'+i).dialog('open');
        $('#defminibutton-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.pageX+5, e.pageY+5]
            });
        });
         
        $('#defminibutton-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });
   
    });
        
    $('#minibutton-'+i).unbind('click');
    $('#minibutton-'+i).click(function() {
        $('.rightbox').empty();
        $('.rightbox').append($('#refcontent-'+i));    
        $('#refcontent-'+i).css('display', 'block');
//        $('#refcontent-'+i).toggleClass('refcontent', 'shownrefcontent');
    });
    
    $('#minibutton-'+i).ready(function(e){
        $('#dialog-'+i).dialog('open');
        $('#minibutton-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.pageX+5, e.pageY+5]
            });
        });
         
        $('#minibutton-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });
   
    });
      
    $('#hottag-'+i).ready(function(e){
        $('#dialog-'+i).dialog('open');
        $('#hottag-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.pageX+5, e.pageY+5]
            });
        });
         
        $('#hottag-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });
   
    });
    
    $('#commentminibutton-'+i).unbind('click');
    $('#commentminibutton-'+i).click(function() {
        $('.rightbox').empty();
        $('.rightbox').append($('#refcontent-'+i));    
        $('#refcontent-'+i).css('display', 'block');
//        $('#refcontent-'+i).toggleClass('refcontent', 'shownrefcontent');
    });
    
    $('#commentminibutton-'+i).ready(function(e){
        $('#dialog-'+i).dialog('open');
        $('#commentminibutton-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.pageX+5, e.pageY+5]
            });
        });
         
        $('#commentminibutton-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });
   
    });
 
}


 


