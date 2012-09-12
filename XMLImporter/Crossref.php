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
 * Description of Crossref
 *
 * @author User
 */
class Crossref extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
    }

    function loadFromXml($DomElement, $position = '')
    {
        global $DB;

        $this->position = $position;

        $this->comments = array();
        $this->defs = array();
        $this->theorems = array();
        $this->packs = array();
        $this->subunits = array();
        $this->compositions = array();
        $this->infos = array();

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                $name = $child->tagName;
                $parser = new DOMDocument();

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

                                $comments = $element->getElementsByTagName('comment');

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

                    case('exercise.pack.ref'):
                        $exercisePackID = $child->getAttribute('exercisePackID');

                        if (!empty($exercisePackID))
                        {
                            $filepath = $this->findFile($exercisePackID, dirname($this->xmlpath), 'exercisepack');
                            @$parser->load($filepath);

                            $element = $parser->documentElement;

                            if (!empty($element))
                            {
                                $position = $position + 1;
                                $exercise = new Pack(dirname($filepath));
                                $exercise->loadFromXml($element, $position);
                                $this->packs[] = $exercise;
                            }
                        }
                        break;

                    case('example.pack.ref'):
                        $examplepackID = $child->getAttribute('examplePackID');

                        if (!empty($examplepackID))
                        {
                            $filepath = $this->findFile($examplepackID, dirname($this->xmlpath), 'examplepack');
                            @$parser->load($filepath);

                            $element = $parser->documentElement;

                            if (!empty($element))
                            {
                                $position = $position + 1;
                                $example = new Pack(dirname($filepath));
                                $example->loadFromXml($element, $position);
                                $this->packs[] = $example;
                            }
                        }
                        break;

                    case('unit.ref'):
                        $unitID = $child->getAttribute('unitId');

                        if (!empty($unitID))
                        {
                            $filepath = $this->findFile($unitID, dirname($this->xmlpath), 'unit');

                            if (!empty($filepath))
                            {
                                @$parser->load($filepath);

                                // may need to change this code to load the entire file
                                // containing the specified comment
                                $element = $parser->documentElement;

                                if (!empty($element))
                                {
                                    $position = $position + 1;
                                    $unit = new Unit(dirname($filepath) . '/');
                                    $unit->loadFromXml($element, $position);
                                    $this->subunits[] = $unit;
                                }
                            }
                            else
                            {
                                $filepath = $this->findFile($unitID, dirname($this->xmlpath), 'subunit');

                                if (!empty($filepath))
                                {

                                    @$parser->load($filepath);

                                    // may need to change this code to load the entire file
                                    // containing the specified comment
                                    $element = $parser->documentElement;

                                    $subunits = $element->getElementsByTagName('unit');

                                    foreach ($subunits as $sub)
                                    {
                                        $subID = $sub->getAttribute('unitid');

                                        if ($subID == $unitID)
                                        {
                                            $position++;
                                            $unit = new Unit(dirname($filepath) . '/');
                                            $unit->loadFromXml($sub, $position);
                                            $this->subunits[] = $unit;
                                        }
                                    }
                                }
                            }
                        }
                        break;
//                                case('composition.ref'):
//                                    $compID = $grandchild->getAttribute('unitId');
//
//                                    if (!empty($compID))
//                                    {
//                                        $IDinDB = $DB->get_record('msm_unit', array('string_id' => $compID));
//
//                                        if (!empty($IDinDB))
//                                        {
//                                            $filepath = $this->findFile($compID, dirname($this->xmlpath));
//                                            $parser->load($filepath);
//
//                                            // may need to change this code to load the entire file
//                                            // containing the specified comment
//                                           $element = $parser->documentElement;
//
//                                            if (!empty($element))
//                                            {
//                                                $position = $position + 1;
//                                                $comp = new Compositior(dirname($filepath));
//                                                $comp->loadFromXml($element, $position);
//                                                $this->compositions[] = $comp;
//                                            }
//                                        }
//                                        else
//                                        {
//                                            $this->companion[] = $compID;
//                                        }
//                                    }
//                                     break;

                    case('info'):
                        $position = $position + 1;
                        $info = new MathInfo($this->xmlpath);
                        $info->loadFromXml($child, $position);
                        $this->infos[] = $info;
                        break;
                }
            }
        }
    }

    function saveIntoDb($position, $parentid = '', $siblingid = '')
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

        if (!empty($this->compositions))
        {
            foreach ($this->compositions as $key => $composition)
            {
                $elementPositions['composition' . '-' . $key] = $composition->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(info.\d+)$/", $element) ? true : false):
                    $infoString = split('-', $element);
                    if (empty($sibling_id))
                    {
                        $info = $this->infos[$infoString[1]];
                        $info->saveIntoDb($info->position, $parentid);
                        $sibling_id = $info->compid;
                    }
                    else
                    {
                        $info = $this->infos[$infoString[1]];
                        $info->saveIntoDb($info->position, $parentid, $sibling_id);
                        $sibling_id = $info->compid;
                    }
                    break;

                case(preg_match("/^(pack.\d+)$/", $element) ? true : false):
                    $packString = split('-', $element);
                    $packRecord = $this->checkForRecord($this->packs[$packString[1]]);

                    if (empty($packRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $pack = $this->packs[$packString[1]];
                            $pack->saveIntoDb($pack->position, $parentid);
                            $sibling_id = $pack->compid;
                        }
                        else
                        {
                            $pack = $this->packs[$packString[1]];
                            $pack->saveIntoDb($pack->position, $parentid, $sibling_id);
                            $sibling_id = $pack->compid;
                        }
                    }
                    else
                    {
                        $packID = $packRecord->id;
                        $packtableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_packs'))->id;

                        $packCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $packID, 'table_id' => $packtableID));
                        $packCompID = $this->insertToCompositor($packID, 'msm_packs', $parentid, $sibling_id);
                        $sibling_id = $packCompID;

                        foreach ($packCompRecords as $packCompRecord)
                        {
                            $this->grabSubunitChilds($packCompRecord, $packCompID);
                        }
                    }
                    break;

                case(preg_match("/^(comment.\d+)$/", $element) ? true : false):
                    $commentString = split('-', $element);
                    if (!empty($this->comments[$commentString[1]]->string_id))
                    {
                        $commentRecord = $this->checkForRecord($this->comments[$commentString[1]]);
                    }
                    else
                    {
                        $commentRecord = $this->checkForRecord($this->comments[$commentString[1]], 'caption');
                    }

                    if (empty($commentRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $comment = $this->comments[$commentString[1]];
                            $comment->saveIntoDb($comment->position, $parentid);
                            $sibling_id = $comment->compid;
                        }
                        else
                        {
                            $comment = $this->comments[$commentString[1]];
                            $comment->saveIntoDb($comment->position, $parentid, $sibling_id);
                            $sibling_id = $comment->compid;
                        }
                    }
                    else
                    {
                        $commentID = $commentRecord->id;
                        $commenttableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_comment'))->id;

                        $commentCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $commentID, 'table_id' => $commenttableID));
                        $commentCompID = $this->insertToCompositor($commentID, 'msm_comment', $parentid, $sibling_id);
                        $sibling_id = $commentCompID;

                        foreach ($commentCompRecords as $commentCompRecord)
                        {
                            $this->grabSubunitChilds($commentCompRecord, $commentCompID);
                        }
                    }
                    break;

                case(preg_match("/^(def.\d+)$/", $element) ? true : false):
                    $defString = split('-', $element);
                    if (!empty($this->defs[$defString[1]]->string_id))
                    {
                        $defRecord = $this->checkForRecord($this->defs[$defString[1]]);
                    }
                    else
                    {
                        $defRecord = $this->checkForRecord($this->defs[$defString[1]], 'caption');
                    }

                    if (empty($defRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $def = $this->defs[$defString[1]];
                            $def->saveIntoDb($def->position, $parentid);
                            $sibling_id = $def->compid;
                        }
                        else
                        {
                            $def = $this->defs[$defString[1]];
                            $def->saveIntoDb($def->position, $parentid, $sibling_id);
                            $sibling_id = $def->compid;
                        }
                    }
                    else
                    {
                        $defID = $defRecord->id;
                        $deftableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_def'))->id;

                        $defCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $defID, 'table_id' => $deftableID));
                        $defCompID = $this->insertToCompositor($defID, 'msm_def', $parentid, $sibling_id);
                        $sibling_id = $defCompID;

                        foreach ($defCompRecords as $defCompRecord)
                        {
                            $this->grabSubunitChilds($defCompRecord, $defCompID);
                        }
                    }
                    break;

                case(preg_match("/^(theorem.\d+)$/", $element) ? true : false):
                    $theoremString = split('-', $element);
                    $theoremRecord = $this->checkForRecord($this->theorems[$theoremString[1]]);
                    
                    if (empty($theoremRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $theorem = $this->theorems[$theoremString[1]];
                            $theorem->saveIntoDb($theorem->position, $parentid);
                            $sibling_id = $theorem->compid;
                        }
                        else
                        {
                            $theorem = $this->theorems[$theoremString[1]];
                            $theorem->saveIntoDb($theorem->position, $parentid, $sibling_id);
                            $sibling_id = $theorem->compid;
                        }
                    }
                    else
                    {
                        $theoremID = $theoremRecord->id;
                        $theoremtableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_theorem'))->id;

                        $theoremCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $theoremID, 'table_id' => $theoremtableID));
                        $theoremCompID = $this->insertToCompositor($theoremID, 'msm_theorem', $parentid, $sibling_id);
                        $sibling_id = $theoremCompID;

                        foreach ($theoremCompRecords as $theoremCompRecord)
                        {
                            $this->grabSubunitChilds($theoremCompRecord, $theoremCompID, true);
                        }
                    }
                    break;

                case(preg_match("/^(subunit.\d+)$/", $element) ? true : false):
                    $subunitString = split('-', $element);
                    $subunitRecord = $this->checkForRecord($this->subunits[$subunitString[1]]);

                    if (empty($subunitRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $subunit = $this->subunits[$subunitString[1]];
                            $subunit->saveIntoDb($subunit->position, $parentid);
                            $sibling_id = $subunit->compid;
                        }
                        else
                        {
                            $subunit = $this->subunits[$subunitString[1]];
                            $subunit->saveIntoDb($subunit->position, $parentid, $sibling_id);
                            $sibling_id = $subunit->compid;
                        }
                    }
                    else
                    {
                        $subunitID = $subunitRecord->id;
                        $unittableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_unit'))->id;

                        $subunitCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $subunitID, 'table_id' => $unittableID));
                        $subunitCompID = $this->insertToCompositor($subunitID, 'msm_unit', $parentid, $sibling_id);
                        $sibling_id = $subunitCompID;

                        foreach ($subunitCompRecords as $unitCompRecord)
                        {
                            $this->grabSubunitChilds($unitCompRecord, $subunitCompID);
                        }
                    }
                    break;

//                case(preg_match("/^(composition.\d+)$/", $element) ? true : false):
//                    $compositionString = split('-', $element);
//
//                    if (is_object($this->compositions[$compositionString[1]]))
//                    {
//                        $compositionID = $this->checkForRecord($this->compositions[$compositionString[1]])->id;
//                    }
//                    else
//                    {
//                        $composittioninfo = explode('/', $this->compositions[$compositionString[1]]);
//                        $compositionID = $composittioninfo[1];
//                    }
//
//                    if (empty($compositionID))
//                    {
//                        if (empty($sibling_id))
//                        {
//                            $composition = $this->compositions[$compositionString[1]];
//                            $composition->saveIntoDb($composition->position, $parentid);
//                            $sibling_id = $composition->compid;
//                        }
//                        else
//                        {
//                            $composition = $this->compositions[$compositionString[1]];
//                            $composition->saveIntoDb($composition->position, $parentid, $sibling_id);
//                            $sibling_id = $composition->compid;
//                        }
//                    }
//                    else
//                    {
//                        $sibling_id = $this->insertToCompositor($compositionID, 'msm_compositor', $parentid, $sibling_id);
//                    }
//                    break;
            }
        }
    }

}

?>
