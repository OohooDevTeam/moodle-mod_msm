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
 * This class is representing all the external link elements that needs to be exported as XML document.
 * ExportExternalLink class is called only by ExportSubordinate class.
 * It inherits methods from the abstract class ExportElement including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @author Ga Young Kim
 */
class ExportExternalLink extends ExportElement
{
    public $id;             // ID of this external link element in msm_external_link database table
    public $compid;         // ID of this external link element in msm_compositor database table
    public $href;           // the URL that this external link is containing
    public $target;         // value to specify how the external link would be displayed (default is either new window/new tab)
    public $type;           // defines the document type that the external link is referring to
    public $info;           // ExportInfo object that is associated with this external link element

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with external link element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Molecules.xsd.  This method also calls the exportData method
     * from ExportInfo class.  The DOMElement object that is returned from exportData calls from classes mentioned above
     * is then appended to the external.link DOMElement and is returned to be appended to the subordinate elements.
     * 
     * @return DOMElement
     */
    public function exportData()
    {
        $extLinkCreator = new DOMDocument();
        $extLinkCreator->formatOutput = true;
        $extLinkCreator->preserveWhiteSpace = false;
        $extLinkNode = $extLinkCreator->createElement("external.link");

        $extLinkNode->setAttribute("href", $this->href);
        if (!empty($this->type))
        {
            $extLinkNode->setAttribute("type", $this->type);
        }
        if (!empty($this->target))
        {
            $extLinkNode->setAttribute("target", $this->target);
        }
        
        if (!empty($this->info))
        {
            $infoNode = $this->info->exportData();
            $importInfoNode = $extLinkCreator->importNode($infoNode, true);
            $extLinkNode->appendChild($importInfoNode);
        }
        
        return $extLinkNode;
    }

    /**
     * This method is used to pull all relevant data linked with external link elements from the database table
     * "msm_external_link".  It also calls the loadDbData method from the ExportInfo class.
     * 
     * @global moodle_database $DB
     * @param int $compid               ID of this external link element in msm_compositor database table
     * @return \ExportExternalLink
     */
    public function loadDbData($compid)
    {
        global $DB;

        $externalLinkCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $externalLinkRecord = $DB->get_record("msm_external_link", array("id" => $externalLinkCompRecord->unit_id));

        $this->compid = $externalLinkCompRecord->id;
        $this->id = $externalLinkRecord->id;

        $this->href = $externalLinkRecord->href;
        $this->target = $externalLinkRecord->target;
        $this->type = $externalLinkRecord->type;

        $childRecord = $DB->get_record("msm_compositor", array("parent_id" => $this->compid));

        if (!empty($childRecord))
        {
            $info = new ExportInfo();
            $info->loadDbData($childRecord->id);
            $this->info = $info;
        }

        return $this;
    }

}

?>
