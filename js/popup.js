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
 * to create a dialog window to display additional informations
 */

/* variable i is passed by each time the defminibutton/minibutton in definition.php/theorem.php has a mouse hovering over and 
 * it tracks the unique ID number of the buttons to call the correct dialog windows.
 */
function popup(i) {   
    var x = 0; // stores the x-axis position of the mouse
    var y = 0; // stores the y-axis position of the mouse
    
    $('#defminibutton-'+i).unbind('click');
    $('#defminibutton-'+i).click(function(e) {
        x = e.clientX+5;
        y = e.clientY+5;

        $('#dialog'+i).dialog('open');
        $('#defminibutton-'+i).mousemove(function () {
            $('#dialog-'+i).dialog('option', {
                position: [x, y]
            });
        });
     
        $('#defminibutton-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('open');
        });
    
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
    $('#minibutton-'+i).click(function(e) {
        x = e.clientX+5;
        y = e.clientY+5;

        $('#dialog'+i).dialog('open');
        $('#minibutton-'+i).mousemove(function () {
            $('#dialog-'+i).dialog('option', {
                position: [x, y]
            });
        });
     
        $('#minibutton-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('open');
        });
    
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
    $('#hottag-'+i).click(function(e) {
        x = e.clientX+5;
        y = e.clientY+5;

        $('#dialog'+i).dialog('open');
        $('#hottag-'+i).mousemove(function () {
            $('#dialog-'+i).dialog('option', {
                position: [x, y]
            });
        });
     
        $('#hottag-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('open');
        });
    
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
    
    $('#commentminibutton-'+i).unbind('click');
    $('#commentminibutton-'+i).click(function(e) {
        x = e.clientX+5;
        y = e.clientY+5;

        $('#dialog'+i).dialog('open');
        $('#commentminibutton-'+i).mousemove(function () {
            $('#dialog-'+i).dialog('option', {
                position: [x, y]
            });
        });
     
        $('#commentminibutton-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('open');
        });
        
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
    
    $('#pic-'+i).unbind('click');
    $('#pic-'+i).click(function(e) {
        x = e.clientX+5;
        y = e.clientY+5;

        $('#dialog'+i).dialog('open');
        $('#pic-'+i).mousemove(function () {
            $('#dialog-'+i).dialog('option', {
                position: [x, y]
            });
        });
     
        $('#pic-'+i).mouseout(function(){               
            $('#dialog-'+i).dialog('open');
        });
    
    });
                
    $('#pic-'+i).ready(function(e){       
        $('#pic-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });
            $('#dialog-'+i).dialog('open');         
        });
         
        $('#pic-'+i).mouseout(function(){ 
            $('#dialog-'+i).dialog('close'); 
        });
    });
    
    $('#copypic-'+i).unbind('click');
    $('#copypic-'+i).click(function(e) {
        x = e.clientX+5;
        y = e.clientY+5;

        $('#dialog'+i).dialog('open');
        $('#copypic-'+i).mousemove(function () {
            $('#dialog-'+i).dialog('option', {
                position: [x, y]
            });
        });
     
        $('#copypic-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('open'); 
        });
    
    });
                
    $('#copypic-'+i).ready(function(e){       
        $('#copypic-'+i).mousemove(function (e) {
            $('#dialog-'+i).dialog('option', {
                position: [e.clientX+5, e.clientY+5]
            });
            $('#dialog-'+i).dialog('open');
        });
         
        $('#copypic-'+i).mouseout(function(){
            $('#dialog-'+i).dialog('close');
        });
    });
 
 
}


