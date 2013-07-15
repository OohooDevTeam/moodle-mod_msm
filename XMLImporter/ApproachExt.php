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
 * This class represents all the approach.ext XML elements in the legacy document
 * (ie. files in the newXML) and it is called by Exercise/PartExercise class.
 * ApproachExt class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class ApproachExt extends Element
{

    public $position;
    public $type;
    public $caption;
    public $content;
    public $ext_name;
    public $version;
    public $answer_exercises = array();
    public $solution_exts = array();

    /**
     *  constructor for the instace of this class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */
    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_ext';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (approach.ext element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        approach.ext DOMElement
     * @param int $position                 integer that keeps track of order if elements
     * @return \ApproachExt
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->type = null; //for attribute in answer.ext
        $this->version = $DomElement->getAttribute('version');
        $this->caption = null;
        $this->content = null;
        $this->ext_name = $DomElement->tagName;

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                $name = $child->tagName;

                switch ($name)
                {
                    case('answer.exercise'):
                        $position = $position + 1;
                        $answer_exercise = new AnswerExercise($this->xmlpath);
                        $answer_exercise->loadFromXml($child, $position);
                        $this->answer_exercises[] = $answer_exercise;
                        break;
                    case('solution.ext'):
                        $position = $position + 1;
                        $solution_ext = new SolutionExt($this->xmlpath);
                        $solution_ext->loadFromXml($child, $position);
                        $this->solution_exts[] = $solution_ext;
                        break;
                }
            }
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of approach.ext element into
     * msm_ext database table.  It calls saveInfoDb method for SolutionExt, AnswerExercise classes.
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

        $data->ext_type = $this->type;
        $data->ext_version = $this->version;
        $data->caption = $this->caption;
        $data->ext_content = $this->content;
        $data->ext_name = $this->ext_name;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $elementPositions = array();
        $sibling_id = null;

        if (!empty($this->answer_exercises))
        {
            foreach ($this->answer_exercises as $key => $answerexercise)
            {
                $elementPositions['answerexercise' . '-' . $key] = $answerexercise->position;
            }
        }

        if (!empty($this->solution_exts))
        {
            foreach ($this->solution_exts as $key => $solutionext)
            {
                $elementPositions['solutionext' . '-' . $key] = $solutionext->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(answerexercise.\d+)$/", $element) ? true : false):
                    $answerexerciseString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $answerexercise = $this->answer_exercises[$answerexerciseString[1]];
                        $answerexercise->saveIntoDb($answerexercise->position, $msmid, $this->compid);
                        $sibling_id = $answerexercise->compid;
                    }
                    else
                    {
                        $answerexercise = $this->answer_exercises[$answerexerciseString[1]];
                        $answerexercise->saveIntoDb($answerexercise->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $answerexercise->compid;
                    }
                    break;

                case(preg_match("/^(solutionext.\d+)$/", $element) ? true : false):
                    $solutionextString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $solutionext = $this->solution_exts[$solutionextString[1]];
                        $solutionext->saveIntoDb($solutionext->position, $msmid, $this->compid);
                        $sibling_id = $solutionext->compid;
                    }
                    else
                    {
                        $solutionext = $this->solution_exts[$solutionextString[1]];
                        $solutionext->saveIntoDb($solutionext->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $solutionext->compid;
                    }
                    break;
            }
        }
    }

}

?>
