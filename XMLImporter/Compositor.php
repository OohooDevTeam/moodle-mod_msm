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
    function loadAndDisplay($parentid, $prevSiblingid, $instanceid = '')
    {
        global $DB;
        $content = '';

        $element = $DB->get_record($this->tablename, array('msm_id' => $instanceid, 'parent_id' => $parentid, 'prev_sibling_id' => $prevSiblingid));

        if (!empty($element))
        {
            $unitid = $DB->get_record('msm_unit', array('id' => $element->unit_id))->id;

            $unit = new Unit();
            $unit->loadFromDb($unitid, $element->id);
            $this->unit = $unit;


            $content = "<div id='topunit'>";
            $content .= $this->unit->displayhtml();

            $content .= "<input id='unitidval' style='visibility:hidden' type='text' name='unitid' value='" . $this->unit->id . "'/>";
            $content .= "<input id='parentval' style='visibility:hidden' type='text' name='parentid' value='" . $parentid . "'/>";
            $content .= "<input id='siblingval' style='visibility:hidden' type='text' name='sibllingid' value='" . $prevSiblingid . "'/>";

            $content .= "</div>";

            return $content;
        }
        else
        {
            echo "no record found";
        }




//        //top level element
//        if (!empty($instanceid))
//        {
//            $rootElement = $DB->get_record($this->tablename, array('msm_id' => $instanceid, 'parent_id' => $parentid, 'prev_sibling_id' => null));
//
//            if (!empty($rootElement))
//            {
//                // searching the name of the table using table_id field in compositor table
//                $tablename = $DB->get_record('msm_table_collection', array('id' => $rootElement->table_id))->tablename;
//
//                switch ($tablename)
//                {
//                    //root element is an object of Unit class
//                    case('msm_unit'):
//                        $unitid = $DB->get_record('msm_unit', array('id' => $rootElement->unit_id))->id;
//
//                        $unit = new Unit();
//                        $unit->loadFromDb($unitid, $rootElement->id);
//                        $this->unit[] = $unit;
//                        break;
//                }
//            }
//          
//            $content = "<div id='topunit'>";
//            $content .= $this->unit[0]->displayhtml();
//            $content .= "</div>";  
//            
//            $this->loadAndDisplay($this->unit[0]->id, 0);
//        }
//        // child elements
//        else
//        {            
//            $tableid = $DB->get_record('msm_table_collection', array('tablename' => 'msm_unit'))->id;
//
//            $subunitelements = $DB->get_records($this->tablename, array('parent_id' => $parentid, 'table_id' => $tableid), 'prev_sibling_id');
//
//            // subunitStack contains all the subunits of current unit and sort them from last child to first child as a stack
//            $subunitstack = array();
//
//            // transferring from subunitelements to subunitstack because the offset numbers in subunitelements are id of the element instead of incremental index number
//            foreach ($subunitelements as $subunit)
//            {
//                $subunitstack[] = $subunit;
//            }
//
//            $subunitid = $DB->get_record('msm_unit', array('id' => $subunitstack[0]->unit_id))->id;
//            $unit = new Unit();
//            $unit->loadFromDb($subunitid, $subunitstack[0]->id);
//            $this->unit[] = $unit;
//           
//            $content = "<div id='subunit'>";
//            $content .= $this->unit[0]->displayhtml();
//            $content .= "</div>"; 
//           

        return $content;
    }

}
?>
