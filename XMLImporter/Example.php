<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Example
 *
 * @author User
 */
class Example extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_example';
        $this->childtable = 'msm_statement_example';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->string_id = $DomElement->getAttribute('id');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));

        $this->description = $this->getDomAttribute($DomElement->getElementsByTagName('description'));

        $this->statement_examples = array();

        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->medias = array();

        $statement_examples = $DomElement->getElementsByTagName('statement.example');

        foreach ($statement_examples as $statement_ex)
        {
            foreach ($this->processIndexAuthor($statement_ex, $position) as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processIndexGlossary($statement_ex, $position) as $indexglossary)
            {
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processIndexSymbols($statement_ex, $position) as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }
            foreach ($this->processSubordinate($statement_ex, $position) as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processMedia($statement_ex, $position) as $media)
            {
                $this->medias[] = $media;
            }

            foreach ($this->processContent($statement_ex, $position) as $content)
            {
                $this->statement_examples[] = $content;
            }
        }

        $part_examples = $DomElement->getElementsByTagName('part.example');

        $this->part_examples = array();

        foreach ($part_examples as $part_ex)
        {
            $position = $position + 1;
            $part_example = new PartExample($this->xmlpath);
            $part_example->loadFromXml($part_ex, $position);
            $this->part_examples[] = $part_example;
        }

        $answers = $DomElement->getElementsByTagName('answer');
        $this->answers = array();

        foreach ($answers as $a)
        {
            $position = $position + 1;
            $answer = new AnswerExample($this->xmlpath);
            $answer->loadFromXml($a, $position);
            $this->answers[] = $answer;
        }
        
        $answerexts = $DomElement->getElementsByTagName('answer.ext');
        $this->answer_exts = array();

        foreach ($answerexts as $ax)
        {
            $position = $position + 1;
            $answerext = new AnswerExt($this->xmlpath);
            $answerext->loadFromXml($ax, $position);
            $this->answer_exts[] = $answerext;
        }
    }

    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();

        $data->string_id = $this->string_id;
        $data->caption = $this->caption;
        $data->textcaption = $this->textcaption;
        $data->description = $this->description;

        $this->id = $DB->insert_record($this->tablename, $data);

        $statement_data = new stdClass();
        foreach ($this->statement_examples as $st)
        {
            $statement_data->statement_example_content = $st;
            $this->statement_example_id = $DB->insert_record('msm_statement_example', $statement_data);
        }

        foreach ($this->answers as $answer)
        {
            $answer->saveIntoDb($answer->position);
        }

        foreach ($this->part_examples as $part_example)
        {
            $part_example->saveIntoDb($part_example->position);
        }
        
        foreach($this->answer_exts as $ans_ext)
        {
            $ans_ext->saveIntoDb($ans_ext->position);
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
