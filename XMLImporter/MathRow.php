<?php

/**
**************************************************************************
**                              MSM                                     **
**************************************************************************
* @package     mod                                                      **
* @subpackage  msm                                                      **
* @name        msm                                                      **
* @copyright   University of Alberta                                    **
* @link        http://ualberta.ca                                       **
* @author      Ga Young Kim                                             **
* @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
**************************************************************************
**************************************************************************/

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
    
    function loadFromDb($id, $compid)
    {
        global $DB;
        
        $mathrowRecord = $DB->get_record($this->tablename, array('id'=>$id));
        
        if(!empty($mathrowRecord))
        {
            $this->compid = $compid;
            $this->rowspan = $mathrowRecord->rowspan;
        }
        
        $childElements = $DB->get_records('msm_compositor', array('parent_id'=>$compid), 'prev_sibling_id');
        $this->childs = array();
        
        foreach($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id'=>$child->table_id))->tablename;
            
            if($childtablename == 'msm_math_cell')
            {
                $mathcell = new MathCell();
                $mathcell->loadFromDb($child->unit_id, $child->id);
                $this->childs[] = $mathcell;
            }
        }
        
        return $this;
    }
    
    function displayhtml()
    {
        $content = '';
        
        $content .= "<tr class='matharrayrow' align='center'>";
        
        foreach($this->childs as $column)
        {
            $content .= $column->displayhtml($this->rowspan);
        }
        
        $content .= "</tr>";        
        
        return $content;
    }
}

?>
