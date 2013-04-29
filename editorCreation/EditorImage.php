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
    public $src;
    public $description;
    public $caption;
    public $height;
    public $width;
    
    function __construct()
    {
        $this->tablename = 'msm_img';
    }

    // code only implements plain texts w/o maps...etc
    // idNumber == DOMElement with tag name of img
    public function getFormData($idNumber)
    {
        $doc = new DOMDocument();
        $imgNode = $doc->importNode($idNumber, true);
        
        $src = $imgNode->getAttribute("src");
        
        $srcInfo = explode("/", $src);
//        
//        $draftitemid = $srcInfo[6];
//        $contextid = $srcInfo[3];
//        $component = $srcInfo[4];
//        $filearea = 
        
        print_object($srcInfo);
        
//        $string = file_prepare_draft_area($draftitemid, $contextid, $component, $filearea, $itemid);
        
        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;
        
        $data = new stdClass();
        
        $compData = new stdClass();
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
