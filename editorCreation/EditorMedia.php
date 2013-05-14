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
    public $imagemaps = array();
    public $image;

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
        $data->active = $this->active;
        $data->inline = $this->inline;
        $data->media_type = $this->media_type;

        $this->id = $DB->insert_record($this->tablename, $data);

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
        
    }

    public function displayData()
    {
        
    }

    public function displayPreview()
    {
        
    }

}

?>
