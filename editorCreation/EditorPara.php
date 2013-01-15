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
    public $position;
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
    function getFormData($idNumber, $position)
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

        $this->position = $position;

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
        
        foreach($this->subordinates as $subordinate)
        {
            $subordinate->insertData($this->compid, $subordinate_sibling, $msmid);
            $subordinate_sibling = $subordinate->compid;
        }
    }

}

?>
