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

        $this->content = array();

        $problembodys = $DomElement->getElementsByTagName('problem.body');
        $doc = new DOMDocument();

        foreach ($problembodys as $prob)
        {
            $position = $position + 1;


            $subordinates = $prob->getElementsByTagName('subordinate');

            foreach ($subordinates as $s)
            {
                $hot = $s->getElementsByTagName('hot')->item(0);

                $position = $position + 1;
                $subordinate = new Subordinate($this->xmlpath);
                $subordinate->loadFromXml($s, $position);
                $this->subordinates[] = $subordinate;

                $s->parentNode->replaceChild($hot, $s);
            }

            $indexauthors = $prob->getElementsByTagName('index.author');
            foreach ($indexauthors as $ia)
            {
                $position = $position + 1;
                $indexauthor = new MathIndex($this->xmlpath);
                $indexauthor->loadFromXml($ia, $position);
                $this->indexauthors[] = $indexauthor;

                $ia->parentNode->removeChild($ia);
            }

            $indexglossarys = $prob->getElementsByTagName('index.glossary');
            foreach ($indexglossarys as $ig)
            {
                $position = $position + 1;
                $indexglossary = new MathIndex($this->xmlpath);
                $indexglossary->loadFromXml($ig, $position);
                $this->indexglossarys[] = $indexglossary;

                $ig->parentNode->removeChild($ig);
            }

            $indexsymbols = $prob->getElementsByTagName('index.symbol');
            foreach ($indexsymbols as $is)
            {
                $position = $position + 1;
                $indexsymbol = new MathIndex($this->xmlpath);
                $indexsymbol->loadFromXml($is, $position);
                $this->indexsymbols[] = $indexsymbol;

                $is->parentNode->removeChild($is);
            }

            $element = $doc->importNode($prob, true);
            $this->content[] = $doc->saveXML($element);
        }
    }

}

?>
