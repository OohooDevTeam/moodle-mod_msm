<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Exercise
 *
 * @author User
 */
class Exercise extends Element
{
    
    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_exercise';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->string_id = $DomElement->getAttribute('id');
        $this->difficulty = $DomElement->getAttribute('Difficulty');
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));
        
        $this->problems = array();
        $problems = $DomElement->getElementsByTagName('problem');        
        foreach($problems as $p)
        {
            $position = $position+1;
            $problem = new Problem($this->xmlpath);
            $problem->loadFromXml($p, $position);
            $this->problems[] = $problem;
        }
        
        $this->approachs = array();
        $approachs = $DomElement->getElementsByTagName('approach');
        
        foreach($approachs as $app)
        {
            $position = $position+1;
            $approach = new Approach($this->xmlpath);
            $approach->loadFromXml($app, $position);
            $this->approachs[] = $approach;
        }
    }
    
    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();
        
        $data->string_id = $this->string_id;
        $data->difficulty = $this->difficulty;
        $data->caption = $this->caption;
        
        $this->id = $DB->insert_record($this->tablename, $data);
        
        foreach($this->problems as $problem)
        {
            $problem->saveIntoDb($problem->position);
        }
//        
//        foreach($this->approachs as $approach)
//        {
//            $approach->saveIntoDb($approach->position);
//        }
    }
}

?>
