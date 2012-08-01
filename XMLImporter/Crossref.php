<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

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
                            $IDinDB = $DB->get_record('msm_comment', array('string_id' => $commentrefID));

                            if (empty($IDinDB))
                            {
                                $filepath = $this->findFile($commentrefID, dirname($this->xmlpath));
                                if (!empty($filepath))
                                {
                                    $parser->load($filepath);

                                    $element = $parser->documentElement;

                                    if (!empty($element))
                                    {
                                        $position = $position + 1;
                                        $comment = new MathComment(dirname($filepath));
                                        $comment->loadFromXml($element, $position);
                                        $this->comments[] = $comment;
                                    }
                                }
                            }
                            else
                            {
                                $position = $position + 1;
                                $this->comments[] = $commentrefID . '/' . $position;
                            }
                            //when db is set up, add code to check the db records first
                            // then if there are no records with specified ID, then..
                            // find the file with comment with specified ID
                        }
                        break;

                    case('definition.ref'):
                        $definitionrefID = $child->getAttribute('definitionID');

                        if (!empty($definitionrefID))
                        {
                            $IDinDB = $DB->get_record('msm_def', array('string_id' => $definitionrefID));

                            if (empty($IDinDB))
                            {
                                $filepath = $this->findFile($definitionrefID, dirname($this->xmlpath));

                                if (!empty($filepath))
                                {
                                    $parser->load($filepath);

                                    $element = $parser->documentElement;

                                    if (!empty($element))
                                    {
                                        $position = $position + 1;
                                        $def = new Definition(dirname($filepath));
                                        $def->loadFromXml($element, $position);
                                        $this->defs[] = $def;
                                    }
                                }
                            }
                            else
                            {
                                $position = $position + 1;
                                $this->defs[] = $definitionrefID . '/' . $position;
                            }
                        }
                        break;

                    case('theorem.ref'):
                        $theoremrefID = $child->getAttribute('theoremID');

                        if (!empty($theormerefID))
                        {
                            $IDinDB = $DB->get_record('msm_theorem', array('string_id' => $theormerefID));

                            if (empty($IDinDB))
                            {
                                $filepath = $this->findFile($theoremrefID, dirname($this->xmlpath));

                                if (!empty($filepath))
                                {
                                    $parser->load($filepath);

                                    $element = $parser->documentElement;

                                    if (!empty($element))
                                    {
                                        $position = $position + 1;
                                        $theorem = new Theorem(dirname($filepath));
                                        $theorem->loadFromXml($element, $position);
                                        $this->theorems[] = $theorem;
                                    }
                                }
                            }
                            else
                            {
                                $position = $position + 1;
                                $this->theorems[] = $theormerefID . '/' . $position;
                            }
                        }
                        break;

                    case('exercise.pack.ref'):
                        $exercisePackID = $child->getAttribute('exercisePackID');

                        if (!empty($exercisePackID))
                        {
                            $IDinDB = $DB->get_record('msm_packs', array('string_id' => $exercisePackID));

                            if (empty($IDinDB))
                            {
                                $filepath = $this->findFile($exercisePackID, dirname($this->xmlpath));
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
                            else
                            {
                                $position = $position + 1;
                                $this->packs[] = $exercisePackID . '/' . $position;
                            }
                        }
                        break;

                    case('example.pack.ref'):
                        $examplepackID = $child->getAttribute('examplePackID');

                        if (!empty($examplepackID))
                        {
                            $IDinDB = $DB->get_record('msm_packs', array('string_id' => $examplepackID));

                            if (empty($IDinDB))
                            {
                                $filepath = $this->findFile($examplepackID, dirname($this->xmlpath));
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
                            else
                            {
                                $position = $position + 1;
                                $this->packs[] = $examplepackID . '/' . $position;
                            }
                        }
                        break;

                    case('unit.ref'):
                        $untiID = $child->getAttribute('unitId');

                        if (!empty($untiID))
                        {
                            $IDinDB = $DB->get_record('msm_unit', array('string_id' => $untiID));

                            if (!empty($IDinDB))
                            {
                                $filepath = $this->findFile($untiID, dirname($this->xmlpath));
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
                            else
                            {
                                $position = $position + 1;
                                $this->subunits[] = $untiID . '/' . $position;
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
                if (is_object($pack))
                {
                    $elementPositions['pack' . '-' . $key] = $pack->position;
                }
                else
                {
                    $packinfo = explode('/', $pack);
                    $elementPositions['pack' . '-' . $key] = $packinfo[1];
                }
            }
        }

        if (!empty($this->comments))
        {
            foreach ($this->comments as $key => $comment)
            {
                if (is_object($comment))
                {
                    $elementPositions['comment' . '-' . $key] = $comment->position;
                }
                else
                {
                    $commentinfo = explode('/', $comment);
                    $elementPositions['comment' . '-' . $key] = $commentinfo[1];
                }
            }
        }

        if (!empty($this->defs))
        {
            foreach ($this->defs as $key => $def)
            {
                if (is_object($def))
                {
                    $elementPositions['def' . '-' . $key] = $def->position;
                }
                else
                {
                    $definfo = explode('/', $def);
                    $elementPositions['def' . '-' . $key] = $definfo[1];
                }
            }
        }

        if (!empty($this->theorems))
        {
            foreach ($this->theorems as $key => $theorem)
            {
                if (is_object($theorem))
                {
                    $elementPositions['theorem' . '-' . $key] = $theorem->position;
                }
                else
                {
                    $theoreminfo = explode('/', $theorem);
                    $elementPositions['theorem' . '-' . $key] = $theoreminfo[1];
                }
            }
        }
        if (!empty($this->subunits))
        {
            foreach ($this->subunits as $key => $subunit)
            {
                if (is_object($subunit))
                {
                    $elementPositions['subunit' . '-' . $key] = $subunit->position;
                }
                else
                {
                    $subunitinfo = explode('/', $subunit);
                    $elementPositions['subunit' . '-' . $key] = $subunitinfo[1];
                }
            }
        }

        if (!empty($this->compositions))
        {
            foreach ($this->compositions as $key => $composition)
            {
                if (is_object($composition))
                {
                    $elementPositions['composition' . '-' . $key] = $composition->position;
                }
                else
                {
                    $compositioninfo = explode('/', $composition);
                    $elementPositions['composition' . '-' . $key] = $compositioninfo[1];
                }
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

                    if (is_object($this->packs[$packString[1]]))
                    {
                        $packRecord = $this->checkForRecord($this->packs[$packString[1]]);
                        if (!empty($packRecord))
                        {
                            print_object($packRecord);
                            $packID = $packRecord->id;
                        }
                    }
                    else
                    {
                        $packinfo = explode('/', $this->packs[$packString[1]]);
                        $packID = $packinfo[1];
                    }

                    if (empty($packID))
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
                        $sibling_id = $this->insertToCompositor($packID, 'msm_packs', $parentid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(comment.\d+)$/", $element) ? true : false):
                    $commentString = split('-', $element);

                    if (is_object($this->comments[$commentString[1]]))
                    {
                        $commentID = $this->checkForRecord($this->comments[$commentString[1]])->id;
                    }
                    else
                    {
                        $commentinfo = explode('/', $this->comments[$commentString[1]]);
                        $commentID = $commentinfo[1];
                    }

                    if (empty($commentID))
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
                        $sibling_id = $this->insertToCompositor($commentID, 'msm_comment', $parentid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(def.\d+)$/", $element) ? true : false):
                    $defString = split('-', $element);

                    if (is_object($this->defs[$defString[1]]))
                    {
                        $defID = $this->checkForRecord($this->defs[$defString[1]])->id;
                    }
                    else
                    {
                        $definfo = explode('/', $this->defs[$defString[1]]);
                        $defID = $definfo[1];
                    }

                    if (empty($defID))
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
                        $sibling_id = $this->insertToCompositor($defID, 'msm_def', $parentid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(theorem.\d+)$/", $element) ? true : false):
                    $theoremString = split('-', $element);

                    if (is_object($this->theorems[$theoremString[1]]))
                    {
                        $theoremID = $this->checkForRecord($this->theorems[$theoremString[1]])->id;
                    }
                    else
                    {
                        $theoreminfo = explode('/', $this->theorems[$theoremString[1]]);
                        $theoremID = $theoreminfo[1];
                    }

                    if (empty($theoremID))
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
                        $sibling_id = $this->insertToCompositor($theoremID, 'msm_theorem', $parentid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(subunit.\d+)$/", $element) ? true : false):
                    $subunitString = split('-', $element);

                    if (is_object($this->subunits[$subunitString[1]]))
                    {
                        $subunitID = $this->checkForRecord($this->subunits[$subunitString[1]])->id;
                    }
                    else
                    {
                        $subunitinfo = explode('/', $this->subunits[$subunitString[1]]);
                        $subunitID = $subunitinfo[1];
                    }

                    if (empty($subunitID))
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
                        $sibling_id = $this->insertToCompositor($subunitID, 'msm_unit', $parentid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(composition.\d+)$/", $element) ? true : false):
                    $compositionString = split('-', $element);

                    if (is_object($this->compositions[$compositionString[1]]))
                    {
                        $compositionID = $this->checkForRecord($this->compositions[$compositionString[1]])->id;
                    }
                    else
                    {
                        $composittioninfo = explode('/', $this->compositions[$compositionString[1]]);
                        $compositionID = $composittioninfo[1];
                    }

                    if (empty($compositionID))
                    {
                        if (empty($sibling_id))
                        {
                            $composition = $this->compositions[$compositionString[1]];
                            $composition->saveIntoDb($composition->position, $parentid);
                            $sibling_id = $composition->compid;
                        }
                        else
                        {
                            $composition = $this->compositions[$compositionString[1]];
                            $composition->saveIntoDb($composition->position, $parentid, $sibling_id);
                            $sibling_id = $composition->compid;
                        }
                    }
                    else
                    {
                        $sibling_id = $this->insertToCompositor($compositionID, 'msm_compositor', $parentid, $sibling_id);
                    }
                    break;
            }
        }
    }

    function loadFromDb($id, $compid)
    {
        global $DB;

        return $this;
    }

    function displayhtml()
    {
        $content = '';

        return $content;
    }

}

?>
