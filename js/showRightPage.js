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
    $('.rightbox').empty();
    $('.rightbox').append($('#proof-'+id));
    
    $('#proof-'+id).css('display', 'block');
    $('#proof-'+id).toggleClass('proof', 'shownproof');
}