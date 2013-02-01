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
    public $compid;

    function __construct()
    {
        $this->tablename = "msm_intro";
    }

    public function getFormData($idNumber, $position)
    {
        $this->position = $position;
        $this->blocks = array();

        $intromatch = '/^msm_intro_content_.*/';
        $childmatch = '/^msm_intro_child_content-.*/';

        $i = 0;
        foreach ($_POST as $elementID => $value)
        {
            if ((preg_match($intromatch, $elementID)) || (preg_match($childmatch, $elementID)))
            {
                $block = new EditorBlock();
                $block->getFormData($elementID, $i);

                $this->blocks[] = $block;
            }
            $i++;
        }

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        foreach ($this->blocks as $block)
        {
            $block->insertData($parentid, $siblingid, $msmid);
            $siblingid = $block->compid;
        }

        $this->compid = $siblingid;
    }

    public function displayData()
    {
        $htmlContent = '';
        $htmlContent .= "<div id='copied_msm_intro-$this->compid' class='copied_msm_structural_element' style='padding-top:2%;'>";
        $htmlContent .= "<div id='msm_element_title_container-$this->compid' class='msm_element_title_containers'>";
        $htmlContent .= "<b style='margin-left: 43%; margin-right: 0%; margin-top: 2%; margin-bottom: 2%;'> INTRODUCTION </b>";
        $htmlContent .= "</div>";

        $blockcaption = $this->blocks[0]->title;

        $htmlContent .= "<div style='margin-top: 2%;'>";
        $htmlContent .= "<label id='msm_intro_title_label-$this->compid' class='msm_unit_intro_title_labels' for='msm_intro_title_input-$this->compid'>Title: </label>";
        $htmlContent .= "<input id='msm_intro_title_input-$this->compid' class='msm_unit_intro_title' placeholder='Optional Title for the introduction.' name='msm_intro_title_input-$this->compid' disabled='disabled' value='$blockcaption'/>";
        $htmlContent .= "</div>";

        $htmlContent .= "<div id='msm_intro_content-input-$this->compid' class='msm_editor_content'>";
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
        $htmlContent .= "</div>";

        $htmlContent .= "<input id='msm_intro_child_button-$this->compid' class='msm_intro_child_buttons' type='button' value='Add additional content' onclick='addIntroContent($this->compid)' disabled='disabled'/>";
        $htmlContent .= "</div>";

        return $htmlContent;
    }

    // compid in this case is string of all intro compid's under same unit
    public function loadData($compid)
    {
        $introids = explode("|", $compid);

        $this->compid = $introids[0];

        foreach ($introids as $introcompid)
        {
            if (!empty($introcompid))
            {
                $block = new EditorBlock();
                $block->loadData($introcompid);
                $this->blocks[] = $block;
            }
        }



        return $this;
    }

}

?>
