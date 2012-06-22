<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Solution
 *
 * @author User
 */
class Solution extends Element
{

    public $position;
    public $content;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_solution';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->subordinates = array();
        $this->medias = array();

        $body = $DomElement->getElementsByTagName('solution.body')->item(0);

//        foreach ($bodys as $b)
//        {
        foreach ($this->processIndexAuthor($body, $position) as $indexauthor)
        {
            $this->indexauthors[] = $indexauthor;
        }

        foreach ($this->processIndexGlossary($body, $position) as $indexglossary)
        {
            $this->indexglossarys[] = $indexglossary;
        }

        foreach ($this->processIndexSymbols($body, $position) as $indexsymbol)
        {
            $this->indexsymbols[] = $indexsymbol;
        }
        foreach ($this->processSubordinate($body, $position) as $subordinate)
        {
            $this->subordinates[] = $subordinate;
        }

        foreach ($this->processMedia($body, $position) as $media)
        {
            $this->medias[] = $media;
        }

        foreach ($this->processContent($body, $position) as $content)
        {
            $this->content .= $content;
        }

//        }
    }

    function saveIntoDb($position)
    {
//        echo "solution save start";
//        $time = time();
//        print_object($time);
        
        global $DB;
        $data = new stdClass();

        $data->caption = $this->caption;

        if (!empty($this->content))
        {
            $data->solution_content = $this->content;
            $this->id = $DB->insert_record($this->tablename, $data);
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
