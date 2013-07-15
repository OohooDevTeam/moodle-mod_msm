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
 * This class represents all the associate XML elements in the legacy document
 * (ie. files in the newXML) and the newly formed XML exported by the editor system
 * and it is called by Definition/Theorem/Comment classes.
 * Associate class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Associate extends Element
{

    public $id;                             // database ID of current associate element in msm_associate
    public $compid;                         // database ID of current associate element in msm_compositor
    public $position;                       // integer that keeps track of order of elements
    public $description;                    // description associated with this element
    public $infos = array();                // MathInfo objects that are linked to this associate element as a reference material
    public $comments = array();             // MathComment objects that are linked to this associate element as a reference material
    public $subunits = array();             // Unit objects that are linked to this associate element as a reference material
    public $defs = array();                 // Definition objects that are linked to this associate element as a reference material
    public $theorems = array();             // Theorem objects that are linked to this associate element as a reference material
    public $refs = array();                 // Pack objects that are linked to this associate element as a reference material

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */
    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_associate';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (associate element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        associate elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \Associate
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->description = $DomElement->getAttribute('type');

        foreach ($DomElement->childNodes as $key => $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                $name = $child->tagName;
                $parser = new DOMDocument();

                switch ($name)
                {
                    // asociates can have reference materials as child elements
                    case('comment.ref'):
                        $commentrefID = $child->getAttribute('commentID');

                        if (!empty($commentrefID))
                        {
                            $filepath = $this->findFile($commentrefID, dirname($this->xmlpath), 'comment');

                            if (!empty($filepath))
                            {
                                @$parser->load($filepath);

                                $element = $parser->documentElement;

                                if ($element->tagName == 'comment')
                                {
                                    $cID = $element->getAttribute('id');
                                    if ($cID == $commentrefID)
                                    {
                                        $position = $position + 1;
                                        $comment = new MathComment(dirname($filepath));
                                        $comment->loadFromXml($element, $position);
                                        $this->comments[] = $comment;
                                    }
                                }
                                else
                                {
                                    $comments = $element->getElementsByTagName('comment');

                                    foreach ($comments as $c)
                                    {
                                        $cID = $c->getAttribute('id');
                                        if ($cID == $commentrefID)
                                        {
                                            $position = $position + 1;
                                            $comment = new MathComment(dirname($filepath));
                                            $comment->loadFromXml($c, $position);
                                            $this->comments[] = $comment;
                                        }
                                    }
                                }
                            }
                        }
                        break;

                    case('definition.ref'):
                        $definitionrefID = $child->getAttribute('definitionID');

                        if (!empty($definitionrefID))
                        {
                            $filepath = $this->findFile($definitionrefID, dirname($this->xmlpath), 'def');

                            if (!empty($filepath))
                            {
                                @$parser->load($filepath);

                                $element = $parser->documentElement;

                                if ($element->tagName == 'def')
                                {
                                    $dID = $element->getAttribute('id');
                                    if ($dID == $definitionrefID)
                                    {
                                        $position = $position + 1;
                                        $def = new Definition(dirname($filepath));
                                        $def->loadFromXml($element, $position);
                                        $this->defs[] = $def;
                                    }
                                }
                                else
                                {
                                    $defs = $element->getElementsByTagName('def');

                                    foreach ($defs as $d)
                                    {
                                        $dID = $d->getAttribute('id');
                                        if ($dID == $definitionrefID)
                                        {
                                            $position = $position + 1;
                                            $def = new Definition(dirname($filepath));
                                            $def->loadFromXml($d, $position);
                                            $this->defs[] = $def;
                                        }
                                    }
                                }
                            }
                        }
                        break;

                    case('showme.pack.ref'):
                        $showmepackrefID = $child->getAttribute('showmePackID');

                        if (!empty($showmepackrefID))
                        {
                            $filepath = $this->findFile($showmepackrefID, dirname($this->xmlpath), 'showmepack');

                            if (!empty($filepath))
                            {
                                @$parser->load($filepath);

                                $element = $parser->documentElement;

                                if (!empty($element))
                                {
                                    $position = $position + 1;
                                    $showme = new Pack(dirname($filepath));
                                    $showme->loadFromXml($element, $position);
                                    $this->refs[] = $showme;
                                }
                            }
                        }

                        break;

                    case('quiz.pack.ref'):
                        $quizpackID = $child->getAttribute('quizPackID');

                        if (!empty($quizpackID))
                        {
                            $filepath = $this->findFile($quizpackID, dirname($this->xmlpath), 'quizpack');
                            if (!empty($filepath))
                            {
                                @$parser->load($filepath);

                                $element = $parser->documentElement;

                                if (!empty($element))
                                {
                                    $position = $position + 1;
                                    $quiz = new Pack(dirname($filepath));
                                    $quiz->loadFromXml($element, $position);
                                    $this->refs[] = $quiz;
                                }
                            }
                        }
                        break;

                    case('theorem.ref'):
                        $theoremrefID = $child->getAttribute('theoremID');

                        if (!empty($theoremrefID))
                        {
                            $filepath = $this->findFile($theoremrefID, dirname($this->xmlpath), 'theorem');

                            if (!empty($filepath))
                            {
                                @$parser->load($filepath);

                                $element = $parser->documentElement;

                                if (!empty($element))
                                {
                                    $position = $position + 1;
                                    $theorem = new Theorem(dirname($filepath));
                                    $theorem->loadFromXml($element, $position, 'true');
                                    $this->theorems[] = $theorem;
                                }
                            }
                        }
                        break;

                    case('unit.ref'):
                        $untiID = $child->getAttribute('unitId');

                        if (!empty($untiID))
                        {
                            $filepath = $this->findFile($untiID, dirname($this->xmlpath), 'unit');
                            @$parser->load($filepath);

                            // may need to change this code to load the entire file
                            // containing the specified comment
                            $element = $parser->documentElement;

                            if (!empty($element))
                            {
                                $position = $position + 1;
                                $unit = new Unit(dirname($filepath));
                                $unit->loadFromXml($element, $position);
                                $this->subunits[] = $unit;
                            }
                        }
                        break;

                    case('info'):
                        $position = $position + 1;
                        $info = new MathInfo($this->xmlpath);
                        $info->loadFromXml($child, $position);
                        $this->infos[] = $info;
                        break;
                }
            }
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of associate element into
     * msm_associate database table.  It calls saveInfoDb method for MathInfo, Defintion, Theorem, Comment, Unit and Pack classes.
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
        $data->description = $this->description;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $elementPositions = array();
        $sibling_id = null;


        if (!empty($this->infos))
        {
            foreach ($this->infos as $key => $info)
            {
                $elementPositions['info' . '-' . $key] = $info->position;
            }
        }

        if (!empty($this->refs))
        {
            foreach ($this->refs as $key => $ref)
            {
                $elementPositions['ref' . '-' . $key] = $ref->position;
            }
        }

        if (!empty($this->comments))
        {
            foreach ($this->comments as $key => $comment)
            {
                $elementPositions['comment' . '-' . $key] = $comment->position;
            }
        }

        if (!empty($this->defs))
        {
            foreach ($this->defs as $key => $def)
            {
                $elementPositions['def' . '-' . $key] = $def->position;
            }
        }

        if (!empty($this->theorems))
        {
            foreach ($this->theorems as $key => $theorem)
            {
                $elementPositions['theorem' . '-' . $key] = $theorem->position;
            }
        }
        if (!empty($this->subunits))
        {
            foreach ($this->subunits as $key => $subunit)
            {
                $elementPositions['subunit' . '-' . $key] = $subunit->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(info.\d+)$/", $element) ? true : false):
                    $infoString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $info = $this->infos[$infoString[1]];
                        $info->saveIntoDb($info->position, $msmid, $this->compid);
                        $sibling_id = $info->compid;
                    }
                    else
                    {
                        $info = $this->infos[$infoString[1]];
                        $info->saveIntoDb($info->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $info->compid;
                    }
                    break;

                case(preg_match("/^(comment.\d+)$/", $element) ? true : false):
                    $commentString = explode('-', $element);
                    $commentRecord = null;
                    if (!empty($this->comments[$commentString[1]]->string_id))
                    {
                        $commentRecord = $this->checkForRecord($msmid, $this->comments[$commentString[1]]);
                    }
                    else
                    {
                        $commentRecord = $this->checkForRecord($msmid, $this->comments[$commentString[1]], 'caption');
                    }

                    if (empty($commentID))
                    {
                        if (empty($sibling_id))
                        {
                            $comment = $this->comments[$commentString[1]];
                            $comment->saveIntoDb($comment->position, $msmid, $this->compid);
                            $sibling_id = $comment->compid;
                        }
                        else
                        {
                            $comment = $this->comments[$commentString[1]];
                            $comment->saveIntoDb($comment->position, $msmid, $this->compid, $sibling_id);
                            $sibling_id = $comment->compid;
                        }
                    }
                    else
                    {
                        $commentID = $commentRecord->id;
                        $commenttableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_comment'))->id;

                        $commentCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $commentID, 'table_id' => $commenttableID));
                        $commentCompID = $this->insertToCompositor($commentID, 'msm_comment', $msmid, $this->compid, $sibling_id);
                        $sibling_id = $commentCompID;

                        foreach ($commentCompRecords as $commentCompRecord)
                        {
                            $this->grabSubunitChilds($commentCompRecord, $commentCompID, $msmid);
                        }
                    }
                    break;

                case(preg_match("/^(subunit.\d+)$/", $element) ? true : false):
                    $subunitString = explode('-', $element);
                    $subunitRecord = $this->checkForRecord($msmid, $this->subunits[$subunitString[1]]);

                    if (empty($subunitRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $subunit = $this->subunits[$subunitString[1]];
                            $subunit->saveIntoDb($subunit->position, $msmid, $this->compid);
                            $sibling_id = $subunit->compid;
                        }
                        else
                        {
                            $subunit = $this->subunits[$subunitString[1]];
                            $subunit->saveIntoDb($subunit->position, $msmid, $this->compid, $sibling_id);
                            $sibling_id = $subunit->compid;
                        }
                    }
                    else
                    {
                        $subunitID = $subunitRecord->id;
                        $unittableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_unit'))->id;

                        $subunitCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $subunitID, 'table_id' => $unittableID));
                        $subunitCompID = $this->insertToCompositor($subunitID, 'msm_unit', $msmid, $this->compid, $sibling_id);
                        $sibling_id = $subunitCompID;

                        foreach ($subunitCompRecords as $unitCompRecord)
                        {
                            $this->grabSubunitChilds($unitCompRecord, $subunitCompID, $msmid);
                        }
                    }
                    break;

                case(preg_match("/^(def.\d+)$/", $element) ? true : false):
                    $defString = explode('-', $element);
                    $defRecord = null;
                    if (!empty($this->defs[$defString[1]]->string_id))
                    {
                        $defRecord = $this->checkForRecord($msmid, $this->defs[$defString[1]]);
                    }
                    else
                    {
                        $defRecord = $this->checkForRecord($msmid, $this->defs[$defString[1]], 'caption');
                    }

                    if (empty($defRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $def = $this->defs[$defString[1]];
                            $def->saveIntoDb($def->position, $msmid, $this->compid);
                            $sibling_id = $def->compid;
                        }
                        else
                        {
                            $def = $this->defs[$defString[1]];
                            $def->saveIntoDb($def->position, $msmid, $this->compid, $sibling_id);
                            $sibling_id = $def->compid;
                        }
                    }
                    else
                    {
                        $defID = $defRecord->id;
                        $deftableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_def'))->id;

                        $defCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $defID, 'table_id' => $deftableID));
                        $defCompID = $this->insertToCompositor($defID, 'msm_def', $msmid, $this->compid, $sibling_id);
                        $sibling_id = $defCompID;

                        foreach ($defCompRecords as $defCompRecord)
                        {
                            $this->grabSubunitChilds($defCompRecord, $defCompID, $msmid);
                        }
                    }
                    break;

                case(preg_match("/^(theorem.\d+)$/", $element) ? true : false):
                    $theoremString = explode('-', $element);
                    $theoremRecord = $this->checkForRecord($msmid, $this->theorems[$theoremString[1]]);

                    if (empty($theoremRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $theorem = $this->theorems[$theoremString[1]];
                            $theorem->saveIntoDb($theorem->position, $msmid, $this->compid);
                            $sibling_id = $theorem->compid;
                        }
                        else
                        {
                            $theorem = $this->theorems[$theoremString[1]];
                            $theorem->saveIntoDb($theorem->position, $msmid, $this->compid, $sibling_id);
                            $sibling_id = $theorem->compid;
                        }
                    }
                    else
                    {
                        $theoremID = $theoremRecord->id;
                        $theoremtableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_theorem'))->id;

                        $theoremCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $theoremID, 'table_id' => $theoremtableID));
                        $theoremCompID = $this->insertToCompositor($theoremID, 'msm_theorem', $msmid, $this->compid, $sibling_id);
                        $sibling_id = $theoremCompID;

                        foreach ($theoremCompRecords as $theoremCompRecord)
                        {
                            $this->grabSubunitChilds($theoremCompRecord, $theoremCompID, $msmid);
                        }
                    }
                    break;

                case(preg_match("/^(ref.\d+)$/", $element) ? true : false):
                    $refString = explode('-', $element);
                    $refRecord = $this->checkForRecord($msmid, $this->refs[$refString[1]]);

                    if (empty($refRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $ref = $this->refs[$refString[1]];
                            $ref->saveIntoDb($ref->position, $msmid, $this->compid);
                            $sibling_id = $ref->compid;
                        }
                        else
                        {
                            $ref = $this->refs[$refString[1]];
                            $ref->saveIntoDb($ref->position, $msmid, $this->compid, $sibling_id);
                            $sibling_id = $ref->compid;
                        }
                    }
                    else
                    {
                        $refID = $refRecord->id;
                        $reftableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_packs'))->id;

                        $refCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $refID, 'table_id' => $reftableID));
                        $refCompID = $this->insertToCompositor($refID, 'msm_packs', $msmid, $this->compid, $sibling_id);
                        $sibling_id = $refCompID;

                        foreach ($refCompRecords as $refCompRecord)
                        {
                            $this->grabSubunitChilds($refCompRecord, $refCompID, $msmid);
                        }
                    }
                    break;
            }
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the associate element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from MathInfo, Defintion, Theorem, Comment, Unit and Pack
     * classes are also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                   ID of the current associate element from msm_associate
     * @param int $compid               ID of the current associate element from msm_compositor
     * @return \Associate
     */
    function loadFromDb($id, $compid)
    {
        global $DB;
        $associateCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $associateRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($associateRecord))
        {
            $this->compid = $compid;
            $this->description = $associateRecord->description;
        }

        $childElements = $DB->get_records('msm_compositor', array('msm_id' => $associateCompRecord->msm_id, 'parent_id' => $compid), 'prev_sibling_id');

        $this->infos = array();
        $this->childs = array();

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            switch ($childtablename)
            {
                case('msm_info'):
                    $info = new MathInfo();
                    $info->loadFromDb($child->unit_id, $child->id);
                    $this->infos[] = $info;
                    break;

                case('msm_theorem'):
                    $theorem = new Theorem();
                    $theorem->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $theorem;
                    break;

                case('msm_def'):
                    $def = new Definition();
                    $def->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $def;
                    break;

                case('msm_comment'):
                    $comment = new MathComment();
                    $comment->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $comment;
                    break;

                case('msm_unit'):
                    $unit = new Unit();
                    $unit->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $unit;
                    break;

                case('msm_packs'):
                    $pack = new Pack();
                    $pack->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $pack;
                    break;
            }
        }

        return $this;
    }

    /**
     * This method produces an HTML code to display the retrieved data from method above and
     * also calls the same method in MathInfo, Defintion, Theorem, Comment, Unit and Pack classes to
     * display the data from thoses classes.
     * 
     * @global moodle_database $DB
     * @return string
     */
    function displayhtml()
    {
        global $DB;

        $content = '';

        $associateParentID = $DB->get_record('msm_compositor', array('id' => $this->compid))->parent_id;

        $associateParenttable = $DB->get_record('msm_compositor', array('id' => $associateParentID))->table_id;

        $associateParentTablename = $DB->get_record('msm_table_collection', array('id' => $associateParenttable))->tablename;

        // Definition/Theorem and Comment linked to associates needs to be processed differently due to them having different views in display
        if ($associateParentTablename == 'msm_def')
        {
            if ($this->description != 'Quiz')
            {
                if (!empty($this->childs))
                {
                    $content .= "<li class='defminibutton' id='defminibutton-" . $this->infos[0]->compid . "' onmouseover='infoopen(" . $this->infos[0]->compid . ")'>";
                    $content .= "<span style='cursor:pointer'>";
                    $content .= $this->description;
                    $content .= "</span>";
                    $content .= "</li>";
                    $content .= "<div class='refcontent' id='refcontent-" . $this->infos[0]->compid . "' style='display:none;'>";
                    foreach ($this->childs as $child)
                    {
                        $content .= $child->displayhtml();
                    }
                    $content .= "</div>";
                }
                else
                {
                    $content .= "<li class='defminibutton' id='defminibutton-" . $this->infos[0]->compid . "' onmouseover='popup(" . $this->infos[0]->compid . ")'>";
                    $content .= "<span style='cursor:pointer'>";
                    $content .= $this->description;
                    $content .= "</span>";
                    $content .= "</li>";
                }

                $patterns = array();
                $replacements = array();
                $patterns[0] = "/<p.*?>/";
                $patterns[1] = "/<\/p>/";
                $patterns[2] = "/<span.*?>/";
                $patterns[3] = "/<\/span>/";
                $replacements[0] = "";
                $replacements[1] = "";
                $replacements[2] = "";
                $replacements[3] = "";

                $modifiedCaption = preg_replace($patterns, $replacements, $this->infos[0]->caption);
                $caption = htmlentities($modifiedCaption);

                $content .= '<div id="dialog-' . $this->infos[0]->compid . '" class="dialogs" title="' . $caption . '">';
                $content .= $this->displayContent($this->infos[0], $this->infos[0]->info_content);
                $content .= "</div>";
            }
        }

        if ($associateParentTablename == 'msm_theorem')
        {
            if ($this->description != 'Quiz')
            {
                if (!empty($this->childs))
                {
//                    $content .= "<li class='minibutton' id='minibutton-" . $this->infos[0]->compid . "' onmouseover='infoopen(" . $this->infos[0]->compid . ")' onclick='showRightpage(" . $this->infos[0]->compid . ")'>";
                    $content .= "<li class='minibutton' id='minibutton-" . $this->infos[0]->compid . "' onmouseover='infoopen(" . $this->infos[0]->compid . ")'>";
                    $content .= "<span style='cursor:pointer'>";
                    $content .= $this->description;
                    $content .= "</span>";
                    $content .= "</li>";
                    $content .= "<div class='refcontent' id='refcontent-" . $this->infos[0]->compid . "' style='display:none;'>";
                    foreach ($this->childs as $child)
                    {
                        $content .= $child->displayhtml();
                    }
                    $content .= "</div>";
                }
                else
                {
                    $content .= "<li class='minibutton' id='minibutton-" . $this->infos[0]->compid . "' onmouseover='popup(" . $this->infos[0]->compid . ")'>";
                    $content .= "<span style='cursor:pointer'>";
                    $content .= $this->description;
                    $content .= "</span>";
                    $content .= "</li>";
                }

                $patterns = array();
                $replacements = array();
                $patterns[0] = "/<p.*?>/";
                $patterns[1] = "/<\/p>/";
                $patterns[2] = "/<span.*?>/";
                $patterns[3] = "/<\/span>/";
                $replacements[0] = "";
                $replacements[1] = "";
                $replacements[2] = "";
                $replacements[3] = "";

                $modifiedCaption = preg_replace($patterns, $replacements, $this->infos[0]->caption);
                $caption = htmlentities($modifiedCaption);

                $content .= '<div id="dialog-' . $this->infos[0]->compid . '" class="dialogs" title="' . $caption . '">';
                $content .= $this->displayContent($this->infos[0], $this->infos[0]->info_content);
                $content .= "</div>";
            }
        }

        if ($associateParentTablename == 'msm_comment')
        {
            if (!empty($this->childs))
            {
//                $content .= "<li class='commentminibutton' id='commentminibutton-" . $this->infos[0]->compid . "' onmouseover='infoopen(" . $this->infos[0]->compid . ")' onclick='showRightpage(" . $this->infos[0]->compid . ")'>";
                $content .= "<li class='commentminibutton' id='commentminibutton-" . $this->infos[0]->compid . "' onmouseover='infoopen(" . $this->infos[0]->compid . ")'>";
                $content .= "<span style='cursor:pointer'>";
                $content .= $this->description;
                $content .= "</span>";
                $content .= "</li>";
                $content .= "<div class='refcontent' id='refcontent-" . $this->infos[0]->compid . "' style='display:none;'>";
                foreach ($this->childs as $child)
                {
                    $content .= $child->displayhtml();
                }
                $content .= "</div>";
            }
            else
            {
                $content .= "<li class='commentminibutton' id='commentminibutton-" . $this->infos[0]->compid . "' onmouseover='popup(" . $this->infos[0]->compid . ")'>";
                $content .= "<span style='cursor:pointer'>";
                $content .= $this->description;
                $content .= "</span>";
                $content .= "</li>";
            }

            $patterns = array();
            $replacements = array();
            $patterns[0] = "/<p.*?>/";
            $patterns[1] = "/<\/p>/";
            $patterns[2] = "/<span.*?>/";
            $patterns[3] = "/<\/span>/";
            $replacements[0] = "";
            $replacements[1] = "";
            $replacements[2] = "";
            $replacements[3] = "";

            $modifiedCaption = preg_replace($patterns, $replacements, $this->infos[0]->caption);
            $caption = htmlentities($modifiedCaption);

            $content .= '<div id="dialog-' . $this->infos[0]->compid . '" class="dialogs" title="' . $caption . '">';
            $content .= $this->displayContent($this->infos[0], $this->infos[0]->info_content);
            $content .= "</div>";
        }

        return $content;
    }

}

?>
