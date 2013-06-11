<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorImage
 *
 * @author User
 */
class EditorImage extends EditorElement
{

    public $id;
    public $compid;
    public $string_id; // save the alt name
    public $src;
    public $description;
    public $caption;
    public $height;
    public $width;
    public $fileoptions;

    function __construct()
    {
        $this->tablename = 'msm_img';
    }

    // code only implements plain texts w/o maps...etc
    // idNumber == DOMElement with tag name of img
    public function getFormData($idNumber)
    {
        global $CFG;

        $doc = new DOMDocument();
        $imgNode = $doc->importNode($idNumber, true);
        $src = null;
        // processing the src value to add the wwwroot to the front of the string and remove the 
        // ../../ due to relative pathing

        $srcAttr = $imgNode->getAttribute("src");
        $wwwroot = "$CFG->wwwroot/";

        if (strstr(trim($srcAttr), trim($wwwroot)))
        {
            $src = $srcAttr;
        }
        else
        {
            $srcInfo = explode("/", $srcAttr);
            $src = $CFG->wwwroot;
            for ($i = 2; $i < sizeof($srcInfo); $i++)
            {
                $src .= "/" . $srcInfo[$i];
            }
        }

        $this->src = $src;

        $this->height = $imgNode->getAttribute("height");
        $this->width = $imgNode->getAttribute("width");
        $this->string_id = $imgNode->getAttribute("alt");
        $this->fileoptions = json_decode($_POST["msm_file_options"])->image;

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB, $CFG;

        $data = new stdClass();
        $data->string_id = $this->string_id;
// $data->description = $this->description;
// $data->extended_caption = $this->caption;
        $data->src = $this->src . "||" . $msmid;
        $data->height = $this->height;
        $data->width = $this->width;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record("msm_table_collection", array("tablename" => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record("msm_compositor", $compData);
    }

    public function displayData()
    {
        
    }

    public function loadData($compid)
    {
        global $DB;

        $imgCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));

        $this->compid = $imgCompRecord->id;
        $this->id = $imgCompRecord->unit_id;

        $imgRecord = $DB->get_record($this->tablename, array("id" => $this->id));

        $srcInfo = explode("||", $imgRecord->src);

        $this->src = $srcInfo[0];
        $this->height = $imgRecord->height;
        $this->width = $imgRecord->width;
        $this->string_id = $imgRecord->string_id;

        return $this;
    }

    public function displayPreview()
    {
        $previewHtml = '';

        return $previewHtml;
    }

}

?>
