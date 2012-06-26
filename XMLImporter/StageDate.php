<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

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
    function saveIntoDb($position)
    {        
        global $DB;
        $data = new stdClass();
        foreach($this->dates as $date)
        {
            $data->stagedate = $date;
            $this->id = $DB->insert_record($this->tablename, $data);
        }
    }

}

?>
