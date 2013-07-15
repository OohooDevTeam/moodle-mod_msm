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
 *  This class represents all the intro XML elements in the legacy document
 * (ie. files in the newXML) and the newly formed XML exported by the editor system
 * and it is called by Unit class. Intro class inherits from the abstract class Element
 * and for all the methods inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Intro extends Element
{

    public $id;                     // database ID associated with current intro element in msm_intro
    public $compid;                 // database ID associated with current intro element in msm_compositor
    public $position;               // integer that keeps track of order of elements
    public $string_id;              // unique identifier for this intro either user-defined(legacy material) or equal to compid above(new XML)
    public $caption;                // title associated with this intro element
    public $blocks = array();       // Block object associated with this intro

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_intro';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (intro element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        intro elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \Intro
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->string_id = $DomElement->getAttribute('id');
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $blocks = $DomElement->getElementsByTagName('block');

        foreach ($blocks as $key => $b)
        {
            $position = $position + 1;
            $block = new Block($this->xmlpath);
            // to make sure that the first block caption goes to intro caption instead of going to the block
            if ($key == 0)
            {
                $block->loadFromXml($b, $position, "intro-$key");
            }
            else
            {
                $block->loadFromXml($b, $position);
            }
            $this->blocks[] = $block;
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of intro element into
     * msm_intro database table.  It calls saveInfoDb method for Block class.
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
        $data->intro_caption = $this->caption;

        $this->id = $DB->insert_record($this->tablename, $data);

        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $sibling_id = 0;
        foreach ($this->blocks as $key => $block)
        {
            $block->saveIntoDb($position, $msmid, $this->compid, $sibling_id);
            $sibling_id = $block->compid;
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the intro element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from Block classes are also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current intro element in msm_intro table
     * @param int $compid                   database ID of the current intro element in msm_compositor table
     * @return \Intro
     */
    function loadFromDb($id, $compid)
    {
        global $DB;

        $introrecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($introrecord))
        {
            $this->compid = $compid;
            $this->id = $introrecord->id;
            $this->caption = $introrecord->intro_caption;

            $childElements = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');

            foreach ($childElements as $child)
            {
                $childTable = $DB->get_record("msm_table_collection", array('id' => $child->table_id));

                // these conditional statements are to accomodate for the difference in db structure between legacy material import and the newly
                // exported materials --> only a temporary solution till the legacy material importer is fixed
                if ($childTable->tablename == 'msm_block')
                {
                    $block = new Block();
                    $block->loadFromDb($child->unit_id, $child->id); //this should be compositor id
                    $this->blocks[] = $block;
                }
                else
                {
                    $block = new Block();
                    $block->loadFromDb('', $child->id); //this should be compositor id
                    $this->blocks[] = $block;
                }
            }

            if (empty($childElements))
            {
                $block = new Block();
                $block->loadFromDb('', $compid); //this should be compositor id
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

        if (!empty($this->caption))
        {
            $content .= "<h3>$this->caption</h3>";
        }

        foreach ($this->blocks as $key => $block)
        {
            if ($key == 0)
            {
                $content .= $block->displayhtml($isindex, true);
            }
            else
            {
                $content .= $block->displayhtml($isindex);
            }
        }


        return $content;
    }

}

?>
