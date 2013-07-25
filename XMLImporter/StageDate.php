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
 * This class represents all the date XML elements in the legacy document
 * (ie. files in the newXML) and the newly formed XML exported by the editor system
 * and it is called by Unit classes.  StageDate class inherits from the
 * abstract class Element and for all the methods inherited, read documents for Element class.
 * 
 * @TODO do not have modified dates..etc processed
 * 
 * @author Ga Young Kim
 */
class StageDate extends Element
{

    public $id;                 // database ID associated with the date element in msm_stage_date table
    public $compid;             // database ID associated with the date element in msm_compositor table
    public $position;           // integer that keeps track of order of elements
    public $dates = array();    // month/day/year associated with each date element

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_stage_date';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (date element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        date elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \StageDate
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $dates = $DomElement->getElementsByTagName('date');

        foreach ($dates as $key => $date)
        {
            $month = $this->getDomAttribute($date->getElementsByTagName('month'));
            if ($month < 10) // to format the date as YYYYMMDD
            {
                $month = '0' . $month;
            }

            $day = $this->getDomAttribute($date->getElementsByTagName('day'));
            if ($day < 10)// to format the date as YYYYMMDD
            {
                $day = '0' . $day;
            }

            $year = $this->getDomAttribute($date->getElementsByTagName('year'));

            $this->dates[] = $year . $month . $day;
        }
        return $this;
    }

    /**
     * This method inserts each instance of dates as a record in the msm_stage_date table.
     * The data inserted by this method is later retrieved by Unit class to display the creation date properly.
     * 
     * @global moodle_database $DB
     * @param int $position             integer that keeps track of order if elements
     * @param int $msmid                MSM instance ID
     * @param int $parentid             ID of the parent element from msm_compositor
     * @param int $siblingid            ID of the previous sibling element from msm_compositor
     * @param String $type              type of person element (ie. author/contributor/part of index.author)
     */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();
        foreach ($this->dates as $date)
        {
            $data->stagedate = $date;
            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        }
    }

}

?>
