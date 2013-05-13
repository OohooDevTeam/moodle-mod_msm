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
    public $editorname;

    function __construct()
    {
        $this->tablename = 'msm_img';
    }

    // code only implements plain texts w/o maps...etc
    // idNumber == DOMElement with tag name of img
    public function getFormData($idNumber, $edName = '')
    {
        $doc = new DOMDocument();
        $imgNode = $doc->importNode($idNumber, true);

        $this->src = $imgNode->getAttribute("src");

        $this->height = $imgNode->getAttribute("height");
        $this->width = $imgNode->getAttribute("width");
        $this->string_id = $imgNode->getAttribute("alt");
        $this->editorname = $edName;
        
        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB, $CFG;
        
         $data = new stdClass();
         $data->string_id = $this->string_id;
//         $data->description = $this->description;
//         $data->extended_caption = $this->caption;
         $data->src = $this->src;
         $data->height = $this->height;
         $data->width = $this->width;
         
         $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record("msm_table_collection", array("tablename"=>$this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;
        
        $this->compid = $DB->insert_record("msm_compositor", $compData);        
        
        $msm = $DB->get_record('msm', array('id' => $msmid), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        $urlInfo = explode("/", $this->src);
        $draftitemid = $urlInfo[sizeof($urlInfo)-2];
        print_object($draftitemid);
        file_save_draft_area_files($draftitemid, $context->id, "mod_msm", "msm_images", $this->id);

        /**
         * Saves files from a draft file area to a real one (merging the list of files).
         * Can rewrite URLs in some content at the same time if desired.
         *
         * @category files
         * @global stdClass $USER
         * @param int $draftitemid the id of the draft area to use. Normally obtained
         *      from file_get_submitted_draft_itemid('elementname') or similar.
         * @param int $contextid This parameter and the next two identify the file area to save to.
         * @param string $component
         * @param string $filearea indentifies the file area.
         * @param int $itemid helps identifies the file area.
         * @param array $options area options (subdirs=>false, maxfiles=-1, maxbytes=0)
         * @param string $text some html content that needs to have embedded links rewritten
         *      to the @@PLUGINFILE@@ form for saving in the database.
         * @param bool $forcehttps force https urls.
         * @return string|null if $text was passed in, the rewritten $text is returned. Otherwise NULL.
         */
//       
    }

    public function displayData()
    {
        
    }

    public function loadData($compid)
    {
        
    }

    public function displayPreview()
    {
        $previewHtml = '';

        return $previewHtml;
    }

}

?>
