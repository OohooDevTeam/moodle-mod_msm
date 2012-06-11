<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PartExample
 *
 * @author User
 */
class PartExample extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_part_example';
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
        $this->partid = $DomElement->getAttribute('partid');
        $this->counter = $DomElement->getAttribute('counter');
        $this->equiv_mark = $DomElement->getAttribute('equivalence.mark');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();

        $this->content = array();

        $part_example_bodys = $DomElement->getElementsByTagName('part.example.body');
        
        foreach ($part_example_bodys as $peb)
        {
            foreach ($this->processSubordinate($peb, $position)->subordinates as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processSubordinate($peb, $position)->indexauthors as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processSubordinate($peb, $position)->indexglossarys as $indexglossary)
            {
                $this->indexglossarys[] = $subordinate;
            }

            foreach ($this->processSubordinate($peb, $position)->indexsymbols as $indexsymbol)
            {
                $this->indexsymbols[] = $subordinate;
            }

            foreach ($this->processSubordinate($peb, $position)->content as $content)
            {
                $this->content[] = $content;
            }
        }
    }

}

?>
