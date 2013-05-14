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
 *  EditorPara class inherits from the EditorElement class and it represents the
 * paragraph elements in the HTML content of tinyMCE editor.  Usually the
 * parent class that calls this class function is EditorBlock, EditorIntro or EditorUnit classes.
 * 
 */
class EditorPara extends EditorElement
{

    public $align;
    public $description;
    public $content;
    public $id;
    public $compid;
    public $imgs = array();
    public $subordinates = array();

    function __construct()
    {
        $this->tablename = "msm_para";
    }

    /**
     * This method is an abstract method inherited from EditorElement.  It finds the needed information for database table
     * from the POST object(from editor form submission).  It calls the same method from another class(EditorSubordinate) to process its
     * children's data.
     * 
     * @param DOMElement $idNumber corresponds to <p> elements from content
     * @return \EditorPara
     */
    function getFormData($idNumber)
    {
        $doc = new DOMDocument();
        $paraNode = $doc->importNode($idNumber, true);

        $style = $paraNode->getAttribute("style");
        // break style attribute to each of properties
        $styleProperites = explode(";", $style);
        foreach ($styleProperites as $property)
        {
            $propertyValue = explode(":", $property);

            if ($propertyValue[0] == "text-align")
            {
                $this->align = $propertyValue[1];
            }
        }

        $this->content = $doc->saveHTML($paraNode);
        
        foreach ($this->processImage($this->content) as $key => $image)
        {
            $this->imgs[] = $image;
        }
        
        foreach ($this->processSubordinate($this->content) as $key => $subordinates)
        {
            $this->subordinates[] = $subordinates;
        }

        return $this;
    }

    /**
     * This method is an abstract method inherited from EditorElement.  Its main purpose is to
     * insert the data obtained from the POST object via method above to the msm_para table and to 
     * insert structural data (its parent/sibling...etc) to the compositor table. This method also calls 
     * insertData method from EditorSubordinate class.
     * 
     * @global moodle_database $DB
     * @param integer $parentid         Database ID from msm_compositor of the parent element
     * @param integer $siblingid        Database ID from msm_compositor of the previous sibling element
     * @param integer $msmid            The instance ID of the MSM module.
     */
    function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        if (!empty($this->align))
        {
            $data->para_align = $this->align;
        }
        $data->para_content = $this->content;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

        $subordinate_sibling = 0;
        $img_sibling = 0;

        foreach ($this->imgs as $key=>$img)
        {
            $img->insertData($this->compid, $img_sibling, $msmid);
            $img_sibling = $img->compid;
            
            $content = $this->replaceImages($key, $img, $this->content, "p");
        }
        $this->content = $content;
        
        $data->id = $this->id;
        $data->para_content = $this->content;
        $this->id = $DB->update_record($this->tablename, $data);

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
        $htmlContent .= $this->content;
        return $htmlContent;
    }

    /**
     * This abstract method from EditoElement extracts appropriate information from the 
     * msm_para table and also triggers extraction of data from its children using the 
     * data given by the msm_compositor table. It calls the loadData method from the EditorSubordinate 
     * class.
     * 
     * @global moodle_database $DB
     * @param integer $compid           The database ID from the msm_compositor table
     * @return \EditorPara
     */
    public function loadData($compid)
    {
        global $DB;

        $paraCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $paraCompRecord->unit_id;

        $paraRecord = $DB->get_record($this->tablename, array('id' => $this->id));

        $this->align = $paraRecord->para_align;
        $this->content = $paraRecord->para_content;
        $this->description = $paraRecord->description;

        $childRecords = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');

        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            if ($childTable->tablename == 'msm_subordinate')
            {
                $subordinate = new EditorSubordinate();
                $subordinate->loadData($child->id);
                $this->subordinates[] = $subordinate;
            }
            else
            {
                echo "another child of para? " . $childTable->tablename;
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

        $previewHtml .= $this->content;

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
