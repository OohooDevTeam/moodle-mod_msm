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
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();

        $answer_showme_blocks = $DomElement->getElementsByTagName('answer.showme.block');

        foreach ($answer_showme_blocks as $asb)
        {
            $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

            $answer_showme_block_bodys = $asb->getElementsByTagName('answer.showme.block.body');

            foreach ($answer_showme_block_bodys as $asbb)
            {
                foreach ($this->processSubordinate($asbb, $position)->subordinates as $subordinate)
                {
                    $this->subordinates[] = $subordinate;
                }

                foreach ($this->processSubordinate($asbb, $position)->indexauthors as $indexauthor)
                {
                    $this->indexauthors[] = $indexauthor;
                }

                foreach ($this->processSubordinate($asbb, $position)->indexglossarys as $indexglossary)
                {
                    $this->indexglossarys[] = $subordinate;
                }

                foreach ($this->processSubordinate($asbb, $position)->indexsymbols as $indexsymbol)
                {
                    $this->indexsymbols[] = $subordinate;
                }

                foreach ($this->processSubordinate($asbb, $position)->content as $content)
                {
                    $this->content[] = $content;
                }
            }
        }
    }

}

?>
