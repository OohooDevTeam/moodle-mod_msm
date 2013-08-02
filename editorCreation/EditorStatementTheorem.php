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
 * EditorStatementTheorem class inherits from the EditorElement class and it represents the
 * statement theorem elements(ie. main content body elments of theorem) in the MSM editor.
 * EditorTheorem class calls the methods associated with this class.
 * This class can also represent statement theorem elements in theorem as a reference material.
 *
 */
class EditorStatementTheorem extends EditorElement
{

    public $id;                         // database ID associated with the statement.theorem element in msm_statement_theorem table
    public $compid;                     // database ID associated with the statement.theorem element in msm_compositor table
    public $errorArray = array();       // HTML IDs of empty contents --> used to put colored border around the input/textarea to warn user of empty content
    public $content;                    // content elements associated with the statement.theorem element
    public $children = array();         // EditorPartTheorem objects associated with the statement.theorem element
    public $subordinates = array();     // EditorSubordinate objects associated with the statement.theorem element
    public $medias = array();           // EditorMedia objects associated with the statement.theorem element
    public $isRef;                      // database ID associated with the referenced already-existing statement.theorem element in msm_compositor table

    // constructor for this class
    function __construct()
    {
        $this->tablename = 'msm_statement_theorem';
    }

    /**
     * This method is an abstract method inherited from EditorElement.  It finds the needed information for database table
     * from the POST object(from editor form submission).  This method is called by EditorTheorem and this method calls 
     * EditorSubordinate and EditorPartTheorem classes.
     * 
     * @param string $idNumber          the string id given in HTML form and if it is a reference material, it ends with "|ref"
     * @param string $ref             database ID of already existing theorem that is identical to current one
     * @return \EditorStatementTheorem
     */
    public function getFormData($idNumber, $ref = '')
    {
        $idInfo = explode("|", $idNumber);

        // the statement theorem is a part of a reference theorem material
        if (sizeof($idInfo) > 1)
        {
            if ($_POST["msm_theoremref_content_input-" . $idInfo[0]] != '')
            {
                $content = $_POST['msm_theoremref_content_input-' . $idInfo[0]];
                $this->content = $this->processMath($content);

                if (empty($ref))
                {
                    foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                    {
                        $this->subordinates[] = $subordinates;
                    }

                    foreach ($this->processImage($this->content) as $key => $media)
                    {
                        $this->medias[] = $media;
                    }
                }
            }
            else
            {
                $this->errorArray[] = 'msm_theoremref_content_input-' . $idInfo[0] . '_ifr';
            }

            $partmatch = "/^msm_theoremref_part_content-$idInfo[0]-\d+$/";

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

                    $idParam = $newId . "|ref";

                    $partTheorem = new EditorPartTheorem();
                    $partTheorem->getFormData($idParam);
                    $this->children[] = $partTheorem;
                }
            }
        }
        else if (sizeof($idInfo) == 1)
        { // the statement theorem is a part of a theorem material
            if ($_POST['msm_theorem_content_input-' . $idNumber] != '')
            {
                $content = $_POST['msm_theorem_content_input-' . $idNumber];
                $this->content = $this->processMath($content);

                foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                {
                    $this->subordinates[] = $subordinates;
                }

                foreach ($this->processImage($this->content) as $key => $media)
                {
                    $this->medias[] = $media;
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
                    $partTheorem->getFormData($newId, $this->isRef);
                    $this->children[] = $partTheorem;
                    $i++;
                }
            }
        }
        return $this;
    }

    /**
     * This method is an abstract method inherited from EditorElement.  Its main purpose is to
     * insert the data obtained from the POST object via method above to the msm_statement_theorem table and to 
     * insert structural data (its parent/sibling...etc) to the compositor table. This method also calls 
     * insertData method from EditorSubordinate and EditorPartTheorem classes.
     * 
     * @global moodle_database $DB
     * @param integer $parentid         Database ID from msm_compositor of the parent element
     * @param integer $siblingid        Database ID from msm_compositor of the previous sibling element
     * @param integer $msmid            The instance ID of the MSM module.
     * @param string $ref               Optional param that indicates that its either from internal/external theorem 
     */
    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();

        if (empty($this->isRef))
        {
            $pParser = new DOMDocument();
            $pParser->loadHTML($this->content);
            $divs = $pParser->getElementsByTagName("div");

            if ($divs->length > 0)
            {
                $data->statement_content = $this->content;
            }
            else
            {
                $data->statement_content = "<div>$this->content</div>";
            }

            $this->id = $DB->insert_record($this->tablename, $data);
        }
        else
        {
            $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->isRef), "prev_sibling_id");

            foreach ($childRecords as $child)
            {
                $childTable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));

                switch ($childTable->tablename)
                {
                    case "msm_part_theorem":
                        $partThr = new EditorPartTheorem();
                        $partThr->id = $child->unit_id;
                        $partThr->isRef = $child->id;
                        $this->children[] = $partThr;
                        break;
                    case "msm_subordinate":
                        $subord = new EditorSubordinate();
                        $subord->id = $child->unit_id;
                        $subord->isRef = $child->id;
                        $this->subordinates[] = $subord;
                        break;
                    case "msm_media":
                        $med = new EditorMedia();
                        $med->id = $child->unit_id;
                        $med->isRef = $child->id;
                        $this->medias[] = $med;
                        break;
                }
            }
        }

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

        $media_sibliing = 0;
        $content = '';
        foreach ($this->medias as $key => $media)
        {
            $media->insertData($this->compid, $media_sibliing, $msmid);
            $media_sibliing = $media->compid;
            if (empty($this->isRef))
            {
                $content = $this->replaceImages($key, $media->image, $data->statement_content, "div");
            }
        }

        if (empty($this->isRef))
        {
            if (!empty($this->medias))
            {
                $this->content = $content;

                $data->id = $this->id;
                $data->statement_content = $this->content;
                $this->id = $DB->update_record($this->tablename, $data);
            }
        }
        $subordinate_sibling = 0;
        foreach ($this->subordinates as $subordinate)
        {
            $subordinate->insertData($this->compid, $subordinate_sibling, $msmid);
            $subordinate_sibling = $subordinate->compid;
        }
    }

    /**
     * This method is an abstract method from EditorElement that has a purpose of displaying the 
     * data extracted from DB from loadData method by outputting the HTML code.  This method calls 
     * displayData from the EditorSubordinate and EditorPartTheorem classes.
     * 
     * @global moodle_database $DB
     * @return HTML string
     */
    public function displayData()
    {
        global $DB;

        $currentCompRecord = $DB->get_record("msm_compositor", array("id" => $this->compid));

        $htmlContent = '';

        $htmlContent .= "<div id='msm_theorem_statement_container-$currentCompRecord->parent_id-$this->compid' class='msm_theorem_statement_containers'>";
        $htmlContent .= "<div id='msm_theorem_statement_title_container-$currentCompRecord->parent_id-$this->compid' class='msm_theorem_statement_title_containers'>";
        $htmlContent .= "<b> Theorem Content </b>";
        $htmlContent .= "<span style='visibility: hidden;'>Drag here to move this element.</span>";
        $htmlContent .= "</div>";
        $htmlContent .= "<div id='msm_theorem_content_input-$currentCompRecord->parent_id-$this->compid' class='msm_unit_child_content msm_editor_content'>";
        $htmlContent .= html_entity_decode($this->content);
        $htmlContent .= "</div>";

        $htmlContent .= "<div class='msm_subordinate_containers' id='msm_subordinate_container-statementtheoremcontent$currentCompRecord->parent_id-$this->compid'>";
        $htmlContent .= "</div>";
        $htmlContent .= "<div class='msm_subordinate_result_containers' id='msm_subordinate_result_container-statementtheoremcontent$currentCompRecord->parent_id-$this->compid'>";
        foreach ($this->subordinates as $subordinate)
        {
            $htmlContent .= $subordinate->displayData($this->compid);
        }
        $htmlContent .= "</div>";

        $htmlContent .= "<div id='msm_theorem_part_droparea-$currentCompRecord->parent_id-$this->compid' class='msm_theorem_part_dropareas'>";
        foreach ($this->children as $partTheorem)
        {
            $htmlContent .= $partTheorem->displayData();
        }
        $htmlContent .= "<div class='msm_dnd_containers' id='msm_dnd_container-$currentCompRecord->parent_id-$this->compid'>Drag additional content to here.<p>Valid child Elements: Part of a Theorem</p></div>";
        $htmlContent .= "</div>";
        $htmlContent .= "</div>";

        return $htmlContent;
    }

    /**
     * This abstract method from EditorElement extracts appropriate information from the 
     * msm_statement_theorem table and also triggers extraction of data from its children using the 
     * data given by the msm_compositor table. It calls the loadData method from the EditorPartTheorem 
     * and EditorSubordinate classes.
     * 
     * @global moodle_database $DB
     * @param integer $compid           The database ID from the msm_compositor table
     * @return \EditorStatementTheorem
     */
    public function loadData($compid)
    {
        global $DB;

        $statementCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));
        $this->compid = $compid;
        $this->id = $statementCompRecord->unit_id;

        $statementRecord = $DB->get_record($this->tablename, array('id' => $this->id));
        $this->content = $statementRecord->statement_content;

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            switch ($childTable->tablename)
            {
                case "msm_subordinate":
                    $subordinate = new EditorSubordinate();
                    $subordinate->loadData($child->id);
                    $this->subordinates[] = $subordinate;
                    break;
                case "msm_part_theorem":
                    $partTheorem = new EditorPartTheorem();
                    $partTheorem->loadData($child->id);
                    $this->children[] = $partTheorem;
                    break;
            }
        }

        return $this;
    }

    /**
     * This method is called by the EditorInfo class to display the statement theorem as a reference material.
     * The information is hidden until the user triggers the display by clicking on the associate mini buttons.
     * 
     * @param string $parentId          End of HTML ID that made the parent(ie. theorem) HTML element unique
     * @return HTML string
     */
    function displayRefData($parentId)
    {
        $htmlContent = '';

        $htmlContent .= "<div id='msm_theoremref_statement_container-$parentId-$this->compid' class='msm_theoremref_statement_containers'>";
        $htmlContent .= "<div id='msm_theoremref_statement_title_container-$parentId-$this->compid' class='msm_theoremref_statement_title_containers'>";
        $htmlContent .= "<b> Theorem Content </b>";
        $htmlContent .= "<span style='visibility: hidden;'>Drag here to move this element.</span>";
        $htmlContent .= "</div>";
        $htmlContent .= "<div id='msm_theoremref_content_input-$parentId-$this->compid' class='msm_unit_child_content msm_editor_content'>";
        $htmlContent .= html_entity_decode($this->content);
        $htmlContent .= "</div>";

        $htmlContent .= "<div class='msm_subordinate_containers' id='msm_subordinate_container-theoremrefcontent$parentId-$this->compid'>";
        $htmlContent .= "</div>";
        $htmlContent .= "<div class='msm_subordinate_result_containers' id='msm_subordinate_result_container-theoremrefcontent$parentId-$this->compid'>";
        foreach ($this->subordinates as $subordinate)
        {
            $htmlContent .= $subordinate->displayData("$parentId-$this->compid");
        }
        $htmlContent .= "</div>";

        $htmlContent .= "<div id='msm_theoremref_part_droparea-$parentId-$this->compid' class='msm_theoremref_part_dropareas'>";
        foreach ($this->children as $partTheorem)
        {
            $htmlContent .= $partTheorem->displayRefData("$parentId-$this->compid");
        }
        $htmlContent .= "<div class='msm_dnd_containers' id='msm_dnd_container-$parentId-$this->compid'>Drag additional content to here.<p>Valid child Elements: Part of a Theorem</p></div>";
        $htmlContent .= "</div>";
        $htmlContent .= "</div>";

        return $htmlContent;
    }

    /**
     * This method is triggered when the View navigation button on the editor is clicked to show the preview of the unit to the user.
     * It generates the appropriate HTML code to display the information as it is layed out on the MSM editor not according to how
     * the elements are structured in the database.  Hence allowing user to preview the material while making changes without having to 
     * commit to saving it in the database.
     * For cases where the statement theorem are a part of a reference theorem material, it will not appear till the associate button is 
     * triggered by a click.
     * 
     * @return HTML string 
     */
    public function displayPreview()
    {
        $previewHtml = '';

        $previewHtml .= html_entity_decode($this->content);

        if (!empty($this->subordinates))
        {
            foreach ($this->subordinates as $subordinate)
            {
                $previewHtml .= $subordinate->displayPreview();
            }
        }

        $previewHtml .= "<ol class='parttheorem' style='list-style-type:lower-roman;'>";
        foreach ($this->children as $childComponent)
        {
            $previewHtml .= $childComponent->displayPreview();
        }
        $previewHtml .= "</ol>";

        return $previewHtml;
    }

}

?>