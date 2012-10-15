<?php

/**
**************************************************************************
**                              MSM                                     **
**************************************************************************
* @package     mod                                                      **
* @subpackage  msm                                                      **
* @name        msm                                                      **
* @copyright   University of Alberta                                    **
* @link        http://ualberta.ca                                       **
* @author      Ga Young Kim                                             **
* @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
**************************************************************************
**************************************************************************/

/**
 * Description of StageDate
 *
 * @author User
 */
class StageDate extends Element
{
    
    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_stage_date';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        
        $this->dates = array();
        
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
    }
    
    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {        
        global $DB;
        $data = new stdClass();
        foreach($this->dates as $date)
        {
            $data->stagedate = $date;
            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        }
    }
    

}

?>
