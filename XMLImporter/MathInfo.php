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
 * This class represents all the info XML elements in the legacy document
 * (ie. files in the newXML) and the newly formed XML exported by the editor system
 * and it is called by Associate/Companion/Crossref/Subordinate/ExternalLink/ImgArea/
 * MathCell/MathIndex/MathQuiz/Media/PartQuiz/QuizChoice classes.  The info element
 * in the XML files represents the extra information given in form of a popup.
 * MathInfo class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class MathInfo extends Element {

    public $id;                             // Database ID associated with this info element in msm_info table
    public $compid;                         // Database ID associated with this info element in msm_compositor table
    public $position;                       // integer that keeps track of order of elements
    public $info_content;                   // content associated with this info element
    public $caption;                        // title associated with this info element
    public $subordinates = array();         // Subordinate objects associated with this info element (ie. meaning this info has nested popups)
    public $indexauthors = array();         // MathIndex objects associated with this info element --> this represents the authors
    public $indexglossarys = array();       // MathIndex objects associated with this info element --> this represents the glossarys
    public $indexsymbols = array();         // MathIndex objects associated with this info element --> this represents the symbols
    public $medias = array();               // Media objects associated with this info element --> images, videos
    public $tables = array();               // Table objects associated with this info element

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '') {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_info';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (info element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        info elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \MathInfo
     */
    public function loadFromXml($DomElement, $position = '') {
        $this->position = $position;

        //for now to just show text in title
        //@TODO need to have methods implemented to process math
        foreach ($DomElement->childNodes as $child) {
            if ($child->nodeType == XML_ELEMENT_NODE) {
                if ($child->tagName == "info.caption") {
                    $this->caption = $child->textContent;
                    break;
                }
            }
        }

        foreach ($this->processIndexAuthor($DomElement, $position) as $indexauthor) {
            $this->indexauthors[] = $indexauthor;
        }

        foreach ($this->processIndexGlossary($DomElement, $position) as $indexglossary) {
            $this->indexglossarys[] = $indexglossary;
        }

        foreach ($this->processIndexSymbols($DomElement, $position) as $indexsymbol) {
            $this->indexsymbols[] = $indexsymbol;
        }
        foreach ($this->processSubordinate($DomElement, $position) as $subordinate) {
            $this->subordinates[] = $subordinate;
        }

        foreach ($this->processMedia($DomElement, $position) as $media) {
            $this->medias[] = $media;
        }

        foreach ($this->processTable($DomElement, $position) as $table) {
            $this->tables[] = $table;
        }

        foreach ($this->processContent($DomElement, $position) as $content) {
            $this->info_content .= $content;
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of info element into
     * msm_info database table.  It calls saveInfoDb method for Media, MathIndex, Subordinate,
     * and Table classes.
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
        if (!empty($this->caption)) {
            $data->caption = $this->caption;
        }
        if (!empty($this->info_content)) {
            $infocontent = '';

            $contentparser = new DOMDocument();
            @$contentparser->loadXML($this->info_content, true);

            $contentNode = $contentparser->documentElement;

            foreach ($contentNode->childNodes as $child) {
                $infocontent .= $contentparser->saveXML($contentparser->importNode($child, true));
            }

            $this->info_content = "<div>$infocontent</div>";

            $data->info_content = $this->info_content;
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
                        $indexauthor = $this->subordinates[$indexauthorString[1]];
                        $indexauthor->saveIntoDb($indexauthor->position, $msmid, $this->compid);
                        $sibling_id = $indexauthor->compid;
                    } else {
                        $indexauthor = $this->subordinates[$indexauthorString[1]];
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

        // process the images in the content to have src attribute with proper
        // pathing to moodle file storage area with pluginfile.php script
        if (!empty($this->medias)) {
            $newdata = new stdClass();
            $newdata->id = $this->id;
            if (isset($this->caption)) {
                $newdata->caption = $this->caption;
            }

            $newdata->info_content = $this->processDbContent($this->info_content, $this);

            $DB->update_record($this->tablename, $newdata);
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the info element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from Subordinate/Media/Table
     * classes are also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                   ID of the current info element from msm_info
     * @param int $compid               ID of the current info element from msm_compositor
     * @return \MathInfo
     */
    function loadFromDb($id, $compid) {
        global $DB;

        $infoRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($infoRecord)) {
            if (empty($infoRecord->caption)) {
                $this->caption = null;
            } else {
                $this->caption = $infoRecord->caption;
            }
            $this->info_content = $infoRecord->info_content;
            $this->id = $infoRecord->id;
            $this->compid = $compid;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childElements as $child) {
            $childtable = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            switch ($childtable) {
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
     * displays the information using jQuery UI Dialog plugin to create a popup like window
     * that becomes triggered by mouseover event.
     * 
     * @return string
     */
    function displayhtml() {
        $content = '';

        if (($this->caption === null) || (strlen(trim($this->caption)) == 0)) {
            $content .= '<div id="dialog-' . $this->compid . '" class="dialogs">';
        } else {
            // removing HTML tags since new jquery UI dialog titles cannot have HTML tags
            $patterns = array();
            $replacements = array();
            $patterns[0] = "/<p.*?>/";
            $patterns[1] = "/<\/p>/";
            $patterns[2] = "/<div.*?>/";
            $patterns[3] = "/<\/div>/";
            $patterns[4] = "/<span.*?>/";
            $patterns[5] = "/<\/span>/";
            $replacements[0] = "";
            $replacements[1] = "";
            $replacements[2] = "";
            $replacements[3] = "";
            $replacements[4] = "";
            $replacements[5] = "";

            $caption = $this->displayContent($this, "<div>$this->caption</div>");
            $modifiedCaption = preg_replace($patterns, $replacements, $caption);

            $caption = htmlentities($modifiedCaption);

            $content .= "<div id='dialog-$this->compid' class='dialogs' title='$caption'>";
        }
        $content .= $this->displayContent($this, $this->info_content);

        $content .= "</div>";

        return $content;
    }

}

?>
