<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorInfo
 *
 * @author User
 */
class EditorInfo extends EditorElement
{

    public $id;
    public $compid;
    public $caption;
    public $content;
    public $position;
    public $errorArray = array();
    public $subordinates = array();
    public $ref;

    function __construct()
    {
        $this->tablename = 'msm_info';
    }

    // idNumber --> parentid-currentelementid
    public function getFormData($idNumber, $position)
    {
        $this->position = $position;
        
        $subid = explode("|", $idNumber);

        if (sizeof($subid) > 1)
        {
            $allSubordinateValues = $_POST['msm_unit_subordinate_container'];
//        
            $tempallSubordinates = explode(",", $allSubordinateValues);

            // copying the array from string processing above (due to it ending in comma, the last
            // element is empty)
            $allSubordinates = array();

            for ($i = 0; $i < sizeof($tempallSubordinates) - 1; $i++)
            {
                $allSubordinates[] = $tempallSubordinates[$i];
            }

            $i = 0;
            foreach ($allSubordinates as $index => $subordinate)
            {
                $idValuePair = explode("|", $subordinate);

                if (strpos($idValuePair[0], $subid[0]) !== false)
                {
                    if (strpos($idValuePair[0], 'info') !== false)
                    {
                        if ($idValuePair[0] == 'msm_subordinate_infoTitle-' . $subid[0])
                        {
                            // converting &gt;..etc back to html characters 
                            $this->caption = htmlspecialchars_decode($idValuePair[1]);
                        }
                        else if ($idValuePair[0] == 'msm_subordinate_infoContent-' . $subid[0])
                        {
                            $this->content = htmlspecialchars_decode($idValuePair[1]);
                        }
                    }
                }
            }
            // add reference processing stuff
        }
        else if (sizeof($subid) == 1)
        {
            $this->caption = $_POST['msm_info_title-' . $idNumber];

            if ($_POST['msm_info_content-' . $idNumber] != '')
            {
                $this->content = $_POST['msm_info_content-' . $idNumber];

                foreach ($this->processSubordinate($this->content) as $key => $subordinates)
                {
                    $this->subordinates[] = $subordinates;
                }
            }
            else
            {
                $this->errorArray[] = 'msm_info_content-' . $idNumber . '_ifr';
            }

            $refType = $_POST['msm_associate_reftype-' . $idNumber];

            $indexNumber = explode("-", $idNumber); // indexNumber[0] = parent id number

            $param = $indexNumber[0] . "|ref";

            switch ($refType)
            {
                case "Definition":
                    $def = new EditorDefinition();
                    $def->getFormData($param, '');
                    $this->ref = $def;
                    break;
                case "Theorem":
                    $theorem = new EditorTheorem();
                    $theorem->getFormData($param, '');
                    $this->ref = $theorem;
                    break;
                case "Comment":
                    $comment = new EditorComment();
                    $comment->getFormData($param, '');
                    $this->ref = $comment;
                    break;
                case "Example":
                    break;
                case "Sections of this Composition":
                    break;
            }
        }
        
        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $data->caption = $this->caption;
        $data->info_content = $this->content;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

        if (!empty($this->ref))
        {
            $this->ref->insertData($parentid, $this->compid, $msmid);
        }


        $subordinate_sibling = 0;
        foreach ($this->subordinates as $subordinate)
        {
            $subordinate->insertData($this->compid, $subordinate_sibling, $msmid);
            $subordinate_sibling = $subordinate->compid;
        }
    }

}

?>
