<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MathArray
 *
 * @author User
 */
class MathArray extends Element
{

    public $position;
 function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_math_array';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
   
        $this->position = $position;

        $this->string_id = $DomElement->getAttribute('id');
        $this->no_column = $DomElement->getAttribute('column'); //specifies number of column

        $this->rows = array();

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == 'row')
                {
                    $position = $position + 1;
                    $row = new MathRow($this->xmlpath);
                    $row->loadFromXml($child, $position);
                    $this->rows[] = $row;
                }
            }
        }

//        $this->content = array();
//        $this->indexauthors = array();
//        $this->indexglossarys = array();
//        $this->indexsymbols = array();
//        $this->subordinates = array();
//        $this->medias = array();
//
//        $this->string_id = $DomElement->getAttribute('id');
//        $this->no_column = $DomElement->getAttribute('column'); //specifies number of column
//
//        $position = $position + 1;
//
//        foreach ($this->processIndexAuthor($DomElement, $position) as $indexauthor)
//        {
//            $this->indexauthors[] = $indexauthor;
//        }
//
//        foreach ($this->processIndexGlossary($DomElement, $position) as $indexglossary)
//        {
//            $this->indexglossarys[] = $indexglossary;
//        }
//
//        foreach ($this->processIndexSymbols($DomElement, $position) as $indexsymbol)
//        {
//            $this->indexsymbols[] = $indexsymbol;
//        }
//        foreach ($this->processSubordinate($DomElement, $position) as $subordinate)
//        {
//            $this->subordinates[] = $subordinate;
//        }
//
//        foreach ($this->processMedia($DomElement, $position) as $media)
//        {
//            $this->medias[] = $media;
//        }
//
//
//        foreach ($this->processContent($DomElement, $position) as $content)
//        {
//            $this->content[] = $content;
//        }
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
        $data->string_id = $this->string_id;
        $data->no_column = $this->no_column;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
        
        $elementPosition = array();
        $sibling_id = null;
        foreach ($this->rows as $key => $row)
        {
            $elementPosition['row' . '-' . $key] = $row->position;
        }

        asort($elementPosition);

        foreach ($elementPosition as $element => $value)
        {
            $rowString = split('-', $element);

            if(empty($sibling_id))
            {
               $this->rows[$rowString[1]]->saveIntoDb($this->rows[$rowString[1]]->position, $this->compid);
               $sibling_id = $this->rows[$rowString[1]]->compid;
            }
            else
            {
                $this->rows[$rowString[1]]->saveIntoDb($this->rows[$rowString[1]]->position, $this->compid, $sibling_id);
               $sibling_id = $this->rows[$rowString[1]]->compid;
            }
           
        }
