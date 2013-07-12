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
 * This class represents al the cite XML elements in the legacy document
 * (ie. files in the newXML) and it is called by ImgArea, and Subordinate classes.
 * Cite class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Cite extends Element
{

    public $id;                      // database ID of current cite element in msm_cite
    public $compid;                  // database ID of current cite element in msm_compositor
    public $position;                // integer that keeps track of order if elements
    public $cite_label;              // label that is associated with this cite element
    public $caption;                 // title that is associated with this cite element
    public $items = array();         // Item objects that are assocaited with this cite element

    /**
     * constructor for this class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_cite';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (cite element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param moodle_database $DomElement           cite element
     * @param int $position                         integer that keeps track of order if elements
     * @return \Cite
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->cite_label = $DomElement->getAttribute('label');
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $items = $DomElement->getElementsByTagName('item');
        foreach ($items as $i)
        {
            $position = $position + 1;
            $item = new Item($this->xmlpath);
            $item->loadFromXml($i, $position);
            $this->items[] = $item;
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of cite element into
     * msm_cite database table.  It calls saveInfoDb method for Item class.
     * 
     * @global moodle_database $DB
     * @param int $position              integer that keeps track of order if elements
     * @param int $msmid                 MSM instance ID
     * @param int $parentid              ID of the parent element from msm_compositor
     * @param int $siblingid             ID of the previous sibling element from msm_compositor
     */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();
        $data->cite_label = $this->cite_label;
        $data->caption = $this->caption;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $elementPosition = array();
        foreach ($this->items as $key => $item)
        {
            $elementPosition['item' . '-' . $key] = $item->position;
        }

        asort($elementPosition);

        foreach ($elementPosition as $element => $value)
        {
            $itemString = explode('-', $element);

            $this->items[$itemString[1]]->saveIntoDb($this->items[$itemString[1]]->position, $msmid, $this->compid);
        }
    }

}

?>
