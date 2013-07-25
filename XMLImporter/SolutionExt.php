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
 * This class represents all the solution.ext XML elements in the legacy document
 * (ie. files in the newXML) and it is called by ApproachExt classes and it 
 * is used to give further information on the possible solutions for different approaches.
 * SolutionExt class inherits from the abstract class Element and for all  the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class SolutionExt extends Element
{

    public $id;                 // database ID associated with the solution.ext element in msm_ext table
    public $compid;             // database ID associated with the solution.ext element in msm_compositor table
    public $position;           // integer that keeps track of order of elements
    public $caption;            // title element associated with the solution.ext element
    public $ext_name;           // string that indicates what type of extention element it is in database
                                // (eg. can be solution/Answer/Appoarch)
    public $steps = array();    // Step objects associated with the solution.ext element

    /**
     * constructor for the instace of this class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_ext';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (solution.ext element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        solution.ext DOMElement
     * @param int $position                 integer that keeps track of order if elements
     * @return \SolutionExt
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));
        $this->ext_name = $DomElement->tagName;

        $steps = $DomElement->getElementsByTagName('step');
        foreach ($steps as $s)
        {
            $position = $position + 1;
            $step = new Step($this->xmlpath);
            $step->loadFromXml($s, $position);
            $this->steps[] = $step;
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of solution.ext element into
     * msm_ext database table.  It calls saveInfoDb method for Step class.
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
        $data->caption = $this->caption;
        $data->ext_name = $this->ext_name;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $elementPosition = array();
        foreach ($this->steps as $key => $step)
        {
            $elementPosition['step' . '-' . $key] = $step->position;
        }

        asort($elementPosition);

        foreach ($elementPosition as $element => $value)
        {
            $stepString = explode('-', $element);

            $this->steps[$stepString[1]]->saveIntoDb($this->steps[$stepString[1]]->position, $msmid, $this->compid);
        }
    }

}

?>
