/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function previewInfo(elementid, dialogid)
{
    var x = 0; // stores the x-axis position of the mouse
    var y = 0; // stores the y-axis position of the mouse
    
    console.log("elementid: "+elementid);
    console.log("dialogid: "+dialogid);    
    
    $('#'+elementid).unbind('click');
    $('#'+elementid).click(function(e) {
        x = e.pageX+5;
        y = e.pageY+5;

        $('#'+dialogid).dialog('open');
        $('#'+elementid).mousemove(function () {
            $('#'+dialogid).dialog('option', {
                position: [x, y]
            });
        });
     
        $('#'+elementid).mouseout(function(){
            $('#'+dialogid).dialog('open');
        });
    
    });
                
    $('#'+elementid).ready(function(e){        
        $('#'+elementid).mousemove(function (e) {
            $('#'+dialogid).dialog('option', {
                position: [e.pageX+5, e.pageY+5]
            });
            $('#'+dialogid).dialog('open');
        });
         
        $('#'+elementid).mouseout(function(){
            $('#'+dialogid).dialog('close');
        });
    });
}
