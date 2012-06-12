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

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName != 'caption')
                {
                    foreach ($this->processSubordinate($child, $position)->subordinates as $subordinate)
                    {
                        $this->subordinates[] = $subordinate;
                    }

                    foreach ($this->processSubordinate($child, $position)->indexauthors as $indexauthor)
                    {
                        $this->indexauthors[] = $indexauthor;
                    }

                    foreach ($this->processSubordinate($child, $position)->indexglossarys as $indexglossary)
                    {
                        $this->indexglossarys[] = $indexglossary;
                    }

                    foreach ($this->processSubordinate($child, $position)->indexsymbols as $indexsymbol)
                    {
                        $this->indexsymbols[] = $indexsymbol;
                    }

                    foreach ($this->processSubordinate($child, $position)->content as $content)
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
    }

}

?>
