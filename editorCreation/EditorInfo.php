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
            $tempallSubordinates = explode(",", $allSubordinateValues);

            // copying the array from string processing above (due to it ending in comma, the last
            // element is empty)
            $allSubordinates = array();

            for ($i = 0; $i < sizeof($tempallSubordinates) - 1; $i++)
            {
                $allSubordinates[] = $tempallSubordinates[$i];
            }

            $i = 0;
            foreach ($allSubordinates as $index => $subordinate)
            {
                $idValuePair = explode("|", $subordinate);

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

            if (!empty($_POST['msm_currentUnit_id']))
            {
                $indexNumber = explode("-", $idNumber);

                $newId = '';
                for ($i = 0; $i < sizeof($indexNumber) - 2; $i++)
                {
                    $newId .= $indexNumber[$i] . "-";
                }
                $newId .= $indexNumber[sizeof($indexNumber) - 2];

                $param = $newId . "|ref";
            }
            else
            {
                $param = $idNumber . "|ref";
            }
//
//            echo "param";
//            print_object($param);

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

    public function displayData()
    {
        global $DB;

        $htmlContent = '';

        $infoCompRecord = $DB->get_record('msm_compositor', array('id' => $this->compid));

        $parentRecord = $DB->get_record('msm_compositor', array('id' => $infoCompRecord->parent_id)); // associate/subordinate
        $parentTable = $DB->get_record('msm_table_collection', array('id' => $parentRecord->table_id));

//        $parentElementRecord = $DB->get_record("msm_compositor", array("id"=>$parentRecord->parent_id)); //the def/theorem/comment 

        if ($parentTable->tablename == 'msm_associate')
        {
            $htmlContent .= "<label for='msm_info_title-$parentRecord->parent_id-$parentRecord->id-$this->compid'>title: </label>";
            $htmlContent .= "<div id='msm_info_title-$parentRecord->parent_id-$parentRecord->id-$this->compid' class='msm_editor_content'>";
            $htmlContent .= $this->caption;
            $htmlContent .= "</div>";
            $htmlContent .= "<label for='msm_info_content-$parentRecord->parent_id-$parentRecord->id-$this->compid'>content: </label>";
            $htmlContent .= "<div id='msm_info_content-$parentRecord->parent_id-$parentRecord->id-$this->compid' class='msm_editor_content'>";
            $htmlContent .= $this->content;
            $htmlContent .= "</div>";

            $htmlContent .= "<div id='msm_associate_reftype_option-$this->compid' class='msm_associate_reftype_optionarea'>";
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
                $htmlContent .= $this->ref->displayRefData();
            }

            $htmlContent .= "</div>";
        }
        else if ($parentTable->tablename == 'msm_subordinate')
        {
            if (empty($this->caption))
            {
                $htmlContent .= "<div id='msm_subordinate_info_dialog-$parentRecord->parent_id-$parentRecord->id-$this->compid' class='msm_subordinate_info_dialogs' style='display:none;'>";
            }
            else
            {
                $htmlContent .= "<div id='msm_subordinate_info_dialog-$parentRecord->parent_id-$parentRecord->id-$this->compid' class='msm_subordinate_info_dialogs' title='$this->caption' style='display:none;'>";
            }

            $htmlContent .= $this->content;
            $htmlContent .= "</div>";
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

        $referenceRecords = $DB->get_records('msm_compositor', array('parent_id' => $infoCompRecord->parent_id), 'prev_sibling_id');

        foreach ($referenceRecords as $ref)
        {
            $refTable = $DB->get_record('msm_table_collection', array('id' => $ref->table_id));

            switch ($refTable->tablename)
            {
                case "msm_def":
                    $def = new EditorDefinition();
                    $def->loadData($ref->id);
                    $this->ref = $def;
                    break;
                case "msm_comment":
                    $comment = new EditorComment();
                    $comment->loadData($ref->id);
                    $this->ref = $comment;
                    break;
                case "msm_unit":
                    break;
                case "msm_theorem":
                    $theorem = new EditorTheorem();
                    $theorem->loadData($ref->id);
                    $this->ref = $theorem;
                    break;
            }
        }

        return $this;
    }

}

?>
