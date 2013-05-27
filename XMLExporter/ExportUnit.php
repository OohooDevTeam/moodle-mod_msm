<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportUnit
 *
 * @author User
 */
class ExportUnit extends ExportElement
{

    public $compid;
    public $id;
    public $contentchildren = array(); // includes intro/preface/summary/trialer/historical.notes/body
    public $unitchildren = array(); // comp id of any subunit elements that will be inserted as legitimate.children element
    public $dates = array();
    public $authors;
    public $title;
    public $shortname;
    public $description;
    public $standalone;
    public $unittag;
    public $acknowledgement;

    public function exportData()
    {
        
    }

    public function loadDbData($compid)
    {
        global $DB;

        $unitCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));

        $unitRecord = $DB->get_record("msm_unit", array("id" => $unitCompRecord->unit_id));

        $this->compid = $compid;
        $this->id = $unitRecord->id;
        $this->title = $unitRecord->title;
        $this->shortname = $unitRecord->short_name;
        $this->description = $unitRecord->description;
        $this->standalone = $unitRecord->standalone;
        $this->acknowledgement = $unitRecord->acknowledgements;
        $unitnameData = $DB->get_record("msm_unit_name", array("id" => $unitRecord->compchildtype));
        $this->unittag = $unitnameData->unitname;

        if ($unitnameData->depth == 0)
        {
            $msmRecord = $DB->get_record("msm", array("id" => $unitCompRecord->msm_id));

            $date = new DateTime();
            $date->setTimestamp($msmRecord->timecreated);
            $this->dates["creation"] = $date->format("Y-m-d");
        }

        $unitTableid = $DB->get_record("msm_table_collection", array("tablename" => "msm_unit"));

        $unitChildren = $DB->get_records("msm_compositor", array("parent_id" => $compid, "msm_id" => $unitCompRecord->msm_id, "table_id" => $unitTableid->id), "prev_sibling_id");

        foreach ($unitChildren as $subunit)
        {
            $nestedUnit = new ExportUnit();
            $nestedUnit->loadDbData($subunit->id);
            $this->unitchildren[] = $nestedUnit;
        }

        $contentChildren = $DB->get_records("msm_compositor", array("parent_id" => $compid, "msm_id" => $unitCompRecord->msm_id), "prev_sibling_id");

        foreach ($contentChildren as $content)
        {
            if ($content->table_id != $unitTableid->id)
            {
                $contentTable = $DB->get_record("msm_table_collection", array("id" => $content->table_id));

                switch ($contentTable->tablename)
                {
                    case "msm_def":
                        $def = new ExportDefinition();
                        $def->loadDbData($content->id);
                        $this->contentchildren[] = $def;
                        break;
                    case "msm_theorem":
                        $theorem = new ExportTheorem();
                        $theorem->loadDbData($content->id);
                        $this->contentchildren[] = $theorem;
                        break;
                    case "msm_block":
                        $block = new ExportBlock();
                        $block->loadDbData($content->id);
                        $this->contentchildren[] = $block;
                        break;
                    case "msm_comment":
                        $comment = new ExportComment();
                        $comment->loadDbData($content->id);
                        $this->contentchildren[] = $comment;
                        break;
                    case "msm_intro":
                        $intro = new ExportIntro();
                        $intro->loadDbData($content->id);
                        $this->contentchildren[] = $intro;
                        break;
                    case "msm_extra_info":
                        $extraInfo = new ExportExtraInfo();
                        $extraInfo->loadDbData($content->id);
                        $this->contentchildren[] = $extraInfo;
                        break;
                }
            }
        }

        return $this;
    }

}

?>
