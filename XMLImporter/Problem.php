<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Problem
 *
 * @author User
 */
class Problem extends Element
{

    public $position;
    public $content;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_problem';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->subordinates = array();

        $this->medias = array();

        //$this->content = array();

        $problembody = $DomElement->getElementsByTagName('problem.body')->item(0);
        $doc = new DOMDocument();
//
//        foreach ($problembodys as $prob)
//        {
            foreach ($this->processIndexAuthor($problembody, $position) as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processIndexGlossary($problembody, $position) as $indexglossary)
            {
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processIndexSymbols($problembody, $position) as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }
            foreach ($this->processSubordinate($problembody, $position) as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processMedia($problembody, $position) as $media)
            {
                $this->medias[] = $media;
            }

            foreach ($this->processContent($problembody, $position) as $content)
            {
                $this->content .= $content;
            }
//        }
    }

    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();

        $data->textcaption = $this->textcaption;
        $data->caption = $this->caption;

        if (!empty($this->content))
        {
//            foreach ($this->content as $content)
//            {
            echo "problem content not empty";
            print_object($this->content);
                $data->problem_content = $this->content;
                $this->id = $DB->insert_record($this->tablename, $data);
//            }
        }
        else
        {
             echo "problem content is empty";
            $this->id = $DB->insert_record($this->tablename, $data);
            print_object($this->id);
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
