<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportTable
 *
 * @author User
 */
class ExportTable extends ExportElement
{
    public $id;
    public $compid;
    public $content;
    public $class;
    public $summary;
    public $title;
    public $subordinates = array();
    public $medias = array();
    
    public function exportData()
    {
        $tableCreator = new DOMDocument();

        $tableCreator->loadXML($this->content);
        
        $tableNode = $tableCreator->documentElement;

        return $tableNode;
    }

    public function loadDbData($compid)
    {
        global $DB;
        
        $tableCompRecord = $DB->get_record("msm_compositor", array("id"=>compid));
        $tableUnitRecord = $DB->get_record("msm_table", array("id"=>$tableCompRecord->unit_id));
        
        $this->id = $tableUnitRecord->id;
        $this->compid = $compid;
        $this->content = $tableUnitRecord->table_content;
        $this->class = $tableUnitRecord->table_class;
        $this->summary = $tableUnitRecord->table_summary;
        $this->title = $tableUnitRecord->title;
        
        $childRecords = $DB->get_records("msm_compositor", array("parent_id"=>$this->id), "prev_sibling_id");
        
        foreach($childRecords as $child)
        {
            $childtable = $DB->get_record("msm_table_collection", array("id"=>$child->table_id));
            
            if($childtable->tablename == "msm_subordinate")
            {
                $subordinate = new ExportSubordinate();
                $subordinate->loadDbData($child->id);
                $this->subordinates[] = $subordinate;
            }
            else if($childtable->tablename == "msm_media")
            {
                $media = new ExportMedia();
                $media->loadDbData($child->id);
                $this->medias[] = $media;                
            }
        }
        
        return $this;
    }
}

?>
