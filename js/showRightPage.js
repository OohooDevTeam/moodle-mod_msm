/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function showRightpage(id)
{
    $('.rightbox').empty();
    $('.rightbox').append($('#proof-'+id));
    
    $('#proof-'+id).css('display', 'block');
    $('#proof-'+id).toggleClass('proof', 'shownproof');
}