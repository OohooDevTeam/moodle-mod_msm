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
 * This class represents al the step XML elements in the legacy document
 * (ie. files in the newXML) and it is called by AnswerExt/SolutionExt classes.
 * Step class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Step extends Element
{

    public $id;                             // database ID associated with the step element in msm_step table
    public $compid;                         // database ID associated with the step element in msm_compositor table
    public $position;                       // integer that keeps track of order of elements
    public $caption;                        // title element associated with step element
    public $partref;                        // represents part of exercise whose wolution begins with this step
    public $content;                        // content elements in the step.body elements of the step element (by their partid)
    public $pilots = array();               // Pilot objects associated with the step element
    public $indexauthors = array();         // MathIndex objects associated with the step element --> info on authors
    public $indexglossarys = array();       // MathIndex objects associated with the step element --> info on terms
    public $indexsymbols = array();         // MathIndex objects associated with the step element --> info on symbols
    public $subordinates = array();         // Subordinate objects associated with the step element 
    public $medias = array();               // Media objects associated with the step element 
    public $tables = array();               // Table objects associated with the step element 
    public $matharrays = array();           // MathArray objects associated with the step element 

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_step';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (step element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        step element in XML file
     * @param int $position                 integer that keeps track of order if elements
     * @return \Step
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $this->partref = $DomElement->getAttribute('partref');

        $this->pilots = array();
        $pilots = $DomElement->getElementsByTagName('pilot');
        foreach ($pilots as $p)
        {
            $position = $position + 1;
            $pilot = new Pilot($this->xmlpath);
            $pilot->loadFromXml($p, $position);
            $this->pilots[] = $pilot;
        }

        $step_bodys = $DomElement->getElementsByTagName('step.body');

        foreach ($step_bodys as $stb)
        {
            foreach ($this->processIndexAuthor($stb, $position) as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processIndexGlossary($stb, $position) as $indexglossary)
            {
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processIndexSymbols($stb, $position) as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }
            foreach ($this->processSubordinate($stb, $position) as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processMedia($stb, $position) as $media)
            {
                $this->medias[] = $media;
            }

            foreach ($this->processTable($stb, $position) as $table)
            {
                $this->tables[] = $table;
            }

            foreach ($this->processContent($stb, $position) as $content)
            {
                $this->content .= $content;
            }
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of step element into
     * msm_step database table.  It calls saveInfoDb method for Pilot, Subordinate, MathArray,
     * Media, Table, MathIndex class.
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

        $data->partref = $this->partref;
        $data->caption = $this->caption;

        $data->step_content = $this->content;
        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $elementPositions = array();
        $sibling_id = null;


        if (!empty($this->pilots))
        {
            foreach ($this->pilots as $key => $pilot)
            {
                $elementPositions['pilot' . '-' . $key] = $pilot->position;
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
                case(preg_match("/^(pilot.\d+)$/", $element) ? true : false):
                    $pilotString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $pilot = $this->pilots[$pilotString[1]];
                        $pilot->saveIntoDb($pilot->position, $msmid, $this->compid);
                        $sibling_id = $pilot->compid;
                    }
                    else
                    {
                        $pilot = $this->pilots[$pilotString[1]];
                        $pilot->saveIntoDb($pilot->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $pilot->compid;
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
