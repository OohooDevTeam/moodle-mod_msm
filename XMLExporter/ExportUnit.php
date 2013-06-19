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
    public $msmid;
    public $contentchildren = array(); // includes intro/preface/summary/trialer/historical.notes/body
    public $unitchildren = array(); // comp id of any subunit elements that will be inserted as legitimate.children element
    public $dates = array();
    public $authors;
    public $title;
    public $shortname;
    public $description;
    public $standalone;
    public $unittag;

//    public $acknowledgement;

    public function exportData()
    {
        global $CFG;

        $XMLcreator = new DOMDocument();
        // to format the resulting XML document
        $XMLcreator->formatOutput = true;
        $XMLcreator->preserveWhiteSpace = false;
        $XMLcreator->encoding = "UTF-8";
//        $XMLcreator->schemaValidate("$CFG->dirroot/mod/msm/NewSchemas/Unit.xsd");
        $unitNode = $XMLcreator->createElement("unit");
        $unitNode->setAttribute("tagname", $this->unittag);
        $unitNode->setAttribute("unitid", "$this->msmid-$this->compid");
        $unitNode->setAttribute("standalone", $this->standalone);

        $descriptionNode = null;
        $titlesNode = null;
//        $authorNode = null;
//        $ackNode = null;
        $datesNode = null;
        $childrenNode = null;


        if (!empty($this->description))
        {
            $descriptionNode = $XMLcreator->createElement("description");
            $descriptionText = $XMLcreator->createTextNode($this->description);
            $descriptionNode->appendChild($descriptionText);
            $unitNode->appendChild($descriptionNode);
        }

        if ((!empty($this->title)) || (!empty($this->shortname)))
        {
            $titlesNode = $XMLcreator->createElement("titles");

            $titleNode = null;
            $plaintitleNode = null;

            if (!empty($this->title))
            {
                $titleNode = $XMLcreator->createElement("title");
                $titleText = $XMLcreator->createTextNode($this->title);
                $titleNode->appendChild($titleText);
            }
            if (!empty($this->shortname))
            {
                $plaintitleNode = $XMLcreator->createElement("plain.title");
                $plaintitleText = $XMLcreator->createTextNode($this->shortname);
                $plaintitleNode->appendChild($plaintitleText);
            }

            if (!empty($titleNode))
            {
                $titlesNode->appendChild($titleNode);
            }
            if (!empty($plaintitleNode))
            {
                $titlesNode->appendChild($plaintitleNode);
            }
            $unitNode->appendChild($titlesNode);
        }

        // currently empty
//        if(!empty($this->authors))
//        {
//            
//        }
//
//        if (!empty($this->acknowledgement))
//        {
//            print_object($this->acknowledgement);
//            $ackNode = $XMLcreator->createElement("acknowledgements");
//            $ackText = $XMLcreator->createTextNode($this->acknowledgement);
//            $ackNode->appendChild($ackText);
//            $unitNode->appendChild($ackNode);
//        }

        if (!empty($this->dates))
        {
            $datesNode = $XMLcreator->createElement("dates");

            $createdateString = explode("-", $this->dates["creation"]);

            $lastdateString = '';
            if (!empty($this->dates["lastrevision"]))
            {
                $lastdateString = explode("-", $this->dates["lastrevision"]);
            }
            else
            {
                $lastdateString = explode("-", $this->dates["creation"]);
            }

            $creationNode = $XMLcreator->createElement("creation");
            $creationDateNode = $XMLcreator->createElement("date");
            $creationMonthNode = $XMLcreator->createElement("month");
            $creationMonthText = $XMLcreator->createTextNode($createdateString[1]);
            $creationDayNode = $XMLcreator->createElement("day");
            $creationDayText = $XMLcreator->createTextNode($createdateString[2]);
            $creationYearNode = $XMLcreator->createElement("year");
            $creationYearText = $XMLcreator->createTextNode($createdateString[0]);

            $creationMonthNode->appendChild($creationMonthText);
            $creationDayNode->appendChild($creationDayText);
            $creationYearNode->appendChild($creationYearText);

            $creationDateNode->appendChild($creationMonthNode);
            $creationDateNode->appendChild($creationDayNode);
            $creationDateNode->appendChild($creationYearNode);

            $creationNode->appendChild($creationDateNode);

            $lastRevNode = $XMLcreator->createElement("last.revision");
            $lastRevDateNode = $XMLcreator->createElement("date");
            $lastRevMonthNode = $XMLcreator->createElement("month");
            $lastRevMonthText = $XMLcreator->createTextNode($lastdateString[1]);
            $lastRevDayNode = $XMLcreator->createElement("day");
            $lastRevDayText = $XMLcreator->createTextNode($lastdateString[2]);
            $lastRevYearNode = $XMLcreator->createElement("year");
            $lastRevYearText = $XMLcreator->createTextNode($lastdateString[0]);

            $lastRevMonthNode->appendChild($lastRevMonthText);
            $lastRevDayNode->appendChild($lastRevDayText);
            $lastRevYearNode->appendChild($lastRevYearText);

            $lastRevDateNode->appendChild($lastRevMonthNode);
            $lastRevDateNode->appendChild($lastRevDayNode);
            $lastRevDateNode->appendChild($lastRevYearNode);

            $lastRevNode->appendChild($lastRevDateNode);

            $datesNode->appendChild($creationNode);
            $datesNode->appendChild($lastRevNode);
            $unitNode->appendChild($datesNode);
        }

        if (!empty($this->contentchildren))
        {
            $isbody = false;
            $bodyNode = null;
            foreach ($this->contentchildren as $content)
            {
                switch (get_class($content))
                {
                    case "ExportExtraInfo":
                        if ($isbody)
                        {
                            $unitNode->appendChild($bodyNode);
                            $isbody = false;
                        }
                        $extraInfoNode = $content->exportData();
                        $newextraInfoNode = $XMLcreator->importNode($extraInfoNode, true);
                        $unitNode->appendChild($newextraInfoNode);
                        break;
                    case "ExportIntro":
                        if ($isbody)
                        {
                            $unitNode->appendChild($bodyNode);
                            $isbody = false;
                        }
                        $introNode = $content->exportData();
                        $newintroNode = $XMLcreator->importNode($introNode, true);
                        $unitNode->appendChild($newintroNode);
                        break;
                    default:
                        if (!$isbody)
                        {
                            $bodyNode = $XMLcreator->createElement("body");
                            $isbody = true;
                        }

                        if (get_class($content) != "ExportBlock")
                        {
                            $blockNode = $XMLcreator->createElement("block");
                            $blockbodyNode = $XMLcreator->createElement("block.body");

                            $contentNode = $content->exportData();
                            $newcontentNode = $XMLcreator->importNode($contentNode, true);
                            $blockbodyNode->appendChild($newcontentNode);
                            $blockNode->appendChild($blockbodyNode);
                            $bodyNode->appendChild($blockNode);
                        }
                        else
                        {
                            $contentNode = $content->exportData();
                            $newcontentNode = $XMLcreator->importNode($contentNode, true);
                            $bodyNode->appendChild($newcontentNode);
                        }
                }
            }
            if ($isbody)
            {
                $unitNode->appendChild($bodyNode);
                $isbody = false;
            }
        }

        if (!empty($this->unitchildren))
        {
            $childrenNode = $XMLcreator->createElement("legitimate.children");
            foreach ($this->unitchildren as $child)
            {
                $unitchoiceNode = $XMLcreator->createElement("unit.choice");
                $unitchoiceNode->setAttribute("unitId", "$child->msmid-$child->compid");
                $childrenNode->appendChild($unitchoiceNode);
            }
            $unitNode->appendChild($childrenNode);
        }
        $XMLcreator->appendChild($unitNode);

//        $this->makeExportFile($XMLcreator);
//        foreach($this->unitchildren as $subunit)
//        {
//            $subunit->exportData();
//        }

        return $XMLcreator;
    }

    public function loadDbData($compid)
    {
        global $DB;

        $unitCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));

        $unitRecord = $DB->get_record("msm_unit", array("id" => $unitCompRecord->unit_id));

        $this->compid = $compid;
        $this->msmid = $unitCompRecord->msm_id;
        $this->id = $unitRecord->id;
        $this->title = $unitRecord->title;
//        $this->authors = $unitRecord->authors;
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

            if ($msmRecord->timemodified > 0)
            {
                $date = new DateTime();
                $date->setTimestamp($msmRecord->timemodified);
                $this->dates["lastrevision"] = $date->format("Y-m-d");
            }
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
