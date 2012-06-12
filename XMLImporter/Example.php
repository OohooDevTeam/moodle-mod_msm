<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Example
 *
 * @author User
 */
class Example extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_example';
        $this->childtable = 'msm_statement_example';
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

        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));

        $this->description = $this->getDomAttribute($DomElement->getElementsByTagName('description'));

        $this->statement_examples = array();
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();

        $statement_examples = $DomElement->getElementsByTagName('statement.example');

        foreach ($statement_examples as $statement_ex)
        {
            foreach ($this->processSubordinate($statement_ex, $position)->subordinates as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processSubordinate($statement_ex, $position)->indexauthors as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processSubordinate($statement_ex, $position)->indexglossarys as $indexglossary)
            {
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processSubordinate($statement_ex, $position)->indexsymbols as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }

            foreach ($this->processSubordinate($statement_ex, $position)->content as $content)
            {
                $this->statement_examples[] = $content;
            }
        }
        
        $part_examples = $DomElement->getElementsByTagName('part.example');

        $this->part_examples = array();

        foreach ($part_examples as $part_ex)
        {
            $position = $position + 1;
            $part_example = new PartExample($this->xmlpath);
            $part_example->loadFromXml($part_ex, $position);
            $this->part_examples[] = $part_example;
        }
    }

}

?>
