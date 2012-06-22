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

    function saveIntoDb($position)
    {
//         echo "approach save start";
//        $time = time();
//        print_object($time);
        
        global $DB;
        $data = new stdClass();

        $data->approach_version = $this->version;
        if (!empty($this->content))
        {
            foreach ($this->content as $content)
            {
                $data->solution_hint = $content;
                $this->id = $DB->insert_record($this->tablename, $data);
            }
        }
        else
        {
            $this->id = $DB->insert_record($this->tablename, $data);
        }

        foreach ($this->answerexercises as $answerexercise)
        {
            $answerexercise->saveIntoDb($answerexercise->position);
        }

        foreach ($this->solutions as $solution)
        {
            $solution->saveIntoDb($solution->position);
        }
        
        foreach ($this->subordinates as $key => $subordinate)
        {
            $subordinate->saveIntoDb($subordinate->position);
        }

        foreach ($this->indexglossarys as $key => $indexglossary)
        {
            $indexglossary->saveIntoDb($indexglossary->position);
        }

        foreach ($this->indexsymbols as $key => $indexsymbol)
        {
            $indexsymbol->saveIntoDb($indexsymbol->position);
        }

        foreach ($this->indexauthors as $key => $indexauthor)
        {
            $indexauthor->saveIntoDb($indexauthor->position);
        }
        
        foreach ($this->medias as $key => $media)
        {
            $media->saveIntoDb($media->position);
        }
    }

}

?>
