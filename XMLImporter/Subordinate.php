<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Subordinate
 *
 * @author User
 */
class Subordinate extends Element
{

    public $position;
    public $hot;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_subordinate';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        global $DB;

        $this->position = $position;

        $this->infos = array();
        $this->companion = array(); // if the ref already exists inside db table, then store in here as id number
        $this->packs = array();
        $this->comments = array();
        $this->externalref = array();
        $this->defs = array();
        $this->theorems = array();
        $this->subunits = array();
        $this->crossref = array();
        $this->compositions = array();

        $this->cites = array();
        $this->external_links = array();
        $this->subordinate_refs = array();

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == 'hot')
                {
                    $this->hot = $this->getContent($child);
                }
                if ($child->tagName == 'info')
                {
                    $position = $position + 1;
                    $info = new MathInfo($this->xmlpath);
                    $info->loadFromXml($child, $position);
                    $this->infos[] = $info;
                }
                if ($child->tagName == 'companion')
                {
                    foreach ($child->childNodes as $grandchild)
                    {
                        if ($grandchild->nodeType == XML_ELEMENT_NODE)
                        {
                            $name = $grandchild->tagName;
                            $parser = new DOMDocument();

                            switch ($name)
                            {
                                case('comment.ref'):
                                    $commentrefID = $grandchild->getAttribute('commentID');

                                    if (!empty($commentrefID))
                                    {
                                        $IDinDB = $DB->get_record('msm_comment', array('string_id' => $commentrefID));

                                        if (empty($IDinDB))
                                        {
                                            $filepath = $this->findFile($commentrefID, dirname($this->xmlpath));

                                            if (!empty($filepath))
                                            {
                                                @$parser->load($filepath);

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
                                            $this->companion[] = $commentrefID;
                                        }
                                        //when db is set up, add code to check the db records first
                                        // then if there are no records with specified ID, then..
                                        // find the file with comment with specified ID
                                    }
                                    break;

                                case('definition.ref'):
                                    $definitionrefID = $grandchild->getAttribute('definitionID');

                                    if (!empty($definitionrefID))
                                    {
                                        $IDinDB = $DB->get_record('msm_def', array('string_id' => $definitionrefID));

                                        if (empty($IDinDB))
                                        {
                                            $filepath = $this->findFile($definitionrefID, dirname($this->xmlpath));

                                            if (!empty($filepath))
                                            {
                                                @$parser->load($filepath);
                                                
                                                $element = $parser->documentElement;

                                                if (!empty($element))
                                                {
                                                    $position = $position + 1;
                                                    $def = new Defintion(dirname($filepath));
                                                    $def->loadFromXml($element, $position);
                                                    $this->defs[] = $def;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $this->companion[] = $definitionrefID;
                                        }
                                    }
                                    break;

                                case('theorem.ref'):
                                    $theoremrefID = $grandchild->getAttribute('theoremID');

                                    if (empty($theoremrefID))
                                    {
                                        $IDinDB = $DB->get_record('msm_theorem', array('string_id' => $theoremrefID));

                                        if (!empty($IDinDB))
                                        {
                                            $filepath = $this->findFile($theoremrefID, dirname($this->xmlpath));

                                            if (!empty($filepath))
                                            {
                                                @$parser->load($filepath);

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
                                            $this->companion[] = $theormerefID;
                                        }
                                    }
                                    break;
//                                    
                                case('showme.pack.ref'):
                                    $showmepackrefID = $grandchild->getAttribute('showmePackID');

                                    if (!empty($showmepackrefID))
                                    {
                                        $IDinDB = $DB->get_record('msm_packs', array('string_id' => $showmepackrefID));

                                        if (empty($IDinDB))
                                        {
                                            $filepath = $this->findFile($showmepackrefID, dirname($this->xmlpath));


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
                                        else
                                        {
                                            $this->companion[] = $showmepackrefID;
                                        }
                                    }

                                    break;

                                case('quiz.pack.ref'):
                                    $quizpackID = $grandchild->getAttribute('quizPackID');

                                    if (!empty($quizpackID))
                                    {
                                        $IDinDB = $DB->get_record('msm_packs', array('string_id' => $quizpackID));

                                        if (empty($IDinDB))
                                        {
                                            $filepath = $this->findFile($quizpackID, dirname($this->xmlpath));
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
                                        else
                                        {
                                            $this->companion[] = $quizpackID;
                                        }
                                    }
                                    break;

                                case('unit.ref'):
                                    $untiID = $grandchild->getAttribute('unitId');

                                    if (!empty($untiID))
                                    {
                                        $IDinDB = $DB->get_record('msm_unit', array('string_id' => $untiID));

                                        if (empty($IDinDB))
                                        {
                                            $filepath = $this->findFile($untiID, dirname($this->xmlpath));
                                            if (!empty($filepath))
                                            {
                                                @$parser->load($filepath);

                                                $element = $parser->documentElement;

                                                if (!empty($element))
                                                {
                                                    $position = $position + 1;
                                                    $unit = new Unit(dirname($filepath));
                                                    $unit->loadFromXml($element, $position);
                                                    $this->subunits[] = $unit;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $this->companion[] = $untiID;
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                }
                if ($child->tagName == 'crossref')
                {
                    foreach ($child->childNodes as $grandchild)
                    {
                        if ($grandchild->nodeType == XML_ELEMENT_NODE)
                        {
                            $name = $grandchild->tagName;
                            $parser = new DOMDocument();

                            switch ($name)
                            {
                                case('comment.ref'):
                                    $commentrefID = $grandchild->getAttribute('commentID');

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
                                            $this->companion[] = $commentrefID;
                                        }
                                        //when db is set up, add code to check the db records first
                                        // then if there are no records with specified ID, then..
                                        // find the file with comment with specified ID
                                    }
                                    break;

                                case('definition.ref'):
                                    $definitionrefID = $grandchild->getAttribute('definitionID');

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
                                            $this->companion[] = $definitionrefID;
                                        }
                                    }
                                    break;

                                case('theorem.ref'):
                                    $theoremrefID = $grandchild->getAttribute('theoremID');

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
                                            $this->companion[] = $theormerefID;
                                        }
                                    }
                                    break;

                                case('exercise.pack.ref'):
                                    $exercisePackID = $grandchild->getAttribute('exercisePackID');

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
                                            $this->companion[] = $exercisePackID;
                                        }
                                    }
                                    break;

                                case('example.pack.ref'):
                                    $examplepackID = $grandchild->getAttribute('examplePackID');

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
                                            $this->companion[] = $examplepackID;
                                        }
                                    }
                                    break;

                                case('unit.ref'):
                                    $untiID = $grandchild->getAttribute('unitId');

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
                                            $this->companion[] = $untiID;
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
                            }
                        }
                    }
                }
                if ($child->tagName == 'external.link')
                {
                    $position = $position + 1;
                    $link = new ExternalLink($this->xmlpath);
                    $link->loadFromXml($child, $position);
                    $this->external_links[] = $link;
                }
                if ($child->tagName == 'cite')
                {
                    $position = $position + 1;
                    $cite = new Cite($this->xmlpath);
                    $cite->loadFromXml($child, $position);
                    $this->cites[] = $cite;
                }
            }
        }
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position, $parentid = '', $siblingid = '')
    {
        global $DB;

        $data = new stdClass();
        $data->hot = $this->hot;

        $numOfRecords = $DB->count_records($this->tablename);
        if ($numOfRecords > 0)
        {
            // need the limit to be $numOfRecords+1 to process the last record
            for ($i = 1; $i < $numOfRecords + 1; $i++)
            {
                $string = $DB->get_field($this->tablename, 'hot', array('id' => $i));

                if ($string == $this->hot)
                {
                    $recordID = $i;
                    break;
                }
                else
                {
                    $recordID = false;
                }
            }
        }
        else
        {
            $recordID = false;
        }

        if (empty($recordID))
        {
            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
        }
        else //record already exists
        {
            $this->compid = $this->insertToCompositor($recordID, $this->tablename, $parentid, $siblingid);
        }

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

        if (!empty($this->external_links))
        {
            foreach ($this->external_links as $key => $external_link)
            {
                $elementPositions['externallink' . '-' . $key] = $external_link->position;
            }
        }

        if (!empty($this->cites))
        {
            foreach ($this->cites as $key => $cite)
            {
                $elementPositions['cite' . '-' . $key] = $cite->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(info.\d+)$/", $element) ? true : false):
                    $infoString = split('-', $element);
//
//                    $infoRecord = $this->checkForRecord($this->infos[$infoString[1]]);
//
//                    if (empty($infoRecord))
//                    {
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
//                    }
//                    else
//                    {
//                        $infoID = $infoRecord->id;
//                        $info = $this->infos[$infoString[1]];
//                        $info->compid = $this->insertToCompositor($infoID, $info->tablename, $this->compid, $sibling_id);
//                    }
                    break;

                case(preg_match("/^(pack.\d+)$/", $element) ? true : false):
                    $packString = split('-', $element);

                    $packRecord = $this->checkForRecord($this->packs[$packString[1]]);

                    if (empty($packRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $pack = $this->packs[$packString[1]];
                            $pack->saveIntoDb($pack->position, $this->compid);
                            $sibling_id = $pack->compid;
                        }
                        else
                        {
                            $pack = $this->packs[$packString[1]];
                            $pack->saveIntoDb($pack->position, $this->compid, $sibling_id);
                            $sibling_id = $pack->compid;
                        }
                    }
                    else
                    {
                        $packID = $packRecord->id;
                        $pack = $this->packs[$packString[1]];
                        $pack->compid = $this->insertToCompositor($packID, $pack->tablename, $this->compid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(comment.\d+)$/", $element) ? true : false):
                    $commentString = split('-', $element);

                    $commentRecord = $this->checkForRecord($this->comments[$commentString[1]]);

                    if (empty($commentRecord))
                    {
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
                    }
                    else
                    {
                        $commentID = $commentRecord->id;
                        $comment = $this->comments[$commentString[1]];
                        $comment->compid = $this->insertToCompositor($commentID, $comment->tablename, $this->compid, $sibling_id);
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

                    $theoeremRecord = $this->checkForRecord($this->theorems[$theoremString[1]]);

                    if (empty($theoeremRecord))
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
                        $theoremID = $theoeremRecord->id;
                        $theorem = $this->theorems[$theoremString[1]];
                        $theorem->compid = $this->insertToCompositor($theoremID, $theorem->tablename, $this->compid, $sibling_id);
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
                    }
                    break;

                case(preg_match("/^(composition.\d+)$/", $element) ? true : false):
                    $compositionString = split('-', $element);

                    $compositionRecord = $this->checkForRecord($this->compositions[$compositionString[1]]);

                    if (empty($compositionRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $composition = $this->compositions[$compositionString[1]];
                            $composition->saveIntoDb($composition->position, $this->compid);
                            $sibling_id = $composition->compid;
                        }
                        else
                        {
                            $composition = $this->compositions[$compositionString[1]];
                            $composition->saveIntoDb($composition->position, $this->compid, $sibling_id);
                            $sibling_id = $composition->compid;
                        }
                    }
                    else
                    {
                        $compositionID = $compositionRecord->id;
                        $composition = $this->compositions[$compositionString[1]];
                        $composition = $this->insertToCompositor($compositionID, $composition->tablename, $this->compid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(externallink.\d+)$/", $element) ? true : false):
                    $externallinkString = split('-', $element);

                    $externallinkRecord = $this->checkForRecord($this->external_links[$externallinkString[1]], 'href');

                    if (empty($externallinkRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $external_link = $this->external_links[$externallinkString[1]];
                            $external_link->saveIntoDb($external_link->position, $this->compid);
                            $sibling_id = $external_link->compid;
                        }
                        else
                        {
                            $external_link = $this->external_links[$externallinkString[1]];
                            $external_link->saveIntoDb($external_link->position, $this->compid, $sibling_id);
                            $sibling_id = $external_link->compid;
                        }
                    }
                    else
                    {
                        $compositionID = $externallinkRecord->id;
                        $external_link = $this->external_links[$externallinkString[1]];
                        $external_link = $this->insertToCompositor($compositionID, $external_link->tablename, $this->compid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(cite.\d+)$/", $element) ? true : false):
                    $citeString = split('-', $element);

//                    $citeRecord = $this->checkForRecord($this->cites[$citeString[1]]);
//
//                    if (empty($citeRecord))
//                    {
                        if (empty($sibling_id))
                        {
                            $cite = $this->cites[$citeString[1]];
                            $cite->saveIntoDb($cite->position, $this->compid);
                            $sibling_id = $cite->compid;
                        }
                        else
                        {
                            $cite = $this->cites[$citeString[1]];
                            $cite->saveIntoDb($cite->position, $this->compid, $sibling_id);
                            $sibling_id = $cite->compid;
                        }
//                    }
//                    else
//                    {
//                        $citeID = $citeRecord->id;
//                        $cite = $this->cites[$citeString[1]];
//                        $cite = $this->insertToCompositor($citeID, $cite->tablename, $this->compid, $sibling_id);
//                    }
                    break;
            }
        }

    }

}

?>
