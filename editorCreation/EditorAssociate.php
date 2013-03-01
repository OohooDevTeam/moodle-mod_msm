<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorAssociate
 *
 * @author User
 */
class EditorAssociate extends EditorElement
{

    public $id;
    public $compid;
//    public $errorArray = array();
    public $type; // in db--> description of the associate
    public $infos = array();

    function __construct()
    {
        $this->tablename = 'msm_associate';
    }

    // idNumber = parent_number-currentelement_number
    public function getFormData($idNumber)
    {
//        print_object($_POST);
        $this->type = $_POST['msm_associate_dropdown-' . $idNumber];

        $indexNumber = explode("-", $idNumber);

//        echo "indexNumber";
//        print_object($indexNumber);

        $infomatch = "/^msm_info_content-$idNumber.*$/";

//        print_object($idNumber);

        $i = 0;
        foreach ($_POST as $id => $value)
        {
            if (preg_match($infomatch, $id))
            {
//                print_object($id);
                $idInfo = explode("-", $id);

                $newId = '';
                for ($i = 1; $i < sizeof($idInfo) - 1; $i++)
                {
                    $newId .= $idInfo[$i] . "-";
                }
                $newId .= $idInfo[sizeof($idInfo) - 1];

                $info = new EditorInfo();
                $info->getFormData($newId);
                $this->infos[] = $info;

                $i++;
            }
        }
//        print_object($this);

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $data->description = $this->type;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

        $sibling_id = 0;
        foreach ($this->infos as $info)
        {
            $info->insertData($this->compid, $sibling_id, $msmid);
            $sibling_id = $info->compid;
        }
    }

    public function displayData()
    {
        global $DB;

        $currentAssociateRecord = $DB->get_record("msm_compositor", array("id" => $this->compid));

        $htmlContent = '';

        $htmlContent .= "<div id='msm_associate_childs-$currentAssociateRecord->parent_id-$this->compid' class='msm_associate_childs'>";
        $htmlContent .= "<div id='msm_associate_info_header-$this->compid' class='msm_associate_info_headers'>";
        $htmlContent .= "<b> ASSOCIATED INFORMATION </b>";
        $htmlContent .= "<span style='visibility: hidden;'>Drag here to move this element.</span>";
        $htmlContent .= "</div>";

        $htmlContent .= "<div class='msm_associate_optionarea'>";
        $htmlContent .= "<span class='msm_associate_option_label'>Type of information: </span>";
        $htmlContent .= "<select id='msm_associate_dropdown-$currentAssociateRecord->parent_id-$this->compid' class='msm_associated_dropdown' name='msm_associate_dropdown-$currentAssociateRecord->parent_id-$this->compid' disabled='disabled'>";
        switch ($this->type)
        {
            case "Comment":
                $htmlContent .= "<option value='Comment' selected='selected'>Comment</option>";
                $htmlContent .= "<option value='Explanation'>Explanation</option>";
                $htmlContent .= "<option value='Example'>Example</option>";
                $htmlContent .= "<option value='Illustration'>Illustration</option>";
                $htmlContent .= "<option value='Remark'>Remark</option>";
                $htmlContent .= "<option value='Exploration'>Exploration</option>";
                break;
            case "Explanation":
                $htmlContent .= "<option value='Comment'>Comment</option>";
                $htmlContent .= "<option value='Explanation' selected='selected'>Explanation</option>";
                $htmlContent .= "<option value='Example'>Example</option>";
                $htmlContent .= "<option value='Illustration'>Illustration</option>";
                $htmlContent .= "<option value='Remark'>Remark</option>";
                $htmlContent .= "<option value='Exploration'>Exploration</option>";
                break;
            case "Example":
                $htmlContent .= "<option value='Comment'>Comment</option>";
                $htmlContent .= "<option value='Explanation'>Explanation</option>";
                $htmlContent .= "<option value='Example' selected='selected'>Example</option>";
                $htmlContent .= "<option value='Illustration'>Illustration</option>";
                $htmlContent .= "<option value='Remark'>Remark</option>";
                $htmlContent .= "<option value='Exploration'>Exploration</option>";
                break;
            case "Illustration":
                $htmlContent .= "<option value='Comment'>Comment</option>";
                $htmlContent .= "<option value='Explanation'>Explanation</option>";
                $htmlContent .= "<option value='Example'>Example</option>";
                $htmlContent .= "<option value='Illustration' selected='selected'>Illustration</option>";
                $htmlContent .= "<option value='Remark'>Remark</option>";
                $htmlContent .= "<option value='Exploration'>Exploration</option>";
                break;
            case "Remark":
                $htmlContent .= "<option value='Comment'>Comment</option>";
                $htmlContent .= "<option value='Explanation'>Explanation</option>";
                $htmlContent .= "<option value='Example'>Example</option>";
                $htmlContent .= "<option value='Illustration'>Illustration</option>";
                $htmlContent .= "<option value='Remark' selected='selected'>Remark</option>";
                $htmlContent .= "<option value='Exploration'>Exploration</option>";
                break;
            case "Exploration":
                $htmlContent .= "<option value='Comment'>Comment</option>";
                $htmlContent .= "<option value='Explanation'>Explanation</option>";
                $htmlContent .= "<option value='Example'>Example</option>";
                $htmlContent .= "<option value='Illustration'>Illustration</option>";
                $htmlContent .= "<option value='Remark'>Remark</option>";
                $htmlContent .= "<option value='Exploration' selected='selected'>Exploration</option>";
                break;
        }
        $htmlContent .= "</select>";
        $htmlContent .= "</div>";

        foreach ($this->infos as $info)
        {
            $htmlContent .= $info->displayData();
        }

        $htmlContent .= "</div>";

        return $htmlContent;
    }

