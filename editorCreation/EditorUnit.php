<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorUnit
 *
 * @author User
 */
class EditorUnit extends EditorElement
{

    public $id;
    public $compid;
    public $title;
    public $description;
    public $position;
    public $unitName;
    public $children = array(); // need it for load/display part

    public function __construct()
    {
        $this->tablename = 'msm_unit';
    }

    // idNumber contains the msm id number
    public function getFormData($idNumber, $position)
    {
        global $DB;
        $this->position = $position;
        $this->errorArray = array();

        $this->title = $_POST['msm_unit_title'];
        $this->description = $_POST['msm_unit_description_input'];

        $this->unitName = $DB->get_record('msm_unit_name', array('msmid' => $idNumber, 'depth' => 0))->id;

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $data->title = $this->title;
        $data->description = $this->description;
        // when saving the unit for the first time, no structure is given(ie no subunit is specified until user structures the unit in hierarchy column)
        // so the default value is the name of the top unit specified by the user
        $data->compchildtype = $this->unitName;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $this->compid = $DB->insert_record('msm_compositor', $compData);
    }

    public function loadData($compid)
    {
        global $DB;

        $unitCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $unitCompRecord->unit_id;

        $unitRecord = $DB->get_record("msm_unit", array('id' => $this->id));

        $this->title = $unitRecord->title;
        $this->description = $unitRecord->description;

        $unitNameRecord = $DB->get_record('msm_unit_name', array('id' => $unitRecord->compchildtype));

        $this->unitName = $unitNameRecord->unitname;

        $childRecords = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');

        $introids = '';
        $insertionKey = null;
        $children = array();

        $index = 0; //keys responds to db id's so need index number for the array
        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            switch ($childTable->tablename)
            {
                case "msm_comment":
                    $comment = new EditorComment();
                    $comment->loadData($child->id);
                    $children[] = $comment;
                    break;
                case "msm_def":
                    $def = new EditorDefinition();
                    $def->loadData($child->id);
                    $children[] = $def;
                    break;
                case "msm_theorem":
                    $theorem = new EditorTheorem();
                    $theorem->loadData($child->id);
                    $children[] = $theorem;
                    break;
                case "msm_intro":
                    // intro needs to be processed differently as only one intro is allowed on editor
                    // but when saved to db, due to multiple blocks, it can have multiple intro db data
                    $introids .= $child->id . "|";
                    // index number of $this->children array where first intro belongs
                    if ($insertionKey == null)
                    {
                        $insertionKey = $index;
                    }
                    break;
                case "msm_unit":
                    $block = new EditorBlock();
                    $block->loadData($child->id);
                    $children[] = $block;
                    break;
//                default:
//                    echo "other tag" + $childTable->tablename;
                //missing other ones
            }
            $index++;
        }

        // to process intro elements
        if ($introids != '')
        {
            $intro = new EditorIntro();
            $intro->loadData($introids);

            if (!empty($insertionKey))
            {
                $tempArray = array();
                foreach ($children as $item)
                {
                    $tempArray[] = $item;
                }

                // insert intro at the right plce in the array and copy back to class propery
                $result = array_merge(array_slice($tempArray, 0, $insertionKey, true), array($insertionKey => $intro), array_slice($tempArray, $insertionKey, count($tempArray) - 1, false));

                foreach ($result as $resultItem)
                {
                    $this->children[] = $resultItem;
                }
            }
        }
        else
        {
            foreach ($children as $item)
            {
                $this->children[] = $item;
            }
        }
        return $this;
    }

//    
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
        $htmlContent .= "<label id='msm_unit_title_label-$this->compid' class='msm_unit_title_labels' for='msm_unit_title-$this->compid'>$this->unitName title: </label>";
        $htmlContent .= "<input id='msm_unit_title-$this->compid' class='msm_title_input' placeholder = 'Please enter the title of this $this->unitName.' name='msm_unit_title-$this->compid' value='$this->title' disabled='disabled'/>";

        $htmlContent .= "<label id='msm_unit_description_label-$this->compid' class='msm_unit_description_labels' for='msm_unit_description_input-$this->compid'>Description: </label>";
        $htmlContent .= "<input id='msm_unit_description_input-$this->compid' class='msm_unit_description_inputs' placeholder = 'Insert description to search this element in future.' name='msm_unit_description_input-$this->compid' value='$this->description'  disabled='disabled'/>";
        $htmlContent .= "</div>";

        $htmlContent .= "<div id='msm_editor_middle_droparea'>";
        $htmlContent .= "<div id='msm_child_appending_area'>";

        foreach ($this->children as $childElement)
        {
            $htmlContent .= $childElement->displayData();
        }

        $htmlContent .= "</div>";
        $htmlContent .= "<input id='msm_unit_name_input' value='$unitNameString' style='visibility:hidden;' name='msm_unit_name_input'/>";
        $htmlContent .= "</div>";

        return $htmlContent;
    }

}

?>
