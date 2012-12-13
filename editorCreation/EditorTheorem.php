<?php //

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorTheorem
 *
 * @author User
 */
class EditorTheorem extends EditorElement
{

    public $id;
    public $compid;
    public $type;
    public $title;
    public $contents = array();
    public $associateType;
    public $tablename;
    public $position;
    public $description;
    public $children = array(); //associates

//    public $partTheorems = array();

    public function __construct()
    {
        $this->tablename = 'msm_theorem';
    }

    public function getFormData($idNumber, $position)
    {
        $this->type = $_POST['msm_theorem_type_dropdown-' . $idNumber];
        $this->description = $_POST['msm_theorem_descripton_input-' . $idNumber];
//        $this->associateType = $_POST['msm_theorem_associate_dropdown-' . $idNumber];
        $this->title = $_POST['msm_theorem_title_input-' . $idNumber];
        $this->position = $position;

        $this->errorArray = array();
        $contentmatch = '/^msm_theorem_content_input-.*/';

        $i = 0; //position for the part theorem

        foreach ($_POST as $id => $value)
        {
            if (preg_match($contentmatch, $id))
            {
                $indexNumber = explode("-", $id);
                $statementTheorem = new EditorStatementTheorem();
                $statementTheorem->getFormData($indexNumber[1], $i);
                $this->content[] = $statementTheorem;
                $i++;
            }
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
                $this->children[] = $associate;                
                $i++;
            }
        }

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $compData = new stdClass();

        $data->theorem_type = $this->type;
        $data->caption = $this->title;
        $data->description = $this->description;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record("msm_table_collection", array("tablename" => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record("msm_compositor", $compData);

        $sibling_id = 0;
        foreach($this->content as $statementTheorem)
        {
            $statementTheorem->insertData($this->compid, $sibling_id, $msmid);
            $sibling_id = $statementTheorem->compid;
        }
        
        $sibling_id = 0;
        
        foreach($this->children as $associate)
        {
            $associate->insertData($this->compid, $sibling_id, $msmid);
            $sibling_id = $associate->compid;
        }
    }

}

?>
