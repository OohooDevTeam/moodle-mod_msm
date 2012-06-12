<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Problem
 *
 * @author User
 */
class Problem extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_problem';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->suboridnates = array();

        $this->content = array();

        $problembodys = $DomElement->getElementsByTagName('problem.body');
        $doc = new DOMDocument();

        foreach ($problembodys as $prob)
        {
            foreach ($this->processSubordinate($prob, $position)->subordinates as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processSubordinate($prob, $position)->indexauthors as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processSubordinate($prob, $position)->indexglossarys as $indexglossary)
            {
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processSubordinate($prob, $position)->indexsymbols as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }

            foreach ($this->processSubordinate($prob, $position)->content as $content)
            {
                $this->content[] = $content;
            }
        }
    }

}

?>
