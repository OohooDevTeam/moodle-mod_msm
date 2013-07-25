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
 * This class does not inherit from the Element class and is used in lib.php to fill in the
 * appropriate data for the msm_table_collection database table.  This database table contains
 * names of all the tables needed in the MSM module.  The IDs given to each of these tables
 * are then usually used to query the msm_compositor table where it has table_id field that stores ID
 * corresponding to this table IDs.
 *
 * @author Ga Young
 */
class TableCollection
{

    /**
     *  constructor for this class
     */
    function __construct()
    {
        $this->tablename = "msm_table_collection";
    }

    /**
     * This method has an array with all the names of the database tables and inserts 
     * them one by one into msm_table_collection database table
     * @global moodle_database $DB
     */
    function insertTablename()
    {
        global $DB;

        $table = new stdClass();
        $listOfTables = array();

        $listOfTables[] = "msm_answer";
        $listOfTables[] = "msm_answer_exercise";
        $listOfTables[] = "msm_answer_showme";
        $listOfTables[] = "msm_approach";
        $listOfTables[] = "msm_associate";
        $listOfTables[] = "msm_cite";
        $listOfTables[] = "msm_comment";
        $listOfTables[] = "msm_compositor";
        $listOfTables[] = "msm_content";
        $listOfTables[] = "msm_def";
        $listOfTables[] = "msm_example";
        $listOfTables[] = "msm_exercise";
        $listOfTables[] = "msm_ext";
        $listOfTables[] = "msm_external_link";
        $listOfTables[] = "msm_extra_info";
        $listOfTables[] = "msm_img";
        $listOfTables[] = "msm_index_glossary";
        $listOfTables[] = "msm_index_symbol";
        $listOfTables[] = "msm_info";
        $listOfTables[] = "msm_intro";
        $listOfTables[] = "msm_item";
        $listOfTables[] = "msm_math_array";
        $listOfTables[] = "msm_media";
        $listOfTables[] = 'msm_packs';
        $listOfTables[] = "msm_para";
        $listOfTables[] = "msm_part_example";
        $listOfTables[] = "msm_part_exercise";
        $listOfTables[] = "msm_part_theorem";
        $listOfTables[] = 'msm_person';
        $listOfTables[] = "msm_pilot";
        $listOfTables[] = "msm_problem";
        $listOfTables[] = "msm_proof";
        $listOfTables[] = "msm_proof_block";
        $listOfTables[] = 'msm_quiz';
        $listOfTables[] = 'msm_quiz_choice';
        $listOfTables[] = "msm_showme";
        $listOfTables[] = "msm_solution";
        $listOfTables[] = "msm_solution_hint";
        $listOfTables[] = "msm_stage_date";
        $listOfTables[] = "msm_statement_example";
        $listOfTables[] = "msm_statement_theorem";
        $listOfTables[] = "msm_step";
        $listOfTables[] = "msm_subordinate";
        $listOfTables[] = "msm_table";
        $listOfTables[] = "msm_theorem";
        $listOfTables[] = "msm_unit";
        $listOfTables[] = "msm_math_row";
        $listOfTables[] = "msm_math_cell";
        $listOfTables[] = "msm_image_mapping";
        $listOfTables[] = "msm_block";
        
        foreach($listOfTables as $key=>$listOfTable)
        {
            $table->tablename = $listOfTable;
            
            $this->id = $DB->insert_record($this->tablename, $table, true, true);
        }
    }

}

?>
