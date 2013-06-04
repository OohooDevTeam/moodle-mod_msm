<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorExtraInfo
 *
 * @author User
 */
class EditorExtraInfo extends EditorElement
{

    public $id;
    public $compid;
    public $name;
    public $title;
//    public $errorArray = array();
    public $blocks = array();

    function __construct()
    {
        $this->tablename = "msm_extra_info";
    }

    public function getFormData($idNumber)
    {        
        $this->name = $_POST["msm_extra_type_dropdown-$idNumber"];

        $block = new EditorBlock();
        $block->getFormData('msm_extra_content_input-' . $idNumber);

        $this->blocks[] = $block;

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $data->extra_info_name = $this->name;

        $this->id = $DB->insert_record($this->tablename, $data);

        $classtable = $DB->get_record("msm_table_collection", array("tablename" => $this->tablename));

        $compdata = new stdClass();
        $compdata->msm_id = $msmid;
        $compdata->unit_id = $this->id;
        $compdata->table_id = $classtable->id;
        $compdata->parent_id = $parentid;
        $compdata->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record("msm_compositor", $compdata);

        $sibling_id = 0;
        foreach ($this->blocks as $block)
        {
            $block->insertData($this->compid, $sibling_id, $msmid);
            $sibling_id = $block->compid;
        }
    }

    public function loadData($compid)
    {
        global $DB;

        $unitCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $unitCompRecord->unit_id;

        $unitRecord = $DB->get_record($this->tablename, array('id' => $this->id));
        $this->name = $unitRecord->extra_info_name;

        $childRecords = $DB->get_records("msm_compositor", array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));

            if ($childTable->tablename == "msm_block")
            {
                $block = new EditorBlock();
                $block->loadData($child->id);
                $this->blocks[] = $block;
            }
        }
        $this->title = $this->blocks[0]->title;

        return $this;
    }

    public function displayData()
    {
        $htmlContent = '';

        $htmlContent .= "<div id='copied_msm_extra_info-$this->compid' class='copied_msm_structural_element'>";

        $htmlContent .= "<div class='msm_element_overlays' id='msm_element_overlay-$this->compid' style='display: none;'>";

        $htmlContent .= "<a class='msm_overlayButtons' id='msm_overlayButton_delete-$this->compid' onclick='deleteOverlayElement(event);'> Delete </a>";
        $htmlContent .= "<a class='msm_overlayButtons' id='msm_overlayButton_edit-$this->compid' onclick='editUnit(event);'> Edit </a>";

        $htmlContent .= "</div>";

        $htmlContent .= "<select id='msm_extra_type_dropdown-$this->compid' class='msm_unit_child_dropdown' name='msm_extra_type_dropdown-$this->compid' disabled='disabled'>";
        switch ($this->name)
        {
            case "Summary":
                $htmlContent .= "<option value='Summary' selected='selected'>Summary</option>";
                $htmlContent .= "<option value='Historical'>Historical</option>";
                $htmlContent .= "<option value='Trailer'>Trailer</option>";
                $htmlContent .= "<option value='Acknowledgements'>Acknowledgements</option>";
                $htmlContent .= "<option value='Preface'>Preface</option>";
                break;
            case "Historical":
                $htmlContent .= "<option value='Summary'>Summary</option>";
                $htmlContent .= "<option value='Historical' selected='selected'>Historical</option>";
                $htmlContent .= "<option value='Trailer'>Trailer</option>";
                $htmlContent .= "<option value='Acknowledgements'>Acknowledgements</option>";
                $htmlContent .= "<option value='Preface'>Preface</option>";
                break;
            case "Trailer":
                $htmlContent .= "<option value='Summary'>Summary</option>";
                $htmlContent .= "<option value='Historical'>Historical</option>";
                $htmlContent .= "<option value='Trailer' selected='selected'>Trailer</option>";
                $htmlContent .= "<option value='Acknowledgements'>Acknowledgements</option>";
                $htmlContent .= "<option value='Preface'>Preface</option>";
                break;
            case "Acknowledgements":
                $htmlContent .= "<option value='Summary'>Summary</option>";
                $htmlContent .= "<option value='Historical'>Historical</option>";
                $htmlContent .= "<option value='Trailer'>Trailer</option>";
                $htmlContent .= "<option value='Acknowledgements' selected='selected'>Acknowledgements</option>";
                $htmlContent .= "<option value='Preface'>Preface</option>";
                break;
            case "Preface":
                $htmlContent .= "<option value='Summary'>Summary</option>";
                $htmlContent .= "<option value='Historical'>Historical</option>";
                $htmlContent .= "<option value='Trailer'>Trailer</option>";
                $htmlContent .= "<option value='Acknowledgements'>Acknowledgements</option>";
                $htmlContent .= "<option value='Preface' selected='selected'>Preface</option>";
                break;
        }
        $htmlContent .= "</select>";

        $htmlContent .= "<div id='msm_element_title_container-$this->compid' class='msm_element_title_containers'>";
        $htmlContent .= "<b style='margin-left: 20%;'> Extra Information </b>";
        $htmlContent .= "<span style='visibility: hidden;'>Drag here to move this element.</span>";
        $htmlContent .= "</div>";

        $htmlContent .= "<input id='msm_extra_title_input-$this->compid' class='msm_unit_child_title' placeholder='Title of Extra Information' name='msm_extra_title_input-$this->compid' disabled='disabled' value='$this->title'/>";
        $htmlContent .= "<div id='msm_extra_content_input-$this->compid' class='msm_unit_child_content msm_editor_content'>";

        foreach ($this->blocks[0]->content as $content)
        {
            $htmlContent .= $content->displayData();
        }

        $htmlContent .= "</div>";

        $htmlContent .= "<div class='msm_subordinate_containers' id='msm_subordinate_container-extracontent$this->compid'>";
        $htmlContent .= "</div>";
        $htmlContent .= "<div class='msm_subordinate_result_containers' id='msm_subordinate_result_container-extracontent$this->compid'>";
        foreach ($this->blocks[0]->content as $content)
        {
            foreach ($content->subordinates as $subordinate)
            {
                $htmlContent .= $subordinate->displayData($this->compid);
            }
        }
        $htmlContent .= "</div>";

        $htmlContent .= "</div>";

        return $htmlContent;
    }

    public function displayPreview($id = '')
    {
        $previewHtml = '';

        if (!empty($this->name))
        {
            $previewHtml .= "<h3>$this->name</h3>";
        }

        foreach ($this->blocks as $key => $block)
        {
            $previewHtml .= $block->displayPreview($id);
        }

        return $previewHtml;
    }

}

?>
