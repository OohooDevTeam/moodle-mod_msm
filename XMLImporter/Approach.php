<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Approach
 *
 * @author User
 */
class Approach extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_approach';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->version = $DomElement->getAttribute('version');

        $this->content = array();
        $this->answerexercises = array();
        $this->solutions = array();
        $this->subordinates = array();

        foreach ($DomElement->childNodes as $key => $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == 'solution.hint')
                {
                    $position = $position + 1;
                    $content = new stdClass();
                    $content = $this->getContent($child, $position, $this->xmlpath);
                    $this->content[] = $content->content;

                    foreach ($content->subordinates as $subordinate)
                    {
                        $this->subordinates[$key][] = $subordinate;
                    }
                }
                if ($child->tagName == 'answer.exercise')
                {
                    $answerexercises = $child->getElementsByTagName('answer.exercise.block');

                    foreach ($answerexercises as $ans)
                    {
                        $position = $position + 1;
                        $answerexerciseblock = new AnswerExercise($this->xmlpath);
                        $answerexerciseblock->loadFromXml($ans, $position);
                        $this->answerexercises[] = $answerexerciseblock;
                    }
                }
                if ($child->tagName == 'solution')
                {
                    $position = $position + 1;
                    $solution = new Solution($this->xmlpath);
                    $solution->loadFromXml($child, $position);
                    $this->solutions[] = $solution;
                }
            }
        }
    }

}

?>
