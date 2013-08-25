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
 * This class represents all the problem XML elements in the legacy document
 * (ie. files in the newXML) and it is called by Exercise/PartExercise classes.
 * Problem class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Problem extends Element {

    public $id;                            // database ID associated with the problem element in msm_problem table
    public $compid;                        // database ID associated with the problem element in msm_compositor table
    public $position;                      // integer that keeps track of order of elements
    public $textcaption;                   // plain text title associated with this problem (ie. no math elements)
    public $caption;                       // title associated with this problem
    public $content;                       // content elements associated with the problem element
    public $indexauthors = array();        // MathIndex associated with the problem element --> referncing index.author
    public $indexglossarys = array();      // MathIndex associated with the problem element --> referncing index.glossary
    public $indexsymbols = array();        // MathIndex associated with the problem element --> referncing index.symbol
    public $subordinates = array();        // Subordinate objects associated with the problem elements
    public $tables = array();              // Table objects associated with the problem elements
    public $medias = array();              // Media objects associated with the problem elements

    /**
     * constructor for the instace of this class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '') {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_problem';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (problem element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        problem element in XML file
     * @param int $position                 integer that keeps track of order if elements
     */
    public function loadFromXml($DomElement, $position = '') {
        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $problembody = $DomElement->getElementsByTagName('problem.body')->item(0);

        foreach ($this->processIndexAuthor($problembody, $position) as $indexauthor) {
            $this->indexauthors[] = $indexauthor;
        }

        foreach ($this->processIndexGlossary($problembody, $position) as $indexglossary) {
            $this->indexglossarys[] = $indexglossary;
        }

        foreach ($this->processIndexSymbols($problembody, $position) as $indexsymbol) {
            $this->indexsymbols[] = $indexsymbol;
        }
        foreach ($this->processSubordinate($problembody, $position) as $subordinate) {
            $this->subordinates[] = $subordinate;
        }

        foreach ($this->processMedia($problembody, $position) as $media) {
            $this->medias[] = $media;
        }

        foreach ($this->processTable($problembody, $position) as $table) {
            $this->tables[] = $table;
        }

        foreach ($this->processContent($problembody, $position) as $content) {
            $this->content .= $content;
        }

        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of problem element into
     * msm_problem database table.  This method also calls the saveIntoDb from Subordinate/MathIndex/
     * Media/Table classes.
     * 
     * @global moodle_databse $DB
     * @param int $position              integer that keeps track of order if elements
     * @param int $msmid                 MSM instance ID
     * @param int $parentid              ID of the parent element from msm_compositor
     * @param int $siblingid             ID of the previous sibling element from msm_compositor
     */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '') {
        global $DB;
        $data = new stdClass();

        $data->textcaption = $this->textcaption;
        $data->caption = $this->caption;

        if (!empty($this->content)) {
            $data->problem_content = $this->content;
            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        } else {
            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        }

        $elementPositions = array();
        $sibling_id = null;


        if (!empty($this->subordinates)) {
            foreach ($this->subordinates as $key => $subordinate) {
                $elementPositions['subordinate' . '-' . $key] = $subordinate->position;
            }
        }

        if (!empty($this->indexauthors)) {
            foreach ($this->indexauthors as $key => $indexauthor) {
                $elementPositions['indexauthor' . '-' . $key] = $indexauthor->position;
            }
        }

        if (!empty($this->indexglossarys)) {
            foreach ($this->indexglossarys as $key => $indexglosary) {
                $elementPositions['indexglossary' . '-' . $key] = $indexglosary->position;
            }
        }

        if (!empty($this->indexsymbols)) {
            foreach ($this->indexsymbols as $key => $indexsymbol) {
                $elementPositions['indexsymbol' . '-' . $key] = $indexsymbol->position;
            }
        }

        if (!empty($this->medias)) {
            foreach ($this->medias as $key => $media) {
                $elementPositions['media' . '-' . $key] = $media->position;
            }
        }

        if (!empty($this->tables)) {
            foreach ($this->tables as $key => $table) {
                $elementPositions['table' . '-' . $key] = $table->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value) {
            switch ($element) {
                case(preg_match("/^(subordinate.\d+)$/", $element) ? true : false):
                    $subordinateString = explode('-', $element);

                    if (empty($sibling_id)) {
                        $subordinate = $this->subordinates[$subordinateString[1]];
                        $subordinate->saveIntoDb($subordinate->position, $msmid, $this->compid);
                        $sibling_id = $subordinate->compid;
                    } else {
                        $subordinate = $this->subordinates[$subordinateString[1]];
                        $subordinate->saveIntoDb($subordinate->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $subordinate->compid;
                    }
                    break;

                case(preg_match("/^(indexauthor.\d+)$/", $element) ? true : false):
                    $indexauthorString = explode('-', $element);

                    if (empty($sibling_id)) {
                        $indexauthor = $this->indexauthors[$indexauthorString[1]];
                        $indexauthor->saveIntoDb($indexauthor->position, $msmid, $this->compid);
                        $sibling_id = $indexauthor->compid;
                    } else {
                        $indexauthor = $this->indexauthors[$indexauthorString[1]];
                        $indexauthor->saveIntoDb($indexauthor->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $indexauthor->compid;
                    }
                    break;

                case(preg_match("/^(indexsymbol.\d+)$/", $element) ? true : false):
                    $indexsymbolString = explode('-', $element);

                    if (empty($sibling_id)) {
                        $indexsymbol = $this->indexsymbols[$indexsymbolString[1]];
                        $indexsymbol->saveIntoDb($indexsymbol->position, $msmid, $this->compid);
                        $sibling_id = $indexsymbol->compid;
                    } else {
                        $indexsymbol = $this->indexsymbols[$indexsymbolString[1]];
                        $indexsymbol->saveIntoDb($indexsymbol->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $indexsymbol->compid;
                    }
                    break;

                case(preg_match("/^(indexglossary.\d+)$/", $element) ? true : false):
                    $indexglossaryString = explode('-', $element);

                    if (empty($sibling_id)) {
                        $indexglossary = $this->indexglossarys[$indexglossaryString[1]];
                        $indexglossary->saveIntoDb($indexglossary->position, $msmid, $this->compid);
                        $sibling_id = $indexglossary->compid;
                    } else {
                        $indexglossary = $this->indexglossarys[$indexglossaryString[1]];
                        $indexglossary->saveIntoDb($indexglossary->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $indexglossary->compid;
                    }
                    break;

                case(preg_match("/^(media.\d+)$/", $element) ? true : false):
                    $mediaString = explode('-', $element);

                    if (empty($sibling_id)) {
                        $media = $this->medias[$mediaString[1]];
                        $media->saveIntoDb($media->position, $msmid, $this->compid);
                        $sibling_id = $media->compid;
                    } else {
                        $media = $this->medias[$mediaString[1]];
                        $media->saveIntoDb($media->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $media->compid;
                    }
                    break;

                case(preg_match("/^(table.\d+)$/", $element) ? true : false):
                    $tableString = explode('-', $element);

                    if (empty($sibling_id)) {
                        $table = $this->tables[$tableString[1]];
                        $table->saveIntoDb($table->position, $msmid, $this->compid);
                        $sibling_id = $table->compid;
                    } else {
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
