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
 * This class represents all the cell XML elements in the math.array elements in the legacy document
 * (ie. files in the newXML) and it is called by MathRow class. MathCell class inherits from the abstract class Element
 * and for all the methods inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class MathCell extends Element
{

    public $id;                             // database ID associated with this cell element in msm_math_cell table
    public $compid;                         // database ID associated with this cell element in msm_compositor table        
    public $position;                       // integer that keeps track of order of elements
    public $content;                        // content associated with this cell element
    public $colspan;                        // column span value (same function as HTML code)
    public $halign;                         // horizontal alignment value (ie. left, center or right) --> same as HTML code for table element
    public $valign;                         // vertical alignment value (ie. top, middle, bottom) --> same as HTML code for table element
    public $bgcolor;                        // background color of this cell
    public $fontcolor;                      // font color of this cell
    public $companion = array();            // Companion elements associated with this cell element
    public $subordinates = array();         // Subordinate elements associated with this cell element
    public $refchilds = array();            // Definition/Comment/Theorem/Unit objects that were associated with Companion element in cell element
    public $childs = array();               // MathInfo objects associated with this cell element

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */
    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_math_cell';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (cell element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        cell elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \MathCell
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->colspan = $DomElement->getAttribute('colspan');
        $this->halign = $DomElement->getAttribute('halign');
        $this->valign = $DomElement->getAttribute('valign');
        $this->bgcolor = $DomElement->getAttribute('bgcolor');
        $this->fontcolor = $DomElement->getAttribute('fontcolor');

        $this->companion = array(); // if the ref already exists inside db table, then store in here as id number
        $this->subordinates = array();
        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                $childname = $child->tagName;
                $doc = new DOMDocument;

                switch ($childname)
                {
                    case('math'): // cell has a math child element
                        foreach ($this->processSubordinate($child, $position) as $subordinate)
                        {
                            $this->subordinates[] = $subordinate;
                        }
                        foreach ($this->processContent($child, $position) as $content)
                        {
                            $this->content .= $content;
                        }
                        break;

                    case('text'):   // cell has just a text node as a child
                        $element = $doc->importNode($child, true);
                        $this->content .= $doc->saveXML($element);
                        break;

                    case('companion'): // cell has a companion child element
                        $position++;
                        $companion = new Companion();
                        $companion->loadFromXml($child, $position);
                        $this->companion[] = $companion;
                        break;
                }
            }
        }
         return $this;
    }

    /**
     * This method saves the extracted information from the XML files of cell element into
     * msm_math_cell database table.  It calls saveInfoDb method for Companion and Subordinate classes.
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
        $data->colspan = $this->colspan;
        $data->halign = $this->halign;
        $data->valign = $this->valign;
        $data->bgcolor = $this->bgcolor;
        $data->fontcolor = $this->fontcolor;
        $data->content = $this->content;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $elementPositions = array();
        $sibling_id = null;

        if (!empty($this->companion))
        {
            foreach ($this->companion as $key => $companion)
            {
                $elementPositions['companion' . '-' . $key] = $companion->position;
            }
        }

        if (!empty($this->subordinates))
        {
            foreach ($this->subordinates as $key => $subordinate)
            {
                $elementPositions['subordinate-' . $key] = $subordinate->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(subordinate.\d+)$/", $element) ? true : false):
                    $subordinateString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $subordinate = $this->subordinates[$subordinateString[1]];
                        $subordinate->saveIntoDb($subordinate->position, $msmid, $this->compid);
                        $sibling_id = $subordinate->compid;
                    }
                    else
                    {
                        $subordinate = $this->subordinates[$subordinateString[1]];
                        $subordinate->saveIntoDb($subordinate->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $subordinate->compid;
                    }
                    break;

                case(preg_match("/^(companion.\d+)$/", $element) ? true : false):
                    $companionString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $companion = $this->companion[$companionString[1]];
                        $companion->saveIntoDb($companion->position, $msmid, $this->compid);
                    }
                    else
                    {
                        $companion = $this->companion[$companionString[1]];
                        $companion->saveIntoDb($companion->position, $msmid, $this->compid, $sibling_id);
                    }
                    break;
            }
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the cell element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from Subordinate, Definition,
     * Comment, Theorem, Unit and MathInfo classes are also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current cell element in msm_cell table
     * @param int $compid                   database ID of the current cell element in msm_compositor table
     * @return \MathCell
     */
    function loadFromDb($id, $compid)
    {
        global $DB;

        $mathcellRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($mathcellRecord))
        {
            $this->compid = $compid;
            $this->colspan = $mathcellRecord->colspan;
            $this->halign = $mathcellRecord->halign;
            $this->valign = $mathcellRecord->valign;
            $this->bgcolor = $mathcellRecord->bgcolor;
            $this->fontcolor = $mathcellRecord->fontcolor;
            $this->content = $mathcellRecord->content;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');
       
        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            switch ($childtablename)
            {
                case('msm_comment'):
                    $comment = new MathComment();
                    $comment->loadFromDb($child->unit_id, $child->id);
                    $this->refchilds[] = $comment;
                    break;
                case('msm_def'):
                    $def = new Definition();
                    $def->loadFromDb($child->unit_id, $child->id);
                    $this->refchilds[] = $def;
                    break;
                case('msm_theorem'):
                    $theorem = new Theorem();
                    $theorem->loadFromDb($child->unit_id, $child->id);
                    $this->refchilds[] = $theorem;
                    break;

                // depending on what to do with showme/quiz, it maybe included here or not
                case('msm_unit'):
                    $unit = new Unit();
                    $unit->loadFromDb($child->unit_id, $child->id);
                    $this->refchilds[] = $unit;
                    break;

                case('msm_subordinate'):
                    $subordinate = new Subordinate();
                    $subordinate->loadFromDb($child->unit_id, $child->id);
                    $this->subordinates[] = $subordinate;
                    break;

                case('msm_info'):
                    $info = new MathInfo();
                    $info->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $info;
                    break;
            }
        }
        return $this;
    }

    /**
     * This method displays cell element like a cell element in table in HTML without borders.  MathArray data
     * associated as an ancestor of this cell would make the table element then each associated MathRow objects
     * would make the tr elements appened to the table element.  This method also calls displayhtml for 
     * MathInfo and Definition/Comment/Theorem/Unit classes for reference materials.
     * 
     * @param string $rowspan                 size of rows given by the rowspan property in MathRow class object
     * @param bool $isindex                 flag variable to indicate if this method was called by MathIndex object
     * @return string
     */
    function displayhtml($rowspan, $isindex = false)
    {
        $content = '';

        $content .= "<td class='matharraycell' colspan='" . $this->colspan . "' rowspan='" . $rowspan . "' align='" . $this->halign . "' valign='" . $this->valign . "'>";

        // if info exists then need to set up the dialog popup window, otherwise, just show the content

        if (empty($this->childs))
        {
            if (empty($this->content))
            {
                $content .= ' ';
            }
            else
            {
//                $content .= $this->displayContent($this, $this->content);
                $content .= $this->content;
            }
        }
        else
        {
            if (!$isindex)
            {
                $content .= "<a id='hottag-" . $this->compid . "' class='hottag' onmouseover='popup(" . $this->compid . ")'>";
                $content .= $this->content;
                $content .= "</a>";

                $content .= '<div id="dialog-' . $this->compid . '" class="dialogs" title="' . $this->childs[0]->caption . '">';
                $content .= $this->childs[0]->info_content;
                $content .= "</div>";
            }
            else
            {
                 $content .= $this->content;
            }
        }

        $content .= "</td>";

        return $content;
    }

}

?>
