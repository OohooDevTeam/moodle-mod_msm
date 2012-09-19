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

        $this->string_id = $DomElement->getAttribute('id');
        $this->no_column = $DomElement->getAttribute('column'); //specifies number of column

        $this->rows = array();

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == 'row')
                {
                    $position = $position + 1;
                    $row = new MathRow($this->xmlpath);
                    $row->loadFromXml($child, $position);
                    $this->rows[] = $row;
                }
            }
        }
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();
        $data->string_id = $this->string_id;
        $data->no_column = $this->no_column;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
        
        $elementPosition = array();
        $sibling_id = null;
        foreach ($this->rows as $key => $row)
        {
            $elementPosition['row' . '-' . $key] = $row->position;
        }

        asort($elementPosition);

        foreach ($elementPosition as $element => $value)
        {
            $rowString = split('-', $element);

            if(empty($sibling_id))
            {
               $this->rows[$rowString[1]]->saveIntoDb($this->rows[$rowString[1]]->position, $this->compid);
               $sibling_id = $this->rows[$rowString[1]]->compid;
            }
            else
            {
                $this->rows[$rowString[1]]->saveIntoDb($this->rows[$rowString[1]]->position, $this->compid, $sibling_id);
               $sibling_id = $this->rows[$rowString[1]]->compid;
            }
           
        }

    }
    
    function loadFromDb($id, $compid)
    {
        global $DB;
        
        $matharrayRecord = $DB->get_record($this->tablename, array('id'=>$id));
        
        if(!empty($matharrayRecord))
        {
            $this->no_column = $matharrayRecord->no_column;
            $this->compid = $compid;
        }
        
        $childElements = $DB->get_records('msm_compositor', array('parent_id'=>$compid), 'prev_sibling_id');
        $this->rows = array();
        
        foreach($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id'=>$child->table_id))->tablename;
            
            if($childtablename == 'msm_math_row')
            {
                $mathrow = new MathRow();
                $mathrow->loadFromDb($child->unit_id, $child->id);
                $this->rows[] = $mathrow;
            }
        }
        
        return $this;
    }

    function displayhtml($standalone)
    {
        $content = '';
        
        $content .= "<table border='0' class='matharray'>";
        
        foreach($this->rows as $row)
        {
            $content .= $row->displayhtml($standalone);
        }
        
        $content .= "</table>";
        
        return $content;
    }
}

?>
