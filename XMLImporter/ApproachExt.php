<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApproachExt
 *
 * @author User
 */
class ApproachExt extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_ext';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->type = null; //for attribute in answer.ext
        $this->version = $DomElement->getAttribute('version');
        $this->caption = null;
        $this->content = null;
        $this->ext_name = $DomElement->tagName;

        $this->answer_exercises = array();
        $answer_exercises = $DomElement->getElementsByTagName('answer.exercise');
        foreach ($answer_exercises as $ae)
        {
            $position = $position + 1;
            $answer_exercise = new AnswerExercise($this->xmlpath);
            $answer_exercise->loadFromXml($ae, $position);
            $this->answer_exercises[] = $answer_exercise;
        }

        $this->solution_exts = array();
        $solution_exts = $DomElement->getElementsByTagName('solution.ext');
        foreach ($solution_exts as $se)
        {
            $position = $position + 1;
            $solution_ext = new SolutionExt($this->xmlpath);
            $solution_ext->loadFromXml($se, $position);
            $this->solution_exts[] = $solution_ext;
        }
    }

    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();

        $data->ext_type = $this->type;
        $data->ext_version = $this->version;
        $data->caption = $this->caption;
        $data->ext_content = $this->content;
        $data->ext_name = $this->ext_name;

        $this->id = $DB->insert_record($this->tablename, $data);

        foreach ($this->answer_exercises as $answer_exercise)
        {
            $answer_exercise->saveIntoDb($answer_exercise->position);
        }

        foreach ($this->solution_exts as $solution_ext)
        {
            $solution_ext->saveIntoDb($solution_ext->position);
        }
    }

}

?>
