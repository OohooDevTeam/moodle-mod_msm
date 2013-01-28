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
    public $position;
    public $id;
    public $compid;
    public $children = array(); //associate
    public $subordinates = array();

    function __construct()
    {
        $this->tablename = "msm_comment";
    }

    public function getFormData($idNumber, $position)
    {
        $idInfo = explode("|", $idNumber);

        if (sizeof($idInfo) > 1)
        {
            $this->type = $_POST['msm_commentref_type_dropdown-' . $idInfo[0]];
            $this->description = $_POST['msm_commentref_description_input-' . $idInfo[0]];
            $this->title = $_POST['msm_commentref_title_input-' . $idInfo[0]];
            $this->position = $position;

            $this->errorArray = array();

            if ($_POST['msm_commentref_content_input-' . $idInfo[0]] != '')
            {
                $this->content = $_POST['msm_commentref_content_input-' . $idInfo[0]];

                foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                {
                    $this->subordinates[] = $subordinates;
                }
            }
            else
            {
                $this->errorArray[] = 'msm_commentref_content_input-' . $idInfo[0] . "_ifr";
            }
        }
        else if (sizeof($idInfo) == 1)
        {
            $this->type = $_POST['msm_comment_type_dropdown-' . $idNumber];
            $this->description = $_POST['msm_comment_description_input-' . $idNumber];
            $this->title = $_POST['msm_comment_title_input-' . $idNumber];
            $this->position = $position;

            $this->errorArray = array();

            if ($_POST['msm_comment_content_input-' . $idNumber] != '')
            {
                $this->content = $_POST['msm_comment_content_input-' . $idNumber];

                foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                {
                    $this->subordinates[] = $subordinates;
                }
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
                    $associate->getFormData($indexNumber, $i);
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
        $htmlContent .= "</div>";
        $htmlContent .= "<input id='msm_comment_title_input-$this->compid' class='msm_unit_child_title' placeholder='Title of Comment' name='msm_comment_title_input-$this->compid' disabled='disabled' value='$this->title'/>";
        $htmlContent .= "<div id='msm_comment_content_input-$this->compid' class='msm_editor_content'>";
        $htmlContent .= $this->content;
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

        // need to process content to find all <a> and match with subordinate data..etc
        // need to process child elements

        return $this;
    }

}

?>
