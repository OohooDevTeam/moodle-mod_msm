<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MathRow
 *
 * @author User
 */
class MathRow extends Element
{

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_math_row';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        
        $this->rowspan = $DomElement->getAttribute('rowspan');
        
        $this->cells = array();
        
        foreach($DomElement->childNodes as $child)
        {
            if($child->nodeType == XML_ELEMENT_NODE)
            {
                if($child->tagName == 'cell')
                {
                    $position = $position+1;
                    $cell = new MathCell($this->xmlpath);
                    $cell->loadFromXml($child, $position);
                    $this->cells[] = $cell;
                }
            }
        }
    }
    
    function saveIntoDb($position, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();
        $data->rowspan = $this->rowspan;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
        
        $elementPosition = array();
        $sibling_id = null;
        
        foreach ($this->cells as $key => $cell)
        {
            $elementPosition['cell' . '-' . $key] = $cell->position;
        }

        asort($elementPosition);

        foreach ($elementPosition as $element => $value)
        {
            $cellString = split('-', $element);

            if(empty($sibling_id))
            {
               $this->cells[$cellString[1]]->saveIntoDb($this->cells[$cellString[1]]->position, $this->compid);
               $sibling_id = $this->cells[$cellString[1]]->compid;
            }
            else
            {
                $this->cells[$cellString[1]]->saveIntoDb($this->cells[$cellString[1]]->position, $this->compid, $sibling_id);
               $sibling_id = $this->cells[$cellString[1]]->compid;
            }
        }
    }
}

?>
