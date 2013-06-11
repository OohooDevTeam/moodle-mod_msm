<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportComment
 *
 * @author User
 */
class ExportComment extends ExportElement
{

    public $id;
    public $compid;
    public $caption;
    public $description;
    public $type;
    public $content;
    public $associate = array();
    public $subordinates = array();
    public $medias = array();

    public function exportData($flag = '')
    {
        $commentCreator = new DOMDocument();
        $commentCreator->formatOutput = true;
        $commentCreator->preserveWhiteSpace = false;
        $commentNode = $commentCreator->createElement("comment");
        $commentNode->setAttribute("type", $this->type);
        $commentNode->setAttribute("id", $this->compid);

        if (!empty($this->caption))
        {
            $captionNode = $commentCreator->createElement("caption");
            $captionText = $commentCreator->createTextNode($this->caption);
            $captionNode->appendChild($captionText);
            $commentNode->appendChild($captionNode);
        }

        if (!empty($this->description))
        {
            $descriptionNode = $commentCreator->createElement("description");
            $descriptionText = $commentCreator->createTextNode($this->description);
            $descriptionNode->appendChild($descriptionText);
            $commentNode->appendChild($descriptionNode);
        }

        $commentbodyNode = $commentCreator->createElement("comment.body");
        $createdbodyNode = $this->createXmlContent($commentCreator, $this->content, $commentbodyNode, $this);
        $bodyNode = $commentCreator->importNode($createdbodyNode, true);
        $commentNode->appendChild($bodyNode);

        if (!empty($this->associates))
        {
            foreach ($this->associates as $associate)
            {
                $associateNode = $associate->exportData();
                $newassociateNode = $commentCreator->importNode($associateNode, true);
                $commentNode->appendChild($newassociateNode);
            }
        }
        
        if (!empty($flag))
        {
            $this->createXMLFile($this, $commentNode);
        }
        else
        {
            return $commentNode;
        }
    }

    public function loadDbData($compid)
    {
        global $DB;

        $commentCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $commentRecord = $DB->get_record("msm_comment", array("id" => $commentCompRecord->unit_id));

        $this->id = $commentRecord->id;
        $this->compid = $compid;
        $this->caption = $commentRecord->caption;
        $this->description = $commentRecord->description;
        $this->type = $commentRecord->comment_type;
        $this->content = $commentRecord->comment_content;

        $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->compid), 'prev_sibling_id');

        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));
            if ($childTable->tablename == "msm_subordinate")
            {
                $subordinate = new ExportSubordinate();
                $subordinate->loadDbData($child->id);
                $this->subordinates[] = $subordinate;
            }
            else if ($childTable->tablename == "msm_associate")
            {
                $associate = new ExportAssociate();
                $associate->loadDbData($child->id);
                $this->associates[] = $associate;
            }
            else if($childTable->tablename == "msm_media")
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
