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
    public $errorArray = array();
    public $children = array(); //associate
    public $subordinates = array();

    public function __construct()
    {
        $this->tablename = 'msm_def';
    }

    // $idNumber can be just a parent index number or if it is a reference, it's a string
    // containing parent_id#|ref to have separate processing steps
    function getFormData($idNumber, $position)
    {
        $this->position = $position;
        
        $idInfo = explode("|", $idNumber);

        // processing definitions as reference material
        if (sizeof($idInfo) > 1)
        {
            $this->type = $_POST['msm_defref_type_dropdown-' . $idInfo[0]];
            $this->description = $_POST['msm_defref_description_input-' . $idInfo[0]];
            $this->title = $_POST['msm_defref_title_input-' . $idInfo[0]];

            if ($_POST['msm_defref_content_input-' . $idInfo[0]] != '')
            {
                $this->content = $_POST['msm_defref_content_input-' . $idInfo[0]];

                // grab all anchored elements in content --> it is only from subordinate
                foreach($this->processSubordinate($this->content) as $key=>$subordinates)
                {
                    $this->subordinates[] = $subordinates;
                }
            }
            else
            {
                $this->errorArray[] = 'msm_defref_content_input-' . $idInfo[0] . '_ifr';
            }
        }
        // processing definition as main part of unit
        else if (sizeof($idInfo) == 1)
        {
            $this->type = $_POST['msm_def_type_dropdown-' . $idNumber];
            $this->description = $_POST['msm_def_description_input-' . $idNumber];
            $this->title = $_POST['msm_def_title_input-' . $idNumber];


            if ($_POST['msm_def_content_input-' . $idNumber] != '')
            {
                $this->content = $_POST['msm_def_content_input-' . $idNumber];

                // grab all anchored elements in content --> it is only from subordinate
                foreach($this->processSubordinate($this->content) as $key=>$subordinates)
                {
                    $this->subordinates[] = $subordinates;
                }
            }
            else
            {
                $this->errorArray[] = 'msm_def_content_input-' . $idNumber . '_ifr';
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
        
//        echo "insert def";
    }

    public function displayData()
    {
        
    }

    public function loadData($compid)
    {
        
    }

}

?>
