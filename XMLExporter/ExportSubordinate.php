<?php
/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                       **
 * @subpackage  msm                                                       **
 * @name        msm                                                       **
 * @copyright   University of Alberta                                     **
 * @link        http://ualberta.ca                                        **
 * @author      Ga Young Kim                                              **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * *************************************************************************
 */

/**
 * This class is representing all the subordinate elements that needs to be exported as XML document.  
 * ExportSubordinate class is called directly by ExportPara and ExportElement classes but all the classes
 * with content indirectly calls this class by calling the createXmlContent function in ExportElement.
 * It inherits methods from the abstract class ExportElement including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @author Ga Young Kim
 */
class ExportSubordinate extends ExportElement
{

    public $id;                     // ID of the current subordinate element in the msm_subordinate database table
    public $compid;                 // ID of the current subordinate element in the msm_compositor database table
    public $hot;                    // word(s) that is wrapped in <hot> XML tag to trigger a popup content in ExportInfo
    public $info;                   // ExportInfo object associated with the current subordinate element
    public $external_link;          // ExportExternalLink object associated with the current subordinate element

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with subordinate element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Molecules.xsd.  This method also calls the exportData method
     * from ExportExternalLink and ExportInfo classes.  The DOMElement object that is returned from exportData 
     * calls from classes mentioned above is then appended to the subordinate DOMElement and is returned to replace
     * existing anchored elements in contents.
     * 
     * @return DOMElement
     */
    public function exportData()
    {
        $subordinateCreator = new DOMDocument();
        $subordinateCreator->formatOutput = true;
        $subordinateCreator->preserveWhiteSpace = false;
        $subordinateNode = $subordinateCreator->createElement("subordinate");

        $hotNode = $subordinateCreator->createElement("hot");
        $hotInfo = explode("||", $this->hot);
        $hotText = $subordinateCreator->createTextNode($hotInfo[1]);

        $hotNode->appendChild($hotText);
        $subordinateNode->appendChild($hotNode);

        if (!empty($this->info))
        {
            $infoNode = $this->info->exportData();
            $importInfoNode = $subordinateCreator->importNode($infoNode, true);
            $subordinateNode->appendChild($importInfoNode);
        }
        if(!empty($this->external_link))
        {
            $extLinkNode = $this->external_link->exportData();
            $importExtLinkNode = $subordinateCreator->importNode($extLinkNode, true);
            $subordinateNode->appendChild($importExtLinkNode);
        }

        return $subordinateNode;
    }

    /**
     * This method is used to pull all relevant data linked with associate elements from the database table
     * "msm_associate".  It also calls the loadDbData method from the ExportUnit, ExportDefinition, ExportComment, ExportTheorem
     * and/or from ExportInfo classes.
     * 
     * @global moodle_database $DB
     * @param int $compid                   ID of the current subordinate elements in the msm_compositor table
     * @return \ExportSubordinate
     */
    public function loadDbData($compid)
    {
        global $DB;

        $subordinateCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $subordinateUnitRecord = $DB->get_record("msm_subordinate", array("id" => $subordinateCompRecord->unit_id));

        $this->id = $subordinateUnitRecord->id;
        $this->compid = $compid;
        $this->hot = $subordinateUnitRecord->hot;

        $childRecord = $DB->get_record("msm_compositor", array("parent_id" => $this->compid));
        $childTable = $DB->get_record("msm_table_collection", array("id"=>$childRecord->table_id));
        
        if($childTable->tablename == "msm_info")
        {
            $info = new ExportInfo();
            $info->loadDbData($childRecord->id);
            $this->info = $info;
        }
        else if($childTable->tablename == "msm_external_link")
        {
            $externalLink = new ExportExternalLink();
            $externalLink->loadDbData($childRecord->id);
            $this->external_link = $externalLink;
        }


        return $this;
    }

}

?>
