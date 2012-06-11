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
        $this->caption = $this->getDomAttribute($DomElement->getElementsByTagName('caption'));
        $this->bodys = array();
        $this->subordinates = array();
        $bodys = $DomElement->getElementsByTagName('answer.exercise.block.body');
        foreach ($bodys as $key=>$b)
        {
            $position = $position + 1;
            $content = new stdClass();
            $content = $this->getContent($b, $position, $this->xmlpath);

            $this->bodys[] = $content->content;
            
            foreach($content->subordinates as $subordinate)
            {
                $this->subordinates[$key][] = $subordinate;
            }
        }
    }

}

?>
