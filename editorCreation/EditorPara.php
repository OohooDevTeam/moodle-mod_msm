<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorPara
 *
 * @author User
 */
class EditorPara extends EditorElement
{

    public $align;
    public $description;
    public $content;
    public $id;
    public $compid;
    public $subordinates = array();

    function __construct()
    {
        $this->tablename = "msm_para";
    }

    /**
     * 
     * @param DOMElement $idNumber corresponds to para child from content
     * @param type $position
     * @return \EditorPara
     */
    function getFormData($idNumber)
    {
        $doc = new DOMDocument();
        $paraNode = $doc->importNode($idNumber, true);

        $style = $paraNode->getAttribute("style");
        // break style attribute to each of properties
        $styleProperites = explode(";", $style);
        foreach ($styleProperites as $property)
        {
            $propertyValue = explode(":", $property);

            if ($propertyValue[0] == "text-align")
            {
                $this->align = $propertyValue[1];
            }
        }

        $this->content = $doc->saveHTML($paraNode);

        foreach ($this->processSubordinate($this->content) as $key => $subordinates)
        {
            $this->subordinates[] = $subordinates;
        }

        return $this;
    }

    function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        if (!empty($this->align))
        {
            $data->para_align = $this->align;
        }
        $data->para_content = $this->content;

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

        $htmlContent .= $this->content;
        
        foreach($this->subordinates as $subordinate)
        {
            $htmlContent .= $subordinate->displayData();
        }

        return $htmlContent;
    }

    public function loadData($compid)
    {
        global $DB;

        $paraCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $paraCompRecord->unit_id;

        $paraRecord = $DB->get_record($this->tablename, array('id' => $this->id));

        $this->align = $paraRecord->para_align;
        $this->content = $paraRecord->para_content;
        $this->description = $paraRecord->description;

        $childRecords = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');

        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            if ($childTable->tablename == 'msm_subordinate')
            {
                $subordinate = new EditorSubordinate();
                $subordinate->loadData($child->id);
                $this->subordinates[] = $subordinate;
            }
//            else
//            {
//                echo "another child of para? " . $childTable->tablename;
//            }
        }

        return $this;
    }

}

?>
