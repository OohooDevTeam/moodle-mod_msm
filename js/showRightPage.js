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


function showRightpage(id)
{   
    $('#proofminibutton-'+id).ready(function() {
        $('.rightbox').empty();
        $('.rightbox').append($('#proof-'+id));
        //        $('#proof-'+id).appendTo('.rightbox');
    
        $('#proof-'+id).css('display', 'block');
//        $('#proof-'+id).toggleClass('proof', 'shownproof');    
    });           
//        
//    $('#defminibutton-'+id).ready(function() {
//        $('.rightbox').empty();
//        $('.rightbox').append($('#refcontent-'+id));
//    
//        $('#refcontent-'+id).css('display', 'block');
//        $('#refcontent-'+id).toggleClass('refcontent', 'shownrefcontent');
//    });
//    
//    $('#minibutton-'+id).ready(function() {
//        $('.rightbox').empty();
//        $('.rightbox').append($('#refcontent-'+id));
//    
//        $('#refcontent-'+id).css('display', 'block');
//        $('#refcontent-'+id).toggleClass('refcontent', 'shownrefcontent');
//    });
//    
//    $('#commentminibutton-'+id).ready(function() {
//        $('.rightbox').empty();
//        $('.rightbox').append($('#refcontent-'+id));
//    
//        $('#refcontent-'+id).css('display', 'block');
//        $('#refcontent-'+id).toggleClass('refcontent', 'shownrefcontent');
//    });
}