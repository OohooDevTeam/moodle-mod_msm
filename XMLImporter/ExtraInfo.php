<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * For inserting data into msm_extra_info table from preface,trailer,summary and/or historical.notes elements.
 *
 * @author User
 */
class ExtraInfo extends Element
{

    public $name;
    public $caption;
    public $content;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_extra_info';
    }

    /**
     *
     * @param DOMElement $DomElement 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $nameofElement = $DomElement->tagName;
        switch ($nameofElement)
        {
            case('preface'):
                $this->name = 'preface';
                break;
            case('trailer'):
                $this->name = 'trailer';
                break;
            case('summary'):
                $this->name = 'summary';
                break;
            case('historical.notes'):
                $this->name = 'historical';
                break;
        }

        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexsymbols = array();
        $this->indexglossary = array();
        $this->medias = array();

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName != 'caption')
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
        global $DB;

        $data = new stdClass();
        $data->name = $this->name;
        $data->caption = $this->caption;
        $data->extra_info_content = $this->content;

        $this->id = $DB->insert_record($this->tablename, $data);

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
