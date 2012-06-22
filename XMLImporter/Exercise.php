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

        // problem element can be a direct child of exercise or it could be a child of part.exercise which is a child of exercise
        $this->problems = array();
        $this->approachs = array();
        $this->approach_exts = array();
        $this->part_exercises = array();

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                $name = $child->tagName;

                switch ($name)
                {
                    case('problem'):
                        $position = $position + 1;
                        $problem = new Problem($this->xmlpath);
                        $problem->loadFromXml($child, $position);
                        $this->problems[] = $problem;
                        break;

                    case('apporach'):
                        $position = $position + 1;
                        $approach = new Approach($this->xmlpath);
                        $approach->loadFromXml($child, $position);
                        $this->approachs[] = $approach;
                        break;

                    case('apporach.ext'):
                        $position = $position + 1;
                        $approach_ext = new ApproachExt($this->xmlpath);
                        $approach_ext->loadFromXml($child, $position);
                        $this->approach_exts[] = $approach_ext;
                        break;

                    case('part.exercise'):
                        $position = $position + 1;
                        $part_exercise = new PartExercise($this->xmlpath);
                        $part_exercise->loadFromXml($child, $position);
                        $this->part_exercises[] = $part_exercise;
                        break;
                }
            }
        }
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position)
    {
//        echo "exercise save start";
//        $time = time();
//        print_object($time);
        
        global $DB;
        $data = new stdClass();

        $data->string_id = $this->string_id;
        $data->difficulty = $this->difficulty;
        $data->caption = $this->caption;

        $this->id = $DB->insert_record($this->tablename, $data);

        foreach ($this->problems as $problem)
        {
            $problem->saveIntoDb($problem->position);
        }

        foreach ($this->approachs as $approach)
        {
            $approach->saveIntoDb($approach->position);
        }

        foreach ($this->approach_exts as $approach_ext)
        {
            $approach_ext->saveIntoDb($approach_ext->position);
        }

        foreach ($this->part_exercises as $part_exercise)
        {
            $part_exercise->saveIntoDb($part_exercise->position);
        }
    }

}

?>
