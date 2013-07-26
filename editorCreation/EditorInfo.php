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
    public $medias = array();
    public $ref;
    public $isRef;

    function __construct()
    {
        $this->tablename = 'msm_info';
    }

    // idNumber --> parentid-currentelementid
    public function getFormData($idNumber)
    {
        global $DB;

        $flag = false;
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
                            $content = htmlspecialchars_decode($idValuePair[1]);
                            $this->content = $this->processMath($content);

                            foreach ($this->processImage($this->content) as $key => $media)
                            {
                                $this->medias[] = $media;
                            }

                            foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                            {
                                $this->subordinates[] = $subordinates;
                            }
                        }
                    }
                    else if ($idValuePair[0] == "msm_subordinate_ref-" . $subid[0])
                    {
                        $record = $DB->get_record("msm_compositor", array("id" => $idValuePair[1]));
                        $tableRecord = $DB->get_record("msm_table_collection", array("id" => $record->table_id));

                        switch ($tableRecord->tablename)
                        {
                            case "msm_def":
                                $def = new EditorDefinition();
                                $def->isRef = $record->id;
                                $this->ref = $def;
                                break;
                            case "msm_theorem":
                                $theorem = new EditorTheorem();
                                $theorem->isRef = $record->id;
                                $this->ref = $theorem;
                                break;
                            case "msm_comment":
                                $comment = new EditorComment();
                                $comment->isRef = $record->id;
                                $this->ref = $comment;
                                break;
                        }

                        $flag = true;
                    }
                }
            }

            if (!$flag)
            {
                $newsubid = '';
                foreach ($allSubordinates as $index => $subordinate)
                {
                    $idValuePair = explode("||", $subordinate);

                    if (strpos($idValuePair[0], "msm_subordinate_hotword_match") !== false)
                    {
                        if (strpos($idValuePair[1], $subid[0]))
                        {
                            $newIdInfo = explode("-", $idValuePair[0]);

                            for ($i = 1; $i < sizeof($newIdInfo) - 1; $i++)
                            {
                                $newsubid .= $newIdInfo[$i] . "-";
                            }
                            $newsubid .= $newIdInfo[sizeof($newIdInfo) - 1];
                            break;
                        }
                    }
                }

                if ($newsubid !== '')
                {
                    foreach ($allSubordinates as $index => $subordinate)
                    {
                        $idValuePair = explode("||", $subordinate);

                        if (strpos($idValuePair[0], $newsubid) !== false)
                        {
                            if (strpos($idValuePair[0], 'info') !== false)
                            {
                                if ($idValuePair[0] == 'msm_subordinate_infoTitle-' . $newsubid)
                                {
                                    // converting &gt;..etc back to html characters
                                    $this->caption = htmlspecialchars_decode($idValuePair[1]);
                                }
                                else if ($idValuePair[0] == 'msm_subordinate_infoContent-' . $newsubid)
                                {
                                    $content = htmlspecialchars_decode($idValuePair[1]);
                                    $this->content = $this->processMath($content);

                                    foreach ($this->processImage($this->content) as $key => $media)
                                    {
                                        $this->medias[] = $media;
                                    }

                                    foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                                    {
                                        $this->subordinates[] = $subordinates;
                                    }
                                    break;
                                }
                                else if ($idValuePair[0] == "msm_subordinate_ref-" . $newsubid)
                                {
                                    $record = $DB->get_record("msm_compositor", array("id" => $idValuePair[1]));
                                    $tableRecord = $DB->get_record("msm_table_collection", array("id" => $record->table_id));

                                    switch ($tableRecord->tablename)
                                    {
                                        case "Definition":
                                            $def = new EditorDefinition();
                                            $def->isRef = $record->id;
                                            $this->ref = $def;
                                            break;
                                        case "Theorem":
                                            $theorem = new EditorTheorem();
                                            $theorem->isRef = $record->id;
                                            $this->ref = $theorem;
                                            break;
                                        case "Comment":
                                            $comment = new EditorComment();
                                            $comment->isRef = $record->id;
                                            $this->ref = $comment;
                                            break;
                                    }
                                }
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
                $content = $_POST['msm_info_content-' . $idNumber];
                $this->content = $this->processMath($content);

                foreach ($this->processImage($this->content) as $key => $media)
                {
                    $this->medias[] = $media;
                }

                foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                {
                    $this->subordinates[] = $subordinates;
                }
            }
            else
            {
                $this->errorArray[] = 'msm_info_content-' . $idNumber . '_ifr';
            }

            $refType = '';
            $intExtFlag = '';

            $idNumberInfo = explode("-", $idNumber);

            $newId = '';
            for ($i = 0; $i < sizeof($idNumberInfo) - 2; $i++)
            {
                $newId .= $idNumberInfo[$i] . "-";
            }
            $newId .= $idNumberInfo[sizeof($idNumberInfo) - 2];

            if (isset($_POST['msm_associate_reftype-' . $idNumber]))
            {
                $refType = $_POST['msm_associate_reftype-' . $idNumber];
            }
            // to process internal/external references which do not have dropdown menu
            else // todo: need to add more types
            {
                // only put the intext flag when ref material is being saved the first time...if it is editted then no need to skip the inser to db process
                foreach ($_POST as $key => $value)
                {
                    if (strpos($key, "msm_defref_description_input-$newId") !== false)
                    {
                        $refType = "Definition";
                        $descrInfo = explode("__", $key);
                        if (sizeof($descrInfo) > 2)
                        {
                            $intExtFlag = 'same';
                            break;
                        }
                        else if (sizeof($descrInfo) > 1)
                        {
                            $intExtFlag = 'intext';
                            break;
                        }
                    }
                    else if (strpos($key, "msm_commentref_description_input-$newId") !== false)
                    {
                        $refType = "Comment";
                        $descrInfo = explode("__", $key);
                        if (sizeof($descrInfo) > 2)
                        {
                            $intExtFlag = 'same';
                            break;
                        }
                        else if (sizeof($descrInfo) > 1)
                        {
                            $intExtFlag = 'intext';
                            break;
                        }
                    }
                    else if (strpos($key, "msm_theoremref_description_input-$newId") !== false)
                    {
                        $refType = "Theorem";
                        $descrInfo = explode("__", $key);
                        if (sizeof($descrInfo) > 2)
                        {
                            $intExtFlag = 'same';
                            break;
                        }
                        else if (sizeof($descrInfo) > 1)
                        {
                            $intExtFlag = 'intext';
                            break;
                        }
                    }
                }
            }

            $param = $newId . "|$intExtFlag" . "ref";

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
//        print_object($_POST);
//        print_object($this);
        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();

        if (empty($this->isRef))
        {
            $patterns = array();
            $replacements = array();
            $patterns[0] = "/<div.*?>/";
            $patterns[1] = "/<\/div>/";
            $patterns[0] = "/<p.*?>/";
            $patterns[1] = "/<\/p>/";
            $patterns[2] = "/<span.*?>/";
            $patterns[3] = "/<\/span>/";
            $replacements[0] = "";
            $replacements[1] = "";
            $replacements[2] = "";
            $replacements[3] = "";

            $modifiedCaption = preg_replace($patterns, $replacements, $this->caption);
            $titleString = htmlentities($modifiedCaption);

            $data->caption = $titleString;

            $pParser = new DOMDocument();
            $pParser->loadHTML($this->content);
            $divs = $pParser->getElementsByTagName("div");

            if ($divs->length > 0)
            {
                $data->info_content = $this->content;
            }
            else
            {
                $data->info_content = "<div>$this->content</div>";
            }

            $this->id = $DB->insert_record($this->tablename, $data);
        }
        else
        {
            $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->isRef), "prev_sibling_id");

            foreach ($childRecords as $child)
            {
                $childTable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));

                switch ($childTable->tablename)
                {
                    case "msm_subordinate":
                        $subord = new EditorSubordinate();
                        $subord->id = $child->unit_id;
                        $subord->isRef = $child->id;
                        $this->subordinates[] = $subord;
                        break;
                    case "msm_media":
                        $med = new EditorSubordinate();
                        $med->id = $child->unit_id;
                        $med->isRef = $child->id;
                        $this->medias[] = $med;
                        break;
                }
            }
        }

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

        $media_sibliing = 0;
        $content = '';
        foreach ($this->medias as $key => $media)
        {
            $media->insertData($this->compid, $media_sibliing, $msmid);
            $media_sibliing = $media->compid;
            $content = $this->replaceImages($key, $media->image, $data->info_content, "div");
        }

        if (!empty($this->medias))
        {
            $this->content = $content;

            $data->id = $this->id;
            $data->info_content = $this->content;
            $this->id = $DB->update_record($this->tablename, $data);
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
            $assoIdEnding = "$parentRecord->parent_id-$parentRecord->id-$this->compid";

            $htmlContent .= "<label for='msm_info_title-$assoIdEnding'>title: </label>";
            $htmlContent .= "<div id='msm_info_title-$assoIdEnding' class='msm_info_titles msm_editor_content'>";
            $htmlContent .= html_entity_decode($this->caption);
            $htmlContent .= "</div>";

            $htmlContent .= "<label for='msm_info_content-$assoIdEnding'>content: </label>";
            $htmlContent .= "<div id='msm_info_content-$assoIdEnding' class='msm_info_contents msm_editor_content'>";
            $htmlContent .= html_entity_decode($this->content);
            $htmlContent .= "</div>";

            $htmlContent .= "<div class='msm_subordinate_containers' id='msm_subordinate_container-infocontent$assoIdEnding'>";
            $htmlContent .= "</div>";

            $htmlContent .= "<div class='msm_subordinate_result_containers' id='msm_subordinate_result_container-infocontent$assoIdEnding'>";

            foreach ($this->subordinates as $subordinate)
            {
                $htmlContent .= $subordinate->displayData($parentId);
            }
            $htmlContent .= "</div>";

            $htmlContent .= "<div id='msm_associate_reftype_option-$assoIdEnding' class='msm_associate_reftype_optionarea'>";
//            $htmlContent .= "<span class='msm_associate_reftype_label'>Type of reference to add: </span>";
//            $htmlContent .= "<select id='msm_associate_reftype-$assoIdEnding' class='msm_associate_reftype_dropdown' onchange='processReftype(event)' name='msm_associate_reftype-$assoIdEnding' disabled='disabled'>";
//
//            if (empty($this->ref))
//            {
//                $htmlContent .= "<option value='None' selected='selected'>None</option>";
//                $htmlContent .= "<option value='Comment'>Comment</option>";
//                $htmlContent .= "<option value='Definition'>Definition</option>";
//                $htmlContent .= "<option value='Theorem'>Theorem</option>";
//                $htmlContent .= "<option value='Example'>Example</option>";
//                $htmlContent .= "<option value='Section of this Composition'>Section of this Composition</option>";
//            }
//            else
//            {
//                switch (get_class($this->ref))
//                {
//                    case "EditorDefinition":
//                        $htmlContent .= "<option value='None'>None</option>";
//                        $htmlContent .= "<option value='Comment'>Comment</option>";
//                        $htmlContent .= "<option value='Definition' selected='selected'>Definition</option>";
//                        $htmlContent .= "<option value='Theorem'>Theorem</option>";
//                        $htmlContent .= "<option value='Example'>Example</option>";
//                        $htmlContent .= "<option value='Section of this Composition'>Section of this Composition</option>";
//                        break;
//                    case "EditorComment":
//                        $htmlContent .= "<option value='None'>None</option>";
//                        $htmlContent .= "<option value='Comment' selected='selected'>Comment</option>";
//                        $htmlContent .= "<option value='Definition'>Definition</option>";
//                        $htmlContent .= "<option value='Theorem'>Theorem</option>";
//                        $htmlContent .= "<option value='Example'>Example</option>";
//                        $htmlContent .= "<option value='Section of this Composition'>Section of this Composition</option>";
//                        break;
//                    case "EditorTheorem":
//                        $htmlContent .= "<option value='None'>None</option>";
//                        $htmlContent .= "<option value='Comment'>Comment</option>";
//                        $htmlContent .= "<option value='Definition'>Definition</option>";
//                        $htmlContent .= "<option value='Theorem' selected='selected'>Theorem</option>";
//                        $htmlContent .= "<option value='Example'>Example</option>";
//                        $htmlContent .= "<option value='Section of this Composition'>Section of this Composition</option>";
//                        break;
//                    case "EditorUnit":
//                        $htmlContent .= "<option value='None'>None</option>";
//                        $htmlContent .= "<option value='Comment'>Comment</option>";
//                        $htmlContent .= "<option value='Definition'>Definition</option>";
//                        $htmlContent .= "<option value='Theorem'>Theorem</option>";
//                        $htmlContent .= "<option value='Example'>Example</option>";
//                        $htmlContent .= "<option value='Section of this Composition' selected='selected'>Section of this Composition</option>";
//                        break;
//                }
//            }
//            $htmlContent .= "</select>";

            if (!empty($this->ref))
            {
                $htmlContent .= $this->ref->displayRefData("$parentRecord->parent_id-$parentRecord->id");
            }

            $htmlContent .= "</div>";
        }
        else if (($parentTable->tablename == 'msm_subordinate') || ($parentTable->tablename == "msm_external_link"))
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

        $subordinateChildRecords = $DB->get_records('msm_compositor', array('parent_id' => $infoCompRecord->parent_id), 'prev_sibling_id');

        foreach ($subordinateChildRecords as $subchild)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $subchild->table_id));

            switch ($childTable->tablename)
            {
                case "msm_def":
                    $def = new EditorDefinition();
                    $def->loadData($subchild->id);
                    $this->ref = $def;
                    break;
                case "msm_comment":
                    $comment = new EditorComment();
                    $comment->loadData($subchild->id);
                    $this->ref = $comment;
                    break;
                case "msm_unit":
                    break;
                case "msm_theorem":
                    $theorem = new EditorTheorem();
                    $theorem->loadData($subchild->id);
                    $this->ref = $theorem;
                    break;
            }
        }

        $infoChildRecords = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');

        foreach ($infoChildRecords as $infoChild)
        {
            $infoChildTable = $DB->get_record("msm_table_collection", array("id" => $infoChild->table_id));

            if ($infoChildTable->tablename == "msm_subordinate")
            {
                $subordinate = new EditorSubordinate();
                $subordinate->loadData($infoChild->id);
                $this->subordinates[] = $subordinate;
            }
            else if ($infoChildTable->tablename == "msm_media")
            {
                $media = new EditorMedia();
                $media->loadData($infoChild->id);
                $this->medias[] = $media;
            }
        }

        return $this;
    }

    public function displayPreview($id)
    {
        $previewHtml = '';

        $patterns = array();
        $replacements = array();
        $patterns[0] = "/<p.*?>/";
        $patterns[1] = "/<\/p>/";
        $patterns[2] = "/<span.*?>/";
        $patterns[3] = "/<\/span>/";
        $replacements[0] = "";
        $replacements[1] = "";
        $replacements[2] = "";
        $replacements[3] = "";

        $modifiedCaption = preg_replace($patterns, $replacements, $this->caption);
        $titleString = htmlentities($modifiedCaption);

        if (!empty($titleString))
        {
            $previewHtml .= "<div id='dialog-$id' class='msm_info_dialogs' title='$titleString'>";
        }
        else
        {
            $previewHtml .= "<div id='dialog-$id' class='msm_info_dialogs'>";
        }
        $previewHtml .= html_entity_decode($this->content);
        $previewHtml .= "</div>";

        if (!empty($this->subordinates))
        {
            foreach ($this->subordinates as $subordinate)
            {
                $previewHtml .= $subordinate->displayPreview();
            }
        }


        return $previewHtml;
    }

}

?>