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
                
                foreach($this->processSubordinate($this->content) as $key=>$subordinates)
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
                
                foreach($this->processSubordinate($this->content) as $key=>$subordinates)
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
        foreach($this->subordinates as $subordinate)
        {
            $subordinate->insertData($this->compid, $subordinate_sibling, $msmid);
            $subordinate_sibling = $subordinate->compid;
        }
    }

}

?>
