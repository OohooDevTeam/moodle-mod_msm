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
 * This class is representing all the associate elements that needs to be exported as XML document.  ExportAssociate
 * class is called by ExportDefinition, ExportTheorem and ExportComment classes.
 * It inherits methods from the abstract class ExportElement including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @author Ga Young Kim
 */
class ExportAssociate extends ExportElement
{
    public $id;             // current asscoaite id from msm_associate table
    public $compid;         // current associate id from msm_compositor table
    public $msmid;          // msm instance id
    public $description;    // the type of associate specified from the editor form
    public $info;           // ExportInfo object associated to current associate
    public $ref;            // an object from the one of the following classes that is linked to associate
                            // as reference material: ExportUnit, ExportDefinition, ExportComment or ExportTheorem

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with Associate element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Molecules.xsd.  This method also calls the exportData method
     * from ExportInfo, ExportUnit, ExportDefiniton, ExportComment and ExportTheorem clases.  The DOMElement object 
     * that is returned from exportData calls from classes mentioned above is then appended to the associate DOMElement
     * and is returned to be appended to the one of the following elements: Defintion, Comment or Theorem elements.
     * 
     * @return DOMElement
     */
    public function exportData()
    {
        $associateCreator = new DOMDocument();
        $associateCreator->formatOutput = true;
        $associateCreator->preserveWhiteSpace = false;
        $associateNode = $associateCreator->createElement("associate");
        $associateNode->setAttribute("type", $this->description);

        if (!empty($this->info))
        {
            $infoNode = $this->info->exportData();
            $newinfoNode = $associateCreator->importNode($infoNode, true);
            $associateNode->appendChild($newinfoNode);
        }

        if (!empty($this->ref))
        {
            switch (get_class($this->ref))
            {
                case "ExportDefinition":
                    $refNode = $associateCreator->createElement("definition.ref");
                    $refNode->setAttribute("definitionID", $this->msmid . "-" . $this->ref->compid);
                    break;
                case "ExportTheorem":
                    $refNode = $associateCreator->createElement("theorem.ref");
                    $refNode->setAttribute("theoremID", $this->msmid . "-" . $this->ref->compid);
                    break;
                case "ExportComment":
                    $refNode = $associateCreator->createElement("comment.ref");
                    $refNode->setAttribute("commentID", $this->msmid . "-" . $this->ref->compid);
                    break;
                // need to add ExportUnit later when dealing with internal/exteral references
            }
            $associateNode->appendChild($refNode);
            $this->ref->exportData("ref");
        }

        return $associateNode;
    }

    /**
     * This method is used to pull all relevant data linked with associate elements from the database table
     * "msm_associate".  It also calls the loadDbData method from the ExportUnit, ExportDefinition, ExportComment, ExportTheorem
     * and/or from ExportInfo classes.
     * 
     * @global moodle_database $DB
     * @param int $compid               Current Associate ID from msm_compositor database table
     * @return \ExportAssociate
     */
    public function loadDbData($compid)
    {
        global $DB;

        $associateCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $associateUnitRecord = $DB->get_record("msm_associate", array("id" => $associateCompRecord->unit_id));

        $this->id = $associateUnitRecord->id;
        $this->compid = $compid;
        $this->msmid = $associateCompRecord->msm_id;
        $this->description = $associateUnitRecord->description;

        $refCompRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->compid));

        foreach ($refCompRecords as $refCompRecord)
        {
            if (!empty($refCompRecord))
            {
                $refTable = $DB->get_record("msm_table_collection", array("id" => $refCompRecord->table_id));

                switch ($refTable->tablename)
                {
                    case "msm_def":
                        $def = new ExportDefinition();
                        $def->loadDbData($refCompRecord->id);
                        $this->ref = $def;
                        break;
                    case "msm_theorem":
                        $theorem = new ExportTheorem();
                        $theorem->loadDbData($refCompRecord->id);
                        $this->ref = $theorem;
                        break;
                    case "msm_comment":
                        $comment = new ExportComment();
                        $comment->loadDbData($refCompRecord->id);
                        $this->ref = $comment;
                        break;
                    case "msm_info":
                        $info = new ExportInfo();
                        $info->loadDbData($refCompRecord->id);
                        $this->info = $info;
                        break;
                    // add code to this condition to add feature to reference unit
//                    case "msm_unit":
//                        break;
                }
            }
        }

        return $this;
    }

}

?>
