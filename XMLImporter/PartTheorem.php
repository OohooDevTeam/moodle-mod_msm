<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PartTheorem
 *
 * @author User
 */
class PartTheorem extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_part_theorem';
    }

    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->content = array();
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();

        $this->partid = $DomElement->getAttribute('partid');
        $this->counter = $DomElement->getAttribute('counter');
        $this->equiv_mark = $DomElement->getAttribute('equivalence.mark');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $part_bodys = $DomElement->getElementsByTagName('part.body');
        foreach ($part_bodys as $parb)
        {
            foreach ($this->processSubordinate($parb, $position)->subordinates as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processSubordinate($parb, $position)->indexauthors as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processSubordinate($parb, $position)->indexglossarys as $indexglossary)
            {
                $this->indexglossarys[] = $subordinate;
            }

            foreach ($this->processSubordinate($parb, $position)->indexsymbols as $indexsymbol)
            {
                $this->indexsymbols[] = $subordinate;
            }

            foreach ($this->processSubordinate($parb, $position)->content as $content)
            {
                $this->content[] = $content;
            }
        }
    }

}

?>
