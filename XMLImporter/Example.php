<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                       **
 * @subpackage  msm                                                       **
 * @name        msm                                                       **
 * @copyright   University of Alberta                                     **
 * @link        http://ualberta.ca                                        **
 * @author      Ga Young Kim                                              **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************* */

/**
 * This class represents al the example XML elements in the legacy document
 * (ie. files in the newXML) and it is called by Pack class.
 * Example class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Example extends Element
{

    public $id;                                     // database ID of current example element in msm_example table
    public $compid;                                 // database ID of current example element in msm_compositor table
    public $position;                               // integer that keeps track of order of elements
    public $string_id;                              // unique ID given by user in XML files to identify this example element
                                                    // for new XML files, it's same as its compid listed above but for legacy material, it's user defined string
    public $caption;                                // title associated with this example element   
    public $textcaption;                            // title without math elements associated with this example element
    public $description;                            // description element associated with this example element
    public $statement_examples = array();           // content elements associated with this example
    public $subordinates = array();                 // Subordinate objects associated with this example (extra info shown as popup in content)
    public $indexauthors = array();                 // MathIndex objects associated with this example (index of authors that gives more info about the author)
    public $indexglossarys = array();               // MathIndex objects associated with this example (index of glossary that gives definition/extra info about the term)
    public $indexsymbols = array();                 // MathIndex objects associated with this example (index of symbols that gives definition/extra info about the symbols)
    public $medias = array();                       // Media objects associated with this example (ie. video/images...etc)
    public $tables = array();                       // Table objects assocaited with this example
    public $part_examples = array();                // PartExample objects associated with this example
    public $answers = array();                      // AnswerExample objets associated with this example
    public $answer_exts = array();                  // AnswerExt objects associatedwith this example

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_example';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (example element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        example element
     * @param int $position                 integer that keeps track of order if elements
     * @return \Example
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->string_id = $DomElement->getAttribute('id');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));

        $this->description = $this->getDomAttribute($DomElement->getElementsByTagName('description'));

        $statement_examples = $DomElement->getElementsByTagName('statement.example');

        foreach ($statement_examples as $statement_ex)
        {
            foreach ($this->processIndexAuthor($statement_ex, $position) as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processIndexGlossary($statement_ex, $position) as $indexglossary)
            {
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processIndexSymbols($statement_ex, $position) as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }
            foreach ($this->processSubordinate($statement_ex, $position) as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processMedia($statement_ex, $position) as $media)
            {
                $this->medias[] = $media;
            }

            foreach ($this->processTable($statement_ex, $position) as $table)
            {
                $this->tables[] = $table;
            }

            foreach ($this->processContent($statement_ex, $position) as $content)
            {
                $this->statement_examples[] = $content;
            }
        }

        $part_examples = $DomElement->getElementsByTagName('part.example');

        foreach ($part_examples as $part_ex)
        {
            $position = $position + 1;
            $part_example = new PartExample($this->xmlpath);
            $part_example->loadFromXml($part_ex, $position);
            $this->part_examples[] = $part_example;
        }

        $answers = $DomElement->getElementsByTagName('answer');

        foreach ($answers as $a)
        {
            $position = $position + 1;
            $answer = new AnswerExample($this->xmlpath);
            $answer->loadFromXml($a, $position);
            $this->answers[] = $answer;
        }

        $answerexts = $DomElement->getElementsByTagName('answer.ext');

        foreach ($answerexts as $ax)
        {
            $position = $position + 1;
            $answerext = new AnswerExt($this->xmlpath);
            $answerext->loadFromXml($ax, $position);
            $this->answer_exts[] = $answerext;
        }

        return $this;
    }

    /**
     *  This method saves the extracted information from the XML files of example element and its associated child elements into
     * their respective database tables.  It calls saveInfoDb method for AnswerExample. AnswerExt, PartExample, MathIndex, Media, Table,
     *  and Subordinate classes.
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
        $data->description = $this->description;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $statement_data = new stdClass();
        $statement_sibling_id = 0;
        foreach ($this->statement_examples as $st)
        {
            $statement_data->statement_example_content = $st;
            $this->statement_example_id = $DB->insert_record('msm_statement_example', $statement_data);
            $this->statement_example_compid = $this->insertToCompositor($this->statement_example_id, 'msm_statement_example', $msmid, $this->compid, $statement_sibling_id);
            $statement_sibling_id++;
        }


        $elementPositions = array();
        $sibling_id = null;

        if (!empty($this->answers))
        {
            foreach ($this->answers as $key => $answer)
            {
                $elementPositions['answer' . '-' . $key] = $answer->position;
            }
        }

        if (!empty($this->part_examples))
        {
            foreach ($this->part_examples as $key => $part_example)
            {
                $elementPositions['partexample' . '-' . $key] = $part_example->position;
            }
        }

        if (!empty($this->answer_exts))
        {
            foreach ($this->answer_exts as $key => $answer_ext)
            {
                $elementPositions['answerext' . '-' . $key] = $answer_ext->position;
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
                case(preg_match("/^(answerext.\d+)$/", $element) ? true : false):
                    $answerextString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $answerext = $this->answer_exts[$answerextString[1]];
                        $answerext->saveIntoDb($answerext->position, $msmid, $this->compid);
                        $sibling_id = $answerext->compid;
                    }
                    else
                    {
                        $answerext = $this->answer_exts[$answerextString[1]];
                        $answerext->saveIntoDb($answerext->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $answerext->compid;
                    }
                    break;
                case(preg_match("/^(partexample.\d+)$/", $element) ? true : false):
                    $partexampleString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $partexample = $this->part_examples[$partexampleString[1]];
                        $partexample->saveIntoDb($partexample->position, $msmid, $this->compid);
                        $sibling_id = $partexample->compid;
                    }
                    else
                    {
                        $partexample = $this->part_examples[$partexampleString[1]];
                        $partexample->saveIntoDb($partexample->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $partexample->compid;
                    }
                    break;

                case(preg_match("/^(answer.\d+)$/", $element) ? true : false):
                    $answerString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $answer = $this->answers[$answerString[1]];
                        $answer->saveIntoDb($answer->position, $msmid, $this->compid);
                        $sibling_id = $answer->compid;
                    }
                    else
                    {
                        $answer = $this->answers[$answerString[1]];
                        $answer->saveIntoDb($answer->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $answer->compid;
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
