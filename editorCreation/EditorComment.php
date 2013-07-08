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
 * EditorComment class inherits from the EditorElement class and it represents the
 * comment elements in the MSM editor.  This class can be representing comment element as 
 * a child of unit element or it can also be a reference meterial linked by an associate
 * element.
 *
 */
class EditorComment extends EditorElement
{

    public $type;
    public $description;
    public $title;
    public $id;
    public $compid;
    public $errorArray = array(); // ids to indicate empty content
    public $children = array(); //associate
    public $subordinates = array();
    public $medias = array();

    // constructor for the class
    function __construct()
    {
        $this->tablename = "msm_comment";
    }

    /**
     * This method is an abstract method inherited from EditorElement.  It finds the needed information for database table
     * from the POST object(from editor form submission).  It calls the same method from another class(EditorAssociate) to process its
     * children's data.
     * 
     * @param string $idNumber          represents information needed to identify needed key for the POST object(for reference material, has "|ref" ending)
     * @return \EditorComment
     */
    public function getFormData($idNumber)
    {
        $idInfo = explode("|", $idNumber);

        // the idNumber param contains "|ref" ending meaning that this comment is a reference material
        if (sizeof($idInfo) > 1)
        {
            $match = "/^msm_commentref_content_input-$idInfo[0].*$/";

            $newId = '';
            foreach ($_POST as $id => $value)
            {
                if (preg_match($match, $id))
                {
                    $tempidInfo = explode("-", $id);
                    for ($i = 1; $i < sizeof($tempidInfo) - 1; $i++)
                    {
                        $newId .= $tempidInfo[$i] . "-";
                    }
                    $newId .= $tempidInfo[sizeof($tempidInfo) - 1];
                    break;
                }
            }

            $this->type = $_POST['msm_commentref_type_dropdown-' . $newId];
            $this->description = $_POST['msm_commentref_description_input-' . $newId];
            $this->title = $_POST['msm_commentref_title_input-' . $newId];

            if ($_POST['msm_commentref_content_input-' . $newId] != '')
            {
                $content = $_POST['msm_commentref_content_input-' . $newId];
                $this->content = $this->processMath($content);

                foreach ($this->processImage($this->content) as $key => $media)
                {
                    $this->medias[] = $media;
                }

                // grab all anchored elements in content --> it is only from subordinate
                foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                {
                    $this->subordinates[] = $subordinates;
                }
            }
            else
            {
                // empty content
                $this->errorArray[] = 'msm_commentref_content_input-' . $newId . "_ifr";
            }
        }
        // the idNumber param does not contain "|ref" ending meaning that this comment is part of unit element
        else if (sizeof($idInfo) == 1)
        {
            $this->type = $_POST['msm_comment_type_dropdown-' . $idNumber];
            $this->description = $_POST['msm_comment_description_input-' . $idNumber];
            $this->title = $_POST['msm_comment_title_input-' . $idNumber];

            $this->errorArray = array();

            if ($_POST['msm_comment_content_input-' . $idNumber] != '')
            {
                $content = $_POST['msm_comment_content_input-' . $idNumber];
                $this->content = $this->processMath($content);

                foreach ($this->processImage($this->content) as $key => $media)
                {
                    $this->medias[] = $media;
                }

                // grab all anchored elements in content --> it is only from subordinate
                foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                {
                    $this->subordinates[] = $subordinates;
                }
            }
            else
            {
                // empty content
                $this->errorArray[] = 'msm_comment_content_input-' . $idNumber . "_ifr";
            }

            $match = "/^msm_associate_dropdown-$idNumber-(\d+)/";

            $i = 0;

            foreach ($_POST as $id => $value)
            {
                if (preg_match($match, $id))
                {
                    $idInfo = explode("-", $id);
                    $indexNumber = $idInfo[1] . "-" . $idInfo[2];
                    $associate = new EditorAssociate();
                    $associate->getFormData($indexNumber);
                    $this->children[] = $associate;
                    $i++;
                }
            }

            return $this;
        }
    }

