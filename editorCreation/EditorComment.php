<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorComment
 *
 * @author User
 */
class EditorComment extends EditorElement
{

    public $type;
    public $description;
    public $title;
    public $id;
    public $compid;
    public $children = array(); //associate
    public $subordinates = array();

    function __construct()
    {
        $this->tablename = "msm_comment";
    }

    public function getFormData($idNumber)
    {
        $idInfo = explode("|", $idNumber);

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

            $this->errorArray = array();

            if ($_POST['msm_commentref_content_input-' . $newId] != '')
            {
                $this->content = $_POST['msm_commentref_content_input-' . $newId];

//                foreach ($this->processSubordinate($this->content) as $key => $subordinates)
//                {
//                    $this->subordinates[] = $subordinates;
//                }
            }
            else
            {
                $this->errorArray[] = 'msm_commentref_content_input-' . $newId . "_ifr";
            }
        }
        else if (sizeof($idInfo) == 1)
        {
            $this->type = $_POST['msm_comment_type_dropdown-' . $idNumber];
            $this->description = $_POST['msm_comment_description_input-' . $idNumber];
            $this->title = $_POST['msm_comment_title_input-' . $idNumber];

            $this->errorArray = array();

            if ($_POST['msm_comment_content_input-' . $idNumber] != '')
            {
                $this->content = $_POST['msm_comment_content_input-' . $idNumber];
//
//                foreach ($this->processSubordinate($this->content) as $key => $subordinates)
//                {
//                    $this->subordinates[] = $subordinates;
//                }
            }
            else
            {
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

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $data->comment_type = $this->type;
        $data->caption = $this->title;
        $data->comment_content = $this->content;
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

        $subordinate_sibling = 0;
        foreach ($this->subordinates as $subordinate)
        {
            $subordinate->insertData($this->compid, $subordinate_sibling, $msmid);
            $subordinate_sibling = $subordinate->compid;
        }
    }

    public function displayData()
    {
        $htmlContent = '';

        $htmlContent .= "<div id='copied_msm_comment-$this->compid' class='copied_msm_structural_element'>";
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
        $htmlContent .= $this->content;
        $htmlContent .= "</div>";
        $htmlContent .= "<label id='msm_comment_description_label-$this->compid' class='msm_child_description_labels' for='msm_comment_description_input-$this->compid'>Description: </label>";
        $htmlContent .= "<input id='msm_comment_description_input-$this->compid' class='msm_child_description_inputs' placeholder='Insert description to search this element in future.' value='$this->description' disabled='disabled' name='msm_comment_description_input-$this->compid'/>";

        $htmlContent .= "<div id='msm_associate_container-$this->compid' class='msm_associate_containers'>";
        foreach ($this->children as $associate)
        {
            $htmlContent .= $associate->displayData();
        }
        $htmlContent .= "<input id='msm_associate_button-$this->compid' class='msm_associate_buttons' type='button' value='Add Associated Information' onclick='addAssociateForm($this->compid, \"comment\")' disabled='disabled'/>";
        $htmlContent .= "</div>";

        $htmlContent .= "</div>";

        return $htmlContent;
    }

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
                //add subordinate later
            }
        }

        // need to process content to find all <a> and match with subordinate data..etc
        // need to process child elements

        return $this;
    }

    function displayRefData($parentId)
    {
        global $DB;

//        $currentRecord = $DB->get_record("msm_compositor", array("id" => $this->compid));

//        $parentRecord = $DB->get_record("msm_compositor", array("id" => $currentRecord->parent_id)); // associate record

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
        $htmlContent .= $this->content;
        $htmlContent .= "</div>";
        $htmlContent .= "<label id='msm_commentref_description_label-$parentId-$this->compid' class='msm_child_description_labels' for='msm_commentref_description_input-$parentId-$this->compid'>Description: </label>";
        $htmlContent .= "<input id='msm_commentref_description_input-$parentId-$this->compid' class='msm_child_description_inputs' placeholder='Insert description to search this element in future.' value='$this->description' disabled='disabled' name='msm_commentref_description_input-$parentId-$this->compid'/>";
        $htmlContent .= "</div>";

        return $htmlContent;
    }

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
        $previewHtml .= $this->content;
        $previewHtml .= "<br />";
        $previewHtml .= "</div>";

        $previewHtml .= "<br />";

        if (!empty($this->children))
        {
            $previewHtml .= "<ul class='commentminibuttons'>";
            foreach ($this->children as $key => $associate)
            {
                $previewHtml .= $associate->displayPreview("comment", $id ."-". $key);
            }
            $previewHtml .= "</ul>";
        }


        $previewHtml .= "</div>";
        $previewHtml .= "<br />";


        return $previewHtml;
    }

}

?>
