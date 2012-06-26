<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AnswerExample
 *
 * @author User
 */
class AnswerExample extends Element
{

    public $position;
    public $caption;
    public $answer_type;
    public $answer_version;
    public $logic_type;
    public $example_answer_logic;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_answer';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->answer_type = $DomElement->getAttribute('type');
        $this->answer_version = $DomElement->getAttribute('version');

        $answer_blocks = $DomElement->getElementsByTagName('answer.block');

        $this->answer_block_body = array();
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->medias = array();

        foreach ($answer_blocks as $answer_block)
        {
            foreach ($answer_block->childNodes as $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    if ($child->tagName == 'caption')
                    {
                        $this->caption = $this->getContent($child);
                    }
                    else if ($child->tagName == 'logic')
                    {
                        $this->logic_type = $child->getAttribute('type');
                        $this->example_answer_logic = $child->nodeValue;
                    }
                }
            }

            $answer_block_bodys = $answer_block->getElementsByTagName('answer.block.body');

            foreach ($answer_block_bodys as $answer_block_body)
            {
                foreach ($this->processIndexAuthor($answer_block_body, $position) as $indexauthor)
                {
                    $this->indexauthors[] = $indexauthor;
                }

                foreach ($this->processIndexGlossary($answer_block_body, $position) as $indexglossary)
                {
                    $this->indexglossarys[] = $indexglossary;
                }

                foreach ($this->processIndexSymbols($answer_block_body, $position) as $indexsymbol)
                {
                    $this->indexsymbols[] = $indexsymbol;
                }
                foreach ($this->processSubordinate($answer_block_body, $position) as $subordinate)
                {
                    $this->subordinates[] = $subordinate;
                }

                foreach ($this->processContent($answer_block_body, $position) as $content)
                {
                    $this->answer_block_body[] = $content;
                }

                foreach ($this->processMedia($answer_block_body, $position) as $media)
                {
                    $this->medias[] = $media;
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

        $data->answer_type = $this->answer_type;
        $data->answer_version = $this->answer_version;
        $data->caption = $this->caption;
        $data->logic_type = $this->logic_type;
        $data->answer_logic = $this->example_answer_logic;

        if (!empty($this->answer_block_body))
        {
            foreach ($this->answer_block_body as $block_body)
            {
                $data->answer_content = $block_body;
                $this->id = $DB->insert_record($this->tablename, $data);
            }
        }
        else
        {
            $this->id = $DB->insert_record($this->tablename, $data);
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
