<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AnswerShowme
 *
 * @author User
 */
class AnswerShowme extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_answer_showme';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->content = array();
        $this->subordinates = array();

        $answer_showme_blocks = $DomElement->getElementsByTagName('answer.showme.block');

        foreach ($answer_showme_blocks as $asb)
        {
            $this->caption = $this->getDomAttribute($asb->getElementsByTagName('caption'));

            $answer_showme_block_bodys = $asb->getElementsByTagName('answer.showme.block.body');

            foreach ($answer_showme_block_bodys as $key=>$asbb)
            {
                $position = $position + 1;
                $content = new stdClass();
                $content = $this->getContent($asbb, $position, $this->xmlpath);
                $this->content[] = $content->content;

                foreach($content->subordinates as $subordinate)
            {
                $this->subordinates[$key][] = $subordinate;
            }
            }
        }
    }

}

?>
