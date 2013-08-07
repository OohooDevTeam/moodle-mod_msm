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
 * This class is representing all the comment elements that needs to be exported as XML document.  ExportComment
 * class is called by ExportUnit, ExportSubordinate and ExportAssociate classes.
 * It inherits methods from the abstract class ExportElement including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @author Ga Young Kim
 */
class ExportComment extends ExportElement
{

    public $id;                         // database ID of the current comment element in msm_comment database table
    public $msmid;                      // msm instance id
    public $compid;                     // database ID of the current comment element in msm_compositor database table
    public $caption;                    // title associated with the comment element
    public $description;                // description associated with the comment
    public $type;                       // the type of the comment specified
    public $content;                    // the content associated with the comment
    public $associate = array();        // ExportAssociate objects linked to this comment
    public $subordinates = array();     // any ExportSubordinate objects associated with this comment's content
    public $medias = array();           // any ExportMedia objects associated with this comment's content

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with comment element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Comment.xsd.  This method also calls the exportData method
     * from ExportAssociate, ExportSubordinate and/or ExportMedias.  The DOMElement object 
     * that is returned from exportData calls from classes mentioned above is then appended to the comment DOMElement
     * and is returned to be appended to the one of the following elements: Unit, Associate or Subordinate elements
     * 
     * @param string $flag      A flag that indicates if the comment is a reference material
     *                          If the flag is not empty string, then the comment is a reference material and should create new XML file in standalone folder.
     * @return DOMElement/integer/false
     */
    public function exportData($flag = '')
    {
        $commentCreator = new DOMDocument();
        $commentCreator->formatOutput = true;
        $commentCreator->preserveWhiteSpace = false;
        $commentNode = $commentCreator->createElement("comment");
        $commentNode->setAttribute("type", $this->type);
        $commentNode->setAttribute("id", "$this->msmid-$this->compid");

        if (!empty($this->caption))
        {
            $captionNode = $commentCreator->createElement("caption");
            $captionText = $commentCreator->createTextNode($this->caption);
            $captionNode->appendChild($captionText);
            $commentNode->appendChild($captionNode);
        }

        if (!empty($this->description))
        {
            $descriptionNode = $commentCreator->createElement("description");
            $descriptionText = $commentCreator->createTextNode($this->description);
            $descriptionNode->appendChild($descriptionText);
            $commentNode->appendChild($descriptionNode);
        }

        $commentbodyNode = $commentCreator->createElement("comment.body");
        $createdbodyNode = $this->createXmlContent($commentCreator, $this->content, $commentbodyNode, $this);
        $bodyNode = $commentCreator->importNode($createdbodyNode, true);
        $commentNode->appendChild($bodyNode);

        if (!empty($this->associates))
        {
            foreach ($this->associates as $associate)
            {
                $associateNode = $associate->exportData();
                $newassociateNode = $commentCreator->importNode($associateNode, true);
                $commentNode->appendChild($newassociateNode);
            }
        }
        
        if (!empty($flag)) // comment is a reference material (ie. ExportAssociate called this function)
        {
            // create a new XML file in standalone folder
            $existingUnit = $this->createXMLFile($this, $commentCreator->saveXML() . $commentCreator->saveXML($commentCreator->importNode($commentNode, true))); 
            // return value can be a database ID or false
            return $existingUnit;
        }
        else // comment is a main part of the unit (ie. ExportUnit or ExportSubordinate called this function)
        {
            return $commentNode;
        }
    }

    /**
     * This method is used to pull all relevant data linked with comment elements from the database table
     * "msm_comment".  It also calls the loadDbData method from the ExportSubordinate, ExportAssociate, and/or from ExportMedia classes.
     * 
     * @global moodle_database $DB
     * @param int $compid               database ID of this comment element in the msm_compositor database table
     * @return \ExportComment
     */
    public function loadDbData($compid)
    {
        global $DB;

        $commentCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $commentRecord = $DB->get_record("msm_comment", array("id" => $commentCompRecord->unit_id));

        $this->id = $commentRecord->id;
        $this->compid = $compid;
        $this->msmid = $commentCompRecord->msm_id;
        $this->caption = $commentRecord->caption;
        $this->description = $commentRecord->description;
        $this->type = $commentRecord->comment_type;
        $this->content = $commentRecord->comment_content;

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
            else if($childTable->tablename == "msm_media")
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
