<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorIntro
 *
 * @author User
 */
class EditorIntro extends EditorElement
{

    public $position;
    public $id;
    public $compid;
    public $title;
    public $errorArray = array();
    public $blocks = array();

    function __construct()
    {
        $this->tablename = "msm_intro";
    }

    public function getFormData($idNumber)
    {
        $intromatch = '/^msm_intro_content_.*/';
        $childmatch = '/^msm_intro_child_content-.*/';

        $i = 0;
        foreach ($_POST as $elementID => $value)
        {
            if ((preg_match($intromatch, $elementID)) || (preg_match($childmatch, $elementID)))
            {
                $block = new EditorBlock();
                $block->getFormData($elementID);

                $this->blocks[] = $block;
            }
            $i++;
        }

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $data->intro_caption = $this->blocks[0]->title;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

        $sibling_id = 0;

        foreach ($this->blocks as $block)
        {
            $block->insertData($this->compid, $sibling_id, $msmid);
            $sibling_id = $block->compid;
        }
    }

    public function displayData()
    {
        $htmlContent = '';
        $htmlContent .= "<div id='copied_msm_intro-$this->compid' class='copied_msm_structural_element' style='padding-top:2%;'>";
        $htmlContent .= "<div id='msm_element_title_container-$this->compid' class='msm_element_title_containers'>";
        $htmlContent .= "<b style='margin-left: 43%; margin-right: 0%; margin-top: 2%; margin-bottom: 2%;'> INTRODUCTION </b>";
        $htmlContent .= "<span style='visibility: hidden;'>Drag here to move this element.</span>";
        $htmlContent .= "</div>";

        $htmlContent .= "<div style='margin-top: 2%;'>";
        $htmlContent .= "<label id='msm_intro_title_label-$this->compid' class='msm_unit_intro_title_labels' for='msm_intro_title_input-$this->compid'>Title: </label>";
        $htmlContent .= "<input id='msm_intro_title_input-$this->compid' class='msm_unit_intro_title' placeholder='Optional Title for the introduction.' name='msm_intro_title_input-$this->compid' disabled='disabled' value='$this->title'/>";
        $htmlContent .= "</div>";

        $htmlContent .= "<div id='msm_intro_content_input-$this->compid' class='msm_editor_content'>";

        if (!empty($this->children))
        {
            foreach ($this->children as $content)
            {
                $htmlContent .= $content->displayData();
            }
            $htmlContent .= "</div>";

            $htmlContent .= "<div id='msm_intro_child_container'>";
        }
        else
        {
            foreach ($this->blocks[0]->content as $content)
            {
                $htmlContent .= $content->displayData();
            }
            $htmlContent .= "</div>";

            $htmlContent .= "<div id='msm_intro_child_container'>";
            for ($i = 1; $i < sizeof($this->blocks); $i++)
            {
                $htmlContent .= $this->blocks[$i]->displayData();
            }
        }


        $htmlContent .= "</div>";

        $htmlContent .= "<input id='msm_intro_child_button-$this->compid' class='msm_intro_child_buttons' type='button' value='Add additional content' onclick='addIntroContent($this->compid)' disabled='disabled'/>";
        $htmlContent .= "</div>";

        return $htmlContent;
    }

    // compid in this case is string of all intro compid's under same unit
    public function loadData($compid)
    {
        global $DB;

        $this->children = array();

        $introCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $introCompRecord->unit_id;

        $introRecord = $DB->get_record($this->tablename, array('id' => $this->id));

        $this->title = $introRecord->intro_caption;

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            switch ($childTable->tablename)
            {
                case "msm_block":
                    $block = new EditorBlock();
                    $block->loadData($child->id);
                    $this->blocks[] = $block;
                    break;
                // need these for the legacy material to load properly for now
                // due to changing the db structure to include block table
                // delete after demo
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
            }

//            if ($childTable->tablename == 'msm_block')
//            {
//                $block = new EditorBlock();
//                $block->loadData($child->id);
//                $this->blocks[] = $block;
//            }
//            else
//            {
//                echo "intro has another child element" . $childTable->tablename;
//                break;
//            }
        }

        return $this;
    }

    public function displayPreview($id = '')
    {
        $previewHtml = '';


        if (!empty($this->title))
        {
            $previewHtml .= "<h3>$this->title</h3>";
        }

        foreach ($this->blocks as $key => $block)
        {
            $previewHtml .= $block->displayPreview($id);
        }

        return $previewHtml;
    }

}

?>
