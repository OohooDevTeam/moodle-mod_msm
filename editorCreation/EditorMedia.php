<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorMedia
 *
 * @author User
 */
class EditorMedia extends EditorElement
{

    public $id;
    public $compid;
    public $string_id;
    public $active;
    public $inline;
    public $media_type;
    public $image;
    public $isRef;

//    public $images = array();

    function __construct()
    {
        $this->tablename = "msm_media";
    }

    // idNumber is img domElement
    public function getFormData($idNumber)
    {
        $this->active = 0; //default setup for now until plugin is developed
        $this->inline = 0; //default setup for now until plugin is developed
        $this->media_type = "images"; //default setup for now until plugin is developed

        $image = new EditorImage();
        $image->getFormData($idNumber);
        $this->image = $image;

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();

        if (empty($this->isRef))
        {
            $data->active = $this->active;
            $data->inline = $this->inline;
            $data->media_type = $this->media_type;

            $this->id = $DB->insert_record($this->tablename, $data);
        }
        else
        {
            $childRecords = $DB->get_record("msm_compositor", array("parent_id" => $this->isRef));
            $childTable = $DB->get_record("msm_table_collection", array("id" => $childRecords->table_id));

            if ($childTable->tablename == "msm_img")
            {
                $img = new EditorImage();
                $img->id = $childRecords->unit_id;
                $img->isRef = $childRecords->id;
                $this->image = $img;
            }
        }

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->table_id = $DB->get_record("msm_table_collection", array("tablename" => $this->tablename))->id;
        $compData->unit_id = $this->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record("msm_compositor", $compData);

        $this->image->insertData($this->compid, 0, $msmid);
    }

    public function loadData($compid)
    {
        global $DB;

        $mediaCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));

        $this->id = $mediaCompRecord->unit_id;
        $this->compid = $mediaCompRecord->id;

        $mediaRecord = $DB->get_record($this->tablename, array("id" => $this->id));

        $this->inline = $mediaRecord->inline;
        $this->active = $mediaRecord->active;

        $childRecord = $DB->get_record("msm_compositor", array("parent_id" => $compid));

        if (!empty($childRecord))
        {
            $childTable = $DB->get_record("msm_table_collection", array("id" => $childRecord->table_id));

            if ($childTable->tablename == "msm_img")
            {
                $this->media_type = "images";
                $image = new EditorImage();
                $image->loadData($childRecord->id);
                $this->image = $image;
            }
        }
        return $this;
    }

    public function displayData()
    {
        $htmlContent = '';
        $htmlContent .= $this->image->displayData();
        return $htmlContent;
    }

    public function displayPreview()
    {
        $previewContent = '';
        $previewContent .= $this->image->displayPreview();
        return $previewContent;
    }

}

?>
