/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var x = 0; // stores the x-axis position of the mouse
var y = 0; // stores the y-axis position of the mouse


function showonRight(i)
{
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
    
    $('#copyhottag-'+i).click(function(e) {         
        $('#dialog-'+i).dialog('close');
       
        var cloned = $('#refcontent-'+i).clone();
        cloned.find('*').each(function(){
            var currentid = $(this).attr('id');
            if(typeof currentid != 'undefined')
            {
                $(this).attr('id', 'copy'+currentid);
            }
               
        });
         cloned.appendTo($('.rightbox'));
        $('#copyrefcontent').css('display', 'block').dialog('option', 'momdal');
       
    });
                
    //    $('#copyhottag-'+i).ready(function(e){
    //        $('#dialog-'+i).dialog('open');
    //        $('#copyhottag-'+i).mousemove(function (e) {
    //            $('#dialog-'+i).dialog('option', {
    //                position: [e.pageX+5, e.pageY+5]
    //            });
    //        });
    //         
    //        $('#copyhottag-'+i).mouseout(function(){
    //            $('#dialog-'+i).dialog('close');
    //        });
    //    });
    
    $('#copydefminibutton-'+i).click(function(e) {
        $('#dialog-'+i).dialog('open');
        $('#dialog-'+i).dialog('option', 'modal', true);    
    
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
        $('#dialog-'+i).dialog('open');
        $('#dialog-'+i).dialog('option', 'modal', true);    
    
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
        
    $('#copycommentminibutton-'+i).click(function(e) {
        $('#dialog-'+i).dialog('open');
        $('#dialog-'+i).dialog('option', 'modal', true);    
    
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