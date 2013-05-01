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
        
        $this->src = $imgNode->getAttribute("src");
      
        $this->height = $imgNode->getAttribute("height");
        $this->width = $imgNode->getAttribute("width");
        $this->string_id = $imgNode->getAttribute("alt");
        
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
