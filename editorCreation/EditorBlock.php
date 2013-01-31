<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorBlock
 *
 * @author User
 */
class EditorBlock extends EditorElement
{

    public $title;
    public $position;
    public $id;
    public $compid;
    public $errorArray = array();
    public $content = array();
    public $type;

    // idNumber is a bit different--> it's the actual key of the POST object
    public function getFormData($idNumber, $position)
    {
        $this->position = $position;

        $intromatch = '/^msm_intro_.*/';
        $bodymatch = '/^copied_msm_body-.*/';

        if (preg_match($intromatch, $idNumber))
        {
            $this->processIntroContent($idNumber);
        }
        else if (preg_match($bodymatch, $idNumber))
        {
            $this->processBodyContent($idNumber);
        }

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        if (sizeof($this->content) > 0)
        {
            if ($this->type == "unit")
            {
                $sibling_id = 0;

                if (!empty($this->title))
                {
                    $data = new stdClass();
                    $data->block_caption = $this->title;

                    $this->id = $DB->insert_record("msm_unit", $data);

                    $compData = new stdClass();
                    $compData->msm_id = $msmid;
                    $compData->table_id = $DB->get_record('msm_table_collection', array("tablename" => "msm_unit"))->id;
                    $compData->unit_id = $this->id;
                    $compData->parent_id = $parentid;
                    $compData->prev_sibling_id = $siblingid;

                    $this->compid = $DB->insert_record("msm_compositor", $compData);
//                    
                    foreach ($this->content as $content)
                    {
                        $content->insertData($this->compid, $sibling_id, $msmid);
                        $sibling_id = $content->compid;
                    }
                }
                else
                {
                    foreach ($this->content as $content)
                    {
                        $content->insertData($parentid, $sibling_id, $msmid);
                        $sibling_id = $content->compid;
                    }
                }
            }
            else if ($this->type == "intro")
            {
                $data = new stdClass();
                $data->block_caption = $this->title;

                $this->id = $DB->insert_record('msm_intro', $data);

                $compData = new stdClass();
                $compData->msm_id = $msmid;
                $compData->table_id = $DB->get_record("msm_table_collection", array('tablename' => 'msm_intro'))->id;
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
        }
    }

    function processIntroContent($idNumber)
    {
        $match = "/^msm_intro_child_.*/";

        $idInfo = explode("-", $idNumber);

        if (preg_match($match, $idNumber))
        {
            if (!empty($_POST['msm_intro_child_title-' . $idInfo[1]]))
            {
                $this->title = $_POST['msm_intro_child_title-' . $idInfo[1]];
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
                $this->errorArray[] = 'msm_intro_child_content-' . $idInfo[1] . '_ifr';
            }
        }
        else
        {
            if (!empty($_POST['msm_intro_title_input-' . $idInfo[1]]))
            {
                $this->title = $_POST['msm_intro_title_input-' . $idInfo[1]];
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
                $this->errorArray[] = 'msm_intro_content_input-' . $idInfo[1] . '_ifr';
            }
        }

        $this->type = "intro";

        return $this;
    }

    function processBodyContent($formKey)
    {
        $idInfo = explode("-", $formKey);

        if (!empty($_POST['msm_body_title_input-' . $idInfo[1]]))
        {
            $this->title = $_POST['msm_body_title_input-' . $idInfo[1]];
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
            $this->errorArray[] = 'msm_body_content_input-' . $idInfo[1] . '_ifr';
        }

        $this->type = "unit";

        return $this;
    }

    public function displayData()
    {
        $htmlContent = '';

        if ($this->type == 'msm_intro')
        {
            $htmlContent .= "<div id='msm_intro_child_div-$this->compid' class='msm_intro_child'>";
            $htmlContent .= "<div id='msm_intro_child_dragarea-$this->compid' class='msm_intro_child_dragareas'>";
            $htmlContent .= "</div>";

            $htmlContent .= "<div>";
            $htmlContent .= "Title: ";
            $htmlContent .= "<input id='msm_intro_child_title-$this->compid' class='msm_intro_child_titles' name='msm_intro_child_title-$this->compid' placeholder='Optional Title for the Content' disabled='disabled' value='$this->title'/>";
            $htmlContent .= "</div>";

            $htmlContent .= "<div id='msm_intro_child_content-$this->compid' class='msm_editor_content'>";
            foreach ($this->content as $content)
            {
                $htmlContent .= $content->displayData();
            }
            $htmlContent .= "</div>";
            $htmlContent .= "</div>";
        }
        else if ($this->type == 'msm_unit')
        {
            $htmlContent .= "<div id='copied_msm_body-$this->compid' class='copied_msm_structural_element' style='padding-top: 2%;'>";

            $htmlContent .= "<div id='msm_element_title_container-$this->compid' class='msm_element_title_containers'>";
            $htmlContent .= "<b style='margin-left: 45%; margin-right: 0%; margin-top: 2%; margin-bottom: 2%;'> CONTENT </b>";
            $htmlContent .= "</div>";

            $htmlContent .= "<div style='margin-top: 2%;'>";
            $htmlContent .= "<label id='msm_body_title_label-$this->compid' class='msm_unit_body_title_labels' for='msm_body_title_input-$this->compid'>Title: </label>";
            $htmlContent .= "<input id='msm_body_title_input-$this->compid' class='msm_unit_body_title' placeholder='Optional Title for this Content' name='msm_body_title_input-$this->compid' disabled='disabled' value='$this->title'/>";
            $htmlContent .= "</div>";

            $htmlContent .= "<div id='msm_body_content_input-$this->compid' class='msm_editor_content'>";
                        
            foreach ($this->content as $content)
            {
                $htmlContent .= $content->displayData();
            }

            $htmlContent .= "</div>";

            $htmlContent .= "</div>";
        }

        return $htmlContent;
    }

    //compid = intro/unit comp id
    public function loadData($compid)
    {
        global $DB;

        $compRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        // both id's are for either intro/unit
        $this->compid = $compid;
        $this->id = $compRecord->unit_id;

        $tableRecord = $DB->get_record('msm_table_collection', array('id' => $compRecord->table_id));

        $this->type = $tableRecord->tablename;

        $record = $DB->get_record($tableRecord->tablename, array('id' => $this->id));

        $this->title = $record->block_caption;

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
                default:
                    echo "what tablename? " . $childTable->tablename;
                    break;
            }
        }

        return $this;
    }

}

?>
