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

        $this->caption = $this->getDomAttribute($DomElement->getElementsByTagName('caption'));

        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));

        $this->description = $this->getDomAttribute($DomElement->getElementsByTagName('description'));

        $this->statement_examples = array();

        $statement_examples = $DomElement->getElementsByTagName('statement.example');

        foreach ($statement_examples as $statement_ex)
        {
            $position = $position + 1;
            $content = new stdClass();
            $content = $this->getContent($statement_ex, $position, $this->xmlpath);
            $this->statement_examples[] = $content->content;

            $part_examples = $statement_ex->getElementsByTagName('part.example');

            $this->part_examples = array();

            foreach ($part_examples as $part_ex)
            {
                $position = $position + 1;
                $part_example = new PartExample($this->xmlpath);
                $part_example->loadFromXml($part_ex, $position);
                $this->part_examples[] = $part_example;
            }

            $this->indexs = array();

            $index_authors = $statement_ex->getElementsByTagName('index.author');
            foreach ($index_authors as $inda)
            {
                $position = $position + 1;
                $index_author = new MathIndex($this->xmlpath);
                $index_author->loadFromXml($inda, $position);
                $this->indexs[] = $index_author;
            }
            $index_glossarys = $statement_ex->getElementsByTagName('index.glossary');
            foreach ($index_glossarys as $indg)
            {
                $position = $position + 1;
                $index_glossary = new MathIndex($this->xmlpath);
                $index_glossary->loadFromXml($indg, $position);
                $this->indexs[] = $index_glossary;
            }
            $index_symbols = $statement_ex->getElementsByTagName('index.symbol');
            foreach ($index_symbols as $inds)
            {
                $position = $position + 1;
                $index_symbol = new MathIndex($this->xmlpath);
                $index_symbol->loadFromXml($inds, $position);
                $this->indexs[] = $index_symbol;
            }
        }
    }

}

?>
