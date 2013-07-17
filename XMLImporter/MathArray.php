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
 * This class represents all the math.array XML elements in the legacy document
 * (ie. files in the newXML) and the newly formed XML exported by the editor system
 * and it is called by AnswerShowme, Block, Comment, Definition,  InContent, PartTheorem, ProofBlock,  
 * Showme, StatementTheorem, Table and Unit classes. MathArray class inherits from the abstract class Element
 * and for all the methods inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class MathArray extends Element
{

    public $id;
    public $compid;
    public $position;
    public $string_id;
    public $no_column;
    public $rows = array();

    /**
     * constructor for this class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */
    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_math_array';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (math.array element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        math.array elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \MathArray
     */
    public function loadFromXml($DomElement, $position = '')
    {

        $this->position = $position;

        $this->string_id = $DomElement->getAttribute('id');
        $this->no_column = $DomElement->getAttribute('column'); //specifies number of column

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
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of math.array element into
     * msm_math_array database table.  It calls saveInfoDb method for MathRow classes.
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
        $data->string_id = $this->string_id;
        $data->no_column = $this->no_column;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $elementPosition = array();
        $sibling_id = null;
        foreach ($this->rows as $key => $row)
        {
            $elementPosition['row' . '-' . $key] = $row->position;
        }

        asort($elementPosition);

        foreach ($elementPosition as $element => $value)
        {
            $rowString = explode('-', $element);

            if (empty($sibling_id))
            {
                $this->rows[$rowString[1]]->saveIntoDb($this->rows[$rowString[1]]->position, $msmid, $this->compid);
                $sibling_id = $this->rows[$rowString[1]]->compid;
            }
            else
            {
                $this->rows[$rowString[1]]->saveIntoDb($this->rows[$rowString[1]]->position, $msmid, $this->compid, $sibling_id);
                $sibling_id = $this->rows[$rowString[1]]->compid;
            }
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the math.array element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from MathRow class is also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current math.array element in msm_math_array table
     * @param int $compid                   database ID of the current math.array element in msm_compositor table
     * @return \MathArray
     */
    function loadFromDb($id, $compid)
    {
        global $DB;

        $matharrayRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($matharrayRecord))
        {
            $this->no_column = $matharrayRecord->no_column;
            $this->compid = $compid;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');
        $this->rows = array();

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            if ($childtablename == 'msm_math_row')
            {
                $mathrow = new MathRow();
                $mathrow->loadFromDb($child->unit_id, $child->id);
                $this->rows[] = $mathrow;
            }
        }

        return $this;
    }

    /**
     * This method produces an HTML code to display the retrieved data from method above in a table format
     * without borders and also calls the same method in MathRow class to display the data.
     * 
     * @param bool $isindex             flag variable to indicate if this method was called by MathIndex object
     * @return string
     */
    function displayhtml($isindex = false)
    {
        $content = '';

        $content .= "<table border='0' class='matharray'>";

        foreach ($this->rows as $row)
        {
            $content .= $row->displayhtml($isindex = false);
        }

        $content .= "</table>";

        return $content;
    }

}

?>
