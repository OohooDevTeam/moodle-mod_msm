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
 * This class represents all the quiz XML elements in the legacy document
 * (ie. files in the newXML) and it is called by Pack class.
 * MathQuiz class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class MathQuiz extends Element
{

    public $id;                             // Database ID associated with this quiz element in msm_quiz table
    public $compid;                         // Database ID associated with this quiz element in msm_compositor table
    public $position;                       // integer that keeps track of order of elements
    public $caption;                        // title associated with this quiz element
    public $textcaption;                    // plain text title without any math elements associated with this quiz element
    public $string_id;                      // unique ID given by the user to identify this quiz element
    public $subordinates = array();         // Subordinate objects associated with this quiz element
    public $indexauthors = array();         // MathIndex objects associated with this quiz element --> this represents index.author elements
    public $indexglossarys = array();       // MathIndex objects associated with this quiz element --> this represents index.glossary elements
    public $indexsymbols = array();         // MathIndex objects associated with this quiz element --> this represents index.symbol elements
    public $medias = array();               // Media objects associated with this quiz element
    public $tables = array();               // Table objects associated with this quiz element
    public $questions = array();            // question elements associated with this quiz element
    public $hints = array();                // MathInfo objects associated with this quiz element --> hints given for certain question in quiz
    public $choices = array();              // QuizChoice objects associated with this quiz element --> possible answers to the question in the quiz
    public $parts = array();                // PartQuiz objects associated with this quiz element --> different parts of this quiz

    /**
     *  constructor for the instace of this class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_quiz';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (quiz element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        quiz element 
     * @param int $position                 integer that keeps track of order if elements
     * @return \MathQuiz
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->string_id = $DomElement->getAttribute('id');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));
        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                $name = $child->tagName;

                switch ($name)
                {
                    case('question'):
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
                            $this->questions[] = $content;
                        }

                        foreach ($this->processMedia($child, $position) as $media)
                        {
                            $this->medias[] = $media;
                        }

                        foreach ($this->processTable($child, $position) as $table)
                        {
                            $this->tables[] = $table;
                        }
                        break;

                    case('hint'):
                        $info = $child->getElementsByTagName('info')->item(0);
                        $position = $position + 1;
                        $hintinfo = new MathInfo($this->xmlpath);
                        $hintinfo->loadFromXml($info, $position);
                        $this->hints[] = $hintinfo;
                        break;

                    case('choice'):
                        $position = $position + 1;
                        $choice = new QuizChoice($this->xmlpath);
                        $choice->loadFromXml($child, $position);
                        $this->choices[] = $choice;
                        break;

                    case('part'):
                        $position = $position + 1;
                        $part = new PartQuiz($this->xmlpath);
                        $part->loadFromXml($child, $position);
                        $this->parts[] = $part;
                        break;
                }
            }
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of quiz element into
     * msm_quiz database table.  It calls saveInfoDb method for MathIndex, Subordinate, Media,
     * Table, MathInfo, PartQuiz and QuizChoice classes.
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
        $data->caption = $this->caption;
        $data->textcaption = $this->textcaption;

        if (!empty($this->questions))
        {
            foreach ($this->questions as $question)
            {
                $data->question = $question;
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

        if (!empty($this->hints))
        {
            foreach ($this->hints as $key => $hint)
            {
                $elementPositions['hint' . '-' . $key] = $hint->position;
            }
        }

        if (!empty($this->parts))
        {
            foreach ($this->parts as $key => $part)
            {
                $elementPositions['part' . '-' . $key] = $part->position;
            }
        }

        if (!empty($this->choices))
        {
            foreach ($this->choices as $key => $choice)
            {
                $elementPositions['choice' . '-' . $key] = $choice->position;
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
                case(preg_match("/^(hint.\d+)$/", $element) ? true : false):
                    $hintString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $hint = $this->hints[$hintString[1]];
                        $hint->saveIntoDb($hint->position, $msmid, $this->compid);
                        $sibling_id = $hint->compid;
                    }
                    else
                    {
                        $hint = $this->hints[$hintString[1]];
                        $hint->saveIntoDb($hint->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $hint->compid;
                    }
                    break;

                case(preg_match("/^(part.\d+)$/", $element) ? true : false):
                    $partString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $part = $this->parts[$partString[1]];
                        $part->saveIntoDb($part->position, $msmid, $this->string_id, $this->caption, $this->textcaption, $this->compid);
                        $sibling_id = $part->compid;
                    }
                    else
                    {
                        $part = $this->parts[$partString[1]];
                        $part->saveIntoDb($part->position, $msmid, $this->string_id, $this->caption, $this->textcaption, $this->compid, $sibling_id);
                        $sibling_id = $part->compid;
                    }
                    break;

                case(preg_match("/^(choice.\d+)$/", $element) ? true : false):
                    $choiceString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $choice = $this->choices[$choiceString[1]];
                        $choice->saveIntoDb($choice->position, $msmid, $this->compid);
                        $sibling_id = $choice->compid;
                    }
                    else
                    {
                        $choice = $this->choices[$choiceString[1]];
                        $choice->saveIntoDb($choice->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $choice->compid;
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
