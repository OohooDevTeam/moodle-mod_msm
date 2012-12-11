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

/**
 * to get the choice of associate information drop down menu --> index for identifying html ID and childtype for distiguishing if
 * it's from def/theorem
 */
function processAssociate(e)
{
    var selectedAssociate = e.target.selectedIndex;
    var selectedChildElement = e.target.id
    var selectedtext = null;
    
    var splitResult = selectedChildElement.split('_');
    
//    if((splitResult[1] == "def") || (splitResult[1] == "comment"))
//    {
//        switch(selectedAssociate)
//        {
//            case 0:
//                selectedtext = "None";
//                break;
//            case 1:
//                selectedtext = "Quick Info";
//                break;
//            case 2:
//                selectedtext = "Comment";
//                break;
//            case 3:
//                selectedtext = "Explanation";
//                break;
//            case 4:
//                selectedtext = "Example";
//                break;
//            case 5:
//                selectedtext = "Illustration";
//                break;
//            case 6:
//                selectedtext = "Remark";
//                break;
//            case 7:
//                selectedtext = "Exploration";
//                break;
//        }
//    }
//    else
//    {
        switch(selectedAssociate)
        {
            case 0:
                selectedtext = "None";
                break;
            case 1:
                selectedtext = "Quick Info";
                break;
            case 2:
                selectedtext = "Comment";
                break;
            case 3:
                selectedtext = "Explanation";
                break;
            case 4:
                selectedtext = "Example";
                break;
            case 5:
                selectedtext = "Illustration";
                break;            
            case 6:
                selectedtext = "Proof";
                break;
            case 7:
                selectedtext = "Remark";
                break;
            case 8:
                selectedtext = "Exploration";
                break;
        }
//    }
    
    alert(selectedtext);
}

