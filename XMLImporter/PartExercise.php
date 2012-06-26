<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PartExercise
 *
 * @author User
 */
class PartExercise extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_part_exercise';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->number = $DomElement->getAttribute('number');
        $this->difficulty = $DomElement->getAttribute('Difficulty');

        $this->problems = array();
        $this->approachs = array();
        $this->approach_exts = array();

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

                    case('approach'):
                        $position = $position + 1;
                        $approach = new Approach($this->xmlpath);
                        $approach->loadFromXml($child, $position);
                        $this->approachs[] = $approach;
                        break;

                    case('approach.ext'):
                        $position = $position + 1;
                        $approach_ext = new ApproachExt($this->xmlpath);
                        $approach_ext->loadFromXml($child, $position);
                        $this->approach_exts[] = $approach_ext;
                        break;
                }
            }
        }
    }

    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();

        $data->part_exercise_number = $this->number;
        $data->difficulty = $this->difficulty;

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
    }

}

?>
