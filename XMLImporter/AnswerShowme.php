<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AnswerShowme
 *
 * @author User
 */
class AnswerShowme extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_answer_showme';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->content = array();
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->medias = array();

        $answer_showme_blocks = $DomElement->getElementsByTagName('answer.showme.block');

        foreach ($answer_showme_blocks as $asb)
        {
            $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

            $answer_showme_block_bodys = $asb->getElementsByTagName('answer.showme.block.body');

            foreach ($answer_showme_block_bodys as $asbb)
            {
                foreach ($this->processIndexAuthor($asbb, $position) as $indexauthor)
                {
                    $this->indexauthors[] = $indexauthor;
                }

                foreach ($this->processIndexGlossary($asbb, $position) as $indexglossary)
                {
                    $this->indexglossarys[] = $indexglossary;
                }

                foreach ($this->processIndexSymbols($asbb, $position) as $indexsymbol)
                {
                    $this->indexsymbols[] = $indexsymbol;
                }
                foreach ($this->processSubordinate($asbb, $position) as $subordinate)
                {
                    $this->subordinates[] = $subordinate;
                }

                foreach ($this->processContent($asbb, $position) as $content)
                {
                    $this->content [] = $content;
                }

                foreach ($this->processMedia($asbb, $position) as $media)
                {
                    $this->medias[] = $media;
                }
            }
        }
    }

    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();
        $data->caption = $this->caption;

        if (!empty($this->content))
        {
            foreach ($this->content as $key => $content)
            {
                $data->answer_showme_content = $content;
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
