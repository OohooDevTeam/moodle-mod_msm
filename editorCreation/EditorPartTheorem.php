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
 * EditorPartTheorem class inherits from the EditorElement class and it represents the
 * part thereom elements in the MSM editor.  EditorStatementTheorem class calls the methods associated with this class.
 * This class can also represent part theorem elements in theorem as a reference material.
 *
 */
class EditorPartTheorem extends EditorElement
{

    public $id;
    public $compid;
    public $content;
    public $caption;
    public $errorArray = array();
    public $subordinates = array();
    public $medias = array();

    function __construct()
    {
        $this->tablename = 'msm_part_theorem';
    }

    /**
     * This method is an abstract method inherited from EditorElement.  It finds the needed information for database table
     * from the POST object(from editor form submission).  This method is called by EditorStatementTheorem and this method calls 
     * EditorSubordinate class.
     * 
     * @param string $idNumber          the string id given in HTML form and if it is a reference material, it ends with "|ref"
     * @return \EditorPartTheorem
     */
    public function getFormData($idNumber)
    {
        $idParam = explode("|", $idNumber);

        if (sizeof($idParam) > 1)
        {
            $this->caption = $_POST['msm_theoremref_part_title-' . $idParam[0]];

            if ($_POST['msm_theoremref_part_content-' . $idParam[0]] != '')
            {
                $content = $_POST['msm_theoremref_part_content-' . $idParam[0]];
                $this->content = $this->processMath($content);

                foreach ($this->processImage($this->content) as $key => $media)
                {
                    $this->medias[] = $media;
                }

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
                $content = $_POST['msm_theorem_part_content-' . $idNumber];
                $this->content = $this->processMath($content);

                foreach ($this->processImage($this->content) as $key => $media)
                {
                    $this->medias[] = $media;
                }

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

    /**
     * This method is an abstract method inherited from EditorElement.  Its main purpose is to
     * insert the data obtained from the POST object via method above to the msm_part_theorem table and to 
     * insert structural data (its parent/sibling...etc) to the compositor table. This method also calls 
     * insertData method from EditorSubordinate class.
     * 
     * @global moodle_database $DB
     * @param integer $parentid         Database ID from msm_compositor of the parent element
     * @param integer $siblingid        Database ID from msm_compositor of the previous sibling element
     * @param integer $msmid            The instance ID of the MSM module.
     * @param string $ref               Optional param that indicates that its either from internal/external theorem 
     */
    public function insertData($parentid, $siblingid, $msmid, $ref = '')
    {
        global $DB;

        $data = new stdClass();

        if (empty($ref))
        {
            $data->partid = null;
            $data->counter = null;
            $data->equivalence_mark = null;
            $data->caption = $this->caption;
            $pParser = new DOMDocument();
            $pParser->loadHTML($this->content);
            $divs = $pParser->getElementsByTagName("div");

            if ($divs->length > 0)
            {
                $data->part_content = $this->content;
            }
            else
            {
                $data->part_content = "<div>$this->content</div>";
            }

            $this->id = $DB->insert_record($this->tablename, $data);
        }

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

        $media_sibliing = 0;
        $content = '';
        foreach ($this->medias as $key => $media)
        {
            $media->insertData($this->compid, $media_sibliing, $msmid);
            $media_sibliing = $media->compid;
            $content = $this->replaceImages($key, $media->image, $data->part_content, "div");
        }

        if (!empty($this->medias))
        {
            $this->content = $content;

            $data->id = $this->id;
            $data->part_content = $this->content;
            $this->id = $DB->update_record($this->tablename, $data);
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
     * displayData from the EditorSubordinate class.
     * 
     * @global moodle_database $DB
     * @return HTML string
     */
    public function displayData()
    {
        global $DB;

        $currentCompRecord = $DB->get_record("msm_compositor", array("id" => $this->compid));
        $parentStatementTheoremRecord = $DB->get_record("msm_compositor", array("id" => $currentCompRecord->parent_id));

        $idEnding = "$parentStatementTheoremRecord->parent_id-$currentCompRecord->parent_id-$this->compid";

        $htmlContent = '';
        $htmlContent .= "<div id='msm_theorem_part_container-$idEnding' class='msm_theorem_child'>";
        $htmlContent .= "<div id='msm_theorem_part_title_container-$idEnding' class='msm_theorem_part_title_containers'>";
        $htmlContent .= "<span style='visibility: hidden;'>Drag here to move this element.</span>";
        $htmlContent .= "</div>";
        $htmlContent .= "<label class='msm_theorem_part_tlabel' for='msm_theorem_part_title-$idEnding'>Part Theorem title: </label>";
        $htmlContent .= "<input id='msm_theorem_part_title-$idEnding' class='msm_theorem_part_title' placeholder='Title for this part of the theorem.' name='msm_theorem_part_title-$idEnding' disabled='disabled' value='$this->caption'/>";
        $htmlContent .= "<div id='msm_theorem_part_content-$idEnding' class='msm_theorem_content msm_editor_content'>";
        $htmlContent .= html_entity_decode($this->content);
//        $htmlContent .= $content;
        $htmlContent .= "</div>";

        $htmlContent .= "<div class='msm_subordinate_containers' id='msm_subordinate_container-parttheoremcontent$idEnding'>";
        $htmlContent .= "</div>";
        $htmlContent .= "<div class='msm_subordinate_result_containers' id='msm_subordinate_result_container-parttheoremcontent$idEnding'>";
        foreach ($this->subordinates as $subordinate)
        {
            $htmlContent .= $subordinate->displayData($idEnding);
        }
        $htmlContent .= "</div>";

        $htmlContent .= "</div>";

        return $htmlContent;
    }

    /**
     * This abstract method from EditoElement extracts appropriate information from the 
     * msm_part_theorem table and also triggers extraction of data from its children using the 
     * data given by the msm_compositor table. It calls the loadData method from the EditorSubordinate 
     * class.
     * 
     * @global moodle_database $DB
     * @param integer $compid           The database ID from the msm_compositor table
     * @return \EditorPartTheorem
     */
    public function loadData($compid)
    {
        global $DB;

        $partCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $partCompRecord->unit_id;

        $partRecord = $DB->get_record($this->tablename, array('id' => $this->id));

        $this->caption = $partRecord->caption;
        $this->content = $partRecord->part_content;

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
            }
        }

        return $this;
    }

    /**
     * This method is called by the EditorInfo class to display the part theorem as a reference material.
     * The information is hidden until the user triggers the display by clicking on the associate mini buttons.
     * 
     * @param string $parentId          End of HTML ID that made the parent(ie. statement theorem) HTML element unique
     * @return HTML string
     */
    function displayRefData($parentId)
    {
        $htmlContent = '';
        $htmlContent .= "<div id='msm_theoremref_part_container-$parentId-$this->compid' class='msm_theorem_child'>";
        $htmlContent .= "<div id='msm_theoremref_part_title_container-$parentId-$this->compid' class='msm_theoremref_part_title_containers'>";
        $htmlContent .= "<span style='visibility: hidden;'>Drag here to move this element.</span>";
        $htmlContent .= "</div>";
        $htmlContent .= "<label class='msm_theoremref_part_tlabel' for='msm_theoremref_part_title-$parentId-$this->compid'>Part Theorem title: </label>";
        $htmlContent .= "<input id='msm_theoremref_part_title-$parentId-$this->compid' class='msm_theoremref_part_title' placeholder='Title for this part of the theorem.' name='msm_theoremref_part_title-$parentId-$this->compid' disabled='disabled' value='$this->caption'/>";
        $htmlContent .= "<div id='msm_theoremref_part_content-$parentId-$this->compid' class='msm_theorem_content msm_editor_content'>";
        $htmlContent .= html_entity_decode($this->content);
        $htmlContent .= "</div>";

        $htmlContent .= "<div class='msm_subordinate_containers' id='msm_subordinate_container-theoremrefpart$parentId-$this->compid'>";
        $htmlContent .= "</div>";
        $htmlContent .= "<div class='msm_subordinate_result_containers' id='msm_subordinate_result_container-theoremrefpart$parentId-$this->compid'>";
        foreach ($this->subordinates as $subordinate)
        {
            $htmlContent .= $subordinate->displayData("$parentId-$this->compid");
        }
        $htmlContent .= "</div>";

        $htmlContent .= "</div>";

        return $htmlContent;
    }

    /**
     * This method is triggered when the View navigation button on the editor is clicked to show the preview of the unit to the user.
     * It generates the appropriate HTML code to display the information as it is layed out on the MSM editor not according to how
     * the elements are structured in the database.  Hence allowing user to preview the material while making changes without having to 
     * commit to saving it in the database.
     * For cases where the part theorem are a part of a reference theorem material, it will not appear till the associate button is 
     * triggered by a click.
     * 
     * @return HTML string
     */
    public function displayPreview()
    {
        $previewHtml = '';

        $previewHtml .= "<li>";
        if (!empty($this->caption))
        {
            $previewHtml .= "<span class='parttheoremtitle'>" . $this->caption . "</span>";
        }
        $previewHtml .= html_entity_decode($this->content);

        if (!empty($this->subordinates))
        {
            foreach ($this->subordinates as $subordinate)
            {
                $previewHtml .= $subordinate->displayPreview();
            }
        }

        $previewHtml .= "</li>";
        $previewHtml .= "<br />";

        return $previewHtml;
    }

}

?>