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
    public $string_id;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_example';
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

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position, $parentid = '', $siblingid = '')
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
            $this->compid = $this->insertToCompositor($this->position, $this->tablename, $parentid, $siblingid);
        }


        $elementPositions = array();
        $sibling_id = null;

        if (!empty($this->answers))
        {
            foreach ($this->answers as $key => $answer)
            {
                $elementPositions['answer' . '-' . $key] = $answer->position;
            }
        }
        
        if (!empty($this->part_examples))
        {
            foreach ($this->part_examples as $key => $part_example)
            {
                $elementPositions['partexample' . '-' . $key] = $part_example->position;
            }
        }
        
        if (!empty($this->answer_exts))
        {
            foreach ($this->answer_exts as $key => $answer_ext)
            {
                $elementPositions['answerext' . '-' . $key] = $answer_ext->position;
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
                case(preg_match("/^(answerext.\d+)$/", $element) ? true : false):
                    $answerextString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $answerext = $this->answer_exts[$answerextString[1]];
                        $answerext->saveIntoDb($answerext->position, $this->compid);
                        $sibling_id = $answerext->compid;
                    }
                    else
                    {
                        $answerext = $this->answer_exts[$answerextString[1]];
                        $answerext->saveIntoDb($answerext->position, $this->compid, $sibling_id);
                        $sibling_id = $answerext->compid;
                    }
                    break;
                case(preg_match("/^(partexample.\d+)$/", $element) ? true : false):
                    $partexampleString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $partexample = $this->part_examples[$partexampleString[1]];
                        $partexample->saveIntoDb($partexample->position, $this->compid);
                        $sibling_id = $partexample->compid;
                    }
                    else
                    {
                        $partexample = $this->part_examples[$partexampleString[1]];
                        $partexample->saveIntoDb($partexample->position, $this->compid, $sibling_id);
                        $sibling_id = $partexample->compid;
                    }
                    break;
                    
                 case(preg_match("/^(answer.\d+)$/", $element) ? true : false):
                    $answerString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $answer = $this->answers[$answerString[1]];
                        $answer->saveIntoDb($answer->position, $this->compid);
                        $sibling_id = $answer->compid;
                    }
                    else
                    {
                        $answer = $this->answers[$answerString[1]];
                        $answer->saveIntoDb($answer->position, $this->compid, $sibling_id);
                        $sibling_id = $answer->compid;
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
