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
        $doc = new DOMDocument();

        foreach ($part_example_bodys as $peb)
        {
            $position = $position + 1;


            $subordinates = $peb->getElementsByTagName('subordinate');

            foreach ($subordinates as $s)
            {
                $hot = $s->getElementsByTagName('hot')->item(0);

                $position = $position + 1;
                $subordinate = new Subordinate($this->xmlpath);
                $subordinate->loadFromXml($s, $position);
                $this->subordinates[] = $subordinate;

                $s->parentNode->replaceChild($hot, $s);
            }

            $indexauthors = $peb->getElementsByTagName('index.author');
            foreach ($indexauthors as $ia)
            {
                $position = $position + 1;
                $indexauthor = new MathIndex($this->xmlpath);
                $indexauthor->loadFromXml($ia, $position);
                $this->indexauthors[] = $indexauthor;

                $ia->parentNode->removeChild($ia);
            }

            $indexglossarys = $peb->getElementsByTagName('index.glossary');
            foreach ($indexglossarys as $ig)
            {
                $position = $position + 1;
                $indexglossary = new MathIndex($this->xmlpath);
                $indexglossary->loadFromXml($ig, $position);
                $this->indexglossarys[] = $indexglossary;

                $ig->parentNode->removeChild($ig);
            }

            $indexsymbols = $peb->getElementsByTagName('index.symbol');
            foreach ($indexsymbols as $is)
            {
                $position = $position + 1;
                $indexsymbol = new MathIndex($this->xmlpath);
                $indexsymbol->loadFromXml($is, $position);
                $this->indexsymbols[] = $indexsymbol;

                $is->parentNode->removeChild($is);
            }

            $element = $doc->importNode($peb, true);
            $this->content[] = $doc->saveXML($element);
        }
    }

}

?>
