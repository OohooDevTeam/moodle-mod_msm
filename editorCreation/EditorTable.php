<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorTable
 *
 * @author User
 */
class EditorTable extends EditorElement
{
    public $position;
    public $id;
    public $compid;
    public $content;
    
    function __construct()
    {
        $this->tablename = 'msm_table';
    }

    // idNumber is the DOMElement with Table element as a root
    public function getFormData($idNumber, $position)
    {
        $this->position = $position;
        
        $doc = new DOMDocument();
        $paraNode = $doc->importNode($idNumber, true);
        $this->content = $doc->saveXML($paraNode);
        
        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;
        
        $data = new stdClass();
        $data->table_content = $this->content;
        
        $this->id = $DB->insert_record($this->tablename, $data);
        
        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename'=>$this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;
        
        $this->compid = $DB->insert_record('msm_compositor', $compData);
    }

    public function displayData()
    {
        
    }

    public function loadData($compid)
    {
        
    }
    
    
}

?>
