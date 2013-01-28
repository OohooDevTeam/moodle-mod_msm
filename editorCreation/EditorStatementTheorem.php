<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorStatementTheorem
 *
 * @author User
 */
class EditorStatementTheorem extends EditorElement
{

    public $position;
    public $id;
    public $compid;
    public $errorArray = array();
    public $content;
    public $children = array(); // part.theorem
    public $subordinates = array();

    function __construct()
    {
        $this->tablename = 'msm_statement_theorem';
    }

    public function getFormData($idNumber, $position)
    {
        $this->position = $position;

        $idInfo = explode("|", $idNumber);

        if (sizeof($idInfo) > 1)
        {


            if ($_POST["msm_theoremref_content_input-" . $idInfo[0]] != '')
            {
                $this->content = $_POST['msm_theoremref_content_input-' . $idInfo[0]];

                foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                {
                    $this->subordinates[] = $subordinates;
                }
            }
            else
            {
                $this->errorArray[] = 'msm_theoremref_content_input-' . $idInfo[0] . '_ifr';
            }

            $partmatch = "/^msm_theoremref_part_content-$idInfo[0]-\d+$/";

            $i = 0;

            foreach ($_POST as $id => $content)
            {
                if (preg_match($partmatch, $id))
                {
                    $idParam = $idInfo[0] . "|ref";
                    $partTheorem = new EditorPartTheorem();
                    $partTheorem->getFormData($idParam, $i);
                    $this->children[] = $partTheorem;
                    $i++;
                }
            }
        }
        else if (sizeof($idInfo) == 1)
        {
            if ($_POST['msm_theorem_content_input-' . $idNumber] != '')
            {
                $this->content = $_POST['msm_theorem_content_input-' . $idNumber];

                foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                {
                    $this->subordinates[] = $subordinates;
                }
            }
            else
            {
                $this->errorArray[] = 'msm_theorem_content_input-' . $idNumber . '_ifr';
            }

            $partmatch = "/^msm_theorem_part_content-$idNumber-\d+$/";

            $i = 0;

            foreach ($_POST as $id => $content)
            {
                if (preg_match($partmatch, $id))
                {
                    $indexNumber = explode("-", $id);

                    $newId = '';
                    for ($i = 1; $i < sizeof($indexNumber) - 1; $i++)
                    {
                        $newId .= $indexNumber[$i] . "-";
                    }
                    $newId .= $indexNumber[sizeof($indexNumber) - 1];

                    $partTheorem = new EditorPartTheorem();
                    $partTheorem->getFormData($newId, $i);
                    $this->children[] = $partTheorem;
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
        $data->statement_content = $this->content;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

        $sibling_id = 0;

        foreach ($this->children as $partTheorem)
        {
            $partTheorem->insertData($this->compid, $sibling_id, $msmid);
            $sibling_id = $partTheorem->compid;
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
        $id = $this->compid + "-" + $this->id;
        
        $htmlContent = '';
        
        $htmlContent .= "<div id='msm_theorem_statement_container-$id' class='msm_theorem_statement_containers'>";
        $htmlContent .= "<div id='msm_theorem_statement_title_container-$id' class='msm_theorem_statement_title_containers'>";
        $htmlContent .= "<b> Theorem Content </b>";
        $htmlContent .= "</div>";
        $htmlContent .= "<div id='msm_theorem_content_input-$id' class='msm_editor_content'>";
        $htmlContent .= $this->content;
        $htmlContent .= "</div>";
        $htmlContent .= "</div>";

        return $htmlContent;
    }

    public function loadData($compid)
    {
        global $DB;

        $statementCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));
        $this->compid = $compid;
        $this->id = $statementCompRecord->unit_id;

        $statementRecord = $DB->get_record($this->tablename, array('id' => $this->id));
        $this->content = $statementRecord->statement_content;

        return $this;
    }

}

?>
