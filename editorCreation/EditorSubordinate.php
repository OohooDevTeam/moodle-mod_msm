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
    public $external_link;

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

        $this->hot = $hotNodeId . "||" . $hotNodeText;

        $id = $idNumber->getAttribute("id");

        $idInfo = explode("-", $id);
        $idEnding = '';

        for ($i = 1; $i < sizeof($idInfo) - 1; $i++)
        {
            $idEnding .= $idInfo[$i] . "-";
        }
        $idEnding .= $idInfo[sizeof($idInfo) - 1];

        $allSubordinateValues = $_POST['msm_unit_subordinate_container'];
//
        $tempallSubordinates = explode("//|", $allSubordinateValues);

        // copying the array from string processing above (due to it ending in comma, the last
        // element is empty)
        $allSubordinates = array();

        for ($i = 0; $i < sizeof($tempallSubordinates); $i++)
        {
            $allSubordinates[] = $tempallSubordinates[$i];
        }

        $hasLink = false;
        foreach ($allSubordinates as $index => $subordinate)
        {
            $idValuePair = explode("||", $subordinate);

            if (strpos($idValuePair[0], $idEnding) !== false)
            {
                if (strpos($idValuePair[0], 'url') !== false)
                {
                    if ($idValuePair[0] == 'msm_subordinate_url-' . $idEnding)
                    {
                        $external_link = new EditorExternalLink();
                        $external_link->getFormData($idEnding);
                        $this->external_link = $external_link;
                        $hasLink = true;
                    }
                }
            }
        }

        if (!$hasLink)
        {
            $info = new EditorInfo();
            $info->getFormData($idEnding . "|sub");
            $this->info = $info;
        }

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

        if (!empty($this->external_link))
        {
            $this->external_link->insertData($this->compid, 0, $msmid);
        }

        if (!empty($this->info))
        {
            $this->info->insertData($this->compid, 0, $msmid);
        }
    }

    public function displayData($parentId)
    {
        global $DB;
        $subChild = $DB->get_record("msm_compositor", array("parent_id" => $this->compid));
        $childTable = $DB->get_record("msm_table_collection", array("id" => $subChild->table_id))->tablename;

        $htmlContent = '';

        $hotIdInfo = explode("||", $this->hot);
        $idInfo = explode("-", $hotIdInfo[0]);

        if (sizeof($idInfo) == 2)
        {
            $idEnding = $idInfo[1];
        }
        else if (sizeof($idInfo) > 2)
        {
            $matchString = $idInfo[1];
            for ($i = 2; $i < sizeof($idInfo); $i++)
            {
                $matchString .= "-" . $idInfo[$i];
            }

            // swapping out arbitrary number given to parent element for id with database compositor id        
            $idEnding = preg_replace('/([A-Za-z]*?)\d+((?:-\d+)*)/', "$1", trim($matchString));
            $idEnding .= $parentId;
            $idEnding .= preg_replace('/([A-Za-z]*?)\d+((?:-\d+)*)/', "$2", trim($matchString));
        }
        else
        {
            $idEnding = $idInfo[0];
        }

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

        if (!empty($this->external_link))
        {
            $htmlContent .= "<div id='msm_subordinate_url-$idEnding'>";
            $htmlContent .= $this->external_link->href;
            $htmlContent .= "</div>";

            $htmlContent .= "<div id='msm_subordinate_hotword_match-$idEnding' class='msm_subordinate_hotword_matchs'>";
            $htmlContent .= $hotIdInfo[0];
            $htmlContent .= "</div>";

            $htmlContent .= $this->external_link->info->displayData($parentId, $idEnding);
        }
        else
        {
            $htmlContent .= "<div id='msm_subordinate_hotword_match-$idEnding' class='msm_subordinate_hotword_matchs'>";
            $htmlContent .= $hotIdInfo[0];
            $htmlContent .= "</div>";

            $htmlContent .= $this->info->displayData($parentId, $idEnding);
        }


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
            else if ($childTable->tablename == "msm_external_link")
            {
                $external_link = new EditorExternalLink();
                $external_link->loadData($child->id);
                $this->external_link = $external_link;
            }
        }
        return $this;
    }

    public function displayPreview()
    {
        $previewHtml = '';

        $idInfo = explode("||", $this->hot);
        $indexInfo = explode("-", $idInfo[0]);

        $id = '';
        for ($i = 1; $i < sizeof($indexInfo) - 1; $i++)
        {
            $id .= $indexInfo[$i] . "-";
        }
        $id .= $indexInfo[sizeof($indexInfo) - 1];

        if (!empty($this->external_link))
        {
            $previewHtml .= $this->external_link->displayPreview($id);
        }

        if (!empty($this->info))
        {
            $previewHtml .= $this->info->displayPreview($id);
        }

        return $previewHtml;
    }

}

?>
