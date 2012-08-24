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
        $('#proof-'+id).css('display', 'block');
//        MathJax.Hub.Queue(["Typeset",MathJax.Hub]);   
    });
}