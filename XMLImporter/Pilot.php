<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pilot
 *
 * @author User
 */
class Pilot extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_pilot';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $doc = new DOMDocument();
        $pilot_step = $DomElement->getElementsByTagName('pilot.step')->item(0);
        
        $element = $doc->importNode($pilot_step, true);
        $this->pilot_content .= $doc->saveXML($element);
    }
    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();
        $data->pilot_content = $this->pilot_content;
        
        $this->id = $DB->insert_record($this->tablename, $data);
    }

}

?>
