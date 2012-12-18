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
                $data = new stdClass();
                $data->block_caption = $this->title;

                $this->id = $DB->insert_record('msm_unit', $data);

                $compData = new stdClass();
                $compData->msm_id = $msmid;
                $compData->table_id = $DB->get_record("msm_table_collection", array('tablename' => 'msm_unit'))->id;
                $compData->unit_id = $this->id;
                $compData->parent_id = $parentid;
                $compData->prev_sibling_id = $siblingid;

                $this->compid = $DB->insert_record("msm_compositor", $compData);

                $sibling_id = 0;
                foreach ($this->content as $content)
                {
                    if (get_class($content) == "EditorPara")
                    {
                        $content->insertData($this->compid, $sibling_id, $msmid);
                        $sibling_id = $content->compid;
                    }
                    else if (get_class($content) == "EditorInContent")
                    {
                        $content->insertData($this->compid, $sibling_id, $msmid);
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
                    if (get_class($content) == "EditorPara")
                    {
                        $content->insertData($this->compid, $sibling_id, $msmid);
                        $sibling_id = $content->compid;
                    }
                    else if (get_class($content) == "EditorInContent")
                    {
                        $content->insertData($this->compid, $sibling_id, $msmid);
                        $sibling_id = $content->compid;
                    }
                    else if (get_class($content) == "EditorTable")
                    {
                        $content->insertData($this->compid, $sibling_id, $msmid);
                        $sibling_id = $content->compid;
                    }
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
                ;
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
            ;
        }

        $this->type = "unit";

        return $this;
    }

}

?>
