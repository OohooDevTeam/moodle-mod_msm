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
 * EditorBlock class is inherited from the abstract class EditorElement and contains 
 * abstract methods (getFormData, insertData, and loadData).  This class
 * represents all the content block of unit composition(ie. body of the unit/intro). It is
 * a parent class for all content related classes such as EditorPara/EditorInContent and EditorTable.
 *
 */
class EditorBlock extends EditorElement
{

    public $title;                      // title input associated with this block element
    public $id;                         // database ID of this block element in msm_block table
    public $compid;                     // database ID of this block element in msm_compositor table
    public $errorArray = array();       // contains the id of the component with an empty/null content
    public $content = array();          // content elements associated with this block (eg. EditorPara, EditorInContent...etc)
    public $type;                       // indicates the parent of the block(from EditorIntro object/EditorUnit object)

    // and is used to do separate display process 
    // constructor for the class

    function __construct()
    {
        $this->tablename = "msm_block";
    }

    /**
     * This method is an abstract method from EditorElement that extracts 
     * appropriate information from the POST object.  It calls two methods,
     * processIntroContent and processBodyContent, to process blocks in two different
     * situations.  One case is that the block has EditorIntro as a parent in which case
     * it is processing contents for the intro element of the editor.  Second case is when
     * the block has EditorUnit as a parent which means that the block is a component of 
     * the unit.  For more information on these methods, read comments for those two
     * methods.
     * 
     * @param string $idNumber          A key of the POST object
     * @return \EditorBlock
     */
    public function getFormData($idNumber)
    {
        $intromatch = '/^msm_intro_.*/';
        $bodymatch = '/^copied_msm_body-.*/';
        $extramatch = '/^msm_extra_.*/';

        if (preg_match($intromatch, $idNumber))
        {
            $this->processIntroContent($idNumber);
        }
        else if (preg_match($bodymatch, $idNumber))
        {
            $this->processBodyContent($idNumber);
        }
        else if (preg_match($extramatch, $idNumber))
        {
            $this->processExtraContent($idNumber);
        }

        return $this;
    }

    /**
     * This method is to process blocks that are part of the intro element
     * in the unit.  It looks for specific keys of the POST object to 
     * obtain the appropriate information to be inserted to the database tables.
     * This method calls the processContent method from the EditorElement to call
     * getFormData methods for all the other content related classes.
     * (ie. EditorPara/EditorInContent and EditorTable)
     * 
     * @param string $idNumber          A key of the POST object
     * @return \EditorBlock
     */
    function processIntroContent($idNumber)
    {
        $match = "/^msm_intro_child_.*/";

        $idInfo = explode("-", $idNumber);

        if (preg_match($match, $idNumber))
        {
            if (!empty($_POST['msm_intro_child_title-' . $idInfo[1]]))
            {
                $this->title = $this->processMath($_POST['msm_intro_child_title-' . $idInfo[1]]);
            }

            if (!empty($_POST['msm_intro_child_content-' . $idInfo[1]]))
            {
                $rawintrocontent = $_POST['msm_intro_child_content-' . $idInfo[1]];

                foreach ($this->processContent($rawintrocontent) as $content)
                {
                    $this->content[] = $content;
                }
            }
            else
            {
                // empty content needs to be flagged to give user a warning
                $this->errorArray[] = 'msm_intro_child_content-' . $idInfo[1] . '_ifr';
            }
        }
        else
        {
            if (!empty($_POST['msm_intro_title_input-' . $idInfo[1]]))
            {
                $this->title = $this->processMath($_POST['msm_intro_title_input-' . $idInfo[1]]);
            }
            if (!empty($_POST['msm_intro_content_input-' . $idInfo[1]]))
            {
                $rawintrocontent = $_POST['msm_intro_content_input-' . $idInfo[1]];

                foreach ($this->processContent($rawintrocontent) as $content)
                {
                    $this->content[] = $content;
                }
            }
            else
            {
                // empty content needs to be flagged to give user a warning
                $this->errorArray[] = 'msm_intro_content_input-' . $idInfo[1] . '_ifr';
            }
        }

        $this->type = "intro";

        return $this;
    }