    /**
     * This method is an abstract method inherited from EditorElement.  Its main purpose is to
     * insert the data obtained from the POST object via method above to the msm_comment table and to 
     * insert structural data (its parent/sibling...etc) to the compositor table. This method also calls 
     * insertData method from EditorAssociate and EditorSubordinate classes.
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
        $data->comment_type = $this->type;
        $data->caption = $this->title;

        $cParser = new DOMDocument();
        $cParser->loadHTML($this->content);
        $divs = $cParser->getElementsByTagName("div");

        if ($divs->length > 0)
        {
            $data->comment_content = $this->content;
        }
        else
        {
            $data->comment_content = "<div>$this->content</div>";
        }

        $data->description = $this->description;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

        $sibling_id = 0;

        foreach ($this->children as $associate)
        {
            $associate->insertData($this->compid, $sibling_id, $msmid);
            $sibling_id = $associate->compid;
        }

        $media_sibliing = 0;
        $content = '';
        foreach ($this->medias as $key => $media)
        {
            $media->insertData($this->compid, $media_sibliing, $msmid);
            $media_sibliing = $media->compid;
            $content = $this->replaceImages($key, $media->image, $data->comment_content, "div");
        }
        if (!empty($this->medias))
        {
            $this->content = $content;

            $data->id = $this->id;
            $data->comment_content = $this->content;
            $this->id = $DB->update_record($this->tablename, $data);
        }

        $subordinate_sibling = 0;
        foreach ($this->subordinates as $subordinate)
        {
            $subordinate->insertData($this->compid, $subordinate_sibling, $msmid);
            $subordinate_sibling = $subordinate->compid;
        }
    }

    /**
     * This method is an abstract method from EditorElement that has a purpose of displaying the 
     * data extracted from DB from loadData method by outputting the HTML code.  This method calls 
     * displayData from the EditorAssociate class.
     * 
     * @return HTML string
     */
    public function displayData()
    {
        $htmlContent = '';

        $htmlContent .= "<div id='copied_msm_comment-$this->compid' class='copied_msm_structural_element'>";

        $htmlContent .= "<div class='msm_element_overlays' id='msm_element_overlay-$this->compid' style='display: none;'>";

        $htmlContent .= "<a class='msm_overlayButtons' id='msm_overlayButton_delete-$this->compid' onclick='deleteOverlayElement(event);'> Delete </a>";
        $htmlContent .= "<a class='msm_overlayButtons' id='msm_overlayButton_edit-$this->compid' onclick='editUnit(event);'> Edit </a>";

        $htmlContent .= "</div>";

        $htmlContent .= "<select id='msm_comment_type_dropdown-$this->compid' class='msm_unit_child_dropdown' name='msm_comment_type_dropdown-$this->compid' disabled='disabled'>";

        switch ($this->type)
        {
            case "Comment":
                $htmlContent .= "<option value='Comment' selected='selected'>Comment</option>";
                $htmlContent .= "<option value='Remark'>Remark</option>";
                $htmlContent .= "<option value='Information'>Information</option>";
                break;
            case "Remark":
                $htmlContent .= "<option value='Comment'>Comment</option>";
                $htmlContent .= "<option value='Remark' selected='selected'>Remark</option>";
                $htmlContent .= "<option value='Information'>Information</option>";
                break;
            case "Information":
                $htmlContent .= "<option value='Comment'>Comment</option>";
                $htmlContent .= "<option value='Remark'>Remark</option>";
                $htmlContent .= "<option value='Information' selected='selected'>Information</option>";
                break;
        }
        $htmlContent .= "</select>";

        $htmlContent .= "<div id='msm_element_title_container-$this->compid' class='msm_element_title_containers'>";
        $htmlContent .= "<b style='margin-left: 30%;'> COMMENT </b>";
        $htmlContent .= "<span style='visibility: hidden;'>Drag here to move this element.</span>";
        $htmlContent .= "</div>";
        $htmlContent .= "<input id='msm_comment_title_input-$this->compid' class='msm_unit_child_title' placeholder='Title of Comment' name='msm_comment_title_input-$this->compid' disabled='disabled' value='$this->title'/>";
        $htmlContent .= "<div id='msm_comment_content_input-$this->compid' class='msm_unit_child_content msm_editor_content'>";
        $htmlContent .= html_entity_decode($this->content);
        $htmlContent .= "</div>";

        $htmlContent .= "<div class='msm_subordinate_containers' id='msm_subordinate_container-commentcontent$this->compid'>";
        $htmlContent .= "</div>";
        $htmlContent .= "<div class='msm_subordinate_result_containers' id='msm_subordinate_result_container-commentcontent$this->compid'>";
        foreach ($this->subordinates as $subordinate)
        {
            $htmlContent .= $subordinate->displayData($this->compid);
        }
        $htmlContent .= "</div>";

        $htmlContent .= "<label id='msm_comment_description_label-$this->compid' class='msm_child_description_labels' for='msm_comment_description_input-$this->compid'>Description: </label>";
        $htmlContent .= "<input id='msm_comment_description_input-$this->compid' class='msm_child_description_inputs' placeholder='Insert description to search this element in future.' value='$this->description' disabled='disabled' name='msm_comment_description_input-$this->compid'/>";

        $htmlContent .= "<div id='msm_associate_container-$this->compid' class='msm_associate_containers'>";
        foreach ($this->children as $associate)
        {
            $htmlContent .= $associate->displayData();
        }
//        $htmlContent .= "<input id='msm_associate_button-$this->compid' class='msm_associate_buttons' type='button' value='Add Associated Information' onclick='addAssociateForm($this->compid, \"comment\")' disabled='disabled'/>";
        $htmlContent .= "</div>";
        
        $htmlContent .= "<div class='msm_dnd_containers' id='msm_dnd_container-$this->compid'>Drag additional content to here.<p>Valid child Elements: Associates, internal and/or external references</p></div>";

        $htmlContent .= "</div>";

        return $htmlContent;
    }

