<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorInContent
 *
 * @author User
 */
class EditorInContent extends EditorElement
{

    public $id;
    public $compid;
    public $errorArray = array();
    public $additional_attribute;
    public $type;
    public $content;
    public $position;
    public $suboridnates = array();

    function __construct()
    {
        $this->tablename = 'msm_content';
    }

    public function getFormData($idNumber, $position)
    {
        $this->position = $position;

        $doc = new DOMDocument();
        $listElement = $doc->importNode($idNumber, true);

        if ($listElement->tagName == "ol")
        {
            $this->type = "ordered";
        }
        else if ($listElement->tagName == "ul")
        {
            $this->type = "unordered";
        }

        $styleAttribute = $listElement->getAttribute("style");

        if (!empty($styleAttribute))
        {
            $styleTypes = explode(";", $styleAttribute);

            foreach ($styleTypes as $style)
            {
                $styleValue = explode(":", $style);

                if ($styleValue[0] == "list-style-type")
                {
                    $this->additional_attribute = $styleValue[1];
                }
            }
        }
        else
        {
            $this->additional_attribute = null;
        }

        $this->content = $doc->saveHTML($listElement);
        
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
        $data->additional_attribute = $this->additional_attribute;
        $data->content = $this->content;
        $data->type = $this->type;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record("msm_table_collection", array('tablename' => $this->tablename))->id;
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
