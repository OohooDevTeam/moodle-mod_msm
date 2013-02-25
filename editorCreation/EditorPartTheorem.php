<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorPartTheorem
 *
 * @author User
 */
class EditorPartTheorem extends EditorElement
{

    public $id;
    public $compid;
    public $content;
    public $caption;
    public $errorArray = array();
    public $subordinates = array();

    function __construct()
    {
        $this->tablename = 'msm_part_theorem';
    }

    // idNumber is actually a id name from HTML form
    public function getFormData($idNumber)
    {
        $idParam = explode("|", $idNumber);

        if (sizeof($idParam) > 1)
        {
            $this->caption = $_POST['msm_theoremref_part_title-' . $idParam[0]];

            if ($_POST['msm_theoremref_part_content-' . $idParam[0]] != '')
            {
                $this->content = $_POST['msm_theoremref_part_content-' . $idParam[0]];

                foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                {
                    $this->subordinates[] = $subordinates;
                }
            }
            else
            {
                $this->errorArray[] = 'msm_theoremref_part_content-' . $idParam[0] . '_ifr';
            }
        }
        else if (sizeof($idParam) == 1)
        {

            $this->caption = $_POST['msm_theorem_part_title-' . $idNumber];

            if ($_POST['msm_theorem_part_content-' . $idNumber] != '')
            {
                $this->content = $_POST['msm_theorem_part_content-' . $idNumber];

                foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                {
                    $this->subordinates[] = $subordinates;
                }
            }
            else
            {
                $this->errorArray[] = 'msm_theorem_part_content-' . $idNumber . '_ifr';
            }
        }

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $data->partid = null;
        $data->counter = null;
        $data->equivalence_mark = null;
        $data->caption = $this->caption;
        $data->part_content = $this->content;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

        $subordinate_sibling = 0;
        foreach ($this->subordinates as $subordinate)
        {
            $subordinate->insertData($this->compid, $subordinate_sibling, $msmid);
            $subordinate_sibling = $subordinate->compid;
        }
    }

    public function displayData()
    {
        global $DB;

        $currentCompRecord = $DB->get_record("msm_compositor", array("id" => $this->compid));
        $parentStatementTheoremRecord = $DB->get_record("msm_compositor", array("id"=>$currentCompRecord->parent_id));

        $htmlContent = '';
        $htmlContent .= "<div id='msm_theorem_part_container-$parentStatementTheoremRecord->parent_id-$currentCompRecord->parent_id-$this->compid' class='msm_theorem_child'>";
        $htmlContent .= "<div id='msm_theorem_part_title_container-$this->compid' class='msm_theorem_part_title_containers'>";
        $htmlContent .= "<span style='visibility: hidden;'>Drag here to move this element.</span>";
        $htmlContent .= "</div>";
        $htmlContent .= "<label class='msm_theorem_part_tlabel' for='msm_theorem_part_title-$parentStatementTheoremRecord->parent_id-$currentCompRecord->parent_id-$this->compid'>Part Theorem title: </label>";
        $htmlContent .= "<input id='msm_theorem_part_title-$parentStatementTheoremRecord->parent_id-$currentCompRecord->parent_id-$this->compid' class='msm_theorem_part_title' placeholder='Title for this part of the theorem.' name='msm_theorem_part_title-$parentStatementTheoremRecord->parent_id-$currentCompRecord->parent_id-$this->compid' disabled='disabled' value='$this->caption'/>";
        $htmlContent .= "<div id='msm_theorem_part_content-$parentStatementTheoremRecord->parent_id-$currentCompRecord->parent_id-$this->compid' class='msm_editor_content'>";
        $htmlContent .= $this->content;
        $htmlContent .= "</div>";
        $htmlContent .= "</div>";

        return $htmlContent;
    }

    public function loadData($compid)
    {
        global $DB;

        $partCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $partCompRecord->unit_id;

        $partRecord = $DB->get_record($this->tablename, array('id' => $this->id));

        $this->caption = $partRecord->caption;
        $this->content = $partRecord->part_content;

        return $this;
    }

    function displayRefData()
    {
        global $DB;

        $currentRefCompRecord = $DB->get_record("msm_compositor", array("id" => $this->compid));
        $parentStatementTheoremRefRecord = $DB->get_record("msm_compositor", array("id"=>$currentRefCompRecord->parent_id));
        $htmlContent = '';
        $htmlContent .= "<div id='msm_theoremref_part_container-$this->compid' class='msm_theorem_child'>";
        $htmlContent .= "<div id='msm_theoremref_part_title_container-$this->compid' class='msm_theoremref_part_title_containers'>";
        $htmlContent .= "<span style='visibility: hidden;'>Drag here to move this element.</span>";
        $htmlContent .= "</div>";
        $htmlContent .= "<label class='msm_theoremref_part_tlabel' for='msm_theorerefm_part_title-$parentStatementTheoremRefRecord->parent_id-$currentRefCompRecord->parent_id-$this->compid'>Part Theorem title: </label>";
        $htmlContent .= "<input id='msm_theoremref_part_title-$parentStatementTheoremRefRecord->parent_id-$currentRefCompRecord->parent_id-$this->compid' class='msm_theoremref_part_title' placeholder='Title for this part of the theorem.' name='msm_theoremref_part_title-$parentStatementTheoremRefRecord->parent_id-$currentRefCompRecord->parent_id-$this->compid' disabled='disabled' value='$this->caption'/>";
        $htmlContent .= "<div id='msm_theoremref_part_content-$parentStatementTheoremRefRecord->parent_id-$currentRefCompRecord->parent_id-$this->compid' class='msm_editor_content'>";
        $htmlContent .= $this->content;
        $htmlContent .= "</div>";
        $htmlContent .= "</div>";

        return $htmlContent;
    }

}

?>
