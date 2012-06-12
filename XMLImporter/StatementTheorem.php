<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Statement
 *
 * @author User
 */
class StatementTheorem extends Element
{

    public $position;
    public $content;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_statement_theorem';
    }

    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
       // $this->content = array();
        $this->part_theorems = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->subordinates = array();

        foreach ($DomElement->childNodes as $key => $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == 'part.theorem')
                {
                    $position = $position + 1;
                    $parttheorem = new PartTheorem($this->xmlpath);
                    $parttheorem->loadFromXml($child, $position);
                    $this->part_theorems[] = $parttheorem;
                }
                else
                {
                    foreach ($this->processSubordinate($child, $position)->subordinates as $subordinate)
                    {
                        $this->subordinates[] = $subordinate;
                    }

                    foreach ($this->processSubordinate($child, $position)->indexauthors as $indexauthor)
                    {
                        $this->indexauthors[] = $indexauthor;
                    }

                    foreach ($this->processSubordinate($child, $position)->indexglossarys as $indexglossary)
                    {
                        $this->indexglossarys[] = $indexglossary;
                    }

                    foreach ($this->processSubordinate($child, $position)->indexsymbols as $indexsymbol)
                    {
                        $this->indexsymbols[] = $indexsymbol;
                    }

                    foreach ($this->processSubordinate($child, $position)->content as $content)
                    {
                        $this->content .= $content;
                    }
                }
            }
        }
        
    }   
    
    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();
        
        if(!empty($this->content))
        {
            $data->statement_content = $this->content;
            $this->id = $DB->insert_record($this->tablename, $data);
        }
        
        foreach($this->part_theorems as $key=>$part_theorem)
        {
            $part_theorem->saveIntoDb($part_theorem->position);
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
