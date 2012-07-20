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
     * A recursive function that finds all the unit records while keeping the order specified by the compositor using the parent-child relationship.  
     * These records are put into a stack to be passed to an AJAX call to dynamically load each unit page. 
     * @param int $parentid     Specifies the parent of the element
     * @param int $instanceid   Moodle msm module instance id to get the root element
     */
    function makeStack($parentid, $siblingid = '', $instanceid = '')
    {
        global $DB;

        // stack that will have all the unit records in order given by the compositor table
        $this->childs = array();
        $childs = array();

        $unittableid = $DB->get_record('msm_table_collection', array('tablename' => 'msm_unit'))->id;

        if (!empty($instanceid))
        {
            $unitRecords = $DB->get_records('msm_compositor', array('table_id' => $unittableid, 'msm_id' => $instanceid, 'parent_id' => $parentid, 'prev_sibling_id' => $siblingid));
        }
        else if (empty($siblingid))
        {
            $unitRecords = $DB->get_records('msm_compositor', array('table_id' => $unittableid, 'parent_id' => $parentid), 'prev_sibling_id');
        }
        else
        {
            $unitRecords = $DB->get_records('msm_compositor', array('table_id' => $unittableid, 'parent_id' => $parentid, 'prev_sibling_id' => $siblingid));
        }

        if (!empty($unitRecords))
        {
            foreach ($unitRecords as $unitRecord)
            {
                $childUnits = $DB->get_records('msm_compositor', array('table_id' => $unittableid, 'parent_id' => $unitRecord->id));
                $siblingUnits = $DB->get_records('msm_compositor', array('table_id' => $unittableid, 'parent_id' => $parentid, 'prev_sibling_id' => $unitRecord->id));


                if (!empty($childUnits))
                {
                    $this->makeStack($unitRecord->id);
                }
                if (!empty($siblingUnits))
                {
                    $this->makeStack($parentid, $unitRecord->id);
                }
//                else
//                {
                $unitTableRecord = $DB->get_record('msm_unit', array('id' => $unitRecord->unit_id));

                $unit_id = $unitTableRecord->id;
                $unit_compid = $unitRecord->id;

                $unit = new Unit();
                $unitdata = $unit->loadFromDb($unit_id, $unit_compid);
                array_push($this->childs, $unitdata);
//                }
            }          
          
              return $this->childs;
        }

      
    }

}
?>
