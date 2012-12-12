<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorDefinition
 *
 * @author User
 */
class EditorDefinition extends EditorElement
{

    public $id;
    public $compid;
    public $type;
    public $title;
    public $content;
    public $tablename;
    public $position;
    public $description;
    public $associates = array();

    public function __construct()
    {
        $this->tablename = 'msm_def';
    }

    function getFormData($idNumber, $position)
    {
        $this->type = $_POST['msm_def_type_dropdown-' . $idNumber];
        $this->description = $_POST['msm_def_descripton_input-' . $idNumber];
        $this->title = $_POST['msm_def_title_input-' . $idNumber];
        $this->position = $position;

        $this->errorArray = array();

        if ($_POST['msm_def_content_input-' . $idNumber] != '')
        {
            $this->content = $_POST['msm_def_content_input-' . $idNumber];
        }
        else
        {
            $this->errorArray[] = 'msm_def_content_input-' . $idNumber . '_ifr';
        }
        
        $match = "/^msm_associate_dropdown-$idNumber-(\d+)/";
        
        $i = 0;
        
        foreach($_POST as $id=>$value)
        {
            if(preg_match($match, $id))
            {
                $idInfo = explode("-", $id);
                $indexNumber = $idInfo[1] . "-" . $idInfo[2];
                $associate = new EditorAssociate();
                $associate->getFormData($indexNumber, $i);
                $this->associates[] = $associate;                
                $i++;
            }
        }

        return $this;
    }

    function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $data->def_type = $this->type;
        $data->caption = $this->title;
        $data->def_content = $this->content;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);
        
        $sibling_id = 0;
        
        foreach($this->associates as $associate)
        {
            $associate->insertData($this->compid, $sibling_id, $msmid);
            $sibling_id = $associate->compid;
        }
    }

}

?>
