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
 * This class represents all the comment XML elements in the legacy document
 * (ie. files in the newXML) and and new XML files created from editor and this class is called
 * by Block/Associate/Companion/Crossref/Unit/MathCell/Subordinate/MathIndex classes.
 * MathComment class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class MathComment extends Element
{

    public $id;                             // database ID associated with this comment element in msm_comment table
    public $compid;                         // database ID associated with this comment element in msm_compositor table
    public $position;                       // integer that keeps track of order of elements
    public $string_id;                      // unique string ID given to this comment by the user (in legacy XML) or same as compid above (new XML)
    public $comment_type;                   // type associated with this comment (ie. comment, remark...etc)
    public $caption;                        // title associated with this comment
    public $comment_content = array();      // content elements associated with this comment
    public $description;                    // description associated with this comment (used in search for reference material)
    public $associates = array();           // Associated objects associated with this comment
    public $subordinates = array();         // Subordinate objects associated with this comment
    public $indexauthors = array();         // MathIndex objects associated with this comment --> in this case, it's for authors
    public $indexglossarys = array();       // MathIndex objects associated with this comment --> in this case, it's for glossarys
    public $indexsymbols = array();         // MathIndex objects associated with this comment --> in this case, it's for symbols
    public $medias = array();               // Media objects associated with this comment (ie. images, videos)
    public $tables = array();               // Table objects associated with this comment
    public $matharrays = array();           // MathArray objects associated with this comment

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_comment';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (comment element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        comment elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \MathComment
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->comment_type = $DomElement->getAttribute('type');
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


        $commentbodys = $DomElement->getElementsByTagName('comment.body');

        foreach ($commentbodys as $c)
        {
            foreach ($this->processIndexAuthor($c, $position) as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processIndexGlossary($c, $position) as $indexglossary)
            {
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processIndexSymbols($c, $position) as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }
            foreach ($this->processSubordinate($c, $position) as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processMedia($c, $position) as $media)
            {
                $this->medias[] = $media;
            }

            foreach ($this->processTable($c, $position) as $table)
            {
                $this->tables[] = $table;
            }

            foreach ($this->processMathArray($c, $position) as $matharray)
            {
                $this->matharrays[] = $matharray;
            }

            foreach ($this->processContent($c, $position) as $content)
            {
                $this->comment_content[] = $content;
            }
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of comment element into
     * msm_comment database table.  It calls saveInfoDb method for Associate/Subordinate/Table/
     * MathIndex/Media/MathArray classes.
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
        $ids = array();         // ids associated with these comments --> needed when processing media in content
        $tempContent = array(); // array that contains all the comment contents to later get media elements processed

        $data = new stdClass();
        $data->comment_type = $this->comment_type;
        $data->string_id = $this->string_id;
        $data->description = $this->description;
        if (!empty($this->caption))
        {
            $data->caption = $this->caption;
        }

        if (!empty($this->comment_content))
        {
            foreach ($this->comment_content as $key => $content)
            {
                $commentcontent = '';
                $commentbodyparser = new DOMDocument();
                $commentbodyparser->loadXML($content, true);

                $commentbodyNode = $commentbodyparser->documentElement;

                foreach ($commentbodyNode->childNodes as $child)
                {
                    $commentcontent .= $commentbodyparser->saveXML($commentbodyparser->importNode($child, true));
                }

                $content = "<div>$commentcontent</div>"; // need div as root element for later using DOMDocument for parsing

                $data->comment_content = $content;
                $tempContent[] = $content;

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

        if (!empty($this->associates))
        {
            foreach ($this->associates as $key => $associate)
            {
                $elementPositions['associate' . '-' . $key] = $associate->position;
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
                case(preg_match("/^(associate.\d+)$/", $element) ? true : false):
                    $associateString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $associate = $this->associates[$associateString[1]];
                        $associate->saveIntoDb($associate->position, $msmid, $this->compid);
                        $sibling_id = $associate->compid;
                    }
                    else
                    {
                        $associate = $this->associates[$associateString[1]];
                        $associate->saveIntoDb($associate->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $associate->compid;
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

        // process all media elements --> make image src attribute have proper values
        // containing pluginfiles.php script
        if (!empty($this->medias))
        {
            foreach ($ids as $key => $id)
            {
                $newdata = new stdClass();
                $newdata->id = $this->id;
                $newdata->comment_type = $this->comment_type;
                $newdata->string_id = $this->string_id;
                if (isset($this->caption))
                {
                    $newdata->caption = $this->caption;
                }

                $newdata->description = $this->description;

                $newdata->comment_content = $this->processDbContent($tempContent[$key], $this);

                $DB->update_record($this->tablename, $newdata);
            }
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the comment element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from Associate, Subordinate,
     * Media, MathArray and Table classes are also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current comment element in msm_comment table
     * @param int $compid                   database ID of the current comment element in msm_compositor table
     * @param bool $indexref                flag to indicate that the MathIndex object called this function
     * @return \MathComment
     */
    function loadFromDb($id, $compid, $indexref = false)
    {
        global $DB;

        $commentRecord = $DB->get_record('msm_comment', array('id' => $id));

        if (!empty($commentRecord))
        {
            $this->compid = $compid;
            $this->comment_type = $commentRecord->comment_type;
            $this->caption = $commentRecord->caption;
            $this->comment_content = $commentRecord->comment_content;
            $this->description = $commentRecord->description;
        }

        $childElement = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childElement as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            if (!$indexref)
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

                    case('msm_associate'):
                        $associate = new Associate();
                        $associate->loadFromDb($child->unit_id, $child->id);
                        $this->associates[] = $associate;
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
            else
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
        $content .= "<div class='comment'>";
        if (!empty($this->caption))
        {
            $content .= "<span class='commenttitle'>" . $this->caption . "</span>";
        }

        if (!empty($this->comment_type))
        {
            $content .= "<span class='commenttype'>" . $this->comment_type . "</span>";
        }
        $content .= "<br/>";

        $content .= "<div class='mathcontent'>";
        $content .= $this->displayContent($this, $this->comment_content, $isindex);
        $content .= "<br />";
        $content .= "</div>";

        $content .= "<br />";

        if (!$isindex)
        {
            if (!empty($this->associates))
            {
                $content .= "<ul class='commentminibuttons'>";
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
