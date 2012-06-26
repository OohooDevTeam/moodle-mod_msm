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
    public $content;

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
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->medias = array();

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

        foreach ($this->processIndexAuthor($DomElement, $position) as $indexauthor)
        {
            $this->indexauthors[] = $indexauthor;
        }

        foreach ($this->processIndexGlossary($DomElement, $position) as $indexglossary)
        {
            $this->indexglossarys[] = $indexglossary;
        }

        foreach ($this->processIndexSymbols($DomElement, $position) as $indexsymbol)
        {
            $this->indexsymbols[] = $indexsymbol;
        }
        foreach ($this->processSubordinate($DomElement, $position) as $subordinate)
        {
            $this->subordinates[] = $subordinate;
        }

        foreach ($this->processMedia($DomElement, $position) as $media)
        {
            $this->medias[] = $media;
        }

        foreach ($this->processContent($DomElement, $position) as $content)
        {
            $this->content .= $content;
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

        $data->additional_attribute = $this->additional_attribute;
        $data->type = $this->type;
        if (!empty($this->content))
        {
            $data->content = $this->content;
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
