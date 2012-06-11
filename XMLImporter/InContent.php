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
        $this->subordinates = array();
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

            foreach ($this->processSubordinate($p, $position)->subordinates as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processSubordinate($p, $position)->indexauthors as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processSubordinate($p, $position)->indexglossarys as $indexglossary)
            {
                $this->indexglossarys[] = $subordinate;
            }

            foreach ($this->processSubordinate($p, $position)->indexsymbols as $indexsymbol)
            {
                $this->indexsymbols[] = $subordinate;
            }

            foreach ($this->processSubordinate($p, $position)->content as $content)
            {
                $this->content[] = $content;
            }
        }
    }

}

?>
