<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MathArray
 *
 * @author User
 */
class MathArray extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_math_array';
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
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->subordinates = array();

        $this->string_id = $DomElement->getAttribute('id');
        $this->no_column = $DomElement->getAttribute('column'); //specifies number of column

        $position = $position + 1;

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
            $this->indexglossarys[] = $indexglossary;
        }

        foreach ($this->processSubordinate($DomElement, $position)->indexsymbols as $indexsymbol)
        {
            $this->indexsymbols[] = $indexsymbol;
        }

        foreach ($this->processSubordinate($DomElement, $position)->content as $content)
        {
            $this->content[] = $content;
        }
    }

    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();
        $data->string_id = $this->string_id;
        $data->no_column = $this->no_column;

        if (!empty($this->content))
        {
            foreach ($this->content as $content)
            {
                $data->math_array_content = $content;
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
    }

}

?>
