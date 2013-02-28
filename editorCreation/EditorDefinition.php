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
    function getFormData($idNumber)
    {
        $idInfo = explode("|", $idNumber);

        // processing definitions as reference material
        if (sizeof($idInfo) > 1)
        {
            $match = "/^msm_defref_content_input-$idInfo[0].*$/";

            $newId = '';
            foreach ($_POST as $id => $value)
            {
                if (preg_match($match, $id))
                {
                    $tempidInfo = explode("-", $id);
                    for ($i = 1; $i < sizeof($tempidInfo) - 1; $i++)
                    {
                        $newId .= $tempidInfo[$i] . "-";
                    }
                    $newId .= $tempidInfo[sizeof($tempidInfo) - 1];
                    break;
                }
            }
            $this->type = $_POST['msm_defref_type_dropdown-' . $newId];
            $this->description = $_POST['msm_defref_description_input-' . $newId];
            $this->title = $_POST['msm_defref_title_input-' . $newId];

            if ($_POST['msm_defref_content_input-' . $newId] != '')
            {
                $this->content = $_POST['msm_defref_content_input-' . $newId];

                // grab all anchored elements in content --> it is only from subordinate
                foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                {
                    $this->subordinates[] = $subordinates;
                }
            }
            else
            {
                $this->errorArray[] = 'msm_defref_content_input-' . $newId . '_ifr';
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
                foreach ($this->processSubordinate($this->content) as $key => $subordinates)
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
                    $associate->getFormData($indexNumber);
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
        $data->description = $this->description;

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
    }

    public function displayData()
    {
        $htmlContent = '';

        $htmlContent .= "<div id='copied_msm_def-$this->compid' class='copied_msm_structural_element'>";
        $htmlContent .= "<select id='msm_def_type_dropdown-$this->compid' class='msm_unit_child_dropdown' name='msm_def_type_dropdown-$this->compid' disabled='disabled'>";

        switch ($this->type)
        {
            case "Definition":
                $htmlContent .= "<option value='Definition' selected='selected'>Definition</option>";
                $htmlContent .= "<option value='Notation'>Notation</option>";
                $htmlContent .= "<option value='Convention'>Convention</option>";
                $htmlContent .= "<option value='Agreement'>Agreement</option>";
                $htmlContent .= "<option value='Axiom'>Axiom</option>";
                $htmlContent .= "<option value='Terminology'>Terminology</option>";
                break;
            case "Notation":
                $htmlContent .= "<option value='Definition'>Definition</option>";
                $htmlContent .= "<option value='Notation' selected='selected'>Notation</option>";
                $htmlContent .= "<option value='Convention'>Convention</option>";
                $htmlContent .= "<option value='Agreement'>Agreement</option>";
                $htmlContent .= "<option value='Axiom'>Axiom</option>";
                $htmlContent .= "<option value='Terminology'>Terminology</option>";
                break;
            case "Convention":
                $htmlContent .= "<option value='Definition'>Definition</option>";
                $htmlContent .= "<option value='Notation'>Notation</option>";
                $htmlContent .= "<option value='Convention' selected='selected'>Convention</option>";
                $htmlContent .= "<option value='Agreement'>Agreement</option>";
                $htmlContent .= "<option value='Axiom'>Axiom</option>";
                $htmlContent .= "<option value='Terminology'>Terminology</option>";
                break;
            case "Agreement":
                $htmlContent .= "<option value='Definition'>Definition</option>";
                $htmlContent .= "<option value='Notation'>Notation</option>";
                $htmlContent .= "<option value='Convention'>Convention</option>";
                $htmlContent .= "<option value='Agreement' selected='selected'>Agreement</option>";
                $htmlContent .= "<option value='Axiom'>Axiom</option>";
                $htmlContent .= "<option value='Terminology'>Terminology</option>";
                break;
            case "Axiom":
                $htmlContent .= "<option value='Definition'>Definition</option>";
                $htmlContent .= "<option value='Notation'>Notation</option>";
                $htmlContent .= "<option value='Convention'>Convention</option>";
                $htmlContent .= "<option value='Agreement'>Agreement</option>";
                $htmlContent .= "<option value='Axiom' selected='selected'>Axiom</option>";
                $htmlContent .= "<option value='Terminology'>Terminology</option>";
                break;
            case "Terminology":
                $htmlContent .= "<option value='Definition'>Definition</option>";
                $htmlContent .= "<option value='Notation'>Notation</option>";
                $htmlContent .= "<option value='Convention'>Convention</option>";
                $htmlContent .= "<option value='Agreement'>Agreement</option>";
                $htmlContent .= "<option value='Axiom'>Axiom</option>";
                $htmlContent .= "<option value='Terminology' selected='selected'>Terminology</option>";
                break;
        }
        $htmlContent .= "</select>";

        $htmlContent .= "<div id='msm_element_title_container-$this->compid' class='msm_element_title_containers'>";
        $htmlContent .= "<b style='margin-left: 30%;'> DEFINITION </b>";
        $htmlContent .= "<span style='visibility: hidden;'>Drag here to move this element.</span>";
        $htmlContent .= "</div>";
        $htmlContent .= "<input id='msm_def_title_input-$this->compid' class='msm_unit_child_title' placeholder='Title of Definition' name='msm_def_title_input-$this->compid' disabled='disabled' value='$this->title'/>";
        $htmlContent .= "<div id='msm_def_content_input-$this->compid' class='msm_editor_content'>";
        $htmlContent .= $this->content;
        $htmlContent .= "</div>";
        $htmlContent .= "<label id='msm_def_description_label-$this->compid' class='msm_child_description_labels' for='msm_def_description_label-$this->compid'>Description: </label>";
        $htmlContent .= "<input id='msm_def_description_input-$this->compid' class='msm_child_description_inputs' placeholder='Insert description to search this element in future.' value='$this->description' disabled='disabled' name='msm_def_description_input-$this->compid'/>";

        $htmlContent .= "<div id='msm_associate_container-$this->compid' class='msm_associate_containers'>";
        foreach ($this->children as $associate)
        {
            $htmlContent .= $associate->displayData();
        }
        $htmlContent .= "<input id='msm_associate_button-$this->compid' class='msm_associate_buttons' type='button' value='Add Associated Information' onclick='addAssociateForm($this->compid, \"def\")' disabled='disabled'/>";
        $htmlContent .= "</div>";

        $htmlContent .= "</div>";

        return $htmlContent;
    }

    public function loadData($compid)
    {
        global $DB;

        $defCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $defCompRecord->unit_id;

        $defRecord = $DB->get_record($this->tablename, array('id' => $this->id));

        $this->type = $defRecord->def_type;
        $this->title = $defRecord->caption;
        $this->description = $defRecord->description;
        $this->content = $defRecord->def_content;

        $childRecords = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            switch ($childTable->tablename)
            {
                case "msm_associate":
                    $associate = new EditorAssociate();
                    $associate->loadData($child->id);
                    $this->children[] = $associate;
                    break;
                //add subordinate later
            }
        }

        return $this;
    }

    function displayRefData()
    {
        global $DB;

        $currentRecord = $DB->get_record("msm_compositor", array("id" => $this->compid));

        $parentRecord = $DB->get_record("msm_compositor", array("id" => $currentRecord->parent_id)); // associate record
        // $parentRecord->parent_id == parent def/comment/theorem where associate is a child of

        $htmlContent = '';

        $htmlContent .= "<div id='copied_msm_defref-$parentRecord->parent_id-$parentRecord->id-$this->compid' class='copied_msm_structural_element'>";
        $htmlContent .= "<select id='msm_defref_type_dropdown-$parentRecord->parent_id-$parentRecord->id-$this->compid' class='msm_unit_child_dropdown' name='msm_defref_type_dropdown-$parentRecord->parent_id-$parentRecord->id-$this->compid' disabled='disabled'>";

        switch ($this->type)
        {
            case "Definition":
                $htmlContent .= "<option value='Definition' selected='selected'>Definition</option>";
                $htmlContent .= "<option value='Notation'>Notation</option>";
                $htmlContent .= "<option value='Convention'>Convention</option>";
                $htmlContent .= "<option value='Agreement'>Agreement</option>";
                $htmlContent .= "<option value='Axiom'>Axiom</option>";
                $htmlContent .= "<option value='Terminology'>Terminology</option>";
                break;
            case "Notation":
                $htmlContent .= "<option value='Definition'>Definition</option>";
                $htmlContent .= "<option value='Notation' selected='selected'>Notation</option>";
                $htmlContent .= "<option value='Convention'>Convention</option>";
                $htmlContent .= "<option value='Agreement'>Agreement</option>";
                $htmlContent .= "<option value='Axiom'>Axiom</option>";
                $htmlContent .= "<option value='Terminology'>Terminology</option>";
                break;
            case "Convention":
                $htmlContent .= "<option value='Definition'>Definition</option>";
                $htmlContent .= "<option value='Notation'>Notation</option>";
                $htmlContent .= "<option value='Convention' selected='selected'>Convention</option>";
                $htmlContent .= "<option value='Agreement'>Agreement</option>";
                $htmlContent .= "<option value='Axiom'>Axiom</option>";
                $htmlContent .= "<option value='Terminology'>Terminology</option>";
                break;
            case "Agreement":
                $htmlContent .= "<option value='Definition'>Definition</option>";
                $htmlContent .= "<option value='Notation'>Notation</option>";
                $htmlContent .= "<option value='Convention'>Convention</option>";
                $htmlContent .= "<option value='Agreement' selected='selected'>Agreement</option>";
                $htmlContent .= "<option value='Axiom'>Axiom</option>";
                $htmlContent .= "<option value='Terminology'>Terminology</option>";
                break;
            case "Axiom":
                $htmlContent .= "<option value='Definition'>Definition</option>";
                $htmlContent .= "<option value='Notation'>Notation</option>";
                $htmlContent .= "<option value='Convention'>Convention</option>";
                $htmlContent .= "<option value='Agreement'>Agreement</option>";
                $htmlContent .= "<option value='Axiom' selected='selected'>Axiom</option>";
                $htmlContent .= "<option value='Terminology'>Terminology</option>";
                break;
            case "Terminology":
                $htmlContent .= "<option value='Definition'>Definition</option>";
                $htmlContent .= "<option value='Notation'>Notation</option>";
                $htmlContent .= "<option value='Convention'>Convention</option>";
                $htmlContent .= "<option value='Agreement'>Agreement</option>";
                $htmlContent .= "<option value='Axiom'>Axiom</option>";
                $htmlContent .= "<option value='Terminology' selected='selected'>Terminology</option>";
                break;
        }
        $htmlContent .= "</select>";

        $htmlContent .= "<span class='msm_element_title'>";
        $htmlContent .= "<b style='margin-left: 30%;'> DEFINITION </b>";
        $htmlContent .= "</span>";

        $htmlContent .= "<input id='msm_defref_title_input-$parentRecord->parent_id-$parentRecord->id-$this->compid' class='msm_unit_child_title' placeholder='Title of Definition' name='msm_defref_title_input-$parentRecord->parent_id-$parentRecord->id-$this->compid' disabled='disabled' value='$this->title'/>";

        $htmlContent .= "<div id='msm_defref_content_input-$parentRecord->parent_id-$parentRecord->id-$this->compid' class='msm_editor_content'>";
        $htmlContent .= $this->content;
        $htmlContent .= "</div>";

        $htmlContent .= "<label id='msm_defref_description_label-$parentRecord->parent_id-$parentRecord->id-$this->compid' class='msm_child_description_labels' for='msm_defref_description_label-$parentRecord->parent_id-$parentRecord->id-$this->compid'>Description: </label>";
        $htmlContent .= "<input id='msm_defref_description_input-$parentRecord->parent_id-$parentRecord->id-$this->compid' class='msm_child_description_inputs' placeholder='Insert description to search this element in future.' value='$this->description' disabled='disabled' name='msm_defref_description_input-$parentRecord->parent_id-$parentRecord->id-$this->compid'/>";

        $htmlContent .= "</div>";

        return $htmlContent;
    }

    public function displayPreview($id='')
    {
        $previewHtml = '';

        $previewHtml .= "<br />";
        $previewHtml .= "<div class='def'>";
        if (!empty($this->title))
        {
            $previewHtml .= "<span class='deftitle'>" . $this->title . "</span>";
        }

        if (!empty($this->type))
        {
            $previewHtml .= "<span class='deftype'>" . $this->type . "</span>";
        }
        $previewHtml .= "<br/>";


        $previewHtml .= "<div class='mathcontent'>";
        $previewHtml .= $this->content;
        $previewHtml .= "<br />";
        $previewHtml .= "</div>";

        $previewHtml .= "<br />";

        if (!empty($this->children))
        {
            $previewHtml .= "<ul class='defminibuttons'>";
            foreach ($this->children as $key => $associate)
            {
                $previewHtml .= $associate->displayPreview("def", $id ."-". $key);
            }
            $previewHtml .= "</ul>";
        }
        
        $previewHtml .= "</div>";
        $previewHtml .= "<br />";

        return $previewHtml;
    }

}

?>
