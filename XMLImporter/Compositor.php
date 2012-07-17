<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("Element.php");
require_once("Person.php");
require_once("Unit.php");

/**
 * Description of Compositor
 *
 * @author User
 */
class Compositor
{

    public $unit;

//    public $theorem;

    function __construct()
    {
        $this->tablename = "msm_compositor";
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $instanceid 
     */
    function loadFromDb($instanceid)
    {
        global $DB;

        $this->authors = array();
        // root element has no parent or siblling and it needs to be inside the same instance of msm mmodule
        $rootElement = $DB->get_record($this->tablename, array('msm_id' => $instanceid, 'parent_id' => null, 'prev_sibling_id' => null));

        if (!empty($rootElement))
        {
            // searching the name of the table using table_id field in compositor table
            $tablename = $DB->get_record('msm_table_collection', array('id' => $rootElement->table_id))->tablename;

            switch ($tablename)
            {
                //root element is an object of Unit class
                case('msm_unit'):
                    $unitid = $DB->get_record('msm_unit', array('id' => $rootElement->unit_id))->id;

                    $unit = new Unit();
                    $unit->loadFromDb($unitid, $rootElement->id);
                    $this->unit = $unit;
                    break;
            }        
            
        }

        return $this;
    }

    function displayhtml()
    {
        $content = '';

        $content .= $this->unit->displayhtml();

        return $content;
    }

}

?>
