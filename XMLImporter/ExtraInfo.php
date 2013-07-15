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
 * This class represents any of the acknoewledgments, preface, trailer, summary and/or historical.notes XML elements
 * in the legacy document (ie. files in the newXML) and the newly formed XML exported by the
 * editor system and it is called by Unit class. ExtraInfo class inherits from the abstract
 * class Element and for all the methods inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class ExtraInfo extends Element
{

    public $id;                         // database ID of any of the elements represented 
                                        // by the extrainfo class in msm_extra_info table
    public $compid;                     // database ID of any of the elements represented 
                                        // by the extrainfo class in msm_compositor table
    public $position;                   // integer that keeps track of order of elements   
    public $name;                       // specifying which one of the elements (mentioned above in
                                        // class description)represented by extrainfo class was in the XML file
    public $blocks = array();           // Block object associated with this extrainfo element 

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_extra_info';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (any one of the elements associated
     * with extrainfo in this case) and extract needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        one of the extrainfo elements(eg. preface, historical.notes, summary, trailer, acknoewledgements)
     * @param int $position                 integer that keeps track of order if elements
     * @return \Definition
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $nameofElement = $DomElement->tagName;
        switch ($nameofElement)
        {
            case ('acknowledgements'):
                $this->name = 'Acknowledgements';
                break;
            case('preface'):
                $this->name = 'Preface';
                break;
            case('trailer'):
                $this->name = 'Trailer';
                break;
            case('summary'):
                $this->name = 'Summary';
                break;
            case('historical.notes'):
                $this->name = 'Historical';
                break;
        }

        $blocks = $DomElement->getElementsByTagName("block");
        foreach ($blocks as $b)
        {
            $block = new Block();
            $block->loadFromXml($b);
            $this->blocks[] = $block;
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of any of the elements associated with extrainfo object into
     * msm_extra_info database table.  It calls saveInfoDb method for Block class.
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
        $data->extra_info_name = $this->name;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $elementPositions = array();
        $sibling_id = null;

        if (!empty($this->blocks))
        {
            foreach ($this->blocks as $key => $block)
            {
                $elementPositions["block-$key"] = $block->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(block.\d+)$/", $element) ? true : false):
                    $blockString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $block = $this->blocks[$blockString[1]];
                        $block->saveIntoDb($block->position, $msmid, $this->compid);
                        $sibling_id = $block->compid;
                    }
                    else
                    {
                        $block = $this->blocks[$blockString[1]];
                        $block->saveIntoDb($block->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $block->compid;
                    }
                    break;
            }
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the extrainfo element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from Block class is also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current element associated with extrainfo class in msm_extra_info table
     * @param int $compid                   database ID of the current element associated with extrainfo class in msm_compositor table
     * @return \ExtraInfo
     */
    function loadFromDb($id, $compid)
    {
        global $DB;

        $extrainfoCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $extrainfoRecord = $DB->get_record('msm_extra_info', array('id' => $id));

        if (!empty($extrainfoRecord))
        {
            $this->extra_info_name = $extrainfoRecord->extra_info_name;
        }

        $childElements = $DB->get_records('msm_compositor', array('msm_id' => $extrainfoCompRecord->msm_id, 'parent_id' => $compid), 'prev_sibling_id');

        $this->blocks = array();

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            if ($childtablename->tablename == "msm_block")
            {
                $block = new Block();
                $block->loadFromDb($child->unit_id, $child->id);
                $this->blocks[] = $block;
            }
        }

        return $this;
    }

    /**
     * This method produces an HTML code to display the retrieved data from method above and
     * also calls the same method in Block class to display the data from these classes.
     * 
     * @param bool $isindex             flag variable to indicate if this method was called by MathIndex object
     * @return string
     */
    function displayhtml($isindex = false)
    {
        $content = '';
        $content .= "<div class='extrainfo'>";

        $content .= "<span class='extrainfoname'>" . $this->extra_info_name . "</span>";

        $content .= "<div class='mathcontent'>";
        foreach ($this->blocks as $block)
        {
            $content .= $block->displayhtml();
        }
        $content .= "</div>";

        $content .= "</div>";
        return $content;
    }

}

?>
