<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Compositor
 *
 * @author User
 */
class Compositor
{

    function __construct()
    {
        $this->tablename = "msm_compositor";
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $instanceid 
     */
    function loadFromDb($instanceid, $position)
    {
        global $DB;

        $rootElement = $DB->get_record($this->tablename, array('msm_id' => $instanceid));

        if (!empty($rootElement))
        {
            $this->unit = new stdClass();

            $tablename = $DB->get_record('msm_table_collection', array('id' => $rootElement->table_id))->tablename;

            switch ($tablename)
            {
                case('msm_unit'):
                    $unitrecord = $DB->get_record('msm_unit', array('id' => $rootElement->unit_id));

                    $this->unit->title = $unitrecord->title;
                    $this->unit->creationdate = $unitrecord->creationdate;
                    $this->unit->last_revision_date = $unitrecord->last_revision_date;
                    $this->unitp->position = $position;
                    $position++;
            }
        }
        
        return $this;
    }

    function displayhtml()
    {
        $creationyear = substr($this->unit->creationdate, 0, 4);
        $creationmonth = substr($this->unit->creationdate, 4,-2);
        $creationdate = substr($this->unit->creationdate, 6,8);
        
        $revisionyear = substr($this->unit->last_revision_date, 0, 4);
        $revisionmonth = substr($this->unit->last_revision_date, 4,-2);
        $revisiondate = substr($this->unit->last_revision_date, 6,8);
        
        $content = '';
        $content .= "<div class='title'>";
        $content .= $this->unit->title;
        $content .= "</div>";
        $content .= "<div class='date'>";
        $content .= "created on: ";
        $content .= $creationyear . "-" . $creationmonth . "-" . $creationdate . "<br />";
        $content .= "last revised on: ";
        $content .= $revisionyear . "-" . $revisionmonth . "-" . $revisiondate . "<br />";
        $content .= "</div>";

        return $content;
    }

}

?>
