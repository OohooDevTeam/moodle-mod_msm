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
        
        $this->partid = $DomElement->getAttribute('partid');
        $this->counter = $DomElement->getAttribute('counter');
        $this->equiv_mark = $DomElement->getAttribute('equivalence.mark');

        $this->caption = $this->getDomAttribute($DomElement->getElementsByTagName('caption'));

        $part_bodys = $DomElement->getElementsByTagName('part.body');
        foreach ($part_bodys as $parb)
        {
            $position = $position + 1;
            $content = new stdClass();
            $content = $this->getContent($parb, $position, $this->xmlpath);
            $this->content[] = $content->content;
        }

        $this->indexs = array();
        
        $index_authors = $DomElement->getElementsByTagName('index.author');
        foreach ($index_authors as $inda)
        {
            $position = $position + 1;
            $index_author = new MathIndex($this->xmlpath);
            $index_author->loadFromXml($inda, $position);
            $this->indexs[] = $index_author;
        }
        $index_glossarys = $DomElement->getElementsByTagName('index.glossary');
        foreach ($index_glossarys as $indg)
        {
            $position = $position + 1;
            $index_glossary = new MathIndex($this->xmlpath);
            $index_glossary->loadFromXml($indg, $position);
            $this->indexs[] = $index_glossary;
        }
        $index_symbols = $DomElement->getElementsByTagName('index.symbol');
        foreach ($index_symbols as $inds)
        {
            $position = $position + 1;
            $index_symbol = new MathIndex($this->xmlpath);
            $index_symbol->loadFromXml($inds, $position);
            $this->indexs[] = $index_symbol;
        }
    }

}

?>
