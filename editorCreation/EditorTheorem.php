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
    public $description;
    public $children = array(); //associates

    public function __construct()
    {
        $this->tablename = 'msm_theorem';
    }

    // theorem as reference material would result in $idNumber being "parentid#|ref" while
    // for main unit content, it would just be a parentid#
    public function getFormData($idNumber)
    {
        $this->errorArray = array();

        $idNumberInfo = explode("|", $idNumber);

        // reference material
        if (sizeof($idNumberInfo) > 1)
        {
            $match = "/^msm_theoremref_type_dropdown-$idNumberInfo[0].*$/";
            
            $newTid = '';
            foreach ($_POST as $id => $value)
            {
                if (preg_match($match, $id))
                {
                    $tempidInfo = explode("-", $id);
                    for ($i = 1; $i < sizeof($tempidInfo) - 1; $i++)
                    {
                        $newTid .= $tempidInfo[$i] . "-";
                    }
                    $newTid .= $tempidInfo[sizeof($tempidInfo) - 1];
                    break;
                }
            }
            $this->type = $_POST['msm_theoremref_type_dropdown-' . $newTid];
            $this->description = $_POST['msm_theoremref_description_input-' . $newTid];
            $this->title = $_POST['msm_theoremref_title_input-' . $newTid];
//
            $contentmatch = '/msm_theoremref_content_input-' . $newTid . '-\d+.*$/';

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
                    $statementRefTheorem->getFormData($newId);
                    $this->contents[] = $statementRefTheorem;
                    $i++;
                }
            }
        }
        else if (sizeof($idNumberInfo) == 1) // main unit content
        {
            $this->type = $_POST['msm_theorem_type_dropdown-' . $idNumber];
            $this->description = $_POST['msm_theorem_description_input-' . $idNumber];
            $this->title = $_POST['msm_theorem_title_input-' . $idNumber];

            $contentmatch = "/^msm_theorem_content_input-$idNumber-.*/";

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
                    $statementTheorem->getFormData($newId);
                    $this->contents[] = $statementTheorem;
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
                    $associate = new EditorAssociate();
                    $associate->getFormData($newId);
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
        foreach ($this->contents as $statementTheorem)
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
        
        $htmlContent .= "<div class='msm_element_overlays' id='msm_element_overlay-$this->compid' style='display: none;'>";

        $htmlContent .= "<a class='msm_overlayButtons' id='msm_overlayButton_delete-$this->compid' onclick='deleteOverlayElement(event);'> Delete </a>";
        $htmlContent .= "<a class='msm_overlayButtons' id='msm_overlayButton_edit-$this->compid' onclick='editUnit(event);'> Edit </a>";

        $htmlContent .= "</div>";
        
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
        $htmlContent .= "<span style='visibility: hidden;'>Drag here to move this element.</span>";
        $htmlContent .= "</div>";
        $htmlContent .= "<input id='msm_theorem_title_input-$this->compid' class='msm_unit_child_title' placeholder='Title of Theorem' name='msm_theorem_title_input-$this->compid' disabled='disabled' value='$this->title'/>";
        $htmlContent .= "<div id='msm_theorem_content_container-$this->compid' class='msm_theorem_content_containers'>";
        foreach ($this->contents as $content)
        {
            $htmlContent .= $content->displayData();
        }

        $htmlContent .= "<input id='msm_theorem_child_button-$this->compid' class='msm_theorem_child_buttons' type='button' value='Add content' onclick='addTheoremContent(event)' disabled='disabled'/>";
        $htmlContent .= "</div>";
        $htmlContent .= "<label id='msm_theorem_description_label-$this->compid' class='msm_child_description_labels' for='msm_theorem_description_label-$this->compid'>Description: </label>";
        $htmlContent .= "<input id='msm_theorem_description_input-$this->compid' class='msm_child_description_inputs' placeholder='Insert description to search this element in future.' value='$this->description' disabled='disabled' name='msm_theorem_description_input-$this->compid'/>";

        $htmlContent .= "<div id='msm_associate_container-$this->compid' class='msm_associate_containers'>";
        foreach ($this->children as $associate)
        {
            $htmlContent .= $associate->displayData();
        }
        $htmlContent .= "<input id='msm_associate_button-$this->compid' class='msm_associate_buttons' type='button' value='Add Associated Information' onclick='addAssociateForm($this->compid, \"theorem\")' disabled='disabled'/>";
        $htmlContent .= "</div>";

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
                    $this->contents[] = $statementTheorem;
                    break;
                case "msm_associate":
                    $associate = new EditorAssociate();
                    $associate->loadData($child->id);
                    $this->children[] = $associate;
                    break;
                //associate, proof...etc
            }
        }

        return $this;
    }

    function displayRefData($parentId)
    {
        global $DB;
        $htmlContent = '';

        $htmlContent .= "<div id='copied_msm_theoremref-$parentId-$this->compid' class='copied_msm_structural_element'>";
        $htmlContent .= "<select id='msm_theoremref_type_dropdown-$parentId-$this->compid' class='msm_unit_child_dropdown' name='msm_theoremref_type_dropdown-$parentId-$this->compid' disabled='disabled'>";

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

        $htmlContent .= "<div id='msm_element_title_container-$parentId-$this->compid' class='msm_element_title_containers'>";
        $htmlContent .= "<b style='margin-left: 30%;'> THEOREM </b>";
        $htmlContent .= "</div>";
        $htmlContent .= "<input id='msm_theoremref_title_input-$parentId-$this->compid' class='msm_unit_child_title' placeholder='Title of Theorem' name='msm_theoremref_title_input-$parentId-$this->compid' disabled='disabled' value='$this->title'/>";
        $htmlContent .= "<div id='msm_theoremref_content_container-$parentId-$this->compid' class='msm_theoremref_content_containers'>";
        foreach ($this->contents as $content)
        {
            $htmlContent .= $content->displayRefData("$parentId-$this->compid");
        }
        $htmlContent .= "<input id='msm_theoremref_child_button-$parentId-$this->compid' class='msm_theorem_child_buttons' type='button' value='Add content' onclick='addrefTheoremContent(event)' disabled='disabled'/>";
        $htmlContent .= "</div>";
        $htmlContent .= "<label id='msm_theoremref_description_label-$parentId-$this->compid' class='msm_child_description_labels' for='msm_theoremref_description_label-$parentId-$this->compid'>Description: </label>";
        $htmlContent .= "<input id='msm_theoremref_description_input-$parentId-$this->compid' class='msm_child_description_inputs' placeholder='Insert description to search this element in future.' value='$this->description' disabled='disabled' name='msm_theoremref_description_input-$parentId-$this->compid'/>";
        $htmlContent .= "</div>";

        return $htmlContent;
    }

    public function displayPreview($id = '')
    {
        $previewHtml = '';

        $previewHtml .= "<br />";
        $previewHtml .= "<div class='theorem'>";
        if (!empty($this->title))
        {
            $previewHtml .= "<span class='theoremtitle'>" . $this->title . "</span>";
        }

        if (!empty($this->type))
        {
            $previewHtml .= "<span class='theoremtype'>" . $this->type . "</span>";
        }
        $previewHtml .= "<br/>";

        $previewHtml .= "<div class='mathcontent'>";
        foreach ($this->contents as $statementTheorem)
        {
            $previewHtml .= $statementTheorem->displayPreview($id);
        }
        $previewHtml .= "</div>";

        $previewHtml .= "<br />";

        $previewHtml .= "<ul class='minibuttons'>";
        foreach ($this->children as $key => $associate)
        {
            $previewHtml .= $associate->displayPreview("theorem", $id . "-" . $key);
        }
        $previewHtml .= "</ul>";


        $previewHtml .= "</div>";
        $previewHtml .= "<br />";

        return $previewHtml;
    }

}

?>