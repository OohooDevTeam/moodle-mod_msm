
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
    $('#defminibutton-'+i).click(
        function() {
            $('.rightbox').empty();
            var cloned = $('#refcontent-'+i).clone();
            cloned.find('*').each(function(){
                var currentid = $(this).attr('id');
                if(typeof currentid != 'undefined')
                {
                    $(this).attr('id', 'copy'+currentid);
                }
            });
            cloned.appendTo($('.rightbox')).css('display', 'block');
            MathJax.Hub.Queue(["Typeset",MathJax.Hub]); 
//            $('img').mapster({
//                fillColor: 'ff0000',
//                fillOpacity: 0.5
//            }); 
            
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
        var cloned = $('#refcontent-'+i).clone();
        cloned.find('*').each(function(){
            var currentid = $(this).attr('id');
            if(typeof currentid != 'undefined')
            {
                $(this).attr('id', 'copy'+currentid);
            }
        });
        cloned.appendTo($('.rightbox')).css('display', 'block');
        MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
//        $('img').mapster({
//            fillColor: 'ff0000',
//            fillOpacity: 0.5
//        }); 
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
    
    $('#hottag-'+i).unbind('click');
    $('#hottag-'+i).click(
        function() {
            $('.rightbox').empty();
            var cloned = $('#refcontent-'+i).clone();
            cloned.find('*').each(function(){
                var currentid = $(this).attr('id');
                if(typeof currentid != 'undefined')
                {
                    $(this).attr('id', 'copy'+currentid);
                }
               
            });
            cloned.appendTo($('.rightbox')).css('display', 'block');
            MathJax.Hub.Queue(["Typeset",MathJax.Hub]);          
            
//            $('img').mapster({
//                fillColor: 'ff0000',
//                fillOpacity: 0.5
//            }); 
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
        var cloned = $('#refcontent-'+i).clone();
        cloned.find('*').each(function(){
            var currentid = $(this).attr('id');
            if(typeof currentid != 'undefined')
            {
                $(this).attr('id', 'copy'+currentid);
            }
        });
        cloned.appendTo($('.rightbox')).css('display', 'block');
        MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
//        $('img').mapster({
//            fillColor: 'ff0000',
//            fillOpacity: 0.5
//        }); 
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
 
    $('#copydefminibutton-'+i).click(function(e) {
        x = e.pageX+5;
        y = e.pageY+5;

        $('#dialog'+i).dialog('open');
        $('#copydefminibutton-'+i).mousemove(function () {
            $('#dialog-'+i).dialog('option', {
                position: [x, y]
            });
        });
     
        $('#copydefminibutton-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('open');
        });
    
    });
                
    $('#copydefminibutton-'+i).ready(function(e){
        $('#dialog-'+i).dialog('open');
        $('#copydefminibutton-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.pageX+5, e.pageY+5]
            });
        });
         
        $('#copydefminibutton-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });
   
    });
    
    $('#copyminibutton-'+i).click(function(e) {
        x = e.pageX+5;
        y = e.pageY+5;

        $('#dialog'+i).dialog('open');
        $('#copyminibutton-'+i).mousemove(function () {
            $('#dialog-'+i).dialog('option', {
                position: [x, y]
            });
        });
     
        $('#copyminibutton-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('open');
        });
    
    });
                
    $('#copyminibutton-'+i).ready(function(e){
        $('#dialog-'+i).dialog('open');
        $('#copyminibutton-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.pageX+5, e.pageY+5]
            });
        });
         
        $('#copyminibutton-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });
   
    });
    
    $('#copyhottag-'+i).click(function(e) {
        x = e.pageX+5;
        y = e.pageY+5;

        $('#dialog'+i).dialog('open');
        $('#copyhottag-'+i).mousemove(function () {
            $('#dialog-'+i).dialog('option', {
                position: [x, y]
            });
        });
     
        $('#copyhottag-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('open');
        });
    
    });
                
    $('#copyhottag-'+i).ready(function(e){
        $('#dialog-'+i).dialog('open');
        $('#copyhottag-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.pageX+5, e.pageY+5]
            });
        });
         
        $('#copyhottag-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });
   
    });
    
    $('#copycommentminibutton-'+i).click(function(e) {
        x = e.pageX+5;
        y = e.pageY+5;

        $('#dialog'+i).dialog('open');
        $('#copycommentminibutton-'+i).mousemove(function () {
            $('#dialog-'+i).dialog('option', {
                position: [x, y]
            });
        });
     
        $('#copycommentminibutton-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('open');
        });
    
    });
                
    $('#copycommentminibutton-'+i).ready(function(e){
        $('#dialog-'+i).dialog('open');
        $('#copycommentminibutton-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.pageX+5, e.pageY+5]
            });
        });
         
        $('#copycommentminibutton-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });
   
    });
    
   
}

 


