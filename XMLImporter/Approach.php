<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Approach
 *
 * @author User
 */
class Approach extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_approach';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->version = $DomElement->getAttribute('version');

        $this->content = array();
        $this->answerexercises = array();
        $this->solutions = array();
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->medias = array();

        foreach ($DomElement->childNodes as $key => $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == 'solution.hint')
                {
                    foreach ($this->processIndexAuthor($child, $position) as $indexauthor)
                    {
                        $this->indexauthors[] = $indexauthor;
                    }

                    foreach ($this->processIndexGlossary($child, $position) as $indexglossary)
                    {
                        $this->indexglossarys[] = $indexglossary;
                    }

                    foreach ($this->processIndexSymbols($child, $position) as $indexsymbol)
                    {
                        $this->indexsymbols[] = $indexsymbol;
                    }
                    foreach ($this->processSubordinate($child, $position) as $subordinate)
                    {
                        $this->subordinates[] = $subordinate;
                    }

                    foreach ($this->processContent($child, $position) as $content)
                    {
                        $this->content[] = $content;
                    }

                    foreach ($this->processMedia($child, $position) as $media)
                    {
                        $this->medias[] = $media;
                    }
                }
                if ($child->tagName == 'answer.exercise')
                {
                    $answerexercises = $child->getElementsByTagName('answer.exercise.block');

                    foreach ($answerexercises as $ans)
                    {
                        $position = $position + 1;
                        $answerexerciseblock = new AnswerExercise($this->xmlpath);
                        $answerexerciseblock->loadFromXml($ans, $position);
                        $this->answerexercises[] = $answerexerciseblock;
                    }
                }
                if ($child->tagName == 'solution')
                {
                    $position = $position + 1;
                    $solution = new Solution($this->xmlpath);
                    $solution->loadFromXml($child, $position);
                    $this->solutions[] = $solution;
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

        $data->approach_version = $this->version;
        if (!empty($this->content))
        {
            foreach ($this->content as $content)
            {
                $data->solution_hint = $content;
                $this->id = $DB->insert_record($this->tablename, $data);
                $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
            }
        }
        else
        {
            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
        }

        $elementPositions = array();
        $sibling_id = null;

        if (!empty($this->answerexercises))
        {
            foreach ($this->answerexercises as $key => $answerexercise)
            {
                $elementPositions['answerexercise' . '-' . $key] = $answerexercise->position;
            }
        }

        if (!empty($this->solutions))
        {
            foreach ($this->solutions as $key => $solution)
            {
                $elementPositions['solution' . '-' . $key] = $solution->position;
            }
        }

        if (!empty($this->subordinates))
        {
            foreach ($this->subordinates as $key => $subordinate)
            {
                $elementPositions['subordinate' . '-' . $key] = $subordinate->position;
            }
        }

        if (!empty($this->indexauthors))
        {
            foreach ($this->indexauthors as $key => $indexauthor)
            {
                $elementPositions['indexauthor' . '-' . $key] = $indexauthor->position;
            }
        }

        if (!empty($this->indexglossarys))
        {
            foreach ($this->indexglossarys as $key => $indexglosary)
            {
                $elementPositions['indexglossary' . '-' . $key] = $indexglosary->position;
            }
        }

        if (!empty($this->indexsymbols))
        {
            foreach ($this->indexsymbols as $key => $indexsymbol)
            {
                $elementPositions['indexsymbol' . '-' . $key] = $indexsymbol->position;
            }
        }

        if (!empty($this->medias))
        {
            foreach ($this->medias as $key => $media)
            {
                $elementPositions['media' . '-' . $key] = $media->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(answerexercise.\d+)$/", $element) ? true : false):
                    $answerexerciseString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $answerexercise = $this->answerexercises[$answerexerciseString[1]];
                        $answerexercise->saveIntoDb($answerexercise->position, $this->compid);
                        $sibling_id = $answerexercise->compid;
                    }
                    else
                    {
                        $answerexercise = $this->answerexercises[$answerexerciseString[1]];
                        $answerexercise->saveIntoDb($answerexercise->position, $this->compid, $sibling_id);
                        $sibling_id = $answerexercise->compid;
                    }
                    break;
                    
                case(preg_match("/^(soltuion.\d+)$/", $element) ? true : false):
                    $solutionString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $solution = $this->solutions[$solutionString[1]];
                        $solution->saveIntoDb($solution->position, $this->compid);
                        $sibling_id = $solution->compid;
                    }
                    else
                    {
                        $solution = $this->solutions[$solutionString[1]];
                        $solution->saveIntoDb($solution->position, $this->compid, $sibling_id);
                        $sibling_id = $solution->compid;
                    }
                    break;

                case(preg_match("/^(subordinate.\d+)$/", $element) ? true : false):
                    $subordinateString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $subordinate = $this->subordinates[$subordinateString[1]];
                        $subordinate->saveIntoDb($subordinate->position, $this->compid);
                        $sibling_id = $subordinate->compid;
                    }
                    else
                    {
                        $subordinate = $this->subordinates[$subordinateString[1]];
                        $subordinate->saveIntoDb($subordinate->position, $this->compid, $sibling_id);
                        $sibling_id = $subordinate->compid;
                    }
                    break;

                case(preg_match("/^(indexauthor.\d+)$/", $element) ? true : false):
                    $indexauthorString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $indexauthor = $this->indexauthors[$indexauthorString[1]];
                        $indexauthor->saveIntoDb($indexauthor->position, $this->compid);
                        $sibling_id = $indexauthor->compid;
                    }
                    else
                    {
                        $indexauthor = $this->indexauthors[$indexauthorString[1]];
                        $indexauthor->saveIntoDb($indexauthor->position, $this->compid, $sibling_id);
                        $sibling_id = $indexauthor->compid;
                    }
                    break;

                case(preg_match("/^(indexsymbol.\d+)$/", $element) ? true : false):
                    $indexsymbolString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $indexsymbol = $this->indexsymbols[$indexsymbolString[1]];
                        $indexsymbol->saveIntoDb($indexsymbol->position, $this->compid);
                        $sibling_id = $indexsymbol->compid;
                    }
                    else
                    {
                        $indexsymbol = $this->indexsymbols[$indexsymbolString[1]];
                        $indexsymbol->saveIntoDb($indexsymbol->position, $this->compid, $sibling_id);
                        $sibling_id = $indexsymbol->compid;
                    }
                    break;

                case(preg_match("/^(indexglossary.\d+)$/", $element) ? true : false):
                    $indexglossaryString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $indexglossary = $this->indexglossarys[$indexglossaryString[1]];
                        $indexglossary->saveIntoDb($indexglossary->position, $this->compid);
                        $sibling_id = $indexglossary->compid;
                    }
                    else
                    {
                        $indexglossary = $this->indexglossarys[$indexglossaryString[1]];
                        $indexglossary->saveIntoDb($indexglossary->position, $this->compid, $sibling_id);
                        $sibling_id = $indexglossary->compid;
                    }
                    break;

                case(preg_match("/^(media.\d+)$/", $element) ? true : false):
                    $mediaString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $media = $this->medias[$mediaString[1]];
                        $media->saveIntoDb($media->position, $this->compid);
                        $sibling_id = $media->compid;
                    }
                    else
                    {
                        $media = $this->medias[$mediaString[1]];
                        $media->saveIntoDb($media->position, $this->compid, $sibling_id);
                        $sibling_id = $media->compid;
                    }
                    break;
            }
        }

    }

}

?>
