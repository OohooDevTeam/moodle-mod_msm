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
        
        $this->approach_exts = array();
        $approach_exts = $DomElement->getElementsByTagName('approach.ext');
        foreach($approach_exts as $ae)
        {
            $position = $position+1;
            $approach_ext = new ApproachExt($this->xmlpath);
            $approach_ext->loadFromXml($ae, $position);
            $this->approach_exts[] = $approach_ext;
        }
        
        $this->part_exercises = array();
        $part_exercises = $DomElement->getElementsByTagName('part.exercise');
        foreach($part_exercises as $pe)
        {
            $position = $position+1;
            $part_exercise = new PartExercise($this->xmlpath);
            $part_exercise->loadFromXml($pe, $position);
            $this->part_exercises[] = $part_exercise;
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
        
        foreach($this->approachs as $approach)
        {
            $approach->saveIntoDb($approach->position);
        }
        
        foreach($this->approach_exts as $approach_ext)
        {
            $approach_ext->saveIntoDb($approach_ext->position);
        }
        
        foreach($this->part_exercises as $part_exercise)
        {
            $part_exercise->saveIntoDb($part_exercise->position);
        }
    }
}

?>
