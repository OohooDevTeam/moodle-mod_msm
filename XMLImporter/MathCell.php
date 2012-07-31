<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MathCell
 *
 * @author User
 */
class MathCell extends Element
{

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_math_cell';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->colspan = $DomElement->getAttribute('colspan');
        $this->halign = $DomElement->getAttribute('halign');
        $this->valign = $DomElement->getAttribute('valign');
        $this->bgcolor = $DomElement->getAttribute('bgcolor');
        $this->fontcolor = $DomElement->getAttribute('fontcolor');

        $this->infos = array();
        $this->companion = array(); // if the ref already exists inside db table, then store in here as id number
        $this->packs = array();
        $this->comments = array();
        $this->defs = array();
        $this->theorems = array();
        $this->subunits = array();

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                $childname = $child->tagName;
                $doc = new DOMDocument;

                switch ($childname)
                {
                    case('math'):
                        $element = $doc->importNode($child, true);
                        $this->content .= $doc->saveXML($element);
                        break;

                    case('text'):
                        $element = $doc->importNode($child, true);
                        $this->content .= $doc->saveXML($element);
                        break;

                    case('companion'):
                        foreach ($child->childNodes as $grandchild)
                        {
                            if ($grandchild->nodeType == XML_ELEMENT_NODE)
                            {
                                $grandChildname = $grandchild->tagName;
                                $parser = new DOMDocument();

                                switch ($grandChildname)
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
            }
        }
    }

    function saveIntoDb($position, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();
        $data->colspan = $this->colspan;
        $data->halign = $this->halign;
        $data->valign = $this->valign;
        $data->bgcolor = $this->bgcolor;
        $data->fontcolor = $this->fontcolor;
        $data->content = $this->content;

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
            }
        }
    }

    function loadFromDb($id, $compid)
    {
        global $DB;

        $mathcellRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($mathcellRecord))
        {
            $this->compid = $compid;
            $this->colspan = $mathcellRecord->colspan;
            $this->halign = $mathcellRecord->halign;
            $this->valign = $mathcellRecord->valign;
            $this->bgcolor = $mathcellRecord->bgcolor;
            $this->fontcolor = $mathcellRecord->fontcolor;
            $this->content = $mathcellRecord->content;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');
        $this->refchilds = array();
        $this->childs = array();

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            switch ($chidltablename)
            {
                case('msm_comment'):
                    $comment = new MathComment();
                    $comment->loadFromDb($child->unit_id, $child->id);
                    $this->refchilds[] = $comment;
                    break;
                case('msm_def'):
                    $def = new Definition();
                    $def->loadFromDb($child->unit_id, $child->id);
                    $this->refchilds[] = $def;
                    break;
                case('msm_theorem'):
                    $theorem = new Theorem();
                    $theorem->loadFromDb($child->unit_id, $child->id);
                    $this->refchilds[] = $theorem;
                    break;

                // depending on what to do with showme/quiz, it maybe included here or not
                case('msm_unit'):
                    $unit = new Unit();
                    $unit->loadFromDb($child->unit_id, $child->id);
                    $this->refchilds[] = $unit;
                    break;

                case('msm_info'):
                    $info = new MathInfo();
                    $info->loadFromDb($child->unit_id, $child->id);
                    $this->childs[$this->content] = $info;
            }
        }

        return $this;
    }

    function displayhtml($rowspan)
    {
        $content = '';

        $content .= "<td class='matharraycell' colspan='" . $this->colspan . "' rowspan='" . $rowspan . "' align='" . $this->halign . "' valign='" . $this->valign . "'>";
        
        // if info exists then need to set up the dialog popup window, otherwise, just show the content
        if (empty($this->childs))
        {
            $content .= $this->content;
        }
        else
        {
            $content .= "<a id='hottag-" . $this->compid . "' class='hottag' onmouseover='popup(" . $this->compid . ")'>";
            $content .= $this->content;
            $content .= "</a>";

            $content .= '<div id="dialog-' . $this->compid . '" class="dialogs" title="' . $this->childs[$this->content]->caption . '">';
            $content .= $this->childs[$this->content]->info_content;
            $content .= "</div>";
        }
        
        $content .= "</td>";
        
        return $content;
    }

}

?>
