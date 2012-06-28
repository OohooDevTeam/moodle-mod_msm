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

                            $comment = new Comment(dirname($path));
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

        foreach ($this->infos as $key => $info)
        {
            $info->saveIntoDb($info->position);
        }

        foreach ($this->comments as $key => $comment)
        {
            $comment->saveIntoDb($comment->position);
        }

        foreach ($this->subunits as $key => $unit)
        {
            $unitID = $this->checkForRecord($unit);

            if (empty($unitID))
            {
                $unit->saveIntoDb($unit->position);
            }
        }

        foreach ($this->theorems as $key => $theorem)
        {
            $theoremID = $this->checkForRecord($theorem);

            if (empty($theoremID))
            {
                $theorem->saveIntoDb($theorem->position);
            }
        }

        foreach ($this->defs as $key => $def)
        {
            $defID = $this->checkForRecord($def);

            if (empty($defID))
            {
                $def->saveIntoDb($def->position);
            }
        }
        foreach ($this->refs as $key => $ref)
        {
            $refID = $this->checkForRecord($ref);

            if (empty($refID))
            {
                $ref->saveIntoDb($ref->position);
            }
        }
    }

}

?>
