<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Step
 *
 * @author User
 */
class Step extends Element
{

    public $position;
    public $content;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_step';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $this->partref = $DomElement->getAttribute('partref');
        
        $this->pilots = array();
        $pilots = $DomElement->getElementsByTagName('pilot');        
        foreach($pilots as $p)
        {
            $position = $position+1;
            $pilot = new Pilot($this->xmlpath);
            $pilot->loadFromXml($p, $position);
            $this->pilots[] = $pilot;
        }
        
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();

        $step_bodys = $DomElement->getElementsByTagName('step.body');
        
        foreach ($step_bodys as $stb)
        {
            foreach ($this->processIndexAuthor($stb, $position) as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processIndexGlossary($stb, $position) as $indexglossary)
            {
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processIndexSymbols($stb, $position) as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }
            foreach ($this->processSubordinate($stb, $position) as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processContent($stb, $position) as $content)
            {
                $this->content .= $content;
            }
        }
    }
    
    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();
        
        $data->partref = $this->partref;
        $data->caption = $this->caption;
        $data->content = $this->content;
        
        $this->id = $DB->insert_record($this->tablename, $data);
//        
       foreach($this->pilots as $pilot)
       {
           $pilot->saveIntoDb($pilot->position);
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
    }

}

?>
