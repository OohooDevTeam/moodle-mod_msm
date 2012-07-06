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

    function saveIntoDb($position, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();

        $data->part_exercise_number = $this->number;
        $data->difficulty = $this->difficulty;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
        
         $elementPositions = array();
        $sibling_id = null;


        if (!empty($this->problems))
        {
            foreach ($this->problems as $key => $problem)
            {
                $elementPositions['problem' . '-' . $key] = $problem->position;
            }
        }

        if (!empty($this->approachs))
        {
            foreach ($this->approachs as $key => $approach)
            {
                $elementPositions['approach' . '-' . $key] = $approach->position;
            }
        }

        if (!empty($this->approach_exts))
        {
            foreach ($this->approach_exts as $key => $approachext)
            {
                $elementPositions['approachext' . '-' . $key] = $approachext->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(problem.\d+)$/", $element) ? true : false):
                    $problemString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $problem = $this->problems[$problemString[1]];
                        $problem->saveIntoDb($problem->position, $this->compid);
                        $sibling_id = $problem->compid;
                    }
                    else
                    {
                        $problem = $this->problems[$problemString[1]];
                        $problem->saveIntoDb($problem->position, $this->compid, $sibling_id);
                        $sibling_id = $problem->compid;
                    }
                    break;

                case(preg_match("/^(approach.\d+)$/", $element) ? true : false):
                    $approachString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $approach = $this->approachs[$approachString[1]];
                        $approach->saveIntoDb($approach->position, $this->compid);
                        $sibling_id = $approach->compid;
                    }
                    else
                    {
                        $approach = $this->approachs[$approachString[1]];
                        $approach->saveIntoDb($approach->position, $this->compid, $sibling_id);
                        $sibling_id = $approach->compid;
                    }
                    break;

                case(preg_match("/^(approachext.\d+)$/", $element) ? true : false):
                    $approachextString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $approachext = $this->approach_exts[$approachextString[1]];
                        $approachext->saveIntoDb($approachext->position, $this->compid);
                        $sibling_id = $approachext->compid;
                    }
                    else
                    {
                        $approachext = $this->approach_exts[$approachextString[1]];
                        $approachext->saveIntoDb($approachext->position, $this->compid, $sibling_id);
                        $sibling_id = $approachext->compid;
                    }
                    break;
            }
        }

    }

}

?>
