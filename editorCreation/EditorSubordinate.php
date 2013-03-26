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

//        $this->hot = $doc->saveHTML($doc->importNode($idNumber, true));
//                print_object($this->hot);

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

    public function displayData()
    {
        $htmlContent = '';

        $htmlContent .= $this->info->displayData();

        return $htmlContent;
    }

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
