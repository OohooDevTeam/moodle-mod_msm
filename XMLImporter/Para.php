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
 * This class represents all the para XML elements in the legacy document
 * (ie. files in the newXML) and the newly formed XML exported by the editor system
 * and it is called by Block/Unit classes. Para class inherits from the abstract class Element
 * and for all the methods inherited, read documents for Element class.
 * 
 * NOTE:  This class may become obsolete but initially was created to parse the XML files and upon request 
 * from client to have every para to be searchable but not sure if necessary anymore? --> caption and description isn't
 * implemented in the new material b/c para's are added by tinyMCE automatically when user
 * types in content in the tinyMCE editor.  Possibly not a good idea to prompt user to
 * type in caption/description everytime a paragraph is added?  
 *
 * @author Ga Young Kim
 */
class Para extends Element
{

    public $id;                             // Database ID associated with this para element in msm_para table
    public $compid;                         // Database ID asscciated with this para element in msm_compositor table
    public $position;                       // integer that keeps track of order of elements
    public $string_id;                      // unique sting ID given by the user to this para element (it doesn't exist in new XML files)
    public $align;                          // alignement of the para in the content (left/right/center);
    public $caption;                        // title associated with this para (none exists at the moment in both legacy and new XML files)
    public $description;                    // description associated with this para (none exists at the moment in both legacy and new XML files)
    public $indexauthors = array();         // MathIndex objects associated with this para --> this represents index.author elements
    public $indexglossary = array();        // MathIndex objects associated with this para --> this represents index.glossary elements
    public $indexsymbols = array();         // MathIndex objects associated with this para --> this represents index.symbol elements
    public $subordinates = array();         // Subordinate objects associated with this para
    public $medias = array();               // Media objects associated with this para
    public $tables = array();               // Table objects associated with this para
    public $para_content;                   // content of the para element

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */
    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_para';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (para element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        para elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \Para
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->string_id = $DomElement->getAttribute('id');
        $this->align = $DomElement->getAttribute('align');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $this->description = $this->getDomAttribute($DomElement->getElementsByTagName('description'));

        foreach ($this->processIndexAuthor($DomElement, $position) as $indexauthor)
        {
            $this->indexauthors[] = $indexauthor;
        }

        foreach ($this->processIndexGlossary($DomElement, $position) as $indexglossary)
        {
            $this->indexglossarys[] = $indexglossary;
        }

        foreach ($this->processIndexSymbols($DomElement, $position) as $indexsymbol)
        {
            $this->indexsymbols[] = $indexsymbol;
        }
        foreach ($this->processSubordinate($DomElement, $position) as $subordinate)
        {
            $this->subordinates[] = $subordinate;
        }

        foreach ($this->processMedia($DomElement, $position) as $media)
        {
            $this->medias[] = $media;
        }

        foreach ($this->processTable($DomElement, $position) as $table)
        {
            $this->tables[] = $table;
        }

        foreach ($this->processContent($DomElement, $position) as $content)
        {
            $this->para_content .= $content;
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of para element into
     * msm_para database table.  It calls saveInfoDb method for MathIndex, Subordinate, Media
     * MathArray, and Table classes.
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
        $data->para_align = $this->align;
        $data->caption = $this->caption;
        $data->description = $this->description;

        if (!empty($this->para_content))
        {
            $data->para_content = $this->para_content;

            $this->id = $DB->insert_record($this->tablename, $data);

            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
            $siblingid = $this->compid;
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
        
        // process the images in the content to have proper file pathing in
        // src attribute --> need to have pathing to moodle file storage area
        // which contains pluginfile.php script to correctly process encrypted
        // pathing information
        if (!empty($this->medias))
        {
            $newparadata = new stdClass();
            $newparadata->id = $this->id;
            $newparadata->string_id = $this->string_id;
            $newparadata->para_align = $this->align;
            $newparadata->caption = $this->caption;
            $newparadata->description = $this->description;
            $newparadata->para_content = $this->processDbContent($this->para_content, $this);
            $DB->update_record($this->tablename, $newparadata);
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the para element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from Subordinate, Table
     * MathArray and Media classes are also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current para element in msm_para table
     * @param int $compid                   database ID of the current para element in msm_compositor table
     * @return \Para
     */
    function loadFromDb($id, $compid)
    {
        global $DB;

        $pararecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($pararecord))
        {
            $this->align = $pararecord->para_align;
            $this->caption = $pararecord->caption;
            $this->para_content = $pararecord->para_content;
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
            }
        }
        return $this;
    }

    /**
     * This method produces an HTML code to display the retrieved data from method above and
     * also calls the same method in Subordinate, Media, MathArray and Table classes to
     * display the data from these classes.
     * 
     * @param bool $isindex             flag variable to indicate if this method was called by MathIndex object
     * @return string
     */
    function displayhtml($isindex = false)
    {
        $content = '';
        $content .= $this->displayContent($this, $this->para_content, $isindex);
        return $content;
    }

}

?>
