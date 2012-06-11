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
         $this->suboridnates = array();

        $this->string_id = $DomElement->getAttribute('id');
        $this->no_column = $DomElement->getAttribute('column'); //specifies number of column

        $position = $position + 1;

//        $parabodys = $DomElement->getElementsByTagName('para.body');
//
//        foreach ($parabodys as $parabody)
//        {
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
//        }
    }

}

?>
