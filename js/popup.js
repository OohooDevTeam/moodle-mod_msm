/* 
 * to create a dialog window to display additional informations
 */
var x = 0; // stores the x-axis position of the mouse
var y = 0; // stores the y-axis position of the mouse

/* variable i is passed by each time the defminibutton/minibutton in definition.php/theorem.php has a mouse hovering over and 
 * it tracks the unique ID number of the buttons to call the correct dialog windows.
 */
function popup(i) {
             
    $('#defminibutton-'+i).click(function(e) {
        x = e.pageX+5;
        y = e.pageY+5;

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
                
    $('#defminibutton-'+i).mouseover(function(e){
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
    
    $('#minibutton-'+i).click(function(e) {
        x = e.pageX+5;
        y = e.pageY+5;

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
                
    $('#minibutton-'+i).mouseover(function(e){
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
    
    $('#hottag-'+i).click(function(e) {
        x = e.pageX+5;
        y = e.pageY+5;

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
                
    $('#hottag-'+i).mouseover(function(e){
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
    
    $('#commentminibutton-'+i).click(function(e) {
        x = e.pageX+5;
        y = e.pageY+5;

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
                
    $('#commentminibutton-'+i).mouseover(function(e){
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

