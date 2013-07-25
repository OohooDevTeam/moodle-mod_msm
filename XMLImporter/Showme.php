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
 * This class represents all the showme XML elements in the legacy document
 * (ie. files in the newXML) and it is called by Pack class.
 * Showme class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Showme extends Element
{

    public $id;                         // database ID of the showme element in msm_showme table
    public $compid;                     // database ID of the showme element in msm_compositor table
    public $position;                   // integer that keeps track of order of elements
    public $caption;                    // title element associated with the showme element
    public $textcaption;                // title element without any math elements that is associated with the showme element
    public $statements;                 // content associated with the showme element
    public $subordinate = array();      // Subordinate objects associated with the showme element
    public $indexauthors = array();     // MathIndex objects associated with the showme element --> info on authors
    public $indexglossarys = array();   // MathIndex objects associated with the showme element --> info on terms
    public $indexsymbols = array();     // MathIndex objects associated with the showme element --> info on symbols
    public $tables = array();           // Table objects associated with the showme element
    public $medias = array();           // Media objects associated with the showme element
    public $matharrays = array();       // MathArray objects associated with the showme element
    public $answer_showmes = array();   // AnswerShowme objects associated with the showme element --> for parse/insert to db
    public $childs = array();           // AnswerShowme objects associated with the showme element --> for load/display

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */
    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_showme';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (showme element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        showme element
     * @param int $position                 integer that keeps track of order if elements
     * @return \Showme
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));

        $statements = $DomElement->getElementsByTagName('statement.showme');

        foreach ($statements as $st)
        {
            foreach ($this->processIndexAuthor($st, $position) as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processIndexGlossary($st, $position) as $indexglossary)
            {
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processIndexSymbols($st, $position) as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }
            foreach ($this->processSubordinate($st, $position) as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processMedia($st, $position) as $media)
            {
                $this->medias[] = $media;
            }

            foreach ($this->processMathArray($st, $position) as $matharray)
            {
                $this->matharrays[] = $matharray;
            }

            foreach ($this->processTable($st, $position) as $table)
            {
                $this->tables[] = $table;
            }

            foreach ($this->processContent($st, $position) as $content)
            {
                $this->statements .= $content;
            }
        }

        $answer_showmes = $DomElement->getElementsByTagName('answer.showme');
        foreach ($answer_showmes as $a)
        {
            $position = $position + 1;
            $answer_showme = new AnswerShowme($this->xmlpath);
            $answer_showme->loadFromXml($a, $position);
            $this->answer_showmes[] = $answer_showme;
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of showme element and its associated child elements into
     * their respective database tables.  It calls saveInfoDb method for MathIndex, Media, Table, and Subordinate classes.
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
        $data->caption = $this->caption;
        $data->textcaption = $this->textcaption;

        $data->statement_showme = $this->statements;
        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $elementPositions = array();
        $sibling_id = null;

        if (!empty($this->answer_showmes))
        {
            foreach ($this->answer_showmes as $key => $answer_showme)
            {
                $elementPositions['answershowme' . '-' . $key] = $answer_showme->position;
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

        if (!empty($this->matharrays))
        {
            foreach ($this->matharrays as $key => $matharray)
            {
                $elementPositions['matharray-' . $key] = $matharray->position;
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
                case(preg_match("/^(answershowme.\d+)$/", $element) ? true : false):
                    $answershowmeString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $answershowme = $this->answer_showmes[$answershowmeString[1]];
                        $answershowme->saveIntoDb($answershowme->position, $msmid, $this->compid);
                        $sibling_id = $answershowme->compid;
                    }
                    else
                    {
                        $answershowme = $this->answer_showmes[$answershowmeString[1]];
                        $answershowme->saveIntoDb($answershowme->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $answershowme->compid;
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

                case(preg_match("/^(matharray.\d+)$/", $element) ? true : false):
                    $matharrayString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $matharray = $this->matharrays[$matharrayString[1]];
                        $matharray->saveIntoDb($matharray->position, $msmid, $this->compid);
                        $sibling_id = $matharray->compid;
                    }
                    else
                    {
                        $matharray = $this->matharrays[$matharrayString[1]];
                        $matharray->saveIntoDb($matharray->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $matharray->compid;
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

        // if there are media elements in the showme content, need to change the src to 
        // pluginfile.php format to serve the pictures.
        if (!empty($this->medias))
        {
            $newdata = new stdClass();
            $newdata->id = $this->id;
            $newdata->caption = $this->caption;
            $newdata->textcaption = $this->textcaption;
            $newdata->statement_showme = $this->processDbContent("<div>$this->statements</div>", $this);

            $DB->update_record($this->tablename, $newdata);
        }
    }

    /**
     * This method retrieves all relevant data to display the showme elements from msm_showme database table.
     * The method also calls the loadFromDb for classes AnswerShowme, Subordinate, Table, MathArray and Media.
     * 
     * @global moodle_databse $DB   
     * @param int $id                   ID of current showme element in msm_showme
     * @param int $compid               ID of current showme element in msm_compositor
     * @return \Showme
     */
    function loadFromDb($id, $compid)
    {
        global $DB;

        $showmeRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($showmeRecord))
        {
            $this->compid = $compid;
            $this->caption = $showmeRecord->caption;
            $this->textcaption = $showmeRecord->textcaption;
            $this->statement_showme = $showmeRecord->statement_showme;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            switch ($childtablename)
            {
                case('msm_answer_showme'):
                    $answershowme = new AnswerShowme();
                    $answershowme->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $answershowme;
                    break;

                case('msm_subordinate'):
                    $subordinate = new Subordinate();
                    $subordinate->loadFromDb($child->unit_id, $child->id);
                    $this->subordinates[] = $subordinate;
                    break;

                case('msm_media'):
                    $media = new Media();
                    $media->loadFromDb($child->unit_id, $child->id);
                    $this->medias[] = $media;
                    break;

                case('msm_math_array'):
                    $matharray = new MathArray();
                    $matharray->loadFromDb($child->unit_id, $child->id);
                    $this->matharrays[] = $matharray;
                    break;

                case('msm_table'):
                    $table = new Table();
                    $table->loadFromDb($child->unit_id, $child->id);
                    $this->tables[] = $table;
                    break;
            }
        }

        return $this;
    }

    /**
     * This method takes the data retrieved from the method above and outputs properly
     * structured HTML string for displaying the information.  The method also calls 
     * displayhtml function for AnswerShowme directly and Subordinate, Media, MathArray and Table indirectly by
     * calling the displayContent method in Element class.
     * 
     * @return string
     */
    function displayhtml()
    {
        $content = '';
        $content .= "<br />";
        $content .= "<div class='showme'>";

        $content .= "<span class='showmetitle'>";
        $content .= $this->caption;
        $content .= "</span>";

        $content .= "<span class='showmetype'>";
        $content .= "Example";
        $content .= "</span>";

        $content .= "<br />";

        $content .= "<div class='mathcontent'>";
        $content .= $this->displayContent($this, $this->statement_showme);
        $content .= "</div>";
        $content .= "<br />";
        $content .= "</div>";
        $content .= "<br />";

        foreach ($this->childs as $childComponent)
        {
            $content .= $childComponent->displayhtml();
        }


        return $content;
    }

}

?>