    /**
     * This method is to process blocks that are part of the unit element.
     * It looks for specific keys of the POST object to 
     * obtain the appropriate information to be inserted to the database tables.
     * This method calls the processContent method from the EditorElement to call
     * getFormData methods for all the other content related classes.
     * (ie. EditorPara/EditorInContent and EditorTable)
     * 
     * @param string $formKey          A key of the POST object
     * @return \EditorBlock
     */
    function processBodyContent($formKey)
    {
        $idInfo = explode("-", $formKey);

        if (!empty($_POST['msm_body_title_input-' . $idInfo[1]]))
        {
            $this->title = $this->processMath($_POST['msm_body_title_input-' . $idInfo[1]]);
        }

        if (!empty($_POST['msm_body_content_input-' . $idInfo[1]]))
        {
            $rawintrocontent = $_POST['msm_body_content_input-' . $idInfo[1]];

            foreach ($this->processContent($rawintrocontent) as $content)
            {
                $this->content[] = $content;
            }
        }
        else
        {
            // empty content needs to be flagged to give user a warning
            $this->errorArray[] = 'msm_body_content_input-' . $idInfo[1] . '_ifr';
        }

        $this->type = "unit";

        return $this;
    }

    /**
     * This method is to process blocks that are part of the exra content elements.
     * (eg. prefaces/acknowledgement...etc that are represented by EditorExtraInfo);
     * It looks for specific keys of the POST object to 
     * obtain the appropriate information to be inserted to the database tables.
     * This method calls the processContent method from the EditorElement to call
     * getFormData methods for all the other content related classes.
     * (ie. EditorPara/EditorInContent and EditorTable)
     * 
     * @param string $formKey               A key of the POST object
     * @return \EditorBlock
     */
    function processExtraContent($formKey)
    {
        $idInfo = explode("-", $formKey);

        if (!empty($_POST['msm_extra_title_input-' . $idInfo[1]]))
        {
            $this->title = $this->processMath($_POST['msm_extra_title_input-' . $idInfo[1]]);
        }

        if (!empty($_POST['msm_extra_content_input-' . $idInfo[1]]))
        {
            $rawextracontent = $_POST['msm_extra_content_input-' . $idInfo[1]];

            foreach ($this->processContent("<div>" . $rawextracontent . "</div>") as $content)
            {
                $this->content[] = $content;
            }
        }
        else
        {
            // empty content needs to be flagged to give user a warning
            $this->errorArray[] = 'msm_extra_content_input-' . $idInfo[1] . '_ifr';
        }

        $this->type = "extra";

        return $this;
    }

    /**
     * This method is an abstract method inherited from EditorElement.  Its main purpose is to
     * insert the data obtained from the POST object via method above to the msm_block table and to 
     * insert structural data (its parent/sibling...etc) to the compositor table. This method also calls 
     * insertData method from EditorPara/EditorInContent/EditorTable class.
     * 
     * @global moodle_database $DB
     * @param integer $parentid         The ID of the parent of this object(ie. Intro/Unit) in compositor table.
     * @param integer $siblingid        The ID of the previous sibling of this object in compositor table.
     * @param integer $msmid            The instance ID of the MSM module.
     */
    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $data->block_caption = $this->title;
        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->table_id = $DB->get_record('msm_table_collection', array("tablename" => $this->tablename))->id;
        $compData->unit_id = $this->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record("msm_compositor", $compData);

