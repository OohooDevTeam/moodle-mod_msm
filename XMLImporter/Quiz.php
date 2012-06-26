<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Quiz
 *
 * @author User
 */
class Quiz extends Element
{

    public $position;
    public $question;

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
        $this->string_id = $DomElement->getAttribute('id');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));
        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));
        //$this->questions = array();

        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->medias = array();

        $this->questions = array();
        $this->hints = array();
        $this->choices = array();
        $this->parts = array();

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

                    case('part'):
                        $position = $position + 1;
                        $part = new PartQuiz($this->xmlpath);
                        $part->loadFromXml($child, $position);
                        $this->parts[] = $part;
                        break;
                }
            }
        }
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();
        $data->string_id = $this->string_id;
        $data->caption = $this->caption;
        $data->textcaption = $this->textcaption;

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

        foreach ($this->choices as $choice)
        {
            $choice->saveIntoDb($choice->position);
        }

        foreach ($this->parts as $part)
        {
            $part->saveIntoDb($part->position, $this->string_id, $this->caption, $this->textcaption);
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