    public function loadData($compid)
    {
        global $DB;

        $associateCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $associateCompRecord->unit_id;

        $associateRecord = $DB->get_record($this->tablename, array('id' => $this->id));

        $this->type = $associateRecord->description;

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            switch ($childTable->tablename)
            {
                case "msm_info":
                    $info = new EditorInfo();
                    $info->loadData($child->id);
                    $this->infos[] = $info;
                    break;
            }
        }

        return $this;
    }

    public function displayPreview($prevClass = '', $id = '')
    {
        $previewHtml = '';
        if ($prevClass == 'def')
        {

            if (!empty($this->infos[0]->ref))
            {
                $previewHtml .= "<li class='defminibutton' id='defminibutton-$id' onmouseover='infoopen(\"$id\")'>";
                $previewHtml .= "<span style='cursor:pointer'>";
                $previewHtml .= $this->type;
                $previewHtml .= "</span>";
                $previewHtml .= "</li>";
                $previewHtml .= "<div class='refcontent' id='refcontent-$id' style='display:none;'>";
// foreach ($this->childs as $child)
// {
                $previewHtml .= $this->infos[0]->ref->displayPreview($id);
// }
                $previewHtml .= "</div>";
            }
            else
            {
                $previewHtml .= "<li class='defminibutton' id='defminibutton-" . $id . "' onmouseover='popup(\"$id\")'>";
                $previewHtml .= "<span style='cursor:pointer'>";
                $previewHtml .= $this->type;
                $previewHtml .= "</span>";
                $previewHtml .= "</li>";
            }

            $previewHtml .= $this->infos[0]->displayPreview($id);
        }

        if ($prevClass == 'theorem')
        {
            if (!empty($this->infos[0]->ref))
            {
// $previewHtml .= "<li class='minibutton' id='minibutton-" . $this->infos[0]->compid . "' onmouseover='infoopen(" . $this->infos[0]->compid . ")' onclick='showRightpage(" . $this->infos[0]->compid . ")'>";
                $previewHtml .= "<li class='minibutton' id='minibutton-$id' onmouseover='infoopen(\"$id\")'>";
                $previewHtml .= "<span style='cursor:pointer'>";
                $previewHtml .= $this->type;
                $previewHtml .= "</span>";
                $previewHtml .= "</li>";
                $previewHtml .= "<div class='refcontent' id='refcontent-$id' style='display:none;'>";
// foreach ($this->childs as $child)
// {
                $previewHtml .= $this->infos[0]->ref->displayPreview($id);
// }
                $previewHtml .= "</div>";
            }
            else
            {
                $previewHtml .= "<li class='minibutton' id='minibutton-$id' onmouseover='popup(\"$id\")'>";
                $previewHtml .= "<span style='cursor:pointer'>";
                $previewHtml .= $this->type;
                $previewHtml .= "</span>";
                $previewHtml .= "</li>";
            }

            $previewHtml .= $this->infos[0]->displayPreview($id);
        }


        if ($prevClass == 'comment')
        {
            if (!empty($this->infos[0]->ref))
            {
// $previewHtml .= "<li class='commentminibutton' id='commentminibutton-" . $this->infos[0]->compid . "' onmouseover='infoopen(" . $this->infos[0]->compid . ")' onclick='showRightpage(" . $this->infos[0]->compid . ")'>";
                $previewHtml .= "<li class='commentminibutton' id='commentminibutton-$id' onmouseover='infoopen(\"$id\")'>";
                $previewHtml .= "<span style='cursor:pointer'>";
                $previewHtml .= $this->type;
                $previewHtml .= "</span>";
                $previewHtml .= "</li>";
                $previewHtml .= "<div class='refcontent' id='refcontent-$id' style='display:none;'>";
// foreach ($this->childs as $child)
// {
                $previewHtml .= $this->infos[0]->ref->displayPreview($id);
// }
                $previewHtml .= "</div>";
            }
            else
            {
                $previewHtml .= "<li class='commentminibutton' id='commentminibutton-$id' onmouseover='popup(\"$id\")'>";
                $previewHtml .= "<span style='cursor:pointer'>";
                $previewHtml .= $this->type;
                $previewHtml .= "</span>";
                $previewHtml .= "</li>";
            }

            $previewHtml .= $this->infos[0]->displayPreview($id);
        }

        return $previewHtml;
    }

}

?>
