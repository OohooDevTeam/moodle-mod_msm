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
        $this->solution_exts = array();

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                $name = $child->tagName;

                switch ($name)
                {
                    case('answer.exercise'):
                        $position = $position + 1;
                        $answer_exercise = new AnswerExercise($this->xmlpath);
                        $answer_exercise->loadFromXml($child, $position);
                        $this->answer_exercises[] = $answer_exercise;
                        break;
                    case('solution.ext'):
                        $position = $position + 1;
                        $solution_ext = new SolutionExt($this->xmlpath);
                        $solution_ext->loadFromXml($child, $position);
                        $this->solution_exts[] = $solution_ext;
                        break;
                }
            }
        }
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();

        $data->ext_type = $this->type;
        $data->ext_version = $this->version;
        $data->caption = $this->caption;
        $data->ext_content = $this->content;
        $data->ext_name = $this->ext_name;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
        
         $elementPositions = array();
        $sibling_id = null;

        if (!empty($this->answer_exercises))
        {
            foreach ($this->answer_exercises as $key => $answerexercise)
            {
                $elementPositions['answerexercise' . '-' . $key] = $answerexercise->position;
            }
        }

        if (!empty($this->solution_exts))
        {
            foreach ($this->solution_exts as $key => $solutionext)
            {
                $elementPositions['solutionext' . '-' . $key] = $solutionext->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {               
                case(preg_match("/^(answerexercise.\d+)$/", $element) ? true : false):
                    $answerexerciseString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $answerexercise = $this->answer_exercises[$answerexerciseString[1]];
                        $answerexercise->saveIntoDb($answerexercise->position, $this->compid);
                        $sibling_id = $answerexercise->compid;
                    }
                    else
                    {
                        $answerexercise = $this->answer_exercises[$answerexerciseString[1]];
                        $answerexercise->saveIntoDb($answerexercise->position, $this->compid, $sibling_id);
                        $sibling_id = $answerexercise->compid;
                    }
                    break;

                case(preg_match("/^(solutionext.\d+)$/", $element) ? true : false):
                    $solutionextString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $solutionext = $this->solution_exts[$solutionextString[1]];
                        $solutionext->saveIntoDb($solutionext->position, $this->compid);
                        $sibling_id = $solutionext->compid;
                    }
                    else
                    {
                        $solutionext = $this->solution_exts[$solutionextString[1]];
                        $solutionext->saveIntoDb($solutionext->position, $this->compid, $sibling_id);
                        $sibling_id = $solutionext->compid;
                    }
                    break;
            }
        }
    }

}

?>
