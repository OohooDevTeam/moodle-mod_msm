<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Statement
 *
 * @author User
 */
class StatementTheorem extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_statement_theorem';
    }

    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->content = array();
        $this->part_theorems = array();
        $this->indexs = array();

        foreach ($DomElement->childNodes as $key => $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == 'part.theorem')
                {
                    $position = $position + 1;
                    $parttheorem = new PartTheorem($this->xmlpath);
                    $parttheorem->loadFromXml($child, $position);
                    $this->part_theorems[] = $parttheorem;
                }
                //currently there is no indices in the statement theorem element directly
                // all the indices are wrapped in para...
                else if (($child->tagName == 'index.symbol') || ($child->tagName == 'index.glossary') || ($child->tagName == 'index.author'))
                {
                    $position = $position + 1;
                    $index = new MathIndex($this->xmlpath);
                    $index->loadFromXml($child, $position);
                    $this->indexs[] = $index;
                }
                else
                {
                    $position = $position + 1;
                    $content = new stdClass();
                    $content = $this->getContent($child, $position, $this->xmlpath);
                    $this->content[] = $content->content;
                }
            }
        }
    }

}

?>
