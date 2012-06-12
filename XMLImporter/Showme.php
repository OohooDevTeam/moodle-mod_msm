<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Showme
 *
 * @author User
 */
class Showme extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_showme';
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

        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));

        $this->statements = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();

        $statements = $DomElement->getElementsByTagName('statement.showme');

        foreach ($statements as $st)
        {         
            foreach ($this->processSubordinate($st, $position)->subordinates as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processSubordinate($st, $position)->indexauthors as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processSubordinate($st, $position)->indexglossarys as $indexglossary)
            {
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processSubordinate($st, $position)->indexsymbols as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }

            foreach ($this->processSubordinate($st, $position)->content as $content)
            {
                $this->statements[] = $content;
            }
        }

        $answer_showmes = $DomElement->getElementsByTagName('answer.showme');
        foreach ($answer_showmes as $a)
        {
            $position = $position + 1;
            $answer_showme = new AnswerShowme($this->xmlpath);
            $answer_showme->loadFromXml($a, $position);
            $this->answer_showmes[] = $answer_showme;
        }
    }

}

?>
