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
 * This class is representing all the info elements that needs to be exported as XML document. 
 * ExportInfo class is called by ExportAssociate, ExportExternalLink and ExportSubordinate classes.
 * It inherits methods from the abstract class ExportElement including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @author Ga Young Kim
 */
class ExportInfo extends ExportElement
{

    public $id;                             // ID of the current info element in the msm_info database table
    public $compid;                         // ID of the current info element in the msm_compositor database table
    public $caption;                        // title associated with the current info element
    public $content;                        // content associated with the current info element
    public $subordinates = array();         // ExportSubordinate object that is associated with the current info element
    public $medias = array();               // ExportMedia object that is associated with the current info element

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with info element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Molecules.xsd.  This method also calls the exportData method
     * from ExportSubordinate and ExportMedia classes(by calling createXmlContent).  The DOMElement object 
     * that is returned from exportData calls from classes mentioned above is then appended to the content of info DOMElement
     * and is returned to be appended to the one of the following elements: Subordinate, Associate or External.link elements.
     * 
     * @return DOMElement
     */

    public function exportData()
    {
        $infoCreator = new DOMDocument();
        $infoCreator->formatOutput = true;
        $infoCreator->preserveWhiteSpace = false;
        $infoNode = $infoCreator->createElement("info");

        if (!empty($this->caption))
        {
            $captionNode = $infoCreator->createElement("info.caption");
            $captionText = $infoCreator->createTextNode($this->caption);
            $captionNode->appendChild($captionText);

//            $newcaptionNode = $this->createXmlContent($infoCreator, "<div>$this->caption</div>", $captionNode, '');
//            $newCaption = $infoCreator->importNode($newcaptionNode, true);
            $infoNode->appendChild($captionNode);
        }

        $createdbodyNode = $this->createXmlContent($infoCreator, $this->content, $infoNode, $this);

        $bodyNode = $infoCreator->importNode($createdbodyNode, true);

        return $bodyNode;
    }

    /**
     * This method is used to pull all relevant data linked with info elements from the database table
     * "msm_info".  It also calls the loadDbData method from the ExportSubordinate, and/or from ExportMedia classes.
     * 
     * @global moodle_database $DB
     * @param int $compid               ID of the current info element in msm_compositor database table
     * @return \ExportInfo
     */
    public function loadDbData($compid)
    {
        global $DB;

        $infoCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $infoUnitRecord = $DB->get_record("msm_info", array("id" => $infoCompRecord->unit_id));

        $this->id = $infoUnitRecord->id;
        $this->compid = $compid;
        $this->caption = trim($infoUnitRecord->caption);
        $this->content = $infoUnitRecord->info_content;

        $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->compid), "prev_sibling_id");

        foreach ($childRecords as $child)
        {
            $childtable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));

            if ($childtable->tablename == "msm_subordinate")
            {
                $subordinate = new ExportSubordinate();
                $subordinate->loadDbData($child->id);
                $this->subordinates[] = $subordinate;
            }
            else if ($childtable->tablename == "msm_media")
            {
                $media = new ExportMedia();
                $media->loadDbData($child->id);
                $this->medias[] = $media;
            }
        }

        return $this;
    }

}

?>
