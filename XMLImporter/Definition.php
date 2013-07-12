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
 * (ie. files in the newXML) and it is called by Block/Associate/Companion/Crossref/Unit/MathCell/Subordinate/MathIndex classes.
 * Definition class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Definition extends Element
{

    public $id;                                 // database ID of current def element in msm_def
    public $compid;                             // database ID of current def element in msm_compositor
    public $position;                           // integer that keeps track of order if elements
    public $caption;                            // title associated with this def element
    public $string_id;                          // unique string ID attached to this def element
                                                // (int in new XML material but in legacy material, it's a string value)
    public $type;                               // type of definition (eg. Notation, Definition...etc)
    public $description;                        // description associated with this def to allow to be searched 
    public $def_contents = array();             // contents of this def element
    public $medias = array();                   // Media objects associated with the def element's contents
    public $associates = array();               // Associate objects associated with the def element
    public $subordinates = array();             // Subordinate objects associated with the def element's contents
    public $indexauthors = array();             // MathIndex objects representing authors in def element's contents
    public $indexglossarys = array();           // MathIndex objects representing glossarys in def element's contents
    public $indexsymbols = array();             // MathIndex objects representing symbols in def element's contents
    public $tables = array();                   // Table objects in def element's contents
    public $matharrays = array();               // MathArray objects in def element's contents

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_def';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (def element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        def elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \Definition
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->type = $DomElement->getAttribute('type');

        $this->string_id = $DomElement->getAttribute('id');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $this->description = $this->getDomAttribute($DomElement->getElementsByTagName('description'));

        $associates = $DomElement->getElementsByTagName('associate');

        foreach ($associates as $a)
        {
            $position = $position + 1;
            $associate = new Associate($this->xmlpath);
            $associate->loadFromXml($a, $position);
            $this->associates[] = $associate;
        }
        $defbodys = $DomElement->getElementsByTagName('def.body');

        foreach ($defbodys as $d)
        {
            foreach ($this->processMathArray($d, $position) as $matharray)
            {
                $this->matharrays[] = $matharray;
            }

            foreach ($this->processIndexAuthor($d, $position) as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processIndexGlossary($d, $position) as $indexglossary)
            {
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processIndexSymbols($d, $position) as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }
            foreach ($this->processSubordinate($d, $position) as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processMedia($d, $position) as $media)
            {
                $this->medias[] = $media;
            }

            foreach ($this->processTable($d, $position) as $table)
            {
                $this->tables[] = $table;
            }

            foreach ($this->processContent($d, $position) as $content)
            {
                $this->def_contents[] = $content;
            }
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of def element into
     * msm_def database table.  It calls saveInfoDb method for Associate/Subordinate/Table/MathIndex/Media/MathArray classes.
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
        $data->def_type = $this->type;
        $data->string_id = $this->string_id;

        $ids = array();
        $tempContent = array();

        if (!empty($this->caption))
        {
            $data->caption = $this->caption;
        }

        $data->description = $this->description;

        if (!empty($this->def_contents))
        {
            // need to wrap all the content elements in div to have one root element
            foreach ($this->def_contents as $content)
            {
                $paracontent = '';

                $defbodyparser = new DOMDocument();
                @$defbodyparser->loadXML($content, true);

                $defbodyNode = $defbodyparser->documentElement;

                foreach ($defbodyNode->childNodes as $child)
                {
                    $paracontent .= $defbodyparser->saveXML($defbodyparser->importNode($child, true));
                }

                $content = "<div>$paracontent</div>";

                $data->def_content = $content;
                $tempContent[] = $content;

                $this->id = $DB->insert_record($this->tablename, $data);
                $ids[] = $this->id;
                $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
            }
        }
        else // has def.body as child of def
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

        // if there are media elements in the definition content, need to change the src to 
        // pluginfile.php format to serve the pictures.
        if (!empty($this->medias))
        {
            foreach ($ids as $key => $id)
            {
                $newdata = new stdClass();
                $newdata->id = $id;
                $newdata->def_type = $this->type;
                $newdata->string_id = $this->string_id;
                if (isset($this->caption))
                {
                    $newdata->caption = $this->caption;
                }

                $newdata->description = $this->description;
                $newdata->def_contents = $this->processDbContent($tempContent[$key], $this);
                $DB->update_record($this->tablename, $newdata);
            }
        }

        $sibling_id = 0;

        foreach ($this->associates as $associate)
        {
            $associate->saveIntoDb($associate->position, $msmid, $this->compid, $sibling_id);
            $sibling_id = $associate->compid;
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the def element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from Associate, Subordinate,
     * Media, MathArray and Table classes are also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current def element in msm_def table
     * @param int $compid                   database ID of the current def element in msm_compositor table
     * @param bool $indexref                flag to indicate that the MathIndex object called this function
     * @return \Definition
     */
    function loadFromDb($id, $compid, $indexref = false)
    {
        global $DB;

        $defCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $defRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($defRecord))
        {
            $this->compid = $compid;
            $this->def_type = $defRecord->def_type;
            $this->caption = $defRecord->caption;
            $this->def_content = $defRecord->def_content;
        }
        
        $childElements = $DB->get_records('msm_compositor', array('msm_id' => $defCompRecord->msm_id, 'parent_id' => $compid), 'prev_sibling_id');

        // @todo there definitely is a better way to code this!
        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            if (!$indexref)
            {
                switch ($childtablename)
                {
                    case('msm_associate'):
                        $associate = new Associate();
                        $associate->loadFromDb($child->unit_id, $child->id);
                        $this->associates[] = $associate;
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
            else // definitions associated with MathIndex does not have Associate objects linked to it
            {
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
        }

        return $this;
    }

    /**
     * This method produces an HTML code to display the retrieved data from method above and
     * also calls the same method in Associate, Subordinate, Media, MathArray and Table classes to
     * display the data from these classes.
     * 
     * @param bool $isindex             flag variable to indicate if this method was called by MathIndex object
     * @return string
     */
    function displayhtml($isindex = false)
    {
        $content = '';
        $content .= "<br />";
        $content .= "<div class='def'>";
        if (!empty($this->caption))
        {
            $content .= "<span class='deftitle'>" . $this->caption . "</span>";
        }

        if (!empty($this->def_type))
        {
            $content .= "<span class='deftype'>" . $this->def_type . "</span>";
        }
        $content .= "<br/>";


        $content .= "<div class='mathcontent'>";
        $content .= $this->displayContent($this, $this->def_content, $isindex);
        $content .= "<br />";
        $content .= "</div>";

        $content .= "<br />";

        if (!$isindex)
        {
            if (!empty($this->associates))
            {
                $content .= "<ul class='defminibuttons'>";
                foreach ($this->associates as $key => $associate)
                {
                    $content .= $associate->displayhtml();
                }
                $content .= "</ul>";
            }
        }

        $content .= "</div>";
        $content .= "<br />";

//        print_object($content);

        return $content;
    }

}

?>