//
//        if (!empty($this->content))
//        {
//            foreach ($this->content as $content)
//            {
//                $data->math_array_content = $content;
//                $this->id = $DB->insert_record($this->tablename, $data);
//                $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
//            }
//        }
//        else
//        {
//            $this->id = $DB->insert_record($this->tablename, $data);
//            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
//        }
//
//        $elementPositions = array();
//        $sibling_id = null;
//
//
//        if (!empty($this->subordinates))
//        {
//            foreach ($this->subordinates as $key => $subordinate)
//            {
//                $elementPositions['subordinate' . '-' . $key] = $subordinate->position;
//            }
//        }
//
//        if (!empty($this->indexauthors))
//        {
//            foreach ($this->indexauthors as $key => $indexauthor)
//            {
//                $elementPositions['indexauthor' . '-' . $key] = $indexauthor->position;
//            }
//        }
//
//        if (!empty($this->indexglossarys))
//        {
//            foreach ($this->indexglossarys as $key => $indexglosary)
//            {
//                $elementPositions['indexglossary' . '-' . $key] = $indexglosary->position;
//            }
//        }
//
//        if (!empty($this->indexsymbols))
//        {
//            foreach ($this->indexsymbols as $key => $indexsymbol)
//            {
//                $elementPositions['indexsymbol' . '-' . $key] = $indexsymbol->position;
//            }
//        }
//
//        if (!empty($this->medias))
//        {
//            foreach ($this->medias as $key => $media)
//            {
//                $elementPositions['media' . '-' . $key] = $media->position;
//            }
//        }
//
//        asort($elementPositions);
//
//        foreach ($elementPositions as $element => $value)
//        {
//            switch ($element)
//            {
//                case(preg_match("/^(subordinate.\d+)$/", $element) ? true : false):
//                    $subordinateString = split('-', $element);
//
//                    if (empty($sibling_id))
//                    {
//                        $subordinate = $this->subordinates[$subordinateString[1]];
//                        $subordinate->saveIntoDb($subordinate->position, $this->compid);
//                        $sibling_id = $subordinate->compid;
//                    }
//                    else
//                    {
//                        $subordinate = $this->subordinates[$subordinateString[1]];
//                        $subordinate->saveIntoDb($subordinate->position, $this->compid, $sibling_id);
//                        $sibling_id = $subordinate->compid;
//                    }
//                    break;
//
//                case(preg_match("/^(indexauthor.\d+)$/", $element) ? true : false):
//                    $indexauthorString = split('-', $element);
//
//                    if (empty($sibling_id))
//                    {
//                        $indexauthor = $this->indexauthors[$indexauthorString[1]];
//                        $indexauthor->saveIntoDb($indexauthor->position, $this->compid);
//                        $sibling_id = $indexauthor->compid;
//                    }
//                    else
//                    {
//                        $indexauthor = $this->indexauthors[$indexauthorString[1]];
//                        $indexauthor->saveIntoDb($indexauthor->position, $this->compid, $sibling_id);
//                        $sibling_id = $indexauthor->compid;
//                    }
//                    break;
//
//                case(preg_match("/^(indexsymbol.\d+)$/", $element) ? true : false):
//                    $indexsymbolString = split('-', $element);
//
//                    if (empty($sibling_id))
//                    {
//                        $indexsymbol = $this->indexsymbols[$indexsymbolString[1]];
//                        $indexsymbol->saveIntoDb($indexsymbol->position, $this->compid);
//                        $sibling_id = $indexsymbol->compid;
//                    }
//                    else
//                    {
//                        $indexsymbol = $this->indexsymbols[$indexsymbolString[1]];
//                        $indexsymbol->saveIntoDb($indexsymbol->position, $this->compid, $sibling_id);
//                        $sibling_id = $indexsymbol->compid;
//                    }
//                    break;
//
//                case(preg_match("/^(indexglossary.\d+)$/", $element) ? true : false):
//                    $indexglossaryString = split('-', $element);
//
//                    if (empty($sibling_id))
//                    {
//                        $indexglossary = $this->indexglossarys[$indexglossaryString[1]];
//                        $indexglossary->saveIntoDb($indexglossary->position, $this->compid);
//                        $sibling_id = $indexglossary->compid;
//                    }
//                    else
//                    {
//                        $indexglossary = $this->indexglossarys[$indexglossaryString[1]];
//                        $indexglossary->saveIntoDb($indexglossary->position, $this->compid, $sibling_id);
//                        $sibling_id = $indexglossary->compid;
//                    }
//                    break;
//
//                case(preg_match("/^(media.\d+)$/", $element) ? true : false):
//                    $mediaString = split('-', $element);
//
//                    if (empty($sibling_id))
//                    {
//                        $media = $this->medias[$mediaString[1]];
//                        $media->saveIntoDb($media->position, $this->compid);
//                        $sibling_id = $media->compid;
//                    }
//                    else
//                    {
//                        $media = $this->medias[$mediaString[1]];
//                        $media->saveIntoDb($media->position, $this->compid, $sibling_id);
//                        $sibling_id = $media->compid;
//                    }
//                    break;
//            }
//        }
    }

}

?>
