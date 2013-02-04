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

    public function updateCompRecord($idPair, $parent)
    {
        global $DB;

        $idInfo = explode("-", $idPair);

        $unitCompRecord = $DB->get_record("msm_compositor", array('id' => $idInfo[0]));
        $unitRecord = $DB->get_record($this->tablename, array('id' => $unitCompRecord->unit_id));

        if ($parent != 0)
        {
            $otherCompRecords = $DB->get_records('msm_compositor', array('parent_id' => $parent, 'table_id' => $unitCompRecord->table_id), 'prev_sibling_id');

            $tempArray = array();

            if (sizeof($otherCompRecords) > 0)
            {
                foreach ($otherCompRecords as $rec)
                {
                    $tempArray[] = $rec;
                }

                if (end($tempArray)->prev_sibling_id == 0)
                {
                    $lastSibling = $tempArray[0]->id;
                }
                else
                {
                    $lastSibling = end($tempArray)->id;
                }
            }
            else
            {
                $lastSibling = 0;
            }


            $parentCompRecord = $DB->get_record("msm_compositor", array('id' => $parent));
            $parentUnitRecord = $DB->get_record($this->tablename, array('id' => $parentCompRecord->unit_id));

            $parentCompTypeRecord = $DB->get_record("msm_unit_name", array('id' => $parentUnitRecord->compchildtype));

            $currentUnitDepth = $parentCompTypeRecord->depth + 1;
            $currentUnitCompType = $DB->get_record("msm_unit_name", array('depth' => $currentUnitDepth, 'msmid' => $parentCompRecord->msm_id));

            $newUnitData = new stdClass();
            $newUnitData->id = $unitRecord->id;
            $newUnitData->standalone = $unitRecord->standalone;
            $newUnitData->string_id = $unitRecord->string_id;
            $newUnitData->compchildtype = $currentUnitCompType->id;
            $newUnitData->title = $unitRecord->title;
            $newUnitData->plain_title = $unitRecord->plain_title;
            $newUnitData->creationdate = $unitRecord->creationdate;
            $newUnitData->last_revision_date = $unitRecord->last_revision_date;
            $newUnitData->block_caption = $unitRecord->block_caption;
            $newUnitData->acknowledgements = $unitRecord->acknowledgements;
            $newUnitData->description = $unitRecord->description;

            $DB->update_record($this->tablename, $newUnitData);

            $newCompData = new stdClass();
            $newCompData->id = $unitCompRecord->id;
            $newCompData->msm_id = $unitCompRecord->msm_id;
            $newCompData->table_id = $unitCompRecord->table_id;
            $newCompData->unit_id = $unitRecord->id;
            $newCompData->parent_id = $parent;
            $newCompData->prev_sibling_id = $lastSibling;

            $DB->update_record("msm_compositor", $newCompData);
        }

        return $unitCompRecord->id;
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

        $index = 0; //keys responds to db id's so need index number for the array
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
                    $unitRecord = $DB->get_record("msm_unit", array('id' => $child->unit_id));
                    if ($unitRecord->block_caption != null)
                    {
                        $block = new EditorBlock();
                        $block->loadData($child->id);
                        $this->children[] = $block;
                    }
                    break;
                default: //only para/content/table should go to this condition
                    $block = new EditorBlock();
                    $block->loadData($this->compid);
                    $this->children[] = $block;
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

            if ($insertionKey !== null)
            {
                $tempArray = array();
                foreach ($this->children as $item)
                {
                    $tempArray[] = $item;
                }

                // insert intro at the right plce in the array and copy back to class propery
                $result = array_merge(array_slice($tempArray, 0, $insertionKey, true), array($insertionKey => $intro), array_slice($tempArray, $insertionKey, count($tempArray) - 1, false));

                $this->children = array();

                foreach ($result as $resultItem)
                {
                    $this->children[] = $resultItem;
                }
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
        $htmlContent .= "<label id='msm_unit_title_label' class='msm_unit_title_labels' for='msm_unit_title'>$this->unitName title: </label>";
        $htmlContent .= "<input id='msm_unit_title' class='msm_title_input' placeholder = 'Please enter the title of this $this->unitName.' name='msm_unit_title' value='$this->title' disabled='disabled'/>";

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
            }
            $childOrderString .= ",";
        }

        $childOrderString .= $unitCompRecord->msm_id;
        $htmlContent .= "</div>";
        $htmlContent .= "<input id='msm_child_order' style='visibility:hidden;' name='msm_child_order' value='$childOrderString'/>";

        $htmlContent .= "</div>";
        $htmlContent .= "<input id='msm_unit_name_input' value='$unitNameString' style='visibility:hidden;' name='msm_unit_name_input'/>";


        return $htmlContent;
    }

}
?>
