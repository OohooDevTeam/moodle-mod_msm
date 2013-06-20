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

    public $id;
    public $compid;
    public $content;
    public $subordinates = array();

    function __construct()
    {
        $this->tablename = 'msm_table';
    }

    // idNumber is the DOMElement with Table element as a root
    public function getFormData($idNumber)
    {
        $doc = new DOMDocument();

        $idNumber->setAttribute("class", "mathtable");

        if (!$idNumber->hasAttribute("style"))
        {
            $idNumber->setAttribute("style", "width:100% !important");
        }

        $paraNode = $doc->importNode($idNumber, true);

        $this->content = $doc->saveXML($paraNode);

        foreach ($this->processSubordinate($this->content) as $key => $subordinates)
        {
            $this->subordinates[] = $subordinates;
        }

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
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

        $subordinate_sibling = 0;
        foreach ($this->subordinates as $subordinate)
        {
            $subordinate->insertData($this->compid, $subordinate_sibling, $msmid);
            $subordinate_sibling = $subordinate->compid;
        }
    }

    public function displayData()
    {
        $htmlContent = '';
        $htmlContent .= html_entity_decode($this->content);
        return $htmlContent;
    }

    public function loadData($compid)
    {
        global $DB;

        $tableCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $tableCompRecord->unit_id;

        $tableRecord = $DB->get_record($this->tablename, array('id' => $this->id));

        $this->content = $tableRecord->table_content;

        $childRecords = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');
        
        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            if ($childTable->tablename == "msm_subordinate")
            {
                $subordinate = new EditorSubordinate();
                $subordinate->loadData($child->id);
                $this->subordinates[] = $subordinate;
            }            
        }

        return $this;
    }

    public function displayPreview()
    {
        $previewHtml = '';
        $previewHtml .= html_entity_decode($this->content);

        if (!empty($this->subordinates))
        {
            foreach ($this->subordinates as $subordinate)
            {
                $previewHtml .= $subordinate->displayPreview();
            }
        }

        return $previewHtml;
    }

}

?>
