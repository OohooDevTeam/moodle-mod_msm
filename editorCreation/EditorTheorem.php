<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                       **
 * @subpackage  msm                                                       **
 * @name        msm                                                       **
 * @copyright   University of Alberta                                     **
 * @link        http://ualberta.ca                                        **
 * @author      Ga Young Kim                                              **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************* */

/**
 * EditorTheorem class inherits from the EditorElement class and it represents the
 * theorem elements in the MSM editor.  This class can be representing theorem element as 
 * a child of unit element or it can also be a reference meterial linked by an associate
 * element.
 *
 */
class EditorTheorem extends EditorElement
{

    public $id;
    public $compid;
    public $type;
    public $title;
    public $associateType;
    public $tablename;
    public $description;
    public $contents = array(); // statement theorem
    public $errorArray = array();
    public $children = array(); //associates
    public $isRef;

    public function __construct()
    {
        $this->tablename = 'msm_theorem';
    }

    /**
     * This method is an abstract method inherited from EditorElement.  It finds the needed information for database table
     * from the POST object(from editor form submission).  It calls the same method from another class(EditorAssociate) to process its
     * children's data.
     * 
     * @param string $idNumber          contains parent_HTML_id ending number and if it is a reference material, it ends with "|ref"
     * @return \EditorTheorem
     */
    public function getFormData($idNumber)
    {
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

            if ($idNumberInfo[1] != "ref")
            {
                foreach ($_POST as $key => $value)
                {
                    $pattern = "msm_theoremref_description_input-$newTid";
                    if (strpos($key, $pattern) !== false)
                    {
                        $this->description = $value;
                        $newkey = explode("__", $key);
                        $this->isRef = $newkey[1];
                        break;
                    }
                }
            }
            else
            {
                $this->description = $_POST['msm_theoremref_description_input-' . $newTid];
            }

            $this->title = $_POST['msm_theoremref_title_input-' . $newTid];

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

    /**
     * This method is an abstract method inherited from EditorElement.  Its main purpose is to
     * insert the data obtained from the POST object via method above to the msm_theorem table and to 
     * insert structural data (its parent/sibling...etc) to the compositor table. This method also calls 
     * insertData method from EditorAssociate and EditorStatement classes.
     * 
     * @global moodle_database $DB
     * @param integer $parentid         Database ID from msm_compositor of the parent element
     * @param integer $siblingid        Database ID from msm_compositor of the previous sibling element
     * @param integer $msmid            The instance ID of the MSM module.
     */
    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $compData = new stdClass();

        if (!empty($this->isRef))
        {
            $existingTheorem = $DB->get_record("msm_compositor", array("id" => $this->isRef));
            $this->id = $existingTheorem->unit_id;

            $statementTable = $DB->get_record("msm_table_collection", array("tablename" => "msm_statement_theorem"));
            $partTable = $DB->get_record("msm_table_collection", array("tablename" => "msm_part_theorem"));

            $statementTheorems = $DB->get_records("msm_compositor", array("parent_id" => $existingTheorem->id, "table_id" => $statementTable->id), "prev_sibling_id");

            $i = 0;
            foreach ($statementTheorems as $statement)
            {
                $this->content[$i]->id = $statement->unit_id;

                $partTheorems = $DB->get_records("msm_compositor", array("parent_id" => $statement->id, "table_id" => $partTable->id), "prev_sibling_id");

                $j = 0;
                foreach ($partTheorems as $part)
                {
                    $this->content[$i]->children[$j]->id = $part->unit_id;
                    $j++;
                }
                $i++;
            }
        }
        else
        {
            $data->theorem_type = $this->type;
            $data->caption = $this->title;
            $data->description = $this->description;

            $this->id = $DB->insert_record($this->tablename, $data);
        }

        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record("msm_table_collection", array("tablename" => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record("msm_compositor", $compData);

        $sibling_id = 0;
        foreach ($this->contents as $key => $statementTheorem)
        {
            $statementTheorem->insertData($this->compid, $sibling_id, $msmid, $this->isRef);
            $sibling_id = $statementTheorem->compid;
        }

        $sibling_id = 0;

        foreach ($this->children as $associate)
        {
            $associate->insertData($this->compid, $sibling_id, $msmid);
            $sibling_id = $associate->compid;
        }
    }

    /**
     * This method is an abstract method from EditorElement that has a purpose of displaying the 
     * data extracted from DB from loadData method by outputting the HTML code.  This method calls 
     * displayData from the EditorAssociate and EditorStatement classes.
     * 
     * @return HTML string
     */
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

//        $htmlContent .= "<input id='msm_theorem_child_button-$this->compid' class='msm_theorem_child_buttons' type='button' value='Add content' onclick='addTheoremContent(event)' disabled='disabled'/>";
        $htmlContent .= "</div>";

//        $htmlContent .= "<div class='msm_subordinate_containers' id='msm_subordinate_container-statementtheoremcontent$this->compid'>";
//        $htmlContent .= "</div>";
//        $htmlContent .= "<div class='msm_subordinate_result_containers' id='msm_subordinate_result_container-statementtheoremcontent$this->compid'>";
//        foreach ($this->contents as $content)
//        {
//            foreach ($content->subordinates as $subordinate)
//            {
//                $htmlContent .= $subordinate->displayData();
//            }
//        }
//
//        $htmlContent .= "</div>";

        $htmlContent .= "<label id='msm_theorem_description_label-$this->compid' class='msm_child_description_labels' for='msm_theorem_description_label-$this->compid'>Description: </label>";
        $htmlContent .= "<input id='msm_theorem_description_input-$this->compid' class='msm_child_description_inputs' placeholder='Insert description to search this element in future.' value='$this->description' disabled='disabled' name='msm_theorem_description_input-$this->compid'/>";

        $htmlContent .= "<div id='msm_associate_container-$this->compid' class='msm_associate_containers'>";
        foreach ($this->children as $associate)
        {
            $htmlContent .= $associate->displayData();
        }
//        $htmlContent .= "<input id='msm_associate_button-$this->compid' class='msm_associate_buttons' type='button' value='Add Associated Information' onclick='addAssociateForm($this->compid, \"theorem\")' disabled='disabled'/>";
        $htmlContent .= "<div class='msm_dnd_containers' id='msm_dnd_container-$this->compid'>Drag additional content to here.<p>Valid child Elements: Associates, internal and/or external references</p></div>";

        $htmlContent .= "</div>";

        $htmlContent .= "</div>";

        return $htmlContent;
    }

    /**
     * This abstract method from EditoElement extracts appropriate information from the 
     * msm_theorem table and also triggers extraction of data from its children using the 
     * data given by the msm_compositor table. It calls the loadData method from the EditorAssociate 
     * and EditorStatementTheorem classes.
     * 
     * @global moodle_database $DB
     * @param integer $compid           The database ID from the msm_compositor table
     * @return \EditorTheorem
     */
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

    /**
     * This method is called by the EditorInfo class to display the theorem as a reference material.
     * The information is hidden until the user triggers the display by clicking on the associate mini buttons.
     * 
     * @global moodle_database $DB
     * @param string $parentId          End of HTML ID that made the parent(ie. associate) HTML element unique
     * @return HTML string
     */
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
        $htmlContent .= "<div class='msm_dnd_containers' id='msm_dnd_container-$parentId-$this->compid'>Drag additional content to here.<p>Valid child Elements: Associates, internal and/or external references</p></div>";

//        $htmlContent .= "<input id='msm_theoremref_child_button-$parentId-$this->compid' class='msm_theorem_child_buttons' type='button' value='Add content' onclick='addrefTheoremContent(event)' disabled='disabled'/>";
        $htmlContent .= "</div>";
        $htmlContent .= "<label id='msm_theoremref_description_label-$parentId-$this->compid' class='msm_child_description_labels' for='msm_theoremref_description_label-$parentId-$this->compid'>Description: </label>";

        $htmlContent .= "<input id='msm_theoremref_description_input-$parentId-$this->compid' class='msm_child_description_inputs' placeholder='Insert description to search this element in future.' value='$this->description' disabled='disabled' name='msm_theoremref_description_input-$parentId-$this->compid'/>";
        $htmlContent .= "</div>";

        return $htmlContent;
    }

    /**
     * This method is triggered when the View navigation button on the editor is clicked to show the preview of the unit to the user.
     * It generates the appropriate HTML code to display the information as it is layed out on the MSM editor not according to how
     * the elements are structured in the database.  Hence allowing user to preview the material while making changes without having to 
     * commit to saving it in the database.
     * For cases where theorem are a reference material, it will not appear till the associate button is 
     * triggered by a click.
     * 
     * @param string $id        String to be added to HTML ID of this theorem and its components to make them unique
     * @return HTML string
     */
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