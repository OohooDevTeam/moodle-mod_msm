<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Class representing ol, ul and math.display elements in the transformed XML files.
 *
 * @author User
 */
class InContent extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_content';
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
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();

        //determining the element node of the passed DOMElement to identify the type in DB field
        $nameofElement = $DomElement->tagName;

        switch ($nameofElement)
        {
            case('ol'):
                $this->type = 'ordered';
                $this->additional_attribute = $DomElement->getAttribute('type');
                break;
            case('ul'):
                $this->type = 'unordered';
                $this->additional_attribute = $DomElement->getAttribute('bullet');
                break;
            case('math.display'):
                $this->type = 'display';
                $this->additional_attribute = $DomElement->getAttribute('id');
                break;
        }

        $position = $position + 1;

        $parabodys = $DomElement->getElementsByTagName('para.body');
        $doc = new DOMDocument();

        foreach ($parabodys as $p)
        {
            $position = $position + 1;


            $subordinates = $p->getElementsByTagName('subordinate');

            foreach ($subordinates as $s)
            {
                $hot = $s->getElementsByTagName('hot')->item(0);

                $position = $position + 1;
                $subordinate = new Subordinate($this->xmlpath);
                $subordinate->loadFromXml($s, $position);
                $this->subordinates[] = $subordinate;

                $s->parentNode->replaceChild($hot, $s);
            }

            $indexauthors = $p->getElementsByTagName('index.author');
            foreach ($indexauthors as $ia)
            {
                $position = $position + 1;
                $indexauthor = new MathIndex($this->xmlpath);
                $indexauthor->loadFromXml($ia, $position);
                $this->indexauthors[] = $indexauthor;

                $ia->parentNode->removeChild($ia);
            }

            $indexglossarys = $p->getElementsByTagName('index.glossary');
            foreach ($indexglossarys as $ig)
            {
                $position = $position + 1;
                $indexglossary = new MathIndex($this->xmlpath);
                $indexglossary->loadFromXml($ig, $position);
                $this->indexglossarys[] = $indexglossary;

                $ig->parentNode->removeChild($ig);
            }

            $indexsymbols = $p->getElementsByTagName('index.symbol');
            foreach ($indexsymbols as $is)
            {
                $position = $position + 1;
                $indexsymbol = new MathIndex($this->xmlpath);
                $indexsymbol->loadFromXml($is, $position);
                $this->indexsymbols[] = $indexsymbol;

                $is->parentNode->removeChild($is);
            }

            $element = $doc->importNode($p, true);
            $this->content[] = $doc->saveXML($element);
        }
    }

}

?>
