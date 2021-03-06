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
 * This class represents all the approach XML elements in the legacy document
 * (ie. files in the newXML) and it is called by Exercise/PartExersie classes.
 * Approach class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Approach extends Element
{

    public $id;                                     // database ID associated with this approach element in msm_approach
    public $compid;                                 // database ID associated with this approach element in msm_compositor
    public $position;                               // integer that keeps track of order of elements
    public $version;                                // version number associated with this approach (identifies different approaches in same problem)
    public $content = array();                      // content elements associated with this approach
    public $answerexercises = array();              // AnswerExercise objects associated with this approach element
    public $solutions = array();                    // Solution objects associated with this approach element
    public $subordinates = array();                 // Subordinate objects associated with this approach element
    public $indexauthors = array();                 // MathIndex objects associated with this approach element --> information about authors
    public $indexglossarys = array();               // MathIndex objects associated with this approach element --> information about glossarys
    public $indexsymbols = array();                 // MathIndex objects associated with this approach element --> information about symbols
    public $medias = array();                       // Media objects associated with this approach element
    public $tables = array();                       // Table objects associated with this approach element

    /**
     * constructor for the instace of this class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_approach';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (approach element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        approach DOMElement
     * @param int $position                 integer that keeps track of order if elements
     * @return \Approach
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->version = $DomElement->getAttribute('version');

        foreach ($DomElement->childNodes as $key => $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == 'solution.hint')
                {
                    foreach ($this->processIndexAuthor($child, $position) as $indexauthor)
                    {
                        $this->indexauthors[] = $indexauthor;
                    }

                    foreach ($this->processIndexGlossary($child, $position) as $indexglossary)
                    {
                        $this->indexglossarys[] = $indexglossary;
                    }

                    foreach ($this->processIndexSymbols($child, $position) as $indexsymbol)
                    {
                        $this->indexsymbols[] = $indexsymbol;
                    }
                    foreach ($this->processSubordinate($child, $position) as $subordinate)
                    {
                        $this->subordinates[] = $subordinate;
                    }

                    foreach ($this->processContent($child, $position) as $content)
                    {
                        $this->content[] = $content;
                    }

                    foreach ($this->processMedia($child, $position) as $media)
                    {
                        $this->medias[] = $media;
                    }

                    foreach ($this->processTable($child, $position) as $table)
                    {
                        $this->tables[] = $table;
                    }
                }
                if ($child->tagName == 'answer.exercise')
                {
                    $answerexercises = $child->getElementsByTagName('answer.exercise.block');

                    foreach ($answerexercises as $ans)
                    {
                        $position = $position + 1;
                        $answerexerciseblock = new AnswerExercise($this->xmlpath);
                        $answerexerciseblock->loadFromXml($ans, $position);
                        $this->answerexercises[] = $answerexerciseblock;
                    }
                }
                if ($child->tagName == 'solution')
                {
                    $position = $position + 1;
                    $solution = new Solution($this->xmlpath);
                    $solution->loadFromXml($child, $position);
                    $this->solutions[] = $solution;
                }
            }
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of approach element into
     * msm_approach database table.  It calls saveInfoDb method for AnswerExercise, Solution, Table,
     * Subordinate, Media, and MathIndex classes.
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

        $data->approach_version = $this->version;
        if (!empty($this->content))
        {
            foreach ($this->content as $content)
            {
                $data->solution_hint = $content;
                $this->id = $DB->insert_record($this->tablename, $data);
                $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
            }
        }
        else
        {
            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        }

        $elementPositions = array();
        $sibling_id = null;

        if (!empty($this->answerexercises))
        {
            foreach ($this->answerexercises as $key => $answerexercise)
            {
                $elementPositions['answerexercise' . '-' . $key] = $answerexercise->position;
            }
        }

        if (!empty($this->solutions))
        {
            foreach ($this->solutions as $key => $solution)
            {
                $elementPositions['solution' . '-' . $key] = $solution->position;
            }
        }

        if (!empty($this->subordinates))
        {
            foreach ($this->subordinates as $key => $subordinate)
            {
                $elementPositions['subordinate' . '-' . $key] = $subordinate->position;
            }
        }

        if (!empty($this->indexauthors))
        {
            foreach ($this->indexauthors as $key => $indexauthor)
            {
                $elementPositions['indexauthor' . '-' . $key] = $indexauthor->position;
            }
        }

        if (!empty($this->indexglossarys))
        {
            foreach ($this->indexglossarys as $key => $indexglosary)
            {
                $elementPositions['indexglossary' . '-' . $key] = $indexglosary->position;
            }
        }

        if (!empty($this->indexsymbols))
        {
            foreach ($this->indexsymbols as $key => $indexsymbol)
            {
                $elementPositions['indexsymbol' . '-' . $key] = $indexsymbol->position;
            }
        }

        if (!empty($this->medias))
        {
            foreach ($this->medias as $key => $media)
            {
                $elementPositions['media' . '-' . $key] = $media->position;
            }
        }

        if (!empty($this->tables))
        {
            foreach ($this->tables as $key => $table)
            {
                $elementPositions['table' . '-' . $key] = $table->position;
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
                        $answerexercise = $this->answerexercises[$answerexerciseString[1]];
                        $answerexercise->saveIntoDb($answerexercise->position, $msmid, $this->compid);
                        $sibling_id = $answerexercise->compid;
                    }
                    else
                    {
                        $answerexercise = $this->answerexercises[$answerexerciseString[1]];
                        $answerexercise->saveIntoDb($answerexercise->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $answerexercise->compid;
                    }
                    break;

                case(preg_match("/^(soltuion.\d+)$/", $element) ? true : false):
                    $solutionString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $solution = $this->solutions[$solutionString[1]];
                        $solution->saveIntoDb($solution->position, $msmid, $this->compid);
                        $sibling_id = $solution->compid;
                    }
                    else
                    {
                        $solution = $this->solutions[$solutionString[1]];
                        $solution->saveIntoDb($solution->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $solution->compid;
                    }
                    break;

                case(preg_match("/^(subordinate.\d+)$/", $element) ? true : false):
                    $subordinateString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $subordinate = $this->subordinates[$subordinateString[1]];
                        $subordinate->saveIntoDb($subordinate->position, $msmid, $this->compid);
                        $sibling_id = $subordinate->compid;
                    }
                    else
                    {
                        $subordinate = $this->subordinates[$subordinateString[1]];
                        $subordinate->saveIntoDb($subordinate->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $subordinate->compid;
                    }
                    break;

                case(preg_match("/^(indexauthor.\d+)$/", $element) ? true : false):
                    $indexauthorString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $indexauthor = $this->indexauthors[$indexauthorString[1]];
                        $indexauthor->saveIntoDb($indexauthor->position, $msmid, $this->compid);
                        $sibling_id = $indexauthor->compid;
                    }
                    else
                    {
                        $indexauthor = $this->indexauthors[$indexauthorString[1]];
                        $indexauthor->saveIntoDb($indexauthor->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $indexauthor->compid;
                    }
                    break;

                case(preg_match("/^(indexsymbol.\d+)$/", $element) ? true : false):
                    $indexsymbolString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $indexsymbol = $this->indexsymbols[$indexsymbolString[1]];
                        $indexsymbol->saveIntoDb($indexsymbol->position, $msmid, $this->compid);
                        $sibling_id = $indexsymbol->compid;
                    }
                    else
                    {
                        $indexsymbol = $this->indexsymbols[$indexsymbolString[1]];
                        $indexsymbol->saveIntoDb($indexsymbol->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $indexsymbol->compid;
                    }
                    break;

                case(preg_match("/^(indexglossary.\d+)$/", $element) ? true : false):
                    $indexglossaryString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $indexglossary = $this->indexglossarys[$indexglossaryString[1]];
                        $indexglossary->saveIntoDb($indexglossary->position, $msmid, $this->compid);
                        $sibling_id = $indexglossary->compid;
                    }
                    else
                    {
                        $indexglossary = $this->indexglossarys[$indexglossaryString[1]];
                        $indexglossary->saveIntoDb($indexglossary->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $indexglossary->compid;
                    }
                    break;

                case(preg_match("/^(media.\d+)$/", $element) ? true : false):
                    $mediaString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $media = $this->medias[$mediaString[1]];
                        $media->saveIntoDb($media->position, $msmid, $this->compid);
                        $sibling_id = $media->compid;
                    }
                    else
                    {
                        $media = $this->medias[$mediaString[1]];
                        $media->saveIntoDb($media->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $media->compid;
                    }
                    break;

                case(preg_match("/^(table.\d+)$/", $element) ? true : false):
                    $tableString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $table = $this->tables[$tableString[1]];
                        $table->saveIntoDb($table->position, $msmid, $this->compid);
                        $sibling_id = $table->compid;
                    }
                    else
                    {
                        $table = $this->tables[$tableString[1]];
                        $table->saveIntoDb($table->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $table->compid;
                    }
                    break;
            }
        }
    }

}

?>
