<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Intro
 *
 * @author User
 */
class Intro extends Element
{

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_intro';
    }

    /**
     *
     * @param DOMElement $DomElement 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->id = $DomElement->getAttribute('id');
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0)); 

        $blocks = $DomElement->getElementsByTagName('block');

        $this->blocks = array();

        foreach ($blocks as $b)
        {
            $position = $position + 1;
            $block = new Block($this->xmlpath);
            $block->loadFromXml($b, $position);
            $this->blocks[] = $block;
        }
    }
    
    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();
        $data->string_id = $this->id;
        $data->caption = $this->caption;
        
        $this->id = $DB->insert_record($this->tablename, $data);
        
        echo "xmlpath";
        print_object($this->xmlpath);
        
        foreach($this->blocks as $key=>$block)
        {
            echo "block";
             print_object($block);
            $block->saveIntoDb($block->position);
        }
        
    }

}

?>