    /**
     * This abstract method from EditoElement extracts appropriate information from the 
     * msm_comment table and also triggers extraction of data from its children using the 
     * data given by the msm_compositor table. It calls the loadData method from the EditorAssociate 
     * class.
     * 
     * @global moodle_database $DB
     * @param integer $compid           The database ID from the msm_compositor table
     * @return \EditorComment
     */
    public function loadData($compid)
    {
        global $DB;

        $commentCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $commentCompRecord->unit_id;

        $commentRecord = $DB->get_record($this->tablename, array('id' => $this->id));

        $this->type = $commentRecord->comment_type;
        $this->title = $commentRecord->caption;
        $this->content = $commentRecord->comment_content;
        $this->description = $commentRecord->description;

        $childRecords = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            switch ($childTable->tablename)
            {
                case "msm_associate":
                    $associate = new EditorAssociate();
                    $associate->loadData($child->id);
                    $this->children[] = $associate;
                    break;

                case "msm_subordinate":
                    $subordinate = new EditorSubordinate();
                    $subordinate->loadData($child->id);
                    $this->subordinates[] = $subordinate;
                    break;
                //add subordinate later
            }
        }

        // need to process content to find all <a> and match with subordinate data..etc
        // need to process child elements

        return $this;
    }

    /**
     * This method is called by the EditorInfo class to display the comment as a reference material.
     * The information is hidden until the user triggers the display by clicking on the associate mini buttons.
     * 
     * @global moodle_database $DB
     * @param string $parentId          End of HTML ID that made the parent(ie. associate) HTML element unique
     * @return HTML string
     */
    function displayRefData($parentId)
    {
        global $DB;

        $htmlContent = '';

        $htmlContent .= "<div id='copied_msm_commentref-$parentId-$this->compid' class='copied_msm_structural_element'>";
        $htmlContent .= "<select id='msm_commentref_type_dropdown-$parentId-$this->compid' class='msm_unit_child_dropdown' name='msm_commentref_type_dropdown-$parentId-$this->compid' disabled='disabled'>";

        switch ($this->type)
        {
            case "Comment":
                $htmlContent .= "<option value='Comment' selected='selected'>Comment</option>";
                $htmlContent .= "<option value='Remark'>Remark</option>";
                $htmlContent .= "<option value='Information'>Information</option>";
                break;
            case "Remark":
                $htmlContent .= "<option value='Comment'>Comment</option>";
                $htmlContent .= "<option value='Remark' selected='selected'>Remark</option>";
                $htmlContent .= "<option value='Information'>Information</option>";
                break;
            case "Information":
                $htmlContent .= "<option value='Comment'>Comment</option>";
                $htmlContent .= "<option value='Remark'>Remark</option>";
                $htmlContent .= "<option value='Information' selected='selected'>Information</option>";
                break;
        }
        $htmlContent .= "</select>";

        $htmlContent .= "<div id='msm_element_title_container-$parentId-$this->compid' class='msm_element_title_containers'>";
        $htmlContent .= "<b style='margin-left: 30%;'> COMMENT </b>";
        $htmlContent .= "</div>";
        $htmlContent .= "<input id='msm_commentref_title_input-$parentId-$this->compid' class='msm_unit_child_title' placeholder='Title of Comment' name='msm_commentref_title_input-$parentId-$this->compid' disabled='disabled' value='$this->title'/>";
        $htmlContent .= "<div id='msm_commentref_content_input-$parentId-$this->compid' class='msm_unit_child_content msm_editor_content'>";
        $htmlContent .= html_entity_decode($this->content);
        $htmlContent .= "</div>";

        $htmlContent .= "<div class='msm_subordinate_containers' id='msm_subordinate_container-commentrefcontent$parentId-$this->compid'>";
        $htmlContent .= "</div>";
        $htmlContent .= "<div class='msm_subordinate_result_containers' id='msm_subordinate_result_container-commentrefcontent$parentId-$this->compid'>";
        foreach ($this->subordinates as $subordinate)
        {
            $htmlContent .= $subordinate->displayData("$parentId-$this->compid");
        }
        $htmlContent .= "</div>";

        $htmlContent .= "<label id='msm_commentref_description_label-$parentId-$this->compid' class='msm_child_description_labels' for='msm_commentref_description_input-$parentId-$this->compid'>Description: </label>";
        $htmlContent .= "<input id='msm_commentref_description_input-$parentId-$this->compid' class='msm_child_description_inputs' placeholder='Insert description to search this element in future.' value='$this->description' disabled='disabled' name='msm_commentref_description_input-$parentId-$this->compid'/>";
        $htmlContent .= "</div>";

        return $htmlContent;
    }

    /**
     * This method is triggered when the View navigation button on the editor is clicked to show the preview of the unit to the user.
     * It generates the appropriate HTML code to display the information as it is layed out on the MSM editor not according to how
     * the elements are structured in the database.  Hence allowing user to preview the material while making changes without having to 
     * commit to saving it in the database.  For cases where comments are a reference material, it will not appear till the associate button is 
     * triggered by a click.
     * 
     * @param string $id        String to be added to HTML ID of this comment and its components to make them unique
     * @return string
     */
    public function displayPreview($id = '')
    {
        $previewHtml = '';

        $previewHtml .= "<br />";
        $previewHtml .= "<div class='comment'>";
        if (!empty($this->title))
        {
            $previewHtml .= "<span class='commenttitle'>" . $this->title . "</span>";
        }

        if (!empty($this->type))
        {
            $previewHtml .= "<span class='commenttype'>" . $this->type . "</span>";
        }
        $previewHtml .= "<br/>";

        $previewHtml .= "<div class='mathcontent'>";
        $previewHtml .= html_entity_decode($this->content);

        if (!empty($this->subordinates))
        {
            foreach ($this->subordinates as $subordinate)
            {
                $previewHtml .= $subordinate->displayPreview();
            }
        }
//        $previewHtml .= "<br />";
        $previewHtml .= "</div>";

        $previewHtml .= "<br />";

        if (!empty($this->children))
        {
            $previewHtml .= "<ul class='commentminibuttons'>";
            foreach ($this->children as $key => $associate)
            {
                $previewHtml .= $associate->displayPreview("comment", $id . "-" . $key);
            }
            $previewHtml .= "</ul>";
        }


        $previewHtml .= "</div>";
        $previewHtml .= "<br />";


        return $previewHtml;
    }

}

?>
