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
             $doc = new DOMDocument();
        
        $position = $position + 1;

        $subordinates = $DomElement->getElementsByTagName('subordinate');
       
        $length = $subordinates->length;

        for ($i = 0; $i < $length; $i++)
        {
            $hot = $subordinates->item(0)->getElementsByTagName('hot')->item(0);

            $position = $position + 1;
            $subordinate = new Subordinate($this->xmlpath);
            $subordinate->loadFromXml($subordinates->item(0), $position);
            $this->subordinates[] = $subordinate;

            $subordinates->item(0)->parentNode->replaceChild($hot, $subordinates->item(0));
        }
        

        $indexauthors = $DomElement->getElementsByTagName('index.author');
        $ialength = $indexauthors->length;
        for ($i = 0; $i < $ialength; $i++)
        {
            $position = $position + 1;
            $indexauthor = new MathIndex($this->xmlpath);
            $indexauthor->loadFromXml($indexauthors->item(0), $position);
            $this->indexauthors[] = $indexauthor;

            $indexauthors->item(0)->parentNode->removeChild($indexauthors->item(0));
        }

        $indexglossarys = $DomElement->getElementsByTagName('index.glossary');
        $iglength = $indexglossarys->length;
        for ($i = 0; $i < $iglength; $i++)
        {
            $position = $position + 1;
            $indexglossary = new MathIndex($this->xmlpath);
            $indexglossary->loadFromXml($indexglossarys->item(0), $position);
            $this->indexglossarys[] = $indexglossary;

            $indexglossarys->item(0)->parentNode->removeChild($indexglossarys->item(0));
        }
       
        $indexsymbols = $DomElement->getElementsByTagName('index.symbol');
        $islength = $indexsymbols->length;
        for ($i = 0; $i < $islength; $i++)
        {
            $position = $position + 1;
            $indexsymbol = new MathIndex($this->xmlpath);
            $indexsymbol->loadFromXml($indexsymbols->item(0), $position);
            $this->indexsymbols[] = $indexsymbol;

            $indexsymbols->item(0)->parentNode->removeChild($indexsymbols->item(0));
        }

        $element = $doc->importNode($DomElement, true);
        $this->content[] = $doc->saveXML($element);
        }
    }

}

?>
