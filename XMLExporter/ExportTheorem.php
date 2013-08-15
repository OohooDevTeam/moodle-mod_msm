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
 * This class is representing all the theorem elements that needs to be exported as XML document.
 * ExportTheorem class is called by ExportUnit, ExportSubordinate and ExportAssociate classes.
 * It inherits methods from the abstract class ExportElement including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @author Ga Young Kim
 */
class ExportTheorem extends ExportElement {

    public $id;                             // ID of the current theorem in msm_theorem database table
    public $msmid;                          // MSM instance ID
    public $compid;                         // ID of the current theorem in msm_compositor databse table
    public $statements = array();           // ExportStatementTheorem objects associated with the current theorem element
    public $description;                    // description associated with the current theorem element      
    public $caption;                        // title of the curren theorem element
    public $type;                           // type of the current theorem element (eg. Theorem, Proposition, Lemma...etc)
    public $associates = array();           // ExportAssociate objects linked to the current theorem element

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with theorem element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Theorem.xsd.  This method also calls the exportData method
     * from ExportStatementTheorem, and ExportAssociate.  The DOMElement object that is returned from exportData
     * calls from classes mentioned above is then appended to the theorem DOMElement and is returned to be 
     * appended to the one of the following elements: Unit, Associate or Subordinate elements.
     * 
     * @param string $flag              A flag that indicates if the theorem is a reference material
     *                                  If the flag is not empty string, then the theorem is a reference
     *                                  material and should create new XML file in standalone folder.
     * @return DOMElement/integer/false
     */

    public function exportData($flag = '') {
        $theoremCreator = new DOMDocument();
        $theoremCreator->formatOutput = true;
        $theoremCreator->preserveWhiteSpace = false;
        $theoremNode = $theoremCreator->createElement("theorem");
        $theoremNode->setAttribute("id", "$this->msmid-$this->compid");
        $theoremNode->setAttribute("type", $this->type);

        if (!empty($this->caption)) {
            $oldtitleNode = $theoremCreator->createElement("caption");
            $createdTitleNode = $this->createXmlTitle($theoremCreator, $this->caption, $oldtitleNode);
            $titleNode = $theoremCreator->importNode($createdTitleNode, true);

//            $captionNode = $theoremCreator->createElement("caption");
//            $captionText = $theoremCreator->createTextNode($this->caption);
//            $captionNode->appendChild($captionText);
            $theoremNode->appendChild($titleNode);
        }

        if (!empty($this->description)) {
            $descriptionNode = $theoremCreator->createElement("description");
            $descriptionText = $theoremCreator->createTextNode($this->description);
            $descriptionNode->appendChild($descriptionText);
            $theoremNode->appendChild($descriptionNode);
        }

        if (!empty($this->statements)) {
            foreach ($this->statements as $statement) {
                $statementNode = $statement->exportData();
                $newstatementNode = $theoremCreator->importNode($statementNode, true);
                $theoremNode->appendChild($newstatementNode);
            }
        }

        if (!empty($this->associates)) {
            foreach ($this->associates as $associate) {
                $associateNode = $associate->exportData();
                $newassociateNode = $theoremCreator->importNode($associateNode, true);
                $theoremNode->appendChild($newassociateNode);
            }
        }

        if (!empty($flag)) { // theorem is a reference material (ie. ExportAssociate called this function)
            // create a new XML file in standalone folder
            $existingUnit = $this->createXMLFile($this, $theoremCreator->saveXML() . $theoremCreator->saveXML($theoremCreator->importNode($theoremNode, true)));
            // return value can be a database ID or false
            return $existingUnit;
        } else {  // theorem is a main part of the unit (ie. ExportUnit or ExportSubordinate called this function)
            return $theoremNode;
        }
    }

    /**
     * This method is used to pull all relevant data linked with theorem elements from the database table
     * "msm_theorem".  It also calls the loadDbData method from the ExportStatementTheorem, and ExportAssociate classes.
     * 
     * @global moodle_database $DB
     * @param int $compid                           ID of the current theorem elements in the msm_compositor table
     * @return \ExportTheorem
     */
    public function loadDbData($compid) {
        global $DB;

        $theoremCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $theoremUnitRecord = $DB->get_record("msm_theorem", array("id" => $theoremCompRecord->unit_id));

        $this->id = $theoremUnitRecord->id;
        $this->compid = $compid;
        $this->msmid = $theoremCompRecord->msm_id;
        $this->caption = $theoremUnitRecord->caption;
        $this->description = $theoremUnitRecord->description;
        $this->type = $theoremUnitRecord->theorem_type;

        $childrenCompRecord = $DB->get_records("msm_compositor", array("parent_id" => $this->compid), "prev_sibling_id");

        foreach ($childrenCompRecord as $child) {
            $childtable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));

            if ($childtable->tablename == "msm_statement_theorem") {
                $statement = new ExportStatementTheorem();
                $statement->loadDbData($child->id);
                $this->statements[] = $statement;
            } else if ($childtable->tablename == "msm_associate") {
                $associate = new ExportAssociate();
                $associate->loadDbData($child->id);
                $this->associates[] = $associate;
            }
        }

        return $this;
    }

}

?>
