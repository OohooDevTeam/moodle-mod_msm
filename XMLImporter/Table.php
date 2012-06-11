<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Table
 *
 * @author User
 */
class Table extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_table';
    }

    //put your code here
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->string_id = $DomElement->getAttribute('id');
        $this->table_class = $DomElement->getAttribute('class');
        $this->table_summary = $DomElement->getAttribute('summary');
        $this->table_title = $DomElement->getAttribute('title');
        
        $this->content = array();
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();

        foreach ($this->processSubordinate($DomElement, $position)->subordinates as $subordinate)
        {
            $this->subordinates[] = $subordinate;
        }

        foreach ($this->processSubordinate($DomElement, $position)->indexauthors as $indexauthor)
        {
            $this->indexauthors[] = $indexauthor;
        }

        foreach ($this->processSubordinate($DomElement, $position)->indexglossarys as $indexglossary)
        {
            $this->indexglossarys[] = $subordinate;
        }

        foreach ($this->processSubordinate($DomElement, $position)->indexsymbols as $indexsymbol)
        {
            $this->indexsymbols[] = $subordinate;
        }

        foreach ($this->processSubordinate($DomElement, $position)->content as $content)
        {
            $this->content[] = $content;
        }
    }

}

?>
