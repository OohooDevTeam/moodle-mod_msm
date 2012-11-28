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

    public $introtitle;
    public $introcontent;
    public $position;
    public $id;
    public $compid;

    public function getFormData($idNumber, $position)
    {
        $this->position = $position;

        $this->errorArray = array();

        if (isset($_POST['msm_intro_content_input-' . $idNumber]))
        {
            if (!empty($_POST['msm_intro_title_input-' . $idNumber]))
            {
                $this->introtitle = $_POST['msm_intro_title_input-' . $idNumber];
            }

            if (!empty($_POST['msm_intro_content_input-' . $idNumber]))
            {
                $this->introcontent = $_POST['msm_intro_content_input-' . $idNumber];
                //process contents
            }
            else
            {
                $this->errorArray[] = 'msm_intro_content-' . $idNumber;
            }
        }
        else if (isset($_POST['msm_intro_child_content-' . $idNumber]))
        {
            if (!empty($_POST['msm_intro_child_title-' . $idNumber]))
            {
                $this->introtitle = $_POST['msm_intro_child_title-' . $idNumber];
            }

            if (!empty($_POST['msm_intro_child_content-' . $idNumber]))
            {
                $this->introcontent = $_POST['msm_intro_child_content-' . $idNumber];
                //process contents
            }
            else
            {
                $this->errorArray[] = 'msm_intro_child_content-' . $idNumber;
            }
        }

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();

        if (!empty($this->introcontent))
        {
            $data->block_caption = $this->introtitle;

            $this->id = $DB->insert_record('msm_intro', $data);

            $compData = new stdClass();
            $compData->msm_id = $msmid;
            $compData->table_id = $DB->get_record("msm_table_collection", array('tablename'=>'msm_intro'))->id;
            $compData->unit_id = $this->id;
            $compData->parent_id = $parentid;
            $compData->prev_sibling_id = $siblingid;

            $this->compid = $DB->insert_record("msm_compositor", $compData);
        }
    }

}

?>
