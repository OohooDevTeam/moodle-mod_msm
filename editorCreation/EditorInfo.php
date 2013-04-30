<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorInfo
 *
 * @author User
 */
class EditorInfo extends EditorElement
{

    public $id;
    public $compid;
    public $caption;
    public $content;
    public $errorArray = array();
    public $subordinates = array();
    public $ref;

    function __construct()
    {
        $this->tablename = 'msm_info';
    }

    // idNumber --> parentid-currentelementid
    public function getFormData($idNumber)
    {
        $subid = explode("|", $idNumber);

        if (sizeof($subid) > 1)
        {
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

            $i = 0;
            foreach ($allSubordinates as $index => $subordinate)
            {
                $idValuePair = explode("||", $subordinate);

                if (strpos($idValuePair[0], $subid[0]) !== false)
                {
                    if (strpos($idValuePair[0], 'info') !== false)
                    {
                        if ($idValuePair[0] == 'msm_subordinate_infoTitle-' . $subid[0])
                        {
                            // converting &gt;..etc back to html characters
                            $this->caption = htmlspecialchars_decode($idValuePair[1]);
                        }
                        else if ($idValuePair[0] == 'msm_subordinate_infoContent-' . $subid[0])
                        {
                            $this->content = htmlspecialchars_decode($idValuePair[1]);

                            foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                            {
                                $this->subordinates[] = $subordinates;
                            }
                        }
                    }
                }
            }
            // add reference processing stuff
        }
        else if (sizeof($subid) == 1)
        {
            $this->caption = $_POST['msm_info_title-' . $idNumber];

            if ($_POST['msm_info_content-' . $idNumber] != '')
            {
                $this->content = $_POST['msm_info_content-' . $idNumber];

                foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                {
                    $this->subordinates[] = $subordinates;
                }
            }
            else
            {
                $this->errorArray[] = 'msm_info_content-' . $idNumber . '_ifr';
            }

            $refType = $_POST['msm_associate_reftype-' . $idNumber];

            $idNumberInfo = explode("-", $idNumber);

            $newId = '';
            for ($i = 0; $i < sizeof($idNumberInfo) - 2; $i++)
            {
                $newId .= $idNumberInfo[$i] . "-";
            }
            $newId .= $idNumberInfo[sizeof($idNumberInfo) - 2];
            $param = $newId . "|ref";

            switch ($refType)
            {
                case "Definition":
                    $def = new EditorDefinition();
                    $def->getFormData($param);
                    $this->ref = $def;
                    break;
                case "Theorem":
                    $theorem = new EditorTheorem();
                    $theorem->getFormData($param);
                    $this->ref = $theorem;
                    break;
                case "Comment":
                    $comment = new EditorComment();
                    $comment->getFormData($param);
                    $this->ref = $comment;
                    break;
                case "Example":
                    break;
                case "Sections of this Composition":
                    break;
            }
        }

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $data->caption = $this->caption;
        $data->info_content = $this->content;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

        if (!empty($this->ref))
        {
            $this->ref->insertData($parentid, $this->compid, $msmid);
        }

        $subordinate_sibling = 0;
        foreach ($this->subordinates as $subordinate)
        {
            $subordinate->insertData($this->compid, $subordinate_sibling, $msmid);
            $subordinate_sibling = $subordinate->compid;
        }
    }

