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
 * This class represents al the answer.showme XML elements in the legacy document
 * (ie. files in the newXML) and it is called by Showme class.
 * AnswerShowme class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class AnswerShowme extends Element
{

    public $id;                             // database ID of current answer.showme element in msm_answer_showme
    public $compid;                         // database ID of current answer.showme element in msm_compositor
    public $position;                       // integer that keeps track of order of elements
    public $type;                           // type of the answer.showme element
    public $content = array();              // content associated with the answer.showme (in answer.showme.block) elements
    public $subordinates = array();         // Subordinate objects associated with the content of the answer.showme element
    public $indexauthors = array();         // MathIndex objects associated with the content of the answer.showme element    
    public $indexglossarys = array();       // MathIndex objects associated with the content of the answer.showme element    
    public $indexsymbols = array();         // MathIndex objects associated with the content of the answer.showme element    
    public $medias = array();               // Media objects associated with the content of the answer.showme element    
    public $tables = array();               // Table objects associated with the content of the answer.showme element    
    public $matharrays = array();           // MathArray objects associated with the content of the answer.showme element    

    /**
     * constructor for the instace of this class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_answer_showme';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (answer.showme element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        answer.showme elements
     * @param int $position                 integer that keeps track of order if elements
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->type = $DomElement->getAttribute('type');
        $this->position = $position;

        $answer_showme_blocks = $DomElement->getElementsByTagName('answer.showme.block');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        foreach ($answer_showme_blocks as $asb)
        {
            $answer_showme_block_bodys = $asb->getElementsByTagName('answer.showme.block.body');

            foreach ($answer_showme_block_bodys as $asbb)
            {
                foreach ($this->processMathArray($asbb, $position) as $matharray)
                {
                    $this->matharrays[] = $matharray;
                }

                foreach ($this->processIndexAuthor($asbb, $position) as $indexauthor)
                {
                    $this->indexauthors[] = $indexauthor;
                }

                foreach ($this->processIndexGlossary($asbb, $position) as $indexglossary)
                {
                    $this->indexglossarys[] = $indexglossary;
                }

                foreach ($this->processIndexSymbols($asbb, $position) as $indexsymbol)
                {
                    $this->indexsymbols[] = $indexsymbol;
                }
                foreach ($this->processSubordinate($asbb, $position) as $subordinate)
                {
                    $this->subordinates[] = $subordinate;
                }
                foreach ($this->processMedia($asbb, $position) as $media)
                {
                    $this->medias[] = $media;
                }
                foreach ($this->processTable($asbb, $position) as $table)
                {
                    $this->tables[] = $table;
                }
                foreach ($this->processContent($asbb, $position) as $content)
                {
                    $this->content[] = $content;
                }
            }
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of answer.showme element into
     * msm_answer_showme database table.  It calls saveInfoDb method for MathArray, Subordinate, Media, Table,
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
        $data->caption = $this->caption;
        $data->type = $this->type;

        $ids = array();

        if (!empty($this->content))
        {
            foreach ($this->content as $key => $content)
            {
                $data->answer_showme_content = $content;
                $this->id = $DB->insert_record($this->tablename, $data);
                $ids[] = $this->id;
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


        if (!empty($this->matharrays))
        {
            foreach ($this->matharrays as $key => $matharray)
            {
                $elementPositions['matharray' . '-' . $key] = $matharray->position;
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

        // if the content has media elements in legacy XMl files, need to process all image tags to
        // replace src from relative pathing to moodle file area url with pluginfile.php
        if (!empty($this->medias))
        {
            foreach ($ids as $key => $id)
            {
                $newdata = new stdClass();
                $newdata->id = $id;
                $newdata->caption = $this->caption;
                $newdata->type = $this->type;
                $newdata->answer_showme_content = $this->processDbContent("<div>" . $this->content[$key] . "</div>", $this);

                $DB->update_record($this->tablename, $newdata);
            }
        }
    }

    /**
     * This method retrieves all relevant data to display the answer.showme elements from msm_answer_showme database table.
     * The method also calls the loadFromDb for classes Subordinate, Table, MathArray and Media.
     * 
     * @global moodle_databse $DB   
     * @param int $id                   ID of current answer.showme element in msm_answer_showme
     * @param int $compid               ID of current asnwer.showme element in msm_compositor
     * @return \AnswerShowme
     */
    function loadFromDb($id, $compid)
    {
        global $DB;

        $answershowmeCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $answershowmeRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($answershowmeRecord))
        {
            $this->compid = $compid;
            $this->caption = $answershowmeRecord->caption;
            $this->content = $answershowmeRecord->answer_showme_content;
            $this->type = $answershowmeRecord->type;
        }

        $this->subordinates = array();
        $this->medias = array();
        $this->tables = array();
        $this->matharray = array();

        $childElements = $DB->get_records('msm_compositor', array('msm_id' => $answershowmeCompRecord->msm_id, 'parent_id' => $this->compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            switch ($childtablename)
            {
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

                case('msm_table'):
                    $table = new Table();
                    $table->loadFromDb($child->unit_id, $child->id);
                    $this->tables[] = $table;
                    break;

                case('msm_math_array'):
                    $matharray = new MathArray();
                    $matharray->loadFromDb($child->unit_id, $child->id);
                    $this->matharrays[] = $matharray;
                    break;
            }
        }

        return $this;
    }

    /**
     * This method takes the data retrieved from the method above and outputs properly
     * structured HTML string for displaying the information.  The method also calls 
     * displayhtml function for Subordinate, Media, MathArray and Table indirectly by
     * calling the displayContent method in Element class.
     * 
     * @return string
     */
    function displayhtml()
    {
        $content = '';
        $content .= "<br />";
        $content .= "<div class='answershowme'>";

        $content .= "<span class='answershowmetitle'>";
        $content .= $this->caption;
        $content .= "</span>";

        $content .= "<span class='answershowmetype'>";
        $content .= $this->type;
        $content .= "</span>";
        $content .= "<br />";

        $content .= "<div class='mathcontent'>";
        $content .= $this->displayContent($this, $this->content);
        $content .= "</div>";
        $content .= "<br />";

        $content .= "</div>";

        return $content;
    }

}

?>
