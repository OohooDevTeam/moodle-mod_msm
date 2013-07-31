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
 *  EditorIntro class inherits from the EditorElement class and it represents the
 * intro elements in the MSM editor.  It is called by EditorUnit.
 * 
 */
class EditorIntro extends EditorElement
{

    public $position;
    public $id;
    public $compid;
    public $title;
    public $errorArray = array();
    public $blocks = array();

    function __construct()
    {
        $this->tablename = "msm_intro";
    }

    /**
     * This method is an abstract method inherited from EditorElement.  It finds the needed information for database table
     * from the POST object(from editor form submission).  It calls the same method from another class(EditorBlock) to process its
     * children's data.
     * 
     * @param string $idNumber          contains parent_HTML_id ending number
     * @return \EditorIntro
     */
    public function getFormData($idNumber)
    {
        $intromatch = '/^msm_intro_content_.*/';
        $childmatch = '/^msm_intro_child_content-.*/';

        $i = 0;
        foreach ($_POST as $elementID => $value)
        {
            if ((preg_match($intromatch, $elementID)) || (preg_match($childmatch, $elementID)))
            {
                $block = new EditorBlock();
                $block->getFormData($elementID);

                $this->blocks[] = $block;
            }
            $i++;
        }

        return $this;
    }

     /**
     * This method is an abstract method inherited from EditorElement.  Its main purpose is to
     * insert the data obtained from the POST object via method above to the msm_intro table and to 
     * insert structural data (its parent/sibling...etc) to the compositor table. This method also calls 
     * insertData method from EditorBlock class.
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
        $data->intro_caption = $this->blocks[0]->title;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

        $sibling_id = 0;

        foreach ($this->blocks as $block)
        {
            $block->insertData($this->compid, $sibling_id, $msmid);
            $sibling_id = $block->compid;
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
        $htmlContent .= "<div id='copied_msm_intro-$this->compid' class='copied_msm_structural_element' style='padding-top:2%;'>";

        $htmlContent .= "<div class='msm_element_overlays' id='msm_element_overlay-$this->compid' style='display: none;'>";

        $htmlContent .= "<a class='msm_overlayButtons' id='msm_overlayButton_delete-$this->compid' onclick='deleteOverlayElement(event);'> Delete </a>";
        $htmlContent .= "<a class='msm_overlayButtons' id='msm_overlayButton_edit-$this->compid' onclick='editUnit(event);'> Edit </a>";

        $htmlContent .= "</div>";

        $htmlContent .= "<div id='msm_element_title_container-$this->compid' class='msm_element_title_containers'>";
        $htmlContent .= "<b style='margin-left: 43%; margin-right: 0%; margin-top: 2%; margin-bottom: 2%;'> INTRODUCTION </b>";
        $htmlContent .= "<span style='visibility: hidden;'>Drag here to move this element.</span>";
        $htmlContent .= "</div>";

        $htmlContent .= "<div style='margin-top: 2%;'>";
        $htmlContent .= "<label id='msm_intro_title_label-$this->compid' class='msm_unit_intro_title_labels' for='msm_intro_title_input-$this->compid'>Title: </label>";
        $htmlContent .= "<input id='msm_intro_title_input-$this->compid' class='msm_unit_intro_title' placeholder='Optional Title for the introduction.' name='msm_intro_title_input-$this->compid' disabled='disabled' value='$this->title'/>";
        $htmlContent .= "</div>";

        $htmlContent .= "<div id='msm_intro_content_input-$this->compid' class='msm_unit_child_content msm_editor_content'>";

        // this intro has child content elements
        if (!empty($this->children))
        {
            foreach ($this->children as $content)
            {
                $htmlContent .= $content->displayData();
            }
            $htmlContent .= "</div>";


            $htmlContent .= "<div class='msm_subordinate_containers' id='msm_subordinate_container-introcontent$this->compid'>";
            $htmlContent .= "</div>";

            $htmlContent .= "<div class='msm_subordinate_result_containers' id='msm_subordinate_result_container-introcontent$this->compid'>";
            foreach ($this->children as $content)
            {
                foreach ($content->subordinates as $subordinate)
                {
                    $htmlContent .= $subordinate->displayData($this->compid);
                }
            }
            $htmlContent .= "</div>";

            $htmlContent .= "<div id='msm_intro_child_container'>";
        }
        else // this intro does not have any child contents
        {
            foreach ($this->blocks[0]->content as $content)
            {
                $htmlContent .= $content->displayData();
            }
            $htmlContent .= "</div>";
            
            $htmlContent .= "<div class='msm_subordinate_containers' id='msm_subordinate_container-introcontent$this->compid'>";
            $htmlContent .= "</div>";

            $htmlContent .= "<div class='msm_subordinate_result_containers' id='msm_subordinate_result_container-introcontent$this->compid'>";
            foreach ($this->blocks[0]->content as $content)
            {
                foreach ($content->subordinates as $subordinate)
                {
                    $htmlContent .= $subordinate->displayData($this->compid);
                }
            }
            $htmlContent .= "</div>";

            $htmlContent .= "<div id='msm_intro_child_container'>";
            for ($i = 1; $i < sizeof($this->blocks); $i++)
            {
                $htmlContent .= $this->blocks[$i]->displayData();
            }
        }


        $htmlContent .= "</div>";
        $htmlContent .= "<div class='msm_dnd_containers' id='msm_dnd_container-$this->compid'>Drag additional content to here.<p>Valid child Elements: Extra Contents</p></div>";
//        $htmlContent .= "<input id='msm_intro_child_button-$this->compid' class='msm_intro_child_buttons' type='button' value='Add additional content' onclick='addIntroContent($this->compid)' disabled='disabled'/>";
        $htmlContent .= "</div>";

        return $htmlContent;
    }

    /**
     * This abstract method from EditorElement extracts appropriate information from the 
     * msm_content table and also triggers extraction of data from its children using the 
     * data given by the msm_compositor table. It calls the loadData method from the EditorSubordinate 
     * class.
     * 
     * @global moodle_database $DB
     * @param string $compid            string of all ids in msm_compositor with table_id associated with id of 
     *                                  msm_intro table under the same parent_id(ie. from the same unit)
     * @return \EditorIntro
     */
    public function loadData($compid)
    {
        global $DB;

        $this->children = array();

        $introCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $introCompRecord->unit_id;

        $introRecord = $DB->get_record($this->tablename, array('id' => $this->id));

        $this->title = $introRecord->intro_caption;

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            switch ($childTable->tablename)
            {
                case "msm_block":
                    $block = new EditorBlock();
                    $block->loadData($child->id);
                    $this->blocks[] = $block;
                    break;
                // need these for the legacy material to load properly for now
                // due to changing the db structure to include block table
                // delete after demo
                case "msm_para":
                    $para = new EditorPara();
                    $para->loadData($child->id);
                    $this->children[] = $para;
                    break;
                case "msm_content":
                    $inContent = new EditorInContent();
                    $inContent->loadData($child->id);
                    $this->children[] = $inContent;
                    break;
                case "msm_table":
                    $table = new EditorTable();
                    $table->loadData($child->id);
                    $this->children[] = $table;
                    break;
            }
        }

        return $this;
    }

    /**
     * This method is triggered when the View navigation button on the editor is clicked to show the preview of the unit to the user.
     * It generates the appropriate HTML code to display the information as it is layed out on the MSM editor not according to how
     * the elements are structured in the database.  Hence allowing user to preview the material while making changes without having to 
     * committ to saving it in the database.
     * 
     * @return HTML string
     */
    public function displayPreview($id = '')
    {
        $previewHtml = '';

        if (!empty($this->title))
        {
            $previewHtml .= "<h3>$this->title</h3>";
        }

        foreach ($this->blocks as $key => $block)
        {
            $previewHtml .= $block->displayPreview($id);
        }

        return $previewHtml;
    }

}

?>
