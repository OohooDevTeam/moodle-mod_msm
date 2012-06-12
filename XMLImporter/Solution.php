<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Solution
 *
 * @author User
 */
class Solution extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_solution';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
         $this->suboridnates = array();

        $this->content = array();

        $bodys = $DomElement->getElementsByTagName('solution.body');

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
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processSubordinate($b, $position)->indexsymbols as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }

            foreach ($this->processSubordinate($b, $position)->content as $content)
            {
                $this->content[] = $content;
            }
        }
    }

}

?>
