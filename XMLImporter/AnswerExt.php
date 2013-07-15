<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                       **
 * @subpackage  msm                                                       **
 * @name        msm                                                       **
 * @copyright   University of Alberta                                     **
 * @link        http://ualberta.ca                                        **
 * @author      Ga Young Kim                                              **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************* */

/**
 * This class represents al the answer.ext XML elements in the legacy document
 * (ie. files in the newXML) and it is called by Example class.
 * AnswerExt class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class AnswerExt extends Element
{

    public $id;                         // database ID of current answer.ext element in msm_ext
    public $compid;                     // database ID of current answer.ext element in msm_compositor
    public $position;                   // integer that keeps track of order of elements
    public $type;                       // type associated with answer.ext element (eg. solution)
    public $version;                    // version associated with answer.ext element
    public $caption;                    // title associated with answer.ext element
    public $ext_name;                   // name associated with answer.ext element (from answer.ext, this value would be answer.ext)
    public $steps = array();            // Step object associated with this answer.ext element

    /**
     * constructor for this class
     * 
     * @param string $xmlpath           filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_ext'; // this database table has data from AnswerExt, SolutionExt and ApproachExt classes
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (answer.ext element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        answer.ext element in XML file
     * @param int $position                 integer that keeps track of order if elements
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->type = $DomElement->getAttribute('type');
        $this->version = $DomElement->getAttribute('version');
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));
        $this->ext_name = $DomElement->tagName;

        $steps = $DomElement->getElementsByTagName('step');
        foreach ($steps as $s)
        {
            $position = $position + 1;
            $step = new Step($this->xmlpath);
            @$step->loadFromXml($s, $position);
            $this->steps[] = $step;
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of answer.ext element into
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

        $data->ext_type = $this->type;
        $data->ext_version = $this->version;
        $data->caption = $this->caption;
        $data->ext_content = null;
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
