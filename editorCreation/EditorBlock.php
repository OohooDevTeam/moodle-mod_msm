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
//    public $introcontent;
    public $position;
    public $id;
    public $compid;

    // idNumber is a bit different--> it's the actual key of the POST object
    public function getFormData($idNumber, $position)
    {
        $this->position = $position;

        $this->errorArray = array();
        $this->introcontent = array();

        $match = "/^msm_intro_child_.*/";

        $idInfo = explode("-", $idNumber);

        if (preg_match($match, $idNumber))
        {
            if (!empty($_POST['msm_intro_child_title-' . $idInfo[1]]))
            {
                $this->introtitle = $_POST['msm_intro_child_title-' . $idInfo[1]];
            }

            if (!empty($_POST['msm_intro_child_content-' . $idInfo[1]]))
            {
                $rawintrocontent = $_POST['msm_intro_child_content-' . $idInfo[1]];
                
                foreach($this->processContent($rawintrocontent) as $content)
                {
                    $this->introcontent[] = $content;
                }
               
            }
            else
            {
                $this->errorArray[] = 'msm_intro_child_content-' . $idInfo[1];
            }
        }
        else
        {
            if (!empty($_POST['msm_intro_title_input-' . $idInfo[1]]))
            {
                $this->introtitle = $_POST['msm_intro_title_input-' . $idInfo[1]];
            }
            if (!empty($_POST['msm_intro_content_input-' . $idInfo[1]]))
            {
                $rawintrocontent = $_POST['msm_intro_content_input-' . $idInfo[1]];
                
                foreach($this->processContent($rawintrocontent) as $content)
                {
                    $this->introcontent[] = $content;
                }
            }
            else
            {
                $this->errorArray[] = 'msm_intro_content_input-' . $idInfo[1];
            }
        }

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        if (sizeof($this->introcontent) > 0)
        {
            $data = new stdClass();
            $data->block_caption = $this->introtitle;

            $this->id = $DB->insert_record('msm_intro', $data);

            $compData = new stdClass();
            $compData->msm_id = $msmid;
            $compData->table_id = $DB->get_record("msm_table_collection", array('tablename' => 'msm_intro'))->id;
            $compData->unit_id = $this->id;
            $compData->parent_id = $parentid;
            $compData->prev_sibling_id = $siblingid;

            $this->compid = $DB->insert_record("msm_compositor", $compData);
            
            $sibling_id = 0;
            foreach($this->introcontent as $content)
            {
                if(get_class($content) == "EditorPara")
                {
                    $content->insertData($this->compid, $sibling_id, $msmid);
                    $sibling_id = $content->compid;
                }
            }
        }
    }
    

}

?>
