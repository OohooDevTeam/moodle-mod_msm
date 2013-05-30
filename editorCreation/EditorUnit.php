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
 * EditorUnit class inherits from the EditorElement class and it represents the
 * unit elements in the MSM editor.  The unit element in MSM is the top element
 * that can contain all the other elements as its child elements.  It can also have 
 * itself as a child element which creates nested structure.  These unit elements can be customized by 
 * user to be named differently according to the nested nature of the element.  For example,
 * the very top unit can be called a book then the child unit element of book can be named a book part,
 * then the next nested child element can be named chapter and so on.  When using the databased to access
 * data in other database tables representing other MSM elements, all elements can be accessed by parent-child
 * relationship to the unit by using the msm_compositor table.  Therefore, this class has both direct or indirect access
 * to all the other classes used in MSM.
 *
 */
class EditorUnit extends EditorElement
{

    public $id;
    public $compid;
    public $title;
    public $plain_title;
    public $description;
    public $unitName;
    public $short_name;
    public $children = array(); // need it for load/display part

    public function __construct()
    {
        $this->tablename = 'msm_unit';
    }

    /**
     *  This method is an abstract method inherited from EditorElement.  It finds the needed information for database table
     * from the POST object(from editor form submission).  
     * 
     * @global moodle_database $DB
     * @param integer $idNumber         represents MSM instace id passed through URL
     * @return \EditorUnit
     */
    public function getFormData($idNumber)
    {
        global $DB;
        $this->errorArray = array();

        $this->title = $_POST['msm_unit_title'];
        $this->description = $_POST['msm_unit_description_input'];
        $this->short_name = $_POST['msm_unit_short_title'];

        $this->unitName = $DB->get_record('msm_unit_name', array('msmid' => $idNumber, 'depth' => 0))->id;

        return $this;
    }

