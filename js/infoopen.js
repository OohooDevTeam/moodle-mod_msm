
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



/* variable i is passed by each time the defminibutton/minibutton in definition.php/theorem.php has a mouse hovering over and 
 * it tracks the unique ID number of the buttons to call the correct dialog windows.
 */

function infoopen(i) {    
    
    var x = 0; // stores the x-axis position of the mouse
    var y = 0; // stores the y-axis position of the mouse
    
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
            
        });
    
    $('#defminibutton-'+i).ready(function(e){
        $('#defminibutton-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });            
            $('#dialog-'+i).dialog('open');
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
    });
    
    $('#minibutton-'+i).ready(function(e){        
        $('#minibutton-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });
            $('#dialog-'+i).dialog('open');
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
           
        });

    $('#hottag-'+i).ready(function(e){
        $('#hottag-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });
            $('#dialog-'+i).dialog('open');
        });

        $('#hottag-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });   

    });
    
    $('#activehottag-'+i).unbind('click');
    $('#activehottag-'+i).click(
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
           
        });

    $('#activehottag-'+i).ready(function(e){        
        $('#activehottag-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });
            $('#dialog-'+i).dialog('open');
        });

        $('#activehottag-'+i).mouseout(function(){
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
    });
    
    $('#commentminibutton-'+i).ready(function(e){
        $('#commentminibutton-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });
            $('#dialog-'+i).dialog('open');
        });
         
        $('#commentminibutton-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });
   
    });
    $('#proofminibutton-'+i).unbind('click');
    $('#proofminibutton-'+i).click(function() {
        $('.rightbox').empty();
        var cloned = $('#proof-'+i).clone();
        cloned.find('*').each(function(){
            var currentid = $(this).attr('id');
            if(typeof currentid != 'undefined')
            {
                $(this).attr('id', 'copy'+currentid);
            }
        });
        cloned.appendTo($('.rightbox')).css('display', 'block');
        MathJax.Hub.Queue(["Typeset",MathJax.Hub]);

    });
 
    $('#copydefminibutton-'+i).unbind('click');
    $('#copydefminibutton-'+i).click(function(e) {
        x = e.clientX+5;
        y = e.clientY+5;

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
        $('#copydefminibutton-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });
            $('#dialog-'+i).dialog('open');
        });
         
        $('#copydefminibutton-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });
   
    });
    
    $('#copyminibutton-'+i).unbind('click');
    $('#copyminibutton-'+i).click(function(e) {
        x = e.clientX+5;
        y = e.clientY+5;

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
        $('#copyminibutton-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });
            $('#dialog-'+i).dialog('open');
        });
         
        $('#copyminibutton-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });
   
    });
    
    $('#copyhottag-'+i).unbind('click'); 
    $('#copyhottag-'+i).click(function(e) {
        x = e.clientX+5;
        y = e.clientY+5;

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
        $('#copyhottag-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });
            $('#dialog-'+i).dialog('open');
        });
         
        $('#copyhottag-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });   
    });  
    
    $('#copycommentminibutton-'+i).unbind('click');    
    $('#copycommentminibutton-'+i).click(function(e) {    
        x = e.clientX+5;
        y = e.clientY+5;

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
        $('#copycommentminibutton-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });
            $('#dialog-'+i).dialog('open');
        });
         
        $('#copycommentminibutton-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });
   
    });
    
    // to produce a modal dialog box to display reference materials from the right side of the page\
    $('#copyactivehottag-'+i).unbind('click');   
    $('#copyactivehottag-'+i).click(function(e) {  
        $('#dialog-'+i).dialog('close');
        $('#copyrefcontent-'+i).dialog(
        {
            modal:true,
            width: 840            
        });
        $('#copyrefcontent-'+i).dialog('open').css('display', 'block');
        MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
        // to close the modal dialog box by clicking the outside of the box
        $('.ui-widget-overlay').click(function() {
            $('#copyrefcontent-'+i).dialog('close'); 
        });
    }); 
                
    $('#copyactivehottag-'+i).ready(function(e){
        $('#copyactivehottag-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });
            $('#dialog-'+i).dialog('open');
        });
         
        $('#copyactivehottag-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });   
    });
    
    $('#symbolinfo-'+i).unbind('click');
    $('#symbolinfo-'+i).click(function(e) { 
        $('#symbolrefcontent-'+i).attr('class', 'renderefcontent');        
        MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
        
        $('#dialog-'+i).dialog('close');
        
        $('#symbolrefcontent-'+i).dialog(
        {
            modal:true,
            width: 840            
        });      
        
        $('#symbolrefcontent-'+i).dialog('open').css('display', 'block');
        
        // to close the modal dialog box by clicking the outside of the box
        $('.ui-widget-overlay').click(function() {
            $('#symbolrefcontent-'+i).dialog('close'); 
        });
    });   
                
    $('#symbolinfo-'+i).ready(function(e){
        $('#symbolinfo-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });
            $('#dialog-'+i).dialog('open');
        });
         
        $('#symbolinfo-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });   
    });
   
    $('#glossaryinfo-'+i).unbind('click');
    $('#glossaryinfo-'+i).click(function(e) {        
        $('#glossaryrefcontent-'+i).attr('class', 'renderefcontent');        
        MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
        
        $('#dialog-'+i).dialog('close');
        $('#glossaryrefcontent-'+i).dialog(
        {
            modal:true,
            width: 840            
        });
        $('#glossaryrefcontent-'+i).dialog('open').css('display', 'block');
        // to close the modal dialog box by clicking the outside of the box
        //        alert("opening?");
        $('.ui-widget-overlay').click(function() {
            $('#glossaryrefcontent-'+i).dialog('close'); 
        });
    });   
                
    $('#glossaryinfo-'+i).ready(function(e){
        $('#glossaryinfo-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });
            $('#dialog-'+i).dialog('open');
        });
         
        $('#glossaryinfo-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });   
    });
    $('#authorinfo-'+i).unbind('click');
    $('#authorinfo-'+i).click(function(e) {  
        $('#authorrefcontent-'+i).attr('class', 'renderefcontent');        
        MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
        
        $('#dialog-'+i).dialog('close');
        $('#authorrefcontent-'+i).dialog(
        {
            modal:true,
            width: 840            
        });
        $('#authorrefcontent-'+i).dialog('open').css('display', 'block');
        // to close the modal dialog box by clicking the outside of the box
        $('.ui-widget-overlay').click(function() {
            $('#authorrefcontent-'+i).dialog('close'); 
        });
    });   
                
    $('#authorinfo-'+i).ready(function(e){
        $('#authorinfo-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });
            $('#dialog-'+i).dialog('open');
        });
         
        $('#authorinfo-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });   
    });
    
   
}

 


