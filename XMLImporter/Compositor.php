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
                    $unit->loadFromDb($unitid);
                    $this->unit = $unit;
                    break;

//                case('msm_theorem'):
//                    break;
                // can also be theorem maybe?
                // example/quiz/exercise/showme all could be possible choice...
            }

            $whereclause = "parent_id='" . $rootElement->id . "'" . "and prev_sibling_id='null' OR '0'";
            $firstchild = $DB->get_record_select('msm_compositor', $whereclause);

            $firstchildtable = $DB->get_field('msm_table_collection', 'tablename', array('id' => $firstchild->table_id));

            switch ($firstchildtable)
            {
                case('msm_person'):

                    $author = new Person();
                    $author->loadFromDb($firstchild->unit_id);
                    $this->authors[] = $author;
            }
            
              $whereclause = "parent_id='" . $rootElement->id . "'" . "and prev_sibling_id='" . $firstchild->id . "'";
            $secondchild = $DB->get_record_select('msm_compositor', $whereclause);

            $secondchildtable = $DB->get_field('msm_table_collection', 'tablename', array('id' => $secondchild->table_id));
            
            switch ($secondchildtable)
            {
                case('msm_intro'):

                    $intro = new Intro();
                    $intro->loadFromDb($secondchild->unit_id, $secondchild->id);
                    $this->intro = $intro;
            }          
            
        }

        return $this;
    }

    function displayhtml()
    {
        $content = '';

        $content .= $this->unit->displaytitlehtml();

        foreach ($this->authors as $author)
        {
            $content .= $author->displayhtml();
        }
        $content .= "</div>"; //for closing the border div
        
        $content .= $this->intro->displayhtml();

        return $content;
    }

}

?>
