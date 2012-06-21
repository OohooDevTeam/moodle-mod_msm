<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class is a support class for Quiz class.  It processes information given under part element in XML files.
 * The data retrieve from this class is stored to msm_quiz table, which is similar to the Quiz class.
 *
 * @author User
 */
class PartQuiz extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_quiz';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->medias = array();

        $this->questions = array();
        $this->hints = array();
        $this->choices = array();

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                $name = $child->tagName;

                switch ($name)
                {
                    case('question'):
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
                            $this->questions[] = $content;
                        }

                        foreach ($this->processMedia($child, $position) as $media)
                        {
                            $this->medias[] = $media;
                        }
                        break;

                    case('hint'):
                        $info = $child->getElementsByTagName('info')->item(0);
                        $position = $position + 1;
                        $hintinfo = new MathInfo($this->xmlpath);
                        $hintinfo->loadFromXml($info, $position);
                        $this->hints[] = $hintinfo;
                        break;

                    case('choice'):
                        $position = $position + 1;
                        $choice = new QuizChoice($this->xmlpath);
                        $choice->loadFromXml($child, $position);
                        $this->choices[] = $choice;
                        break;
                }
            }
        }
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position
     * @param String $stringid
     * @param String $caption
     * @param String $textcaption 
     */
    function saveIntoDb($position, $stringid = '', $caption = '', $textcaption = '')
    {
        echo "partquiz save start";
        $time = time();
        print_object($time);
        
        global $DB;
        $data = new stdClass();
        $data->string_id = $stringid;
        $data->caption = $caption;
        $data->textcaption = $textcaption;

       if (!empty($this->questions))
        {
            foreach ($this->questions as $question)
            {
                $data->question = $question;
                $this->id = $DB->insert_record($this->tablename, $data);
            }
        }       

        foreach ($this->hints as $hint)
        {
            $hint->saveIntoDb($hint->position);
        }
        
        foreach($this->choices as $choice)
        {
            $choice->saveIntoDb($choice->position);
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
