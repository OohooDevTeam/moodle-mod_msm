<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Showme
 *
 * @author User
 */
class Showme extends Element
{

    public $position;
    public $statements;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_showme';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));

//        $this->statements = array();
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->medias = array();

        $statements = $DomElement->getElementsByTagName('statement.showme');

        foreach ($statements as $st)
        {
            foreach ($this->processIndexAuthor($st, $position) as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processIndexGlossary($st, $position) as $indexglossary)
            {
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processIndexSymbols($st, $position) as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }
            foreach ($this->processSubordinate($st, $position) as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processMedia($st, $position) as $media)
            {
                $this->medias[] = $media;
            }

            foreach ($this->processContent($st, $position) as $content)
            {
                $this->statements .= $content;
            }
        }

        $this->answer_showmes = array();

        $answer_showmes = $DomElement->getElementsByTagName('answer.showme');
        foreach ($answer_showmes as $a)
        {
            $position = $position + 1;
            $answer_showme = new AnswerShowme($this->xmlpath);
            $answer_showme->loadFromXml($a, $position);
            $this->answer_showmes[] = $answer_showme;
        }
    }

    function saveIntoDb($position)
    {
//        echo "showme save start";
//        $time = time();
//        print_object($time);
//        
        global $DB;

        $data = new stdClass();
        $data->caption = $this->caption;
        $data->textcaption = $this->textcaption;
//        if (!empty($this->statements))
//        {
//            foreach ($this->statements as $statement)
//            {
                $data->statement_showme = $this->statements;
                $this->id = $DB->insert_record($this->tablename, $data);
//            }
//        }
//        else
//        {
//            $this->id = $DB->insert_record($this->tablename, $data);
//        }

        foreach ($this->answer_showmes as $answer_showme)
        {
            $answer_showme->saveIntoDb($answer_showme->position);
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
