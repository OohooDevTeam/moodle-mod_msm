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

        // processing the src value to add the wwwroot to the front of the string and remove the 
        // ../../ due to relative pathing
        $srcInfo = explode("/", $imgNode->getAttribute("src"));
        $src = $CFG->wwwroot;
        for ($i = 2; $i < sizeof($srcInfo); $i++)
        {
            $src .= "/" . $srcInfo[$i];
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

        require_once("$CFG->libdir/filelib.php");

        $data = new stdClass();
        $data->string_id = $this->string_id;
// $data->description = $this->description;
// $data->extended_caption = $this->caption;
        $data->src = $this->src;
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

        // converting src from draft file url to pluginfile url that moodle recognizes for db
        $msm = $DB->get_record('msm', array('id' => $msmid), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        $src = file_save_draft_area_files($this->fileoptions->itemid, $context->id, "mod_msm", $this->fileoptions->env, $this->id, array('subdirs' => 0, 'maxbytes' => 10000000, 'maxfiles' => 1), $this->src);
//        $files = $fs->get_area_files($context->id, "mod_msm", $this->fileoptions->env);
//        $file = $files[0];
//        
//        $url = "{$CFG->wwwroot}/pluginfile.php/{$file->get_contextid()}/mod_msm/editor";
//        $filename = $file->get_filename();
//        $fileurlname = str_replace(' ', '%20', $filename);
//        $fileurl = $url . $file->get_filepath() . $file->get_itemid() . '/' . $fileurlname;        

        $this->src = $src;

        $data->id = $this->id;
        $data->src = $this->src;
        // update the img record with the converted url with @@PLUGINFILE@@
        $this->id = $DB->update_record($this->tablename, $data);
    }

    public function displayData()
    {
        
    }

    public function loadData($compid)
    {
        global $DB, $CFG;

        $imgCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));

        $this->compid = $imgCompRecord->id;
        $this->id = $imgCompRecord->unit_id;

        $imgRecord = $DB->get_record($this->tablename, array("id" => $this->id));

        $src = $imgRecord->src;

        $this->height = $imgRecord->height;
        $this->width = $imgRecord->width;
        $this->string_id = $imgRecord->string_id;
    }

    public function displayPreview()
    {
        $previewHtml = '';

        return $previewHtml;
    }

}

?>
