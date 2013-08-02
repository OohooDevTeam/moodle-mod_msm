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
 * EditorTable class inherits from the EditorElement class and it represents the
 * table elements in the HTML content of tinyMCE editor.  Usually the
 * parent class that calls this class function is EditorBlock, EditorIntro or EditorUnit classes.
 */
class EditorTable extends EditorElement
{

    public $id;                         // database ID associated with the table element in msm_table table
    public $compid;                     // database ID associated with the table element in msm_compositor table
    public $content;                    // content element associated with the table element
    public $subordinates = array();     // EditorSubordinate objects associated with the table element

    // constructor for this class

    function __construct()
    {
        $this->tablename = 'msm_table';
    }

    /**
     * This method is an abstract method inherited from EditorElement.  It finds the needed information for database table
     * from the POST object(from editor form submission).  It calls the same method from another class(EditorSubordinate) to process its
     * children's data.
     * 
     * @param DOMElement $idNumber      table DOMElement from content
     * @return \EditorTable
     */
    public function getFormData($idNumber)
    {
        $doc = new DOMDocument();

        $idNumber->setAttribute("class", "mathtable");

        if (!$idNumber->hasAttribute("style"))
        {
            $idNumber->setAttribute("style", "width:100% !important");
        }

        $paraNode = $doc->importNode($idNumber, true);

        $this->content = $doc->saveXML($paraNode);

        foreach ($this->processSubordinate($this->content) as $key => $subordinates)
        {
            $this->subordinates[] = $subordinates;
        }

        return $this;
    }

    /**
     * This method is an abstract method inherited from EditorElement.  Its main purpose is to
     * insert the data obtained from the POST object via method above to the msm_table table and to 
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
        $data->table_content = $this->content;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

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
     * @return string
     */
    public function displayData()
    {
        $htmlContent = '';
        $htmlContent .= html_entity_decode($this->content);
        return $htmlContent;
    }

    /**
     * This abstract method from EditorElement extracts appropriate information from the 
     * msm_table table and also triggers extraction of data from its children using the 
     * data given by the msm_compositor table. It calls the loadData method from the EditorSubordinate 
     * class.
     * 
     * @global moodle_database $DB
     * @param integer $compid           The database ID from the msm_compositor table
     * @return \EditorTable
     */
    public function loadData($compid)
    {
        global $DB;

        $tableCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $tableCompRecord->unit_id;

        $tableRecord = $DB->get_record($this->tablename, array('id' => $this->id));

        $this->content = $tableRecord->table_content;

        $childRecords = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

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
                $previewHtml .= $subordinate->displayPreview();
            }
        }

        return $previewHtml;
    }

}

?>
