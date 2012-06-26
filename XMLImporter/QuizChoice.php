<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of QuizChoice
 *
 * @author User
 */
class QuizChoice extends Element
{
    public $position;
    public $answer;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_quiz_choice';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $answer = $DomElement->getElementsByTagName('answer')->item(0);
        
        $this->indexauthors = array();
        $this->indexsymbols = array();
        $this->indexglossarys = array();
        $this->subordinates = array();
        $this->medias = array();

        foreach ($this->processIndexAuthor($answer, $position) as $indexauthor)
        {
            $this->indexauthors[] = $indexauthor;
        }

        foreach ($this->processIndexGlossary($answer, $position) as $indexglossary)
        {
            $this->indexglossarys[] = $indexglossary;
        }

        foreach ($this->processIndexSymbols($answer, $position) as $indexsymbol)
        {
            $this->indexsymbols[] = $indexsymbol;
        }
        foreach ($this->processSubordinate($answer, $position) as $subordinate)
        {
            $this->subordinates[] = $subordinate;
        }

        foreach ($this->processMedia($answer, $position) as $media)
        {
            $this->medias[] = $media;
        }

        foreach ($this->processContent($answer, $position) as $content)
        {
            $this->answer .= $content;
        }
        
        $infos = $DomElement->getElementsByTagName('info');
        
        $this->infos = array();
        
        foreach($infos as $i)
        {
            $position = $position+1;
            $info = new MathInfo($this->xmlpath);
            $info->loadFromXml($i, $position);
            $this->infos[] = $info;
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
        $data->answer = $this->answer;
        
        $this->id = $DB->insert_record($this->tablename, $data);
        
        foreach($this->infos as $info)
        {
            $info->saveIntoDb($info->position);
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
