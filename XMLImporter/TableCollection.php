<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TableCollection
 *
 * @author User
 */
class TableCollection
{

    function __construct()
    {
        $this->tablename = "msm_table_collection";
    }

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
        
        foreach($listOfTables as $key=>$listOfTable)
        {
            $table->tablename = $listOfTable;
            
            $this->id = $DB->insert_record($this->tablename, $table);
        }
    }

}

?>
