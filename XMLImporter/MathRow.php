<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                      **
 * @subpackage  msm                                                      **
 * @name        msm                                                      **
 * @copyright   University of Alberta                                    **
 * @link        http://ualberta.ca                                       **
 * @author      Ga Young Kim                                             **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * *************************************************************************
 * ************************************************************************ */

/**
 * This class represents all the row XML elements in the math.array elements in the legacy document
 * (ie. files in the newXML) and it is called by MathArray class. MathRow class inherits from the abstract class Element
 * and for all the methods inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class MathRow extends Element
{

    public $id;                     // database ID associated with this row element in msm_math_row table
    public $compid;                 // database ID associated with this row element in msm_compositor table
    public $position;               // integer that keeps track of order of elements
    public $rowspan;                // row span value (same function as HTML code)
    public $cells = array();        // MathCell objects associated with this row element

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_math_row';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (row element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        row elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \MathRow
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->rowspan = $DomElement->getAttribute('rowspan');

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == 'cell')
                {
                    $position = $position + 1;
                    $cell = new MathCell($this->xmlpath);
                    $cell->loadFromXml($child, $position);
                    $this->cells[] = $cell;
                }
            }
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of row element into
     * msm_math_row database table.  It calls saveInfoDb method for MathCell classes.
     * 
     * @global moodle_databse $DB
     * @param int $position              integer that keeps track of order if elements
     * @param int $msmid                 MSM instance ID
     * @param int $parentid              ID of the parent element from msm_compositor
     * @param int $siblingid             ID of the previous sibling element from msm_compositor
     */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();
        $data->rowspan = $this->rowspan;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $elementPosition = array();
        $sibling_id = null;

        foreach ($this->cells as $key => $cell)
        {
            $elementPosition['cell' . '-' . $key] = $cell->position;
        }

        asort($elementPosition);

        foreach ($elementPosition as $element => $value)
        {
            $cellString = explode('-', $element);

            if (empty($sibling_id))
            {
                $this->cells[$cellString[1]]->saveIntoDb($this->cells[$cellString[1]]->position, $msmid, $this->compid);
                $sibling_id = $this->cells[$cellString[1]]->compid;
            }
            else
            {
                $this->cells[$cellString[1]]->saveIntoDb($this->cells[$cellString[1]]->position, $msmid, $this->compid, $sibling_id);
                $sibling_id = $this->cells[$cellString[1]]->compid;
            }
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the row element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from MathCell class is also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current cell element in msm_cell table
     * @param int $compid                   database ID of the current cell element in msm_compositor table
     * @return \MathRow
     */
    function loadFromDb($id, $compid)
    {
        global $DB;

        $mathrowRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($mathrowRecord))
        {
            $this->compid = $compid;
            $this->rowspan = $mathrowRecord->rowspan;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            if ($childtablename == 'msm_math_cell')
            {
                $mathcell = new MathCell();
                $mathcell->loadFromDb($child->unit_id, $child->id);
                $this->cells[] = $mathcell;
            }
        }

        return $this;
    }

    /**
     * This method displays row element like a row element in table in HTML without borders.  MathArray object
     * that is the parent of this row element creates the table element then this class
     * adds tr elements each time it is called and apends it to the table element.  This method also calls displayhtml
     * in the MathCell class.
     * 
     * @param bool $isindex                 flag variable to indicate if this method was called by MathIndex object
     * @return string
     */
    function displayhtml($isindex = false)
    {
        $content = '';

        $content .= "<tr class='matharrayrow' align='center'>";

        foreach ($this->cells as $column)
        {
            $content .= $column->displayhtml($this->rowspan, $isindex);
        }

        $content .= "</tr>";

        return $content;
    }

}

?>
