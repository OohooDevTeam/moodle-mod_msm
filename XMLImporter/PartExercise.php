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
 * This class represents all the part.exercise XML elements in the legacy document
 * (ie. files in the newXML) and it is called by Exercise class.
 * PartExercise class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class PartExercise extends Element
{

    public $id;                             // Database ID of the part.exercise in the msm_part_exercise table
    public $compid;                         // Database ID of the part.exercise in the msm_compositor table
    public $position;                       // integer that keeps track of order of elements
    public $number;                         // number separating different parts in the exercise
    public $difficulty;                     // difficulty level associated with this part of the exercise
    public $problems = array();             // Problem objects associated with this part.exercise 
    public $approachs = array();            // Approach objects associated with this part.exercise
    public $approach_exts = array();        // ApproachExt objects associated with this part.exercise

    /**
     * constructor for this class
     * 
     * @param string $xmlpath    filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_part_exercise';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (part.exercise element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        part.exercise DOMElement
     * @param int $position                 integer that keeps track of order if elements
     * @return \PartExercise
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->number = $DomElement->getAttribute('number');
        $this->difficulty = $DomElement->getAttribute('Difficulty');

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
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of part.exercise element into
     * msm_part_exercise database table.  It calls saveInfoDb method for Problem, Approach, ApproachExt classes.
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

        $data->part_exercise_number = $this->number;
        $data->difficulty = $this->difficulty;

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
            }
        }
    }

}

?>
