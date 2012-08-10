/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function showRightpage()
{
    var strcontent = $('textarea#proofblockinput').val();
   
        alert(strcontent);
        $(strcontent).appendTo($(".rightbox"));
    
}