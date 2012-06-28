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
     * @param int $position
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->string_id = $DomElement->getAttribute('id');
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

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();
        $data->string_id = $this->string_id;
        $data->caption = $this->caption;

        $this->id = $DB->insert_record($this->tablename, $data);

        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);


        $elementPosition = array();
        foreach ($this->blocks as $key => $block)
        {
            $elementPosition['block' . '-' . $key] = $block->position;
        }

        asort($elementPosition);

        foreach ($elementPosition as $element => $value)
        {
            $blockString = split('-', $element);

            $this->blocks[$blockString[1]]->saveIntoDb($this->blocks[$blockString[1]]->position, $this->compid);
        }
    }

}

?>
