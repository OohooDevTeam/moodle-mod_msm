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
 * This class represents al the companion XML elements in the legacy document
 * (ie. files in the newXML) and the newly formed XML exported by the editor system
 * and it is called by ImgArea/MathCell/Subordinate classes.
 * Companion class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Companion extends Element
{

    public $position;                       // integer that keeps track of order of elements
    public $comments = array();             // MathComment objects associated with current companion element as reference materials
    public $defs = array();                 // Definition objects associated with current companion element as reference materials
    public $theorems = array();             // Theorem objects associated with current companion element as reference materials
    public $packs = array();                // Pack objects associated with current companion element as reference materials
    public $subunits = array();             // Unit objects associated with current companion element as reference materials
    public $infos = array();                // MathInfo objects associated with current companion element

    /**
     * constructor for this class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (companion element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        companion element
     * @param int $position                 integer that keeps track of order if elements
     * @return \Companion
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                $name = $child->tagName;
                $parser = new DOMDocument();

                // companion elements can have reference materials as child elements
                switch ($name)
                {
                    case('comment.ref'):
                        $commentrefID = $child->getAttribute('commentID');

                        if (!empty($commentrefID))
                        {
                            $filepath = $this->findFile($commentrefID, dirname($this->xmlpath), 'comment');

                            if (!empty($filepath))
                            {
                                @$parser->load($filepath);

                                $element = $parser->documentElement;

                                $comments = $parser->getElementsByTagName('comment');

                                foreach ($comments as $c)
                                {
                                    $cID = $d->getAttribute('id');
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
                                $defs = $parser->getElementsByTagName('def');

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
                                    $this->packs[] = $showme;
                                }
                            }
                        }

                        break;

                    case('quiz.pack.ref'):
                        $quizpackID = $child->getAttribute('quizPackID');

                        if (!empty($quizpackID))
                        {
                            $filepath = $this->findFile($quizpackID, dirname($this->xmlpath), 'quizpack');
                            @$parser->load($filepath);

                            $element = $parser->documentElement;

                            if (!empty($element))
                            {
                                $position = $position + 1;
                                $quiz = new Pack(dirname($filepath));
                                $quiz->loadFromXml($element, $position);
                                $this->packs[] = $quiz;
                            }
                        }
                        break;

                    case('unit.ref'):
                        $unitID = $child->getAttribute('unitId');

                        if (!empty($unitID))
                        {
                            $filepath = $this->findFile($unitID, dirname($this->xmlpath), 'unit');
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
     * This method saves the extracted information from the XML files of companion element and its associated child elements into
     * their respective database tables.  It calls saveInfoDb method for MathInfo, Defintion, Theorem, Comment, Unit and Pack classes.
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

        $elementPositions = array();
        $sibling_id = null;


        if (!empty($this->infos))
        {
            foreach ($this->infos as $key => $info)
            {
                $elementPositions['info' . '-' . $key] = $info->position;
            }
        }

        if (!empty($this->packs))
        {
            foreach ($this->packs as $key => $pack)
            {
                $elementPositions['pack' . '-' . $key] = $pack->position;
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
                        $info->saveIntoDb($info->position, $msmid, $parentid);
                        $sibling_id = $info->compid;
                    }
                    else
                    {
                        $info = $this->infos[$infoString[1]];
                        $info->saveIntoDb($info->position, $msmid, $parentid, $sibling_id);
                        $sibling_id = $info->compid;
                    }
                    break;

                case(preg_match("/^(pack.\d+)$/", $element) ? true : false):
                    $packString = explode('-', $element);
                    $packRecord = $this->checkForRecord($msmid, $this->packs[$packString[1]]);
                    if (empty($packRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $pack = $this->packs[$packString[1]];
                            $pack->saveIntoDb($pack->position, $msmid, $parentid);
                            $sibling_id = $pack->compid;
                        }
                        else
                        {
                            $pack = $this->packs[$packString[1]];
                            $pack->saveIntoDb($pack->position, $msmid, $parentid, $sibling_id);
                            $sibling_id = $pack->compid;
                        }
                    }
                    else
                    {
                        $packID = $packRecord->id;
                        $packtableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_packs'))->id;

                        $packCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $packID, 'table_id' => $packtableID));
                        $packCompID = $this->insertToCompositor($packID, 'msm_packs', $msmid, $parentid, $sibling_id);
                        $sibling_id = $packCompID;

                        foreach ($packCompRecords as $packCompRecord)
                        {
                            $this->grabSubunitChilds($packCompRecord, $packCompID, $msmid);
                        }
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

                    if (empty($commentRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $comment = $this->comments[$commentString[1]];
                            $comment->saveIntoDb($comment->position, $msmid, $parentid);
                            $sibling_id = $comment->compid;
                        }
                        else
                        {
                            $comment = $this->comments[$commentString[1]];
                            $comment->saveIntoDb($comment->position, $msmid, $parentid, $sibling_id);
                            $sibling_id = $comment->compid;
                        }
                    }
                    else
                    {
                        $commentID = $commentRecord->id;
                        $commenttableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_comment'))->id;

                        $commentCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $commentID, 'table_id' => $commenttableID));
                        $commentCompID = $this->insertToCompositor($commentID, 'msm_comment', $msmid, $parentid, $sibling_id);
                        $sibling_id = $commentCompID;

                        foreach ($commentCompRecords as $commentCompRecord)
                        {
                            $this->grabSubunitChilds($commentCompRecord, $commentCompID, $msmid);
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
                            $def->saveIntoDb($def->position, $msmid, $parentid);
                            $sibling_id = $def->compid;
                        }
                        else
                        {
                            $def = $this->defs[$defString[1]];
                            $def->saveIntoDb($def->position, $msmid, $parentid, $sibling_id);
                            $sibling_id = $def->compid;
                        }
                    }
                    else
                    {
                        $defID = $defRecord->id;
                        $deftableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_def'))->id;

                        $defCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $defID, 'table_id' => $deftableID));
                        $defCompID = $this->insertToCompositor($defID, 'msm_def', $msmid, $parentid, $sibling_id);
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
                            $theorem->saveIntoDb($theorem->position, $msmid, $parentid);
                            $sibling_id = $theorem->compid;
                        }
                        else
                        {
                            $theorem = $this->theorems[$theoremString[1]];
                            $theorem->saveIntoDb($theorem->position, $msmid, $parentid, $sibling_id);
                            $sibling_id = $theorem->compid;
                        }
                    }
                    else
                    {
                        $theoremID = $theoremRecord->id;
                        $theoremtableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_theorem'))->id;

                        $theoremCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $theoremID, 'table_id' => $theoremtableID));
                        $theoremCompID = $this->insertToCompositor($theoremID, 'msm_theorem', $msmid, $parentid, $sibling_id);
                        $sibling_id = $theoremCompID;

                        foreach ($theoremCompRecords as $theoremCompRecord)
                        {
                            $this->grabSubunitChilds($theoremCompRecord, $theoremCompID, $msmid, true);
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
                            $subunit->saveIntoDb($subunit->position, $msmid, $parentid);
                            $sibling_id = $subunit->compid;
                        }
                        else
                        {
                            $subunit = $this->subunits[$subunitString[1]];
                            $subunit->saveIntoDb($subunit->position, $msmid, $parentid, $sibling_id);
                            $sibling_id = $subunit->compid;
                        }
                    }
                    else
                    {
                        $subunitID = $subunitRecord->id;
                        $unittableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_unit'))->id;

                        $subunitCompRecords = $DB->get_records('msm_compositor', array('msm_id' => $msmid, 'unit_id' => $subunitID, 'table_id' => $unittableID));
                        $subunitCompID = $this->insertToCompositor($subunitID, 'msm_unit', $msmid, $parentid, $sibling_id);
                        $sibling_id = $subunitCompID;

                        foreach ($subunitCompRecords as $unitCompRecord)
                        {
                            if ($this->grabSubunitChilds($unitCompRecord, $subunitCompID, $msmid))
                            {
                                break;
                            }
                        }
                    }
                    break;
            }
        }
    }

}

?>
