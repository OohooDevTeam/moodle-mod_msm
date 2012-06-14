<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SolutionExt
 *
 * @author User
 */
class SolutionExt extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_ext';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));
        $this->ext_name = $DomElement->tagName;
        
        $this->steps = array();
        $steps = $DomElement->getElementsByTagName('step');
        foreach($steps as $s)
        {
            $position = $position+1;
            $step = new Step($this->xmlpath);
            $step->loadFromXml($s, $position);
            $this->steps[] = $step;
        }
    }
    
    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();
        $data->caption = $this->caption;
        $data->ext_name = $this->ext_name;
        
        $this->id = $DB->insert_record($this->tablename, $data);
        
        foreach($this->steps as $step)
        {
            $step->saveIntoDb($step->position);
        }
    }

}

?>