    public function displayData($parentId = '', $idEnding = '')
    {
        global $DB;

        $htmlContent = '';

        $infoCompRecord = $DB->get_record('msm_compositor', array('id' => $this->compid));

        $parentRecord = $DB->get_record('msm_compositor', array('id' => $infoCompRecord->parent_id)); // associate/subordinate
        $parentTable = $DB->get_record('msm_table_collection', array('id' => $parentRecord->table_id));

        if ($parentTable->tablename == 'msm_associate')
        {
            $htmlContent .= "<label for='msm_info_title-$parentRecord->parent_id-$parentRecord->id-$this->compid'>title: </label>";
            $htmlContent .= "<div id='msm_info_title-$parentRecord->parent_id-$parentRecord->id-$this->compid' class='msm_info_titles msm_editor_content'>";
            $htmlContent .= $this->caption;
            $htmlContent .= "</div>";

            $htmlContent .= "<label for='msm_info_content-$parentRecord->parent_id-$parentRecord->id-$this->compid'>content: </label>";
            $htmlContent .= "<div id='msm_info_content-$parentRecord->parent_id-$parentRecord->id-$this->compid' class='msm_info_contents msm_editor_content'>";
            $htmlContent .= $this->content;
            $htmlContent .= "</div>";

            $htmlContent .= "<div class='msm_subordinate_containers' id='msm_subordinate_container-infocontent$parentRecord->parent_id-$parentRecord->id-$this->compid'>";
            $htmlContent .= "</div>";

            $htmlContent .= "<div class='msm_subordinate_result_containers' id='msm_subordinate_result_container-infocontent$parentRecord->parent_id-$parentRecord->id-$this->compid'>";

            foreach ($this->subordinates as $subordinate)
            {
                $htmlContent .= $subordinate->displayData();
            }
            $htmlContent .= "</div>";

            $htmlContent .= "<div id='msm_associate_reftype_option-$parentRecord->parent_id-$parentRecord->id-$this->compid' class='msm_associate_reftype_optionarea'>";
            $htmlContent .= "<span class='msm_associate_reftype_label'>Type of reference to add: </span>";
            $htmlContent .= "<select id='msm_associate_reftype-$parentRecord->parent_id-$parentRecord->id-$this->compid' class='msm_associate_reftype_dropdown' onchange='processReftype(event)' name='msm_associate_reftype-$parentRecord->parent_id-$parentRecord->id-$this->compid' disabled='disabled'>";

            if (empty($this->ref))
            {
                $htmlContent .= "<option value='None' selected='selected'>None</option>";
                $htmlContent .= "<option value='Comment'>Comment</option>";
                $htmlContent .= "<option value='Definition'>Definition</option>";
                $htmlContent .= "<option value='Theorem'>Theorem</option>";
                $htmlContent .= "<option value='Example'>Example</option>";
                $htmlContent .= "<option value='Section of this Composition'>Section of this Composition</option>";
            }
            else
            {
                switch (get_class($this->ref))
                {
                    case "EditorDefinition":
                        $htmlContent .= "<option value='None'>None</option>";
                        $htmlContent .= "<option value='Comment'>Comment</option>";
                        $htmlContent .= "<option value='Definition' selected='selected'>Definition</option>";
                        $htmlContent .= "<option value='Theorem'>Theorem</option>";
                        $htmlContent .= "<option value='Example'>Example</option>";
                        $htmlContent .= "<option value='Section of this Composition'>Section of this Composition</option>";
                        break;
                    case "EditorComment":
                        $htmlContent .= "<option value='None'>None</option>";
                        $htmlContent .= "<option value='Comment' selected='selected'>Comment</option>";
                        $htmlContent .= "<option value='Definition'>Definition</option>";
                        $htmlContent .= "<option value='Theorem'>Theorem</option>";
                        $htmlContent .= "<option value='Example'>Example</option>";
                        $htmlContent .= "<option value='Section of this Composition'>Section of this Composition</option>";
                        break;
                    case "EditorTheorem":
                        $htmlContent .= "<option value='None'>None</option>";
                        $htmlContent .= "<option value='Comment'>Comment</option>";
                        $htmlContent .= "<option value='Definition'>Definition</option>";
                        $htmlContent .= "<option value='Theorem' selected='selected'>Theorem</option>";
                        $htmlContent .= "<option value='Example'>Example</option>";
                        $htmlContent .= "<option value='Section of this Composition'>Section of this Composition</option>";
                        break;
                    case "EditorUnit":
                        $htmlContent .= "<option value='None'>None</option>";
                        $htmlContent .= "<option value='Comment'>Comment</option>";
                        $htmlContent .= "<option value='Definition'>Definition</option>";
                        $htmlContent .= "<option value='Theorem'>Theorem</option>";
                        $htmlContent .= "<option value='Example'>Example</option>";
                        $htmlContent .= "<option value='Section of this Composition' selected='selected'>Section of this Composition</option>";
                        break;
                }
            }
            $htmlContent .= "</select>";

            if (!empty($this->ref))
            {
                $htmlContent .= $this->ref->displayRefData("$parentRecord->parent_id-$parentRecord->id");
            }

            $htmlContent .= "</div>";
        }
        else if ($parentTable->tablename == 'msm_subordinate')
        {

            $idEndingInfo = explode("-", $idEnding);

            if (sizeof($idEndingInfo) > 2)
            {
                // only for nested subordinates
                $containerId = substr($idEnding, 0, -2);
            }
            else
            {
                $containerId = $idEnding;
            }

            if (empty($this->caption))
            {
                $htmlContent .= "<div id='msm_subordinate_infoTitle-$idEnding'>";
                $htmlContent .= "</div>";
            }
            else
            {
                $htmlContent .= "<div id='msm_subordinate_infoTitle-$idEnding'>";
                $htmlContent .= $this->caption;
                $htmlContent .= "</div>";
            }

            $htmlContent .= "<div id='msm_subordinate_infoContent-$idEnding'>";
            $htmlContent .= $this->content;
            $htmlContent .= "</div>";

            $htmlContent .= "</div>"; // end of msm_subordinate_results div

            $htmlContent .= "<div class='msm_subordinate_containers' id='msm_subordinate_container-$containerId'>";
            $htmlContent .= "</div>";

            if (sizeof($this->subordinates) > 0)
            {
                foreach ($this->subordinates as $subordinate)
                {
                    $htmlContent .= $subordinate->displayData($parentId);
                }
            }
        }

        return $htmlContent;
    }

    public function loadData($compid)
    {
        global $DB;

        $infoCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $infoCompRecord->unit_id;

        $infoRecord = $DB->get_record($this->tablename, array('id' => $this->id));

        $this->caption = $infoRecord->caption;
        $this->content = $infoRecord->info_content;

        $childRecords = $DB->get_records('msm_compositor', array('parent_id' => $infoCompRecord->parent_id), 'prev_sibling_id');

        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            switch ($childTable->tablename)
            {
                case "msm_def":
                    $def = new EditorDefinition();
                    $def->loadData($child->id);
                    $this->ref = $def;
                    break;
                case "msm_comment":
                    $comment = new EditorComment();
                    $comment->loadData($child->id);
                    $this->ref = $comment;
                    break;
                case "msm_unit":
                    break;
                case "msm_theorem":
                    $theorem = new EditorTheorem();
                    $theorem->loadData($child->id);
                    $this->ref = $theorem;
                    break;
            }
        }

        $subordinateRecords = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($subordinateRecords as $sub)
        {
            $subordinate = new EditorSubordinate();
            $subordinate->loadData($sub->id);
            $this->subordinates[] = $subordinate;
        }

        return $this;
    }

    public function displayPreview($id)
    {
        $previewHtml = '';
        $titleString = htmlentities($this->caption);
        $previewHtml .= "<div id='dialog-$id' class='msm_info_dialogs' title='$titleString'>";
        $previewHtml .= $this->content;

        if (!empty($this->subordinates))
        {
            foreach ($this->subordinates as $subordinate)
            {
                $previewHtml .= $subordinate->displayPreview();
            }
        }

        $previewHtml .= "</div>";

        return $previewHtml;
    }

}

?>