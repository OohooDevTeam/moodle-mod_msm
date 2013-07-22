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
 * This class represents all the def XML elements in the legacy document
 * (ie. files in the newXML) and the newly formed XML exported by the editor system
 * and it is called by StatementTheorem class.  PartTheorem class inherits from the
 * abstract class Element and for all the methods inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class PartTheorem extends Element
{

    public $id;                             // database ID associated with the part.theorem element in msm_part_theorem table
    public $compid;                         // database ID associated with the part.theorem element in msm_compositor table
    public $position;                       // integer that keeps track of order of elements
    public $partid;                         // unique user-defined string ID to identify the specified part of the theorem
    public $counter;                        // the numbering used (similar to the ordered list list-style-type property)
    public $equiv_mark;                     // marks which parts are equivalent to another(?) --> represents the equivalence.mark attribute
    public $caption;                        // title associated with this part.theorem element
    public $part_content;                   // content of the part.theorem element when being loaded from DB and being displayed
    public $content = array();              // content objects that are associated with this part.theorem --> used for insert into db
    public $subordinates = array();         // Subordinate objects associated with the part.theorem element
    public $indexauthors = array();         // MathIndex objects associated with the part.theorem element (representing the index.author element)
    public $indexglossarys = array();       // MathIndex objects associated with the part.theorem element (representing the index.glossary element)
    public $indexsymbols = array();         // MathIndex objects associated with the part.theorem element (representing the index.symbol element)
    public $medias = array();               // Media objects associated with the part.theorem element
    public $matharrays = array();           // MathArray objects associated with the part.theorem element
    public $tables = array();               // Table objects associated with the part.theorem element
    
    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */
    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_part_theorem';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (part.theorem element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        part.theorem elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \PartTheorem
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->partid = $DomElement->getAttribute('partid');
        $this->counter = $DomElement->getAttribute('counter');
        $this->equiv_mark = $DomElement->getAttribute('equivalence.mark');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $part_bodys = $DomElement->getElementsByTagName('part.body');
        foreach ($part_bodys as $parb)
        {
            foreach ($this->processIndexAuthor($parb, $position) as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processIndexGlossary($parb, $position) as $indexglossary)
            {
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processIndexSymbols($parb, $position) as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }
            foreach ($this->processSubordinate($parb, $position) as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processMedia($parb, $position) as $media)
            {
                $this->medias[] = $media;
            }

            foreach ($this->processTable($parb, $position) as $table)
            {
                $this->tables[] = $table;
            }

            foreach ($this->processMathArray($parb, $position) as $matharray)
            {
                $this->matharrays[] = $matharray;
            }

            foreach ($this->processContent($parb, $position) as $content)
            {
                $this->content[] = $content;
            }
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of part.theorem element into
     * msm_part_theorem database table.  It calls saveInfoDb method for Subordinate/MathIndex/Table/
     * Media/MathArray classes.
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

        $ids = array();
        $tempContent = array();

        $data = new stdClass();

        $data->partid = $this->partid;
        $data->counter = $this->counter;
        $data->equivalence_mark = $this->equiv_mark;
        $data->caption = $this->caption;

        if (!empty($this->content))
        {
            foreach ($this->content as $content)
            {
                $partcontent = '';
                $contentparser = new DOMDocument();
                @$contentparser->loadXML($content, true);
                $contentNode = $contentparser->documentElement;                

                foreach ($contentNode->childNodes as $child)
                {
                    $partcontent .= $contentparser->saveXML($contentparser->importNode($child, true));
                }

                $content = "<div>$partcontent</div>";

                $data->part_content = $content;
                $tempContent[] = $content;
//           
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
                $elementPositions['matharray' . '-' . $key] = $matharray->position;
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

        // if there are media elements in the part.theorem content, need to change the src to 
        // pluginfile.php format to serve the pictures.
        if (!empty($this->medias))
        {
            foreach ($ids as $key => $id)
            {
                $newdata = new stdClass();
                $newdata->id = $id;
                $newdata->partid = $this->partid;
                $newdata->counter = $this->counter;
                $newdata->equivalence_mark = $this->equiv_mark;
                $newdata->caption = $this->caption;
                $newdata->part_content = $this->processDbContent($tempContent[$key], $this);

                $DB->update_record($this->tablename, $newdata);
            }
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the part.theorem element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from Subordinate,
     * Media, MathArray and Table classes are also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current part.theorem element in msm_part_theorem table
     * @param int $compid                   database ID of the current part.theorem element in msm_compositor table
     * @return \PartTheorem
     */
    function loadFromDb($id, $compid)
    {
        global $DB;

        $partTheoremRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($partTheoremRecord))
        {
            $this->compid = $compid;
            $this->caption = $partTheoremRecord->caption;
            $this->part_content = $partTheoremRecord->part_content;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

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

                case('msm_media'):
                    $media = new Media();
                    $media->loadFromDb($child->unit_id, $child->id);
                    $this->medias[] = $media;
                    break;
            }
        }

        return $this;
    }

    /**
     * This method produces an HTML code to display the retrieved data from method above and
     * also calls the same method in Subordinate, Media, MathArray and Table classes to
     * display the data from these classes.  The part theorem elements are displayed as an item in
     * an ordered list with statement.theorem as a parent content.
     * 
     * @param bool $isindex             flag variable to indicate if this method was called by MathIndex object
     * @return string
     */
    function displayhtml($isindex = false)
    {
        $content = '';
        $content .= "<li>";
        if (!empty($this->caption))
        {
            $content .= "<span class='parttheoremtitle'>" . $this->caption . "</span>";
        }
        $content .= $this->displayContent($this, $this->part_content, $isindex);
        $content .= "</li>";
        $content .= "<br />";

        return $content;
    }

}

?>
