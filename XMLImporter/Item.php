<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Item
 *
 * @author User
 */
class Item extends Element
{

    public $position;
    public $length;
    public $citekey;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_item';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->content = array();
        $i=0;
        foreach ($DomElement->childNodes as $key => $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName != 'citekey')
                {
                    foreach ($this->processIndexAuthor($child, $position) as $indexauthor)
                    {
                        $this->indexauthors[] = $indexauthor;
                    }

                    foreach ($this->processIndexGlossary($child, $position) as $indexglossary)
                    {
                        $this->indexglossarys[] = $indexglossary;
                    }

                    foreach ($this->processIndexSymbols($child, $position) as $indexsymbol)
                    {
                        $this->indexsymbols[] = $indexsymbol;
                    }
                    foreach ($this->processSubordinate($child, $position) as $subordinate)
                    {
                        $this->subordinates[] = $subordinate;
                    }

                    foreach ($this->processMedia($child, $position) as $media)
                    {
                        $this->medias[] = $media;
                    }

                    foreach ($this->processContent($child, $position) as $content)
                    {
                        $this->content[$i] .= $content;
                    }
                    $i++;
                }
                if($child->tagName == 'citekey')
                {
                     $this->itemposition[] = $key;
                    $this->citekeys = $child;
                }
                $this->length = $i;
            }
        }
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();
        
        for($i=0; $i < $this->length+1; $i++)
        {
            if(!empty($this->content[$i]))
            {
                 $data->item_content = $this->content[$i];
            }           
            $data->position = $i;
            $data->citekey = $this->citekey;
        }
    }

}

?>
