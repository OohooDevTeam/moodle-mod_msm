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
 * This class represents al the answer.example XML elements in the legacy document
 * (ie. files in the newXML) and it is called by Example class.
 * AnswerExample class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class AnswerExample extends Element
{

    public $position;                       // integer that keeps track of order if elements
    public $caption;                        // title associated with answer element
    public $answer_type;                    // type associated with answer element (eg. solution)
    public $answer_version;                 // version associated with answer element
    public $logic_type;                     // logic element that is associated with answer element
    public $example_answer_logic;           // type associated with logic element in answer.block element
    public $answer_block_body = array();    // content of answer element (which is in answer.block element)
    public $subordinates = array();         // Subordinate objects that is associated with content of answer element
    public $indexauthors = array();         // index.author elements in the content
    public $indexglossarys = array();       // index.glossary elements in the content
    public $indexsymbols = array();         // index.symbols elements in the content
    public $medias = array();               // Media objects in the content of answer element
    public $tables = array();               // Table objects in the content of answer element

    /**
     * constructor for the instace of this class
     * 
     * @param type $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */
    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_answer';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (answer element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement
     * @param int $position                 integer that keeps track of order if elements
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->answer_type = $DomElement->getAttribute('type');
        $this->answer_version = $DomElement->getAttribute('version');

        $answer_blocks = $DomElement->getElementsByTagName('answer.block');

        foreach ($answer_blocks as $answer_block)
        {
            foreach ($answer_block->childNodes as $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    if ($child->tagName == 'caption')
                    {
                        $this->caption = $this->getContent($child);
                    }
                    else if ($child->tagName == 'logic')
                    {
                        $this->logic_type = $child->getAttribute('type');
                        $this->example_answer_logic = $child->nodeValue;
                    }
                }
            }

            // loop through all answer.block.body elements and process its contents
            $answer_block_bodys = $answer_block->getElementsByTagName('answer.block.body');

            foreach ($answer_block_bodys as $answer_block_body)
            {
                foreach ($this->processIndexAuthor($answer_block_body, $position) as $indexauthor)
                {
                    $this->indexauthors[] = $indexauthor;
                }

                foreach ($this->processIndexGlossary($answer_block_body, $position) as $indexglossary)
                {
                    $this->indexglossarys[] = $indexglossary;
                }

                foreach ($this->processIndexSymbols($answer_block_body, $position) as $indexsymbol)
                {
                    $this->indexsymbols[] = $indexsymbol;
                }
                foreach ($this->processSubordinate($answer_block_body, $position) as $subordinate)
                {
                    $this->subordinates[] = $subordinate;
                }

                foreach ($this->processContent($answer_block_body, $position) as $content)
                {
                    $this->answer_block_body[] = $content;
                }

                foreach ($this->processMedia($answer_block_body, $position) as $media)
                {
                    $this->medias[] = $media;
                }

                foreach ($this->processTable($answer_block_body, $position) as $table)
                {
                    $this->tables[] = $table;
                }
            }
        }
        return $this;
    }

   /**
    * This method saves the extracted information from the XML files of answer element into
     * msm_answer database table.  It calls saveInfoDb method for Subordinate, Media, Table,
     * and MathIndex classes.
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

        $data->answer_type = $this->answer_type;
        $data->answer_version = $this->answer_version;
        $data->caption = $this->caption;
        $data->logic_type = $this->logic_type;
        $data->answer_logic = $this->example_answer_logic;

        if (!empty($this->answer_block_body))
        {
            foreach ($this->answer_block_body as $block_body)
            {
                $data->answer_content = $block_body;
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

        // call saveIntoDb for each of child elements in order they appeared in
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