    /**
     * This method is an abstract method inherited from EditorElement.  Its main purpose is to
     * insert the data obtained from the POST object via method above to the msm_unit table and to 
     * insert structural data (its parent/sibling...etc) to the compositor table. 
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
        $data->title = $this->title;
        // TODO temporary value --> later will need to fix this
        $data->plain_title = $this->title;
        $data->description = $this->description;
        $data->short_name = $this->short_name;
        // when saving the unit for the first time, no structure is given(ie no subunit is specified until user structures the unit in hierarchy column)
        // so the default value is the name of the top unit specified by the user
        $data->compchildtype = $this->unitName;
        $data->standalone = 'false';

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $this->compid = $DB->insert_record('msm_compositor', $compData);
    }

    /**
     * This method is called by processTreeContent method in msmLoadUnit.php which is triggered to load by ajax call.
     * It's main function is to update the structural information in msm_compositor database table when the user
     * changes the unit structure by moving the hierarchy tree given on right panel of the MSM editting screen.
     * 
     * @global moodle_database $DB
     * @param string $idPair            contains the unit compositorID-unitID pairing given by jsTree id
     * @param integer $parent           this unit's parent compositor ID
     * @param integer $sibling          this unit's previous sibling compositor ID
     */
    public function updateUnitStructure($idPair, $parent, $sibling)
    {
        global $DB;

        $idInfo = explode("-", $idPair);

        $unitCompRecord = $DB->get_record("msm_compositor", array('id' => $idInfo[0]));
        $unitRecord = $DB->get_record($this->tablename, array('id' => $unitCompRecord->unit_id));
        $unittableRecord = $DB->get_record("msm_table_collection", array("tablename" => "msm_unit"));

        if ($parent != 0)
        {
            $parentCompRecord = $DB->get_record("msm_compositor", array('id' => $parent));
            $parentUnitRecord = $DB->get_record($this->tablename, array('id' => $parentCompRecord->unit_id));

            $parentCompTypeRecord = $DB->get_record("msm_unit_name", array('id' => $parentUnitRecord->compchildtype));

            $currentUnitDepth = $parentCompTypeRecord->depth + 1;
            $currentUnitCompType = $DB->get_record("msm_unit_name", array('depth' => $currentUnitDepth, 'msmid' => $parentCompRecord->msm_id));

            $newUnitData = new stdClass();
            $newUnitData->id = $unitRecord->id;
            $newUnitData->standalone = "false";
            $newUnitData->string_id = $unitRecord->string_id;
            $newUnitData->compchildtype = $currentUnitCompType->id;
            $newUnitData->title = $unitRecord->title;
            $newUnitData->plain_title = $unitRecord->plain_title;
            $newUnitData->short_name = $unitRecord->short_name;
            $newUnitData->creationdate = $unitRecord->creationdate;
            $newUnitData->last_revision_date = $unitRecord->last_revision_date;
            $newUnitData->acknowledgements = $unitRecord->acknowledgements;
            $newUnitData->description = $unitRecord->description;

            $DB->update_record($this->tablename, $newUnitData);

            $newCompData = new stdClass();
            $newCompData->id = $unitCompRecord->id;
            $newCompData->msm_id = $unitCompRecord->msm_id;
            $newCompData->table_id = $unitCompRecord->table_id;
            $newCompData->unit_id = $unitRecord->id;
            $newCompData->parent_id = $parent;
            $newCompData->prev_sibling_id = $sibling;

            $DB->update_record("msm_compositor", $newCompData);
        }
        else if ($parent === '')
        {
            $unitChildCompRecords = $DB->get_records("msm_compositor", array("parent_id" => $unitCompRecord->id, "table_id" => $unittableRecord->id));

            $unitParentCompRecords = $DB->get_records("msm_compositor", array("id" => $unitCompRecord->parent_id, "table_id" => $unittableRecord->id));

            if ((empty($unitChildCompRecords)) && (empty($unitParentCompRecords)))
            {
                $currentUnitDepth = -1;
                $currentUnitCompType = $DB->get_record("msm_unit_name", array('depth' => $currentUnitDepth, 'msmid' => $unitCompRecord->msm_id));

                $standData = new stdClass();
                $standData->id = $unitRecord->id;
                $standData->standalone = "true";
                $standData->string_id = $unitRecord->string_id;
                $standData->compchildtype = $currentUnitCompType->id;
                $standData->title = $unitRecord->title;
                $standData->plain_title = $unitRecord->plain_title;
                $standData->short_name = $unitRecord->short_name;
                $standData->creationdate = $unitRecord->creationdate;
                $standData->last_revision_date = $unitRecord->last_revision_date;
                $standData->acknowledgements = $unitRecord->acknowledgements;
                $standData->description = $unitRecord->description;

                $DB->update_record($this->tablename, $standData);
            }
        }
    }

