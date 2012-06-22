<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Info
 *
 * @author User
 */
class MathInfo extends Element
{

    public $position;
    public $content;
    public $caption;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_info';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        //$this->content = array();
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->medias = array();

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName != 'info.caption')
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
                        $this->content .= $content;
                    }
                }
                else
                {
                    $this->caption = $this->getContent($child);
                }
            }
        }
    }

    function saveIntoDb($position)
    {
//        echo "mathinfo save start";
//        $time = time();
//        print_object($time);
        
        global $DB;

        $data = new stdClass();
        if (!empty($this->caption))
        {
            $data->caption = $this->caption;
        }
        if (!empty($this->content))
        {
            $data->info_content = $this->content;

            $this->id = $DB->insert_record($this->tablename, $data);
        }
        else
        {
            $this->id = $DB->insert_record($this->tablename, $data);
        }

        foreach ($this->subordinates as $key => $subordinate)
        {
            $subordinate->saveIntoDb($subordinate->position);
        }

        foreach ($this->indexglossarys as $key => $indexglossary)
        {
            $indexglossary->saveIntoDb($indexglossary->position);
        }

        foreach ($this->indexsymbols as $key => $indexsymbol)
        {
            $indexsymbol->saveIntoDb($indexsymbol->position);
        }

        foreach ($this->indexauthors as $key => $indexauthor)
        {
            $indexauthor->saveIntoDb($indexauthor->position);
        }

        foreach ($this->medias as $key => $media)
        {
            $media->saveIntoDb($media->position);
        }
    }

}

?>
