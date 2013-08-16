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
 * ************************************************************************* */

/**
 *  EditorInContent class inherits from the EditorElement class and it represents the
 * ordered or unordered list elements in the HTML content of tinyMCE editor.  Usually the
 * parent class that calls this class function is EditorBlock, EditorIntro or EditorUnit classes.
 * 
 */
class EditorInContent extends EditorElement
{

    public $id;                         // database ID associated with the incontent elements in msm_content table
    public $compid;                     // database ID associated with the incontent elements in msm_compositor table
    public $errorArray = array();       // HTML IDs of any content textareas that were submitted with empty values
    public $additional_attribute;       // has styling attributes for bullet point types/numbering types
    public $type;                       // to determine if the element is ordered/unordered list
    public $content;                    // content elements associated with the incontent elements
    public $subordinates = array();     // EditorSubordinate objects associated with the content of this incontent element
    public $medias = array();           // EditorMedia objects associated with the content of this incontent element

    /**
     * constructor for the class
     */
    function __construct()
    {
        $this->tablename = 'msm_content';
    }

    /**
     * This method is an abstract method inherited from EditorElement.  It finds the needed information for database table
     * from the POST object(from editor form submission).  It calls the same method from another class(EditorSubordinate) to process its
     * children's data.
     * 
     * @param DOMElement $idNumber              corresponds to ol/ul child from content
     * @return \EditorInContent
     */
    public function getFormData($idNumber)
    {
        $doc = new DOMDocument();
        $listElement = $doc->importNode($idNumber, true);

        if ($listElement->tagName == "ol")
        {
            $this->type = "ordered";
        }
        else if ($listElement->tagName == "ul")
        {
            $this->type = "unordered";
        }

        $styleAttribute = $listElement->getAttribute("style");

        if (!empty($styleAttribute))
        {
            $styleTypes = explode(";", $styleAttribute);

            foreach ($styleTypes as $style)
            {
                $styleValue = explode(":", $style);

                if ($styleValue[0] == "list-style-type")
                {
                    $this->additional_attribute = $styleValue[1];
                }
            }
        }
        else
        {
            $this->additional_attribute = null;
        }

        $this->content = $doc->saveHTML($listElement);

        foreach ($this->processImage($this->content) as $key => $media)
        {
            $this->medias[] = $media;
        }

        foreach ($this->processSubordinate($this->content) as $key => $subordinates)
        {
            $this->subordinates[] = $subordinates;
        }

        return $this;
    }

    /**
     * This method is an abstract method inherited from EditorElement.  Its main purpose is to
     * insert the data obtained from the POST object via method above to the msm_content table and to 
     * insert structural data (its parent/sibling...etc) to the compositor table. This method also calls 
     * insertData method from EditorSubordinate class.
     * 
     * @global moodle_database $DB
     * @param integer $parentid         Database ID from msm_compositor of the parent element
     * @param integer $siblingid        Database ID from msm_compositor of the previous sibling element
     * @param integer $msmid            The instance ID of the MSM module.
     */
    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $data->additional_attribute = $this->additional_attribute;
        $data->content = $this->content;
        $data->type = $this->type;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record("msm_table_collection", array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

        $media_sibliing = 0;
        $content = '';
        foreach ($this->medias as $key => $media)
        {
            $media->insertData($this->compid, $media_sibliing, $msmid);
            $media_sibliing = $media->compid;
            if ($this->type == "ordered")
            {
                $content = $this->replaceImages($key, $media->image, $this->content, "ol");
            }
            else
            {
                $content = $this->replaceImages($key, $media->image, $this->content, "ul");
            }
        }
        $this->content = $content;

        $data->id = $this->id;
        $data->para_content = $this->content;
        $this->id = $DB->update_record($this->tablename, $data);

        $subordinate_sibling = 0;
        foreach ($this->subordinates as $subordinate)
        {
            $subordinate->insertData($this->compid, $subordinate_sibling, $msmid);
            $subordinate_sibling = $subordinate->compid;
        }
    }

    /**
     * This method is an abstract method from EditorElement that has a purpose of displaying the 
     * data extracted from DB from loadData method by outputting the HTML code.  
     * 
     * @return HTML string
     */
    public function displayData()
    {
        $htmlContent = '';

        $htmlContent .= html_entity_decode($this->content);

        return $htmlContent;
    }

    /**
     * This abstract method from EditorElement extracts appropriate information from the 
     * msm_content table and also triggers extraction of data from its children using the 
     * data given by the msm_compositor table. It calls the loadData method from the EditorSubordinate 
     * class.
     * 
     * @global moodle_database $DB
     * @param integer $compid           The database ID from the msm_compositor table
     * @return \EditorInContent
     */
    public function loadData($compid)
    {
        global $DB;

        $inContentCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $inContentCompRecord->unit_id;

        $inContentRecord = $DB->get_record($this->tablename, array('id' => $this->id));

        $this->additional_attribute = $inContentRecord->additional_attribute;
        $this->type = $inContentRecord->type;
        $this->content = $inContentRecord->content;

        $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $compid), "prev_sibling_id");

        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));

            if ($childTable->tablename == "msm_subordinate")
            {
                $subordinate = new EditorSubordinate();
                $subordinate->loadData($child->id);
                $this->subordinates[] = $subordinate;
            }
        }

        return $this;
    }

    /**
     * This method is triggered when the View navigation button on the editor is clicked to show the preview of the unit to the user.
     * It generates the appropriate HTML code to display the information as it is layed out on the MSM editor not according to how
     * the elements are structured in the database.  Hence allowing user to preview the material while making changes without having to 
     * commit to saving it in the database.
     * 
     * @return HTML string
     */
    public function displayPreview()
    {
        $previewHtml = '';

        $previewHtml .= html_entity_decode($this->content);

        if (!empty($this->subordinates))
        {
            foreach ($this->subordinates as $subordinate)
            {
                $previewHtml .= $subordinate->displayPreview($content);
            }
        }

        return $previewHtml;
    }

}

?>
