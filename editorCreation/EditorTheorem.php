<?php

//

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

    public function __construct()
    {
        $this->tablename = 'msm_theorem';
    }

    // theorem as reference material would result in $idNumber being "parentid#|ref" while
    // for main unit content, it would just be a parentid#
    public function getFormData($idNumber, $position)
    {
        $this->position = $position;
        $this->errorArray = array();

        $idNumberInfo = explode("|", $idNumber);

        // reference material
        if (sizeof($idNumberInfo) > 1)
        {
            $this->type = $_POST['msm_theoremref_type_dropdown-' . $idNumberInfo[0]];
            $this->description = $_POST['msm_theoremref_description_input-' . $idNumberInfo[0]];
            $this->title = $_POST['msm_theoremref_title_input-' . $idNumberInfo[0]];

            $contentmatch = '/^msm_theoremref_content_input-.*/';

            $i = 0; //position for the part theorem

            foreach ($_POST as $id => $value)
            {
                if (preg_match($contentmatch, $id))
                {
                    $indexNumber = explode("-", $id);
                    $statementRefTheorem = new EditorStatementTheorem();
                    $statementRefTheorem->getFormData($idNumberInfo[0], $i);
                    $this->content[] = $statementRefTheorem;
                    $i++;
                }
            }
        }
        else if (sizeof($idNumberInfo) == 1) // main unit content
        {
            $this->type = $_POST['msm_theorem_type_dropdown-' . $idNumber];
            $this->description = $_POST['msm_theorem_description_input-' . $idNumber];
            $this->title = $_POST['msm_theorem_title_input-' . $idNumber];

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
        foreach ($this->content as $statementTheorem)
        {
            $statementTheorem->insertData($this->compid, $sibling_id, $msmid);
            $sibling_id = $statementTheorem->compid;
        }

        $sibling_id = 0;

        foreach ($this->children as $associate)
        {
            $associate->insertData($this->compid, $sibling_id, $msmid);
            $sibling_id = $associate->compid;
        }
    }

}

?>
