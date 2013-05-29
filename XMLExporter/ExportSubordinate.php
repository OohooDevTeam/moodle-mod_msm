<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportSubordinate
 *
 * @author User
 */
class ExportSubordinate extends ExportElement
{

    public $id;
    public $compid;
    public $hot;
    public $info;

    //put your code here
    public function exportData()
    {
        $subordinateCreator = new DOMDocument();
        $subordinateCreator->formatOutput = true;
        $subordinateCreator->preserveWhiteSpace = false;
        $subordinateNode = $subordinateCreator->createElement("subordinate");

        $hotNode = $subordinateCreator->createElement("hot");
        $hotInfo = explode(",", $this->hot);
        $hotText = $subordinateCreator->createTextNode($hotInfo[1]);

        $hotNode->appendChild($hotText);
        $subordinateNode->appendChild($hotNode);

        if (!empty($this->info))
        {
            $infoNode = $this->info->exportData();
            $importInfoNode = $subordinateCreator->importNode($infoNode, true);
            $subordinateNode->appendChild($importInfoNode);
        }

        return $subordinateNode;
    }

    public function loadDbData($compid)
    {
        global $DB;

        $subordinateCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $subordinateUnitRecord = $DB->get_record("msm_subordinate", array("id" => $subordinateCompRecord->unit_id));

        $this->id = $subordinateUnitRecord->id;
        $this->compid = $compid;
        $this->hot = $subordinateUnitRecord->hot;

        $infoRecord = $DB->get_record("msm_compositor", array("parent_id" => $this->compid));

        if (!empty($infoRecord))
        {
            $info = new ExportInfo();
            $info->loadDbData($infoRecord->id);
            $this->info = $info;
        }
        else
        {
            echo "empty info record";
        }

        return $this;
    }

}

?>
