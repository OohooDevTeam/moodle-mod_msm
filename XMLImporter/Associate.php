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
        global $DB;

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
//                            $filepath = $this->findFile($theoremrefID, dirname($this->xmlpath), 'theorem');
//
//                            if (!empty($filepath))
//                            {
//                                @$parser->load($filepath);
//
//                                $element = $parser->documentElement;
//
//                                if (!empty($element))
//                                {
//                                    $position = $position + 1;
//                                    $theorem = new Theorem(dirname($filepath));
//                                    $theorem->loadFromXml($element, $position);
//                                    $this->theorems[] = $theorem;
//                                }
//                            }
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
                    if (!empty($this->comments[$commentString[1]]->string_id))
                    {
                        $commentID = $this->checkForRecord($this->comments[$commentString[1]]);
                    }
                    else
                    {
                        $commentID = $this->checkForRecord($this->comments[$commentString[1]], 'caption');
                    }

                    if (empty($commentID))
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
                        $commentID = $commentID->id;
                        $sibling_id = $this->insertToCompositor($commentID, 'msm_comment', $this->compid, $sibling_id);
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
                        $sibling_id = $this->insertToCompositor($subunitID, 'msm_unit', $this->compid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(def.\d+)$/", $element) ? true : false):
                    $defString = split('-', $element);
                    if (!empty($this->defs[$defString[1]]->string_id))
                    {
                        $defID = $this->checkForRecord($this->defs[$defString[1]]);
                    }
                    else
                    {
                        $defID = $this->checkForRecord($this->defs[$defString[1]], 'caption');
                    }

                    if (empty($defID))
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
                        $defID = $defID->id;
                        $sibling_id = $this->insertToCompositor($defID, 'msm_def', $this->compid, $sibling_id);
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

    function loadFromDb($id, $compid)
    {
        global $DB;

        $associateRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($associateRecord))
        {
            $this->compid = $compid;
            $this->description = $associateRecord->description;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

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

                case('msm_unit'):
                    $unit = new Unit();
                    $unit->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $unit;
                    break;

//                case('msm_packs'):
//                    $pack = new Pack();
//                    $pack->loadFromDb($child->unit_id, $child->id);
//                    $this->childs[] = $pack;
//                    break;
            }
        }

        return $this;
    }

    function displayhtml()
    {
        global $DB;

        $content = '';

        $associateParentID = $DB->get_record('msm_compositor', array('id' => $this->compid))->parent_id;

        $associateParenttable = $DB->get_record('msm_compositor', array('id' => $associateParentID))->table_id;

        $associateParentTablename = $DB->get_record('msm_table_collection', array('id' => $associateParenttable))->tablename;

        if ($associateParentTablename == 'msm_def')
        {
            if (!empty($this->childs))
            {
//              $content .= "<li class='defminibutton' id='defminibutton-" . $this->infos[0]->compid . "' onmouseover='infoopen(" . $this->infos[0]->compid . ")' onclick='showRightpage(" . $this->infos[0]->compid . ")'>";
                $content .= "<li class='defminibutton' id='defminibutton-" . $this->infos[0]->compid . "' onmouseover='infoopen(" . $this->infos[0]->compid . ")'>";

                $content .= "<span style='cursor:pointer'>";
                $content .= $this->description;
//                $content .= "child";
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
            $content .= '<div id="dialog-' . $this->infos[0]->compid . '" class="dialogs" title="' . $this->infos[0]->caption . '">';
            $content .= $this->infos[0]->info_content;
            $content .= "</div>";
        }

        if ($associateParentTablename == 'msm_theorem')
        {
            if (!empty($this->childs))
            {
                $content .= "<li class='minibutton' id='minibutton-" . $this->infos[0]->compid . "' onmouseover='infoopen(" . $this->infos[0]->compid . ")' onclick='showRightpage(" . $this->infos[0]->compid . ")'>";
                $content .= "<span style='cursor:pointer'>";
                $content .= $this->description;
//                $content .= "child";
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

            $content .= '<div id="dialog-' . $this->infos[0]->compid . '" class="dialogs" title="' . $this->infos[0]->caption . '">';
            $content .= $this->infos[0]->info_content;
            $content .= "</div>";
        }

        if ($associateParentTablename == 'msm_comment')
        {
            if (!empty($this->childs))
            {
                $content .= "<li class='commentminibutton' id='commentminibutton-" . $this->infos[0]->compid . "' onmouseover='infoopen(" . $this->infos[0]->compid . ")' onclick='showRightpage(" . $this->infos[0]->compid . ")'>";
                $content .= "<span style='cursor:pointer'>";
                $content .= $this->description;
//                $content .= "child";
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

            $content .= '<div id="dialog-' . $this->infos[0]->compid . '" class="dialogs" title="' . $this->infos[0]->caption . '">';
            $content .= $this->infos[0]->info_content;
            $content .= "</div>";
        }

        return $content;
    }

}

?>