        $sibling_id = 0;
        foreach ($this->content as $content)
        {
            $content->insertData($this->compid, $sibling_id, $msmid);
            $sibling_id = $content->compid;
        }
    }

    /**
     * This has a purpose of displaying the data extracted from DB from loadData method
     * by outputting the HTML code.  This method calls displayData from the 
     * EdtiorPara/EditorInContent/EditorTable class.
     * 
     * @return HTML string
     */
    public function displayData($name = '')
    {
        $htmlContent = '';

        // this method was called by EditorIntro and needs to have different HTML IDs and class names to
        // identify it when editting the form...etc
        if ($this->type == 'msm_intro')
        {
            $htmlContent .= "<div id='msm_intro_child_div-$this->compid' class='msm_intro_child'>";

            $htmlContent .= "<div id='msm_intro_child_dragarea-$this->compid' class='msm_intro_child_dragareas'>";
            $htmlContent .= "<span style='visibility: hidden;'>Drag here to move this element.</span>";
            $htmlContent .= "</div>";

//            $htmlContent .= "<div>";
            $htmlContent .= "<label class='msm_intro_child_title_labels'>Title: </label>";
            $htmlContent .= "<div id='msm_intro_child_title-$this->compid' class='msm_intro_child_titles msm_editor_titles'>";

            if (strpos($this->title, "<div/>") !== false)
            {
                $introChildTitle = '';
            }
            else
            {
                $introChildTitle = $this->title;
            }

            $htmlContent .= $introChildTitle;
            $htmlContent .= "</div>"; // end of title input div     
//            $htmlContent .= "<input id='msm_intro_child_title-$this->compid' class='msm_intro_child_titles' name='msm_intro_child_title-$this->compid' placeholder='Optional Title for the Content' disabled='disabled' value='$this->title'/>";
//            $htmlContent .= "</div>";

            $htmlContent .= "<div id='msm_intro_child_content-$this->compid' class='msm_intro_child_contents msm_editor_content'>";
            foreach ($this->content as $content)
            {
                $htmlContent .= $content->displayData();
            }
            $htmlContent .= "</div>";

            $htmlContent .= "<div class='msm_subordinate_containers' id='msm_subordinate_container-introchild$this->compid'>";
            $htmlContent .= "</div>";

            $htmlContent .= "<div class='msm_subordinate_result_containers' id='msm_subordinate_result_container-introchild$this->compid'>";
            foreach ($this->content as $content)
            {
                foreach ($content->subordinates as $subordinate)
                {
                    $htmlContent .= $subordinate->displayData($this->compid);
                }
            }
            $htmlContent .= "</div>";

            $htmlContent .= "</div>";
        }
        // this method was called by EditorUnit and needs to have different HTML IDs and class names to
        // identify it when editting the form...etc
        else if ($this->type == 'msm_unit')
        {
            $htmlContent .= "<div id='copied_msm_body-$this->compid' class='copied_msm_structural_element' style='padding-top: 2%;'>";
            $htmlContent .= "<div class='msm_element_overlays' id='msm_element_overlay-$this->compid' style='display: none;'>";

            $htmlContent .= "<a class='msm_overlayButtons' id='msm_overlayButton_delete-$this->compid' onclick='deleteOverlayElement(event);'> Delete </a>";
            $htmlContent .= "<a class='msm_overlayButtons' id='msm_overlayButton_edit-$this->compid' onclick='editUnit(event)'> Edit </a>";

            $htmlContent .= "</div>";
            $htmlContent .= "<div id='msm_element_title_container-$this->compid' class='msm_element_title_containers'>";
            $htmlContent .= "<b style='margin-left: 45%; margin-right: 0%; margin-top: 2%; margin-bottom: 2%;'> CONTENT </b>";
            $htmlContent .= "<span style='visibility: hidden;'>Drag here to move this element.</span>";
            $htmlContent .= "</div>";

            $htmlContent .= "<div style='margin-top: 2%;'>";
            $htmlContent .= "<label id='msm_body_title_label-$this->compid' class='msm_unit_body_title_labels' for='msm_body_title_input-$this->compid'>Title: </label>";
            
             $htmlContent .= "<div id='msm_body_title_input-$this->compid' class='msm_unit_body_title msm_editor_titles'>";

            if (strpos($this->title, "<div/>") !== false)
            {
                $bodyTitle = '';
            }
            else
            {
                $bodyTitle = $this->title;
            }

            $htmlContent .= $bodyTitle;
            $htmlContent .= "</div>"; // end of title input div     
            
//            $htmlContent .= "<input id='msm_body_title_input-$this->compid' class='msm_unit_body_title' placeholder='Optional Title for this Content' name='msm_body_title_input-$this->compid' disabled='disabled' value='$this->title'/>";
            $htmlContent .= "</div>";

            $htmlContent .= "<div id='msm_body_content_input-$this->compid' class='msm_unit_child_content msm_editor_content'>";

            foreach ($this->content as $content)
            {
                $htmlContent .= $content->displayData();
            }

            $htmlContent .= "</div>";

            $htmlContent .= "<div class='msm_subordinate_containers' id='msm_subordinate_container-bodycontent$this->compid'>";
            $htmlContent .= "</div>";

            $htmlContent .= "<div class='msm_subordinate_result_containers' id='msm_subordinate_result_container-bodycontent$this->compid'>";
            foreach ($this->content as $content)
            {
                if (isset($content->subordinates))
                {
                    foreach ($content->subordinates as $subordinate)
                    {
                        $htmlContent .= $subordinate->displayData($this->compid);
                    }
                }
            }
            $htmlContent .= "</div>";

            $htmlContent .= "</div>";
        }

        // the method to display block elements associated with extra content elements (ie. elements represented by EditorExtraInfo objects)
        // is in EditorExtraInfo class

        return $htmlContent;
    }

    /**
     * This abstract method from EditorElement extracts appropriate information from the 
     * msm_block table and also triggers extraction of data from its children using the 
     * data given by the msm_compositor table. It calls the loadData method from the 
     * EditorPara/EditorInContent/EditorTable class.
     * 
     * @global moodle_database $DB
     * @param integer $compid           The database ID from the msm_compositor table
     * @return \EditorBlock
     */
    public function loadData($compid)
    {
        global $DB;

        $blockCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $blockCompRecord->id;
        $this->id = $blockCompRecord->unit_id;

        $blockParentRecord = $DB->get_record('msm_compositor', array('id' => $blockCompRecord->parent_id));
        $blockParentTable = $DB->get_record("msm_table_collection", array('id' => $blockParentRecord->table_id));

        $this->type = $blockParentTable->tablename;

        $blockRecord = $DB->get_record($this->tablename, array('id' => $this->id));

        $this->title = $blockRecord->block_caption;

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            switch ($childTable->tablename)
            {
                case "msm_para":
                    $para = new EditorPara();
                    $para->loadData($child->id);
                    $this->content[] = $para;
                    break;
                case "msm_content":
                    $inContent = new EditorInContent();
                    $inContent->loadData($child->id);
                    $this->content[] = $inContent;
                    break;
                case "msm_table":
                    $table = new EditorTable();
                    $table->loadData($child->id);
                    $this->content[] = $table;
                    break;
                case "msm_media":
                    $media = new EditorMedia();
                    $media->loadData($child->id);
                    $this->content[] = $media;
                    break;
                default:
                    echo "what tablename? " . $childTable->tablename;
                    break;
            }
        }

        return $this;
    }

    /**
     * This method is triggered when the View navigation button on the editor is clicked to show the preview of the unit to the user.
     * The method in this class is called by Unit/Intro and is responsible for showing the appropriate components of the block element.
     * This method, in turn, calls the displayPreview of the EditorPara/EditorInContent/EditorTable to display the appropriate UI dialog windows.
     * 
     * @param string $id
     * @return HTML string
     */
    public function displayPreview($id = '')
    {
        $previewHtml = '';

        if (!empty($this->title))
        {
            $previewHtml .= "<h3>$this->title</h3>";
        }

        foreach ($this->content as $key => $child)
        {
            $previewHtml .= $child->displayPreview($key);
        }

        return $previewHtml;
    }

}

?>
