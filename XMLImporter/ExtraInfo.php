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

       $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0)); 

        $position = $position + 1;
        $content = new stdClass();
        $content = $this->getContent($DomElement, $position, $this->xmlpath);
        $this->content = $content->content;
    }
    
    function saveIntoDb($position)
    {
        global $DB;
        
        $data = new stdClass();
        $data->name = $this->name;
        $data->caption = $this->caption->content;
        $data->extra_info_content = $this->content;
        
        $this->id = $DB->insert_record($this->tablename, $data);
    }

}

?>
