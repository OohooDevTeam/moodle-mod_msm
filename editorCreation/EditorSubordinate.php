<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorSubordinate
 *
 * @author User
 */
class EditorSubordinate extends EditorElement
{

    public $id;
    public $compid;
    public $hot;
    public $position;
    public $info;

    // no errorArray necessary b/c null input has been checked already
    // when the subordinate was made

    function __construct()
    {
        $this->tablename = 'msm_subordinate';
    }

    // idNumber in this case is the anchored element passed from
    // contents (of various classes such as EditorDefinition/EditorTheorem...etc)
    public function getFormData($idNumber, $position)
    {
        $this->position = $position;
        $this->hot = $idNumber->nodeValue;

        $id = $idNumber->getAttribute("id");

        $idInfo = explode("-", $id);
        $idEnding = '';

        for ($i = 1; $i < sizeof($idInfo) - 1; $i++)
        {
            $idEnding .= $idInfo[$i] . "-";
        }
        $idEnding .= $idInfo[sizeof($idInfo) - 1];

        $info = new EditorInfo();
        $info->getFormData($idEnding . "|sub", $position);
        $this->info = $info;

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $data->hot = $this->hot;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->table_id = $DB->get_record("msm_table_collection", array('tablename' => $this->tablename))->id;
        $compData->unit_id = $this->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

        if (!empty($this->info))
        {
            $this->info->insertData($this->compid, 0, $msmid);
        }
    }

}

?>
