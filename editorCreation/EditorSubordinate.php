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
    public $info;

    // no errorArray necessary b/c null input has been checked already
    // when the subordinate was made

    function __construct()
    {
        $this->tablename = 'msm_subordinate';
    }

    // idNumber in this case is the anchored element passed from
    // contents (of various classes such as EditorDefinition/EditorTheorem...etc)
    public function getFormData($idNumber)
    {
        $doc = new DOMDocument;

        $hotNode = $doc->importNode($idNumber, true);
        $hotNodeId = $hotNode->getAttribute("id");
        $hotNodeText = $hotNode->textContent;

        $this->hot = $hotNodeId . "," . $hotNodeText;

        $id = $idNumber->getAttribute("id");

        $idInfo = explode("-", $id);
        $idEnding = '';

        for ($i = 1; $i < sizeof($idInfo) - 1; $i++)
        {
            $idEnding .= $idInfo[$i] . "-";
        }
        $idEnding .= $idInfo[sizeof($idInfo) - 1];

        $info = new EditorInfo();
        $info->getFormData($idEnding . "|sub");
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

    public function displayData($flag = false)
    {
        global $DB;
        $subChild = $DB->get_record("msm_compositor", array("parent_id" => $this->compid));
        $childTable = $DB->get_record("msm_table_collection", array("id" => $subChild->table_id))->tablename;

        $htmlContent = '';

        $hotIdInfo = explode(",", $this->hot);
        $idInfo = explode("-", $hotIdInfo[0]);

        $idEnding = '';
        for ($i = 1; $i < sizeof($idInfo) - 1; $i++)
        {
            $idEnding .= $idInfo[$i] . "-";
        }
        $idEnding .= $idInfo[sizeof($idInfo) - 1];

        $htmlContent .= "<div id='msm_subordinate_result-$idEnding' class='msm_subordinate_results'>";
        $htmlContent .= "<div id='msm_subordinate_select-$idEnding'>";
        
        if ($childTable == "msm_info")
        {
            $htmlContent .= "Information";
        }
        else if ($childTable == "msm_external_link")
        {
            $htmlContent .= "External Link";
        }
        $htmlContent .= "</div>";

        $htmlContent .= $this->info->displayData($flag);

        $htmlContent .= "</div>";

        return $htmlContent;
    }

    /**
     * 
     * @global moodle_database $DB
     * @param type $compid
     * @return \EditorSubordinate
     */
    public function loadData($compid)
    {
        global $DB;

        $subordinateCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $subordinateCompRecord->unit_id;

        $subordinateRecord = $DB->get_record($this->tablename, array('id' => $this->id));
        $this->hot = $subordinateRecord->hot;

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            if ($childTable->tablename == 'msm_info')
            {
                $info = new EditorInfo();
                $info->loadData($child->id);
                $this->info = $info;
            }
        }
        return $this;
    }

    public function displayPreview()
    {
        $previewHtml = '';

        $idInfo = explode(",", $this->hot);
        $indexInfo = explode("-", $idInfo[0]);

        $id = '';
        for ($i = 1; $i < sizeof($indexInfo) - 1; $i++)
        {
            $id .= $indexInfo[$i] . "-";
        }
        $id .= $indexInfo[sizeof($indexInfo) - 1];

        $previewHtml .= $this->info->displayPreview($id);

        return $previewHtml;
    }

}

?>
