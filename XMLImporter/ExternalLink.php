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
 * This class represents al the external.link XML elements in the legacy document
 * (ie. files in the newXML) and the newly formed XML exported by the editor system
 * and it is called by ImgArea/Subordinate classes.
 * External Link class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class ExternalLink extends Element
{

    public $id;                         // database ID of the current external link element in msm_external_link
    public $compid;                     // database ID of the current external link element in msm_compositor
    public $position;                   // integer that keeps track of order of elements
    public $href;                       // URL of the page that this external link is linked to
    public $type;                       // type of the document that this external link is linking to
    public $target;                     // specifying where to open the link(basically same function as HTML)
    public $infos = array();            // MathInfo objects associated with this externa link
                                        // (it is the extra info about the external link shown in form of a popup)

    /**
     * constructor for this class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */
    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_external_link';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (external.link element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        external.link element
     * @param int $position                 integer that keeps track of order if elements
     * @return \ExternalLink
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->href = $DomElement->getAttribute('href');
        $this->type = $DomElement->getAttribute('type');
        $this->target = $DomElement->getAttribute('target');

        $infos = $DomElement->getElementsByTagName('info');

        foreach ($infos as $i)
        {
            $position = $position + 1;
            $info = new MathInfo($this->xmlpath);
            $info->loadFromXml($i, $position);
            $this->infos[] = $info;
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of external.link element and its associated child elements into
     * their respective database tables.  It calls saveInfoDb method for MathInfo class.
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
        $data->href = $this->href;
        $data->type = $this->type;
        $data->target = $this->target;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $elementPosition = array();
        foreach ($this->infos as $key => $info)
        {
            $elementPosition['info' . '-' . $key] = $info->position;
        }

        asort($elementPosition);

        foreach ($elementPosition as $element => $value)
        {
            $infoString = explode('-', $element);

            $this->infos[$infoString[1]]->saveIntoDb($this->infos[$infoString[1]]->position, $msmid, $this->compid);
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the external.link element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from MathInfo class is also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                   ID of the current external.link element from msm_external_link
     * @param int $compid               ID of the current external.link element from msm_compositor
     * @return \ExternalLink
     */
    function loadFromDb($id, $compid)
    {
        global $DB;

        $linkCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $linkRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($linkRecord))
        {
            $this->compid = $compid;
            $this->href = $linkRecord->href;
            $this->type = $linkRecord->type;
            $this->target = $linkRecord->target;
        }

        $childElements = $DB->get_records('msm_compositor', array('msm_id' => $linkCompRecord->msm_id, 'parent_id' => $compid), 'prev_sibling_id');

        $this->infos = array();

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            if ($childtablename == 'msm_info')
            {
                $info = new MathInfo();
                $info->loadFromDb($child->unit_id, $child->id);
                $this->infos[] = $info;
            }
        }

        return $this;
    }

    /**
     * This method produces an HTML code to display the anchored element that this
     * external link is associated with.  Then when processing content for display by
     * displayContent function in Element class, the displayContent function calls the 
     * displayhtml function in MathInfo to generate HTML for the popup information.
     * 
     * @param bool $isindex             flag variable to indicate if this method was called by MathIndex object
     * @return string
     */
    function displayhtml($isindex = false)
    {
        $content = '';

        $content .= "<a target='" . $this->target . "' href='" . $this->href . "'>";
        $content .= $this->href;
        $content .= "</a>";

        return $content;
    }

}

?>
