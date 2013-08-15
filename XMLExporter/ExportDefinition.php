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
 * This class is representing all the definition elements that needs to be exported as XML document.
 * ExportDefinition class is called by ExportUnit, ExportSubordinate and ExportAssociate classes.
 * It inherits methods from the abstract class ExportElement including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @author Ga Young Kim
 */
class ExportDefinition extends ExportElement
{

    public $id;                         // ID of this definition in the msm_def database table
    public $compid;                     // ID of this definition in the msm_compositor database table
    public $caption;                    // title associated with this definition
    public $description;                // description associated with this definition
    public $type;                       // the type of the definition specified (eg. definition, convention, agreement...etc)
    public $content;                    // the content associated with this definition
    public $msmid;                      // msm instance id
    public $associates = array();       // ExportAssociate objects associated with this definition
    public $subordinates = array();     // ExportSubordinate objects linked to this definition's content
    public $medias = array();           // ExportMedia objects linked to this definition's content

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with definition element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Defintion.xsd.  This method also calls the exportData method
     * from ExportAssociate, ExportSubordinate and/or ExportMedias.  The DOMElement object 
     * that is returned from exportData calls from classes mentioned above is then appended to the definition DOMElement
     * and is returned to be appended to the one of the following elements: Unit, Associate or Subordinate elements
     * 
     * @param string $flag      A flag that indicates if the definition is a reference material
     *                          If the flag is not empty string, then the definition is a reference material and should create new XML file in standalone folder.
     * @return DOMElement/integer/false
     */
    public function exportData($flag = '')
    {
        $defCreator = new DOMDocument();
        $defCreator->formatOutput = true;
        $defCreator->preserveWhiteSpace = false;
        $defNode = $defCreator->createElement("def");
        $defNode->setAttribute("type", $this->type);
        $defNode->setAttribute("id", "$this->msmid-$this->compid");

        if (!empty($this->caption))
        {
            $oldtitleNode = $defCreator->createElement("caption");
            $createdTitleNode = $this->createXmlTitle($defCreator, $this->caption, $oldtitleNode);
            $titleNode = $defCreator->importNode($createdTitleNode, true);
            
//            $captionNode = $defCreator->createElement("caption");
//            $captionText = $defCreator->createTextNode($this->caption);
//            $captionNode->appendChild($captionText);
            $defNode->appendChild($titleNode);
        }

        if (!empty($this->description))
        {
            $descriptionNode = $defCreator->createElement("description");
            $descriptionText = $defCreator->createTextNode($this->description);
            $descriptionNode->appendChild($descriptionText);
            $defNode->appendChild($descriptionNode);
        }

        $defbodyNode = $defCreator->createElement("def.body");
        $createdbodyNode = $this->createXmlContent($defCreator, $this->content, $defbodyNode, $this);
        $bodyNode = $defCreator->importNode($createdbodyNode, true);

        $defNode->appendChild($bodyNode);

        if (!empty($this->associates))
        {
            foreach ($this->associates as $associate)
            {
                $associateNode = $associate->exportData();
                $newassociateNode = $defCreator->importNode($associateNode, true);
                $defNode->appendChild($newassociateNode);
            }
        }

        if (!empty($flag)) // def is a reference material (ie. ExportAssociate called this function)
        {
             // create a new XML file in standalone folder
            $existingUnit = $this->createXMLFile($this, $defCreator->saveXML() . $defCreator->saveXML($defCreator->importNode($defNode, true)));
            // return value can be a database ID or false
            return $existingUnit;
            
        }
        else // def is a main part of the unit (ie. ExportUnit or ExportSubordinate called this function)
        {
            return $defNode;
        }
    }

    /**
     * This method is used to pull all relevant data linked with def elements from the database table
     * "msm_def".  It also calls the loadDbData method from the ExportSubordinate, ExportAssociate, and/or from ExportMedia classes.
     * 
     * @global moodle_database $DB
     * @param int $compid               database ID of current definition element in msm_compositor table
     * @return \ExportDefinition
     */
    public function loadDbData($compid)
    {
        global $DB;

        $defCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $defRecord = $DB->get_record("msm_def", array("id" => $defCompRecord->unit_id));

        $this->id = $defRecord->id;
        $this->compid = $compid;
        $this->caption = $defRecord->caption;
        $this->description = $defRecord->description;
        $this->type = $defRecord->def_type;
        $this->content = $defRecord->def_content;
        $this->msmid = $defCompRecord->msm_id;

        $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->compid), 'prev_sibling_id');

        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));
            if ($childTable->tablename == "msm_subordinate")
            {
                $subordinate = new ExportSubordinate();
                $subordinate->loadDbData($child->id);
                $this->subordinates[] = $subordinate;
            }
            else if ($childTable->tablename == "msm_associate")
            {
                $associate = new ExportAssociate();
                $associate->loadDbData($child->id);
                $this->associates[] = $associate;
            }
            else if ($childTable->tablename == "msm_media")
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
