<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Info
 *
 * @author User
 */
class MathInfo extends Element
{

    public $position;
    public $content;
    public $caption;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_info';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        //$this->content = array();
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->medias = array();

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName != 'info.caption')
                {
                    foreach ($this->processIndexAuthor($child, $position) as $indexauthor)
                    {
                        $this->indexauthors[] = $indexauthor;
                    }

                    foreach ($this->processIndexGlossary($child, $position) as $indexglossary)
                    {
                        $this->indexglossarys[] = $indexglossary;
                    }

                    foreach ($this->processIndexSymbols($child, $position) as $indexsymbol)
                    {
                        $this->indexsymbols[] = $indexsymbol;
                    }
                    foreach ($this->processSubordinate($child, $position) as $subordinate)
                    {
                        $this->subordinates[] = $subordinate;
                    }

                    foreach ($this->processMedia($child, $position) as $media)
                    {
                        $this->medias[] = $media;
                    }

                    foreach ($this->processContent($child, $position) as $content)
                    {
                        $this->content .= $content;
                    }
                }
                else if ($child->tagName == 'info.caption')
                {
                    $this->caption = $this->getContent($child);
                }
            }
        }
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position, $parentid = '', $siblingid = '')
    {
        global $DB;

        $data = new stdClass();
        if (!empty($this->caption))
        {
            $data->caption = $this->caption;
        }
        if (!empty($this->content))
        {
            $data->info_content = $this->content;

            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
        }

        $elementPositions = array();
        $sibling_id = null;


        if (!empty($this->subordinates))
        {
            foreach ($this->subordinates as $key => $subordinate)
            {
                $elementPositions['subordinate' . '-' . $key] = $subordinate->position;
            }
        }

        if (!empty($this->indexauthors))
        {
            foreach ($this->indexauthors as $key => $indexauthor)
            {
                $elementPositions['indexauthor' . '-' . $key] = $indexauthor->position;
            }
        }

        if (!empty($this->indexglossarys))
        {
            foreach ($this->indexglossarys as $key => $indexglosary)
            {
                $elementPositions['indexglossary' . '-' . $key] = $indexglosary->position;
            }
        }

        if (!empty($this->indexsymbols))
        {
            foreach ($this->indexsymbols as $key => $indexsymbol)
            {
                $elementPositions['indexsymbol' . '-' . $key] = $indexsymbol->position;
            }
        }

        if (!empty($this->medias))
        {
            foreach ($this->medias as $key => $media)
            {
                $elementPositions['media' . '-' . $key] = $media->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(subordinate.\d+)$/", $element) ? true : false):
                    $subordinateString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $subordinate = $this->subordinates[$subordinateString[1]];
                        $subordinate->saveIntoDb($subordinate->position, $this->compid);
                        $sibling_id = $subordinate->compid;
                    }
                    else
                    {
                        $subordinate = $this->subordinates[$subordinateString[1]];
                        $subordinate->saveIntoDb($subordinate->position, $this->compid, $sibling_id);
                        $sibling_id = $subordinate->compid;
                    }
                    break;

                case(preg_match("/^(indexauthor.\d+)$/", $element) ? true : false):
                    $indexauthorString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $indexauthor = $this->subordinates[$indexauthorString[1]];
                        $indexauthor->saveIntoDb($indexauthor->position, $this->compid);
                        $sibling_id = $indexauthor->compid;
                    }
                    else
                    {
                        $indexauthor = $this->subordinates[$indexauthorString[1]];
                        $indexauthor->saveIntoDb($indexauthor->position, $this->compid, $sibling_id);
                        $sibling_id = $indexauthor->compid;
                    }
                    break;

                case(preg_match("/^(indexsymbol.\d+)$/", $element) ? true : false):
                    $indexsymbolString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $indexsymbol = $this->indexsymbols[$indexsymbolString[1]];
                        $indexsymbol->saveIntoDb($indexsymbol->position, $this->compid);
                        $sibling_id = $indexsymbol->compid;
                    }
                    else
                    {
                        $indexsymbol = $this->indexsymbols[$indexsymbolString[1]];
                        $indexsymbol->saveIntoDb($indexsymbol->position, $this->compid, $sibling_id);
                        $sibling_id = $indexsymbol->compid;
                    }
                    break;

                case(preg_match("/^(indexglossary.\d+)$/", $element) ? true : false):
                    $indexglossaryString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $indexglossary = $this->indexglossarys[$indexglossaryString[1]];
                        $indexglossary->saveIntoDb($indexglossary->position, $this->compid);
                        $sibling_id = $indexglossary->compid;
                    }
                    else
                    {
                        $indexglossary = $this->indexglossarys[$indexglossaryString[1]];
                        $indexglossary->saveIntoDb($indexglossary->position, $this->compid, $sibling_id);
                        $sibling_id = $indexglossary->compid;
                    }
                    break;

                case(preg_match("/^(media.\d+)$/", $element) ? true : false):
                    $mediaString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $media = $this->medias[$mediaString[1]];
                        $media->saveIntoDb($media->position, $this->compid);
                        $sibling_id = $media->compid;
                    }
                    else
                    {
                        $media = $this->medias[$mediaString[1]];
                        $media->saveIntoDb($media->position, $this->compid, $sibling_id);
                        $sibling_id = $media->compid;
                    }
                    break;
            }
        }
    }

    function loadFromDb($id, $compid)
    {
        global $DB;

        $infoRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($infoRecord))
        {
            if (empty($infoRecord->caption))
            {
                $this->caption = null;
            }
            else
            {
                $this->caption = $infoRecord->caption;
            }
            $this->info_content = $infoRecord->info_content;
            $this->id = $infoRecord->id;
            $this->compid = $compid;
        }

//        $tableid = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
//
//        $whereclause = "unit_id='" . $this->id . "'" . "and table_id='" . $tableid . "'";
//
//        $infoCompRecord = $DB->get_record_select('msm_compositor', $whereclause);
//
//        $this->compid = $infoCompRecord->id;

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        $this->medias = array();
        $this->subordinates = array();
        
        foreach ($childElements as $child)
        {
            $childtable = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            switch ($childtable)
            {
                case('msm_subordinate'):
                    $subordinate = new Subordinate();
                    $subordinate->loadFromDb($child->unit_id, $child->id);
                    $this->subordinates[] = $subordinate;
                    break;

                case('msm_media'):
                    $media = new Media();
                    $media->loadFromDb($child->unit_id, $child->id);
                    $this->medias[] = $media;
                    break;
            }
        }
       
        return $this;
    }

}

?>
