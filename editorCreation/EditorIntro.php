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

        $intromatch = '/^msm_intro_content.*/';
        $match = '/^msm_intro_child_content.*/';

        $i = 0;
        foreach ($_POST as $elementID => $value)
        {
            if (preg_match($match, $elementID))
            {
                $idInfo = explode("-", $elementID);

                $block = new EditorBlock();
                $block->getFormData($idInfo[1], $i);
                $this->blocks[] = $block;
            }
            else if (preg_match($intromatch, $elementID))
            {
                $block = new EditorBlock();
                $block->getFormData($idNumber, $i);
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

}

?>
