<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Loading and storing for answer.exercise,block element
 *
 * @author User
 */
class AnswerExercise extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_answer_exercise';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));
        $this->content = array();
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();

        $bodys = $DomElement->getElementsByTagName('answer.exercise.block.body');
        foreach ($bodys as $b)
        {
            foreach ($this->processSubordinate($b, $position)->subordinates as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processSubordinate($b, $position)->indexauthors as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processSubordinate($b, $position)->indexglossarys as $indexglossary)
            {
                $this->indexglossarys[] = $subordinate;
            }

            foreach ($this->processSubordinate($b, $position)->indexsymbols as $indexsymbol)
            {
                $this->indexsymbols[] = $subordinate;
            }

            foreach ($this->processSubordinate($b, $position)->content as $content)
            {
                $this->content[] = $content;
            }
        }
    }

}

?>
