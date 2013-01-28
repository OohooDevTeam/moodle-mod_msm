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

//            $contentmatch = '/^msm_theoremref_content_input-.*/';
            $contentmatch = '/msm_theoremref_content_input-' . $idNumberInfo[0] . '-\d+$/';

            $i = 0; //position for the part theorem

            foreach ($_POST as $id => $value)
            {
                if (preg_match($contentmatch, $id))
                {
                    $indexNumber = explode("-", $id);

                    $newId = '';
                    for ($i = 1; $i < sizeof($indexNumber) - 1; $i++)
                    {
                        $newId .= $indexNumber[$i] . "-";
                    }
                    $newId .= $indexNumber[sizeof($indexNumber) - 1] . "|ref";

                    $statementRefTheorem = new EditorStatementTheorem();
                    $statementRefTheorem->getFormData($newId, $i);
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

                    $newId = '';
                    for ($i = 1; $i < sizeof($indexNumber) - 1; $i++)
                    {
                        $newId .= $indexNumber[$i] . "-";
                    }
                    $newId .= $indexNumber[sizeof($indexNumber) - 1];

                    $statementTheorem = new EditorStatementTheorem();
                    $statementTheorem->getFormData($newId, $i);
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
                    
                    $newId = '';
                    for ($i = 1; $i < sizeof($idInfo) - 1; $i++)
                    {
                        $newId .= $idInfo[$i] . "-";
                    }
                    $newId .= $idInfo[sizeof($idInfo) - 1];
                    
//                    $indexNumber = $idInfo[1] . "-" . $idInfo[2];
                    $associate = new EditorAssociate();
                    $associate->getFormData($newId, $i);
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

    public function displayData()
    {
        $htmlContent = '';

        $htmlContent .= "<div id='copied_msm_theorem-$this->compid' class='copied_msm_structural_element'>";
        $htmlContent .= "<select id='msm_theorem_type_dropdown-$this->compid' class='msm_unit_child_dropdown' name='msm_theorem_type_dropdown-$this->compid' disabled='disabled'>";

        switch ($this->type)
        {
            case "Theorem":
                $htmlContent .= "<option value='Theorem' selected='selected'>Theorem</option>";
                $htmlContent .= "<option value='Proposition'>Proposition</option>";
                $htmlContent .= "<option value='Lemma'>Lemma</option>";
                $htmlContent .= "<option value='Corollary'>Corollary</option>";
                break;
            case "Proposition":
                $htmlContent .= "<option value='Theorem'>Theorem</option>";
                $htmlContent .= "<option value='Proposition' selected='selected'>Proposition</option>";
                $htmlContent .= "<option value='Lemma'>Lemma</option>";
                $htmlContent .= "<option value='Corollary'>Corollary</option>";
                break;
            case "Lemma":
                $htmlContent .= "<option value='Theorem'>Theorem</option>";
                $htmlContent .= "<option value='Proposition'>Proposition</option>";
                $htmlContent .= "<option value='Lemma' selected='selected'>Lemma</option>";
                $htmlContent .= "<option value='Corollary'>Corollary</option>";
                break;
            case "Corollary":
                $htmlContent .= "<option value='Theorem'>Theorem</option>";
                $htmlContent .= "<option value='Proposition'>Proposition</option>";
                $htmlContent .= "<option value='Lemma'>Lemma</option>";
                $htmlContent .= "<option value='Corollary' selected='selected'>Corollary</option>";
                break;
        }
        $htmlContent .= "</select>";

        $htmlContent .= "<div id='msm_element_title_container-$this->compid' class='msm_element_title_containers'>";
        $htmlContent .= "<b style='margin-left: 30%;'> THEOREM </b>";
        $htmlContent .= "</div>";
        $htmlContent .= "<input id='msm_theorem_title_input-$this->compid' class='msm_unit_child_title' placeholder='Title of Theorem' name='msm_theorem_title_input-$this->compid' disabled='disabled' value='$this->title'/>";
        $htmlContent .= "<div id='msm_theorem_content_input-$this->compid' class='msm_editor_content'>";
        foreach ($this->content as $content)
        {
            $htmlContent .= $content->displayData();
        }
        $htmlContent .= "</div>";
        $htmlContent .= "<label id='msm_theorem_description_label-$this->compid' class='msm_child_description_labels' for='msm_theorem_description_label-$this->compid'>Description: </label>";
        $htmlContent .= "<input id='msm_theorem_description_input-$this->compid' class='msm_child_description_inputs' placeholder='Insert description to search this element in future.' value='$this->description' disabled='disabled' name='msm_theorem_description_input-$this->compid'/>";
        $htmlContent .= "</div>";

        return $htmlContent;
    }

    public function loadData($compid)
    {
        global $DB;

        $theoremCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $theoremCompRecord->unit_id;

        $theoremRecord = $DB->get_record($this->tablename, array('id' => $this->id));

        $this->type = $theoremRecord->theorem_type;
        $this->title = $theoremRecord->caption;
        $this->description = $theoremRecord->description;

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            switch ($childTable->tablename)
            {
                case "msm_statement_theorem":
                    $statementTheorem = new EditorStatementTheorem();
                    $statementTheorem->loadData($child->id);
                    $this->content[] = $statementTheorem;
                    break;
                //associate, proof...etc
            }
        }

        return $this;
    }

}

?>