    /**
     * This abstract method from EditoElement extracts appropriate information from the 
     * msm_unit table and also triggers extraction of data from its children using the 
     * data given by the msm_compositor table. It calls the loadData method from the EditorTheorem,
     * EditorDefinition, EditorComment, EditorPara, EditorInContent, EditorTable, EditorIntro and
     * EditorBlock classes.
     * 
     * @global moodle_database $DB
     * @param integer $compid           The database ID from the msm_compositor table
     * @return \EditorUnit
     */
    public function loadData($compid)
    {
        global $DB;

        $unitCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $unitCompRecord->unit_id;

        $unitRecord = $DB->get_record("msm_unit", array('id' => $this->id));

        $this->short_name = $unitRecord->short_name;
        $this->plain_title = $unitRecord->plain_title;
        $this->title = $unitRecord->title;
        $this->description = $unitRecord->description;

        $unitNameRecord = $DB->get_record('msm_unit_name', array('id' => $unitRecord->compchildtype));

        $this->unitName = $unitNameRecord->unitname;

        $childRecords = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');

        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            switch ($childTable->tablename)
            {
                case "msm_comment":
                    $comment = new EditorComment();
                    $comment->loadData($child->id);
                    $this->children[] = $comment;
                    break;
                case "msm_def":
                    $def = new EditorDefinition();
                    $def->loadData($child->id);
                    $this->children[] = $def;
                    break;
                case "msm_theorem":
                    $theorem = new EditorTheorem();
                    $theorem->loadData($child->id);
                    $this->children[] = $theorem;
                    break;

                case "msm_extra_info":
                    $extraInfo = new EditorExtraInfo();
                    $extraInfo->loadData($child->id);
                    $this->children[] = $extraInfo;
                    break;

                case "msm_intro":
                    $intro = new EditorIntro();
                    $intro->loadData($child->id);
                    $this->children[] = $intro;
                    break;

                case "msm_para":
                    $para = new EditorPara();
                    $para->loadData($child->id);
                    $this->children[] = $para;
                    break;
                case "msm_content":
                    $inContent = new EditorInContent();
                    $inContent->loadData($child->id);
                    $this->children[] = $inContent;
                    break;
                case "msm_table":
                    $table = new EditorTable();
                    $table->loadData($child->id);
                    $this->children[] = $table;
                    break;
//                case "msm_unit":
//                    $unitRecord = $DB->get_record("msm_unit", array('id' => $child->unit_id));
//                    if ($unitRecord->block_caption != null)
//                    {
//                        $block = new EditorBlock();
//                        $block->loadData($child->id);
//                        $this->children[] = $block;
//                    }
//                    break;
                case "msm_block":
                    $block = new EditorBlock();
                    $block->loadData($child->id);
                    $this->children[] = $block;
                    break;
            }
        }
        return $this;
    }

    /**
     * This method is an abstract method from EditorElement that has a purpose of displaying the 
     * data extracted from DB from loadData method by outputting the HTML code.  This method calls 
     * displayData from the EditorTheorem, EditorDefinition, EditorComment, EditorIntro and
     * EditorBlock classes.
     * 
     * @global moodle_database $DB
     * @return HTML string
     */
    public function displayData()
    {
        global $DB;

        $unitCompRecord = $DB->get_record('msm_compositor', array('id' => $this->compid));

        $unitNameRecords = $DB->get_records('msm_unit_name', array('msmid' => $unitCompRecord->msm_id), 'depth');

        $unitNameString = '';
        foreach ($unitNameRecords as $unitname)
        {
            $unitNameString .= $unitname->unitname . ",";
        }
        $unitNameString .= $unitCompRecord->msm_id;

        $htmlContent = '';

        $htmlContent .= "<div id='msm_unit_info_div'>";
        $htmlContent .= "<label id='msm_unit_title_label' class='msm_unit_title_labels' for='msm_unit_title'>$this->unitName title: </label>";
        $htmlContent .= "<input id='msm_unit_title' class='msm_title_input' placeholder = 'Please enter the title of this $this->unitName.' name='msm_unit_title' value='$this->plain_title' disabled='disabled'/>";

        $htmlContent .= "<label class='msm_unit_short_title_labels' for='msm_unit_short_title'> XML hierarchy Name: </label>";
        $htmlContent .= "<input class='msm_unit_short_titles' id='msm_unit_short_title' placeholder='Please enter short title for this $this->unitName' name='msm_unit_short_title' value='$this->short_name' disabled='disabled'/>";

        $htmlContent .= "<label id='msm_unit_description_label' class='msm_unit_description_labels' for='msm_unit_description_input'>Description: </label>";
        $htmlContent .= "<input id='msm_unit_description_input' class='msm_unit_description_inputs' placeholder = 'Insert description to search this element in future.' name='msm_unit_description_input' value='$this->description'  disabled='disabled'/>";
        $htmlContent .= "</div>";

        $htmlContent .= "<div id='msm_editor_middle_droparea'>";
        $htmlContent .= "<div id='msm_child_appending_area'>";

        $childOrderString = '';
        foreach ($this->children as $childElement)
        {
            $htmlContent .= $childElement->displayData();

            $className = get_class($childElement);
            switch ($className)
            {
                case "EditorDefinition":
                    $childOrderString .= "copied_msm_def-$childElement->compid";
                    break;
                case "EditorTheorem":
                    $childOrderString .= "copied_msm_theorem-$childElement->compid";
                    break;
                case "EditorComment":
                    $childOrderString .= "copied_msm_comment-$childElement->compid";
                    break;
                case "EditorIntro":
                    $childOrderString .= "copied_msm_intro-$childElement->compid";
                    break;
                case "EditorBlock":
                    $childOrderString .= "copied_msm_body-$childElement->compid";
                    break;

                case "EditorExtraInfo":
                    $childOrderString .= "copied_msm_extra_info-$childElement->compid";
                    break;
            }
            $childOrderString .= ",";
        }

        $childOrderString .= $unitCompRecord->msm_id;
        $htmlContent .= "</div>";

        $htmlContent .= "<input id='msm_child_order' style='visibility:hidden;' name='msm_child_order' value='$childOrderString'/>";
        $htmlContent .= "<input id='msm_unit_subordinate_container' name='msm_unit_subordinate_container' style='visibility: hidden;'/>";

        $htmlContent .= "</div>";
        $htmlContent .= '<input class="msm_editor_buttons" id="msm_editor_new" type="button" onclick="newUnit()" value="New Unit"/> 
                        <input type="button" class="msm_editor_buttons" id="msm_editor_remove" onclick="removeUnit(event)" value="Remove this Unit"/>';
        $htmlContent .= "<input id='msm_unit_name_input' value='$unitNameString' style='visibility:hidden;' name='msm_unit_name_input'/>";
        $htmlContent .= "<input id='msm_file_options' name='msm_file_options' style='display:none;'/>";

        return $htmlContent;
    }

    /**
     * This method updates the msm_unit database table whenever any of the information associated with unit is
     * editted(eg. unit title, description...etc).
     * 
     * @global moodle_database $DB
     * @param integer $compid           the database id from msm_compositor of the changed unit
     */
    function updateDbRecord($compid)
    {
        global $DB;

        $oldCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));

        $newCompData = new stdClass();
        $newUnitData = new stdClass();

        $newCompData->id = $compid;
        $newCompData->table_id = $oldCompRecord->table_id;
        $newCompData->msm_id = $oldCompRecord->msm_id;
        $newCompData->unit_id = $oldCompRecord->unit_id;
        $newCompData->parent_id = 0;
        $newCompData->prev_sibling_id = 0;

        $DB->update_record("msm_compositor", $newCompData);

        $oldUnitRecord = $DB->get_record($this->tablename, array("id" => $oldCompRecord->unit_id));

        $newUnitData->id = $oldCompRecord->unit_id;
        $newUnitData->title = $this->title;
        $newUnitData->plain_title = $this->title;
        $newUnitData->short_name = $this->short_name;
        $newUnitData->description = $this->description;
        $newUnitData->compchildtype = $this->unitName;
        $newUnitData->standalone = $oldUnitRecord->standalone;

        $DB->update_record($this->tablename, $newUnitData);

        $this->compid = $compid;
        $this->id = $oldCompRecord->unit_id;
    }

    /**
     * This method is triggered when the View navigation button on the editor is clicked to show the preview of the unit to the user.
     * It generates the appropriate HTML code to display the information as it is layed out on the MSM editor not according to how
     * the elements are structured in the database.  Hence allowing user to preview the material while making changes without having to 
     * commit to saving it in the database.
     * 
     * @return HTML string
     */
    public function displayPreview()
    {
        $previewHtml = '';

        if (!empty($this->title))
        {
            $previewHtml .= "<div class='title' style='text-align: center;'>";
            $previewHtml .= "<h2>";
            $previewHtml .= $this->title;
            $previewHtml .= "</h2>";
            $previewHtml .= "</div>";
        }

        return $previewHtml;
    }

}

?>
