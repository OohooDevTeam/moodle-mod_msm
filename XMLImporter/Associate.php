<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Associate
 *
 * @author User
 */
class Associate extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_associate';
    }

    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->description = $DomElement->getAttribute('type');

        $this->infos = array();
        $this->comments = array();
        $this->subunits = array();
        $this->defs = array();
        $this->theorems = array();
        $this->refs = array();

        foreach ($DomElement->childNodes as $key => $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                $name = $child->tagName;
                $parser = new DOMDocument();

                switch ($name)
                {
                    case('comment.ref'):
                        $position = $position + 1;
                        $commentID = $child->getAttribute('commentID');
                        $path = $this->findFile($commentID, dirname($this->xmlpath));

                        if (!empty($path))
                        {
                            $parser->load($path);

                            $element = $parser->documentElement;

                            $comment = new MathComment(dirname($path));
                            $comment->loadFromXml($element, $position);
                            $this->comments[] = $comment;
                        }
                        break;

                    case('showme.pack.ref'):
                        $position = $position + 1;
                        $showmepackID = $child->getAttribute('showmePackID');
                        $path = $this->findFile($showmepackID, dirname($this->xmlpath));

                        if (!empty($path))
                        {
                            @$parser->load($path);
                            $element = $parser->documentElement;

                            $showmepack = new Pack(dirname($path));
                            $showmepack->loadFromXml($element, $position);
                            $this->refs[] = $showmepack;
                        }
                        break;

                    case('quiz.pack.ref'):
                        $position = $position + 1;
                        $quizpackID = $child->getAttribute('quizPackID');
                        $path = $this->findFile($quizpackID, dirname($this->xmlpath));

                        if (!empty($path))
                        {
                            @$parser->load($path);

                            $element = $parser->documentElement;

                            $quizpack = new Pack(dirname($path));
                            $quizpack->loadFromXml($element, $position);
                            $this->refs[] = $quizpack;
                        }
                        break;

                    case('definition.ref'):
                        $position = $position + 1;
                        $definitionID = $child->getAttribute('definitionID');
                        $path = $this->findFile($definitionID, dirname($this->xmlpath));

                        if (!empty($path))
                        {
                            @$parser->load($path);

                            $element = $parser->documentElement;

                            $def = new Definition(dirname($path));
                            $def->loadFromXml($element, $position);
                            $this->defs[] = $def;
                        }
                        break;

                    case('theorem.ref'):
                        $position = $position + 1;
                        $theoremID = $child->getAttribute('theoremID');
                        $theorempartID = $child->getAttibute('theorempartID');
                        $path = $this->findFile($theoremID, dirname($this->xmlpath));

                        if (!empty($path))
                        {
                            @$parser->load($path);

                            $element = $parser->documentElement;

                            $theorem = new Theorem(dirname($path));
                            $theorem->loadFromXml($element, $position);
                            $this->theorems[] = $theorem;
                        }
                        break;

                    case('unit.ref'):
                        $position = $position + 1;
                        $unitID = $child->getAttribute('unitId');
                        $path = $this->findFile($unitID, dirname($this->xmlpath));

                        if (!empty($path))
                        {
                            @$parser->load($path);

                            $element = $parser->documentElement;

                            $unit = new Unit(dirname($path));
                            $unit->loadFromXml($element, $position);
                            $this->subunits[] = $unit;
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
    }

    function saveIntoDb($position, $parentid = '', $siblingid = '')
    {
        global $DB;

        $data = new stdClass();
        $data->description = $this->description;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);

        $elementPositions = array();
        $sibling_id = null;


        if (!empty($this->infos))
        {
            foreach ($this->infos as $key => $info)
            {
                $elementPositions['info' . '-' . $key] = $info->position;
            }
        }

        if (!empty($this->comments))
        {
            foreach ($this->comments as $key => $comment)
            {
                $elementPositions['comment' . '-' . $key] = $comment->position;
            }
        }

        if (!empty($this->subunits))
        {
            foreach ($this->subunits as $key => $subunit)
            {
                $elementPositions['subunit' . '-' . $key] = $subunit->position;
            }
        }

        if (!empty($this->theorems))
        {
            foreach ($this->theorems as $key => $theorem)
            {
                $elementPositions['theorem' . '-' . $key] = $theorem->position;
            }
        }

        if (!empty($this->defs))
        {
            foreach ($this->defs as $key => $def)
            {
                $elementPositions['def' . '-' . $key] = $def->position;
            }
        }

        if (!empty($this->refs))
        {
            foreach ($this->refs as $key => $ref)
            {
                $elementPositions['ref' . '-' . $key] = $ref->position;
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
                        $info->saveIntoDb($info->position, $this->compid);
                        $sibling_id = $info->compid;
                    }
                    else
                    {
                        $info = $this->infos[$infoString[1]];
                        $info->saveIntoDb($info->position, $this->compid, $sibling_id);
                        $sibling_id = $info->compid;
                    }
                    break;

                case(preg_match("/^(comment.\d+)$/", $element) ? true : false):
                    $commentString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $comment = $this->comments[$commentString[1]];
                        $comment->saveIntoDb($comment->position, $this->compid);
                        $sibling_id = $comment->compid;
                    }
                    else
                    {
                        $comment = $this->comments[$commentString[1]];
                        $comment->saveIntoDb($comment->position, $this->compid, $sibling_id);
                        $sibling_id = $comment->compid;
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
                            $subunit->saveIntoDb($subunit->position, $this->compid);
                            $sibling_id = $subunit->compid;
                        }
                        else
                        {
                            $subunit = $this->subunits[$subunitString[1]];
                            $subunit->saveIntoDb($subunit->position, $this->compid, $sibling_id);
                            $sibling_id = $subunit->compid;
                        }
                    }
                    else
                    {
                        $subunitID = $subunitRecord->id;

                        $subunit = $this->subunits[$subunitString[1]];
                        $subunit->compid = $this->insertToCompositor($subunitID, $subunit->tablename, $this->compid, $sibling_id);
                        $sibling_id = $subunit->compid;
                    }


                    break;

                case(preg_match("/^(def.\d+)$/", $element) ? true : false):
                    $defString = split('-', $element);
                    $defRecord = $this->checkForRecord($this->defs[$defString[1]]);

                    if (empty($defRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $def = $this->defs[$defString[1]];
                            $def->saveIntoDb($def->position, $this->compid);
                            $sibling_id = $def->compid;
                        }
                        else
                        {
                            $def = $this->defs[$defString[1]];
                            $def->saveIntoDb($def->position, $this->compid, $sibling_id);
                            $sibling_id = $def->compid;
                        }
                    }
                    else
                    {
                        $defID = $defRecord->id;
                        $def = $this->defs[$defString[1]];
                        $def->compid = $this->insertToCompositor($defID, $def->tablename, $this->compid, $sibling_id);
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
                            $theorem->saveIntoDb($theorem->position, $this->compid);
                            $sibling_id = $theorem->compid;
                        }
                        else
                        {
                            $theorem = $this->theorems[$theoremString[1]];
                            $theorem->saveIntoDb($theorem->position, $this->compid, $sibling_id);
                            $sibling_id = $theorem->compid;
                        }
                    }
                    else
                    {
                        $theoremID = $theoremRecord->id;
                        $theorem = $this->theorems[$theoremString[1]];
                        $theorem->compid = $this->insertToCompositor($theoremID, $theorem->tablename, $this->compid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(ref.\d+)$/", $element) ? true : false):
                    $refString = split('-', $element);

                    $refRecord = $this->checkForRecord($this->refs[$refString[1]]);

                    if (empty($refRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $ref = $this->refs[$refString[1]];
                            $ref->saveIntoDb($ref->position, $this->compid);
                            $sibling_id = $ref->compid;
                        }
                        else
                        {
                            $ref = $this->refs[$refString[1]];
                            $ref->saveIntoDb($ref->position, $this->compid, $sibling_id);
                            $sibling_id = $ref->compid;
                        }
                    }
                    else
                    {
                        $refID = $refRecord->id;
                        $ref = $this->refs[$refString[1]];
                        $ref->compid = $this->insertToCompositor($refID, $ref->tablename, $this->compid, $sibling_id);
                    }
                    break;
            }
        }
    }

}

?>
