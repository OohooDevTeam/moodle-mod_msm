<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                      **
 * @subpackage  msm                                                      **
 * @name        msm                                                      **
 * @copyright   University of Alberta                                    **
 * @link        http://ualberta.ca                                       **
 * @author      Ga Young Kim                                             **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * *************************************************************************
 * ************************************************************************ */

/**
 * This class represents al the exercise XML elements in the legacy document
 * (ie. files in the newXML) and it is called by Pack class.
 * Example class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Exercise extends Element
{

    public $id;                                     // database ID of current example element in msm_exercise table
    public $compid;                                 // database ID of current example element in msm_compositor table
    public $position;                               // integer that keeps track of order of elements
    public $string_id;                              // unique ID given by user in XML files to identify this exercise element
    // for new XML files, it's same as its compid listed above but for legacy material, it's user defined string
    public $difficulty;                             // difficulty level associated with this exercise element
    public $caption;                                // title associated with this exercise element
    public $problems = array();                     // Problem object associated with this exercise element (problem part of this exercise)
    public $approachs = array();                    // Approach object associated with this exercise element (different approaches that students might take to solve the problem)
    public $approach_exts = array();                // ApporachExt object associated with this exercise element (different approaches that students might take to solve the problem)
    public $part_exercises = array();               // PartExercise object associated with this exercise element (different approaches that students might take to solve the problem)

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_exercise';
    }

    /**
     *  This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (exercise element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        exercise element
     * @param int $position                 integer that keeps track of order if elements
     * @return \Exercise
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->string_id = $DomElement->getAttribute('id');
        $this->difficulty = $DomElement->getAttribute('Difficulty');
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));
        
        // problem element can be a direct child of exercise or it could be a child of part.exercise which is a child of exercise

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

                    case('approach.ext'):
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
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of exercise element and its associated child elements into
     * their respective database tables.  It calls saveInfoDb method for PartExercise, Approach, ApproachExt, and Problem classes.
     * 
     * @global moodle_databse $DB
     * @param int $position              integer that keeps track of order if elements
     * @param int $msmid                 MSM instance ID
     * @param int $parentid              ID of the parent element from msm_compositor
     * @param int $siblingid             ID of the previous sibling element from msm_compositor
     */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;

        $data = new stdClass();

        $data->string_id = $this->string_id;
        $data->difficulty = $this->difficulty;
        $data->caption = $this->caption;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

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
            foreach ($this->approach_exts as $key => $approach_ext)
            {
                $elementPositions['approachext' . '-' . $key] = $approach_ext->position;
            }
        }

        if (!empty($this->part_exercises))
        {
            foreach ($this->part_exercises as $key => $part_exercise)
            {
                $elementPositions['partexercise' . '-' . $key] = $part_exercise->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(problem.\d+)$/", $element) ? true : false):
                    $problemString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $problem = $this->problems[$problemString[1]];
                        $problem->saveIntoDb($problem->position, $msmid, $this->compid);
                        $sibling_id = $problem->compid;
                    }
                    else
                    {
                        $problem = $this->problems[$problemString[1]];
                        $problem->saveIntoDb($problem->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $problem->compid;
                    }
                    break;

                case(preg_match("/^(approach.\d+)$/", $element) ? true : false):
                    $approachString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $approach = $this->approachs[$approachString[1]];
                        $approach->saveIntoDb($approach->position, $msmid, $this->compid);
                        $sibling_id = $approach->compid;
                    }
                    else
                    {
                        $approach = $this->approachs[$approachString[1]];
                        $approach->saveIntoDb($approach->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $approach->compid;
                    }
                    break;

                case(preg_match("/^(approachext.\d+)$/", $element) ? true : false):
                    $approachextString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $approachext = $this->approach_exts[$approachextString[1]];
                        $approachext->saveIntoDb($approachext->position, $msmid, $this->compid);
                        $sibling_id = $approachext->compid;
                    }
                    else
                    {
                        $approachext = $this->approach_exts[$approachextString[1]];
                        $approachext->saveIntoDb($approachext->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $approachext->compid;
                    }
                    break;

                case(preg_match("/^(partexercise.\d+)$/", $element) ? true : false):
                    $partexerciseString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $partexercise = $this->part_exercises[$partexerciseString[1]];
                        $partexercise->saveIntoDb($partexercise->position, $msmid, $this->compid);
                        $sibling_id = $partexercise->compid;
                    }
                    else
                    {
                        $partexercise = $this->part_exercises[$partexerciseString[1]];
                        $partexercise->saveIntoDb($partexercise->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $partexercise->compid;
                    }
                    break;
            }
        }
    }

}

?>
