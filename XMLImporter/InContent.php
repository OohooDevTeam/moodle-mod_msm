<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                      **
 * @subpackage  msm                                                      **
 * @name        msm                                                      **
 * @copyright   University of Alberta                                    **
 * @link        http://ualberta.ca                                       **
 * @author      Ga Young Kim                                             **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * *************************************************************************
 * ************************************************************************ */

/**
 * Class representing ol, ul and math.display elements in the transformed XML files.
 *
 * @author User
 */
class InContent extends Element
{

    public $position;
    public $content;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_content';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->subordinates = array();
        $this->matharrays = array();
        $this->tables = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->medias = array();

//        $this->content = array();

        $this->childContents = array();

        //determining the element node of the passed DOMElement to identify the type in DB field
        $nameofElement = $DomElement->tagName;

        switch ($nameofElement)
        {
            case('ol'):
                $this->type = 'ordered';
                $this->additional_attribute = $DomElement->getAttribute('type');

                foreach ($DomElement->childNodes as $child)
                {
                    if ($child->nodeType == XML_ELEMENT_NODE)
                    {
                        if ($child->tagName == 'li')
                        {
                            foreach ($child->childNodes as $grandChild)
                            {
                                if ($grandChild->nodeType == XML_ELEMENT_NODE)
                                {
                                    $position++;
                                    $block = new Block($this->xmlpath);
                                    $block->loadFromXml($grandChild, $position);
                                    $this->childContents[] = $block;
                                }
                            }
                        }
                    }
                }
                break;

            case('ul'):
                $this->type = 'unordered';
                $this->additional_attribute = $DomElement->getAttribute('bullet');

                foreach ($DomElement->childNodes as $child)
                {
                    if ($child->nodeType == XML_ELEMENT_NODE)
                    {
                        if ($child->tagName == 'li')
                        {
                            foreach ($child->childNodes as $grandChild)
                            {
                                if ($grandChild->nodeType == XML_ELEMENT_NODE)
                                {
                                    $position++;
                                    $block = new Block($this->xmlpath);
                                    $block->loadFromXml($grandChild, $position);
                                    $this->childContents[] = $block;
                                }
                            }
                        }
                    }
                }
                break;

            case('math.display'):
                $this->type = 'display';
                $this->additional_attribute = $DomElement->getAttribute('id');

                break;
        }

        $position = $position + 1;

        foreach ($this->processMathArray($DomElement, $position) as $matharray)
        {
            $this->matharrays[] = $matharray;
        }

        foreach ($this->processTable($DomElement, $position) as $table)
        {
            $this->tables[] = $table;
        }

        foreach ($this->processIndexAuthor($DomElement, $position) as $indexauthor)
        {
            $this->indexauthors[] = $indexauthor;
        }

        foreach ($this->processIndexGlossary($DomElement, $position) as $indexglossary)
        {
            $this->indexglossarys[] = $indexglossary;
        }

        foreach ($this->processIndexSymbols($DomElement, $position) as $indexsymbol)
        {
            $this->indexsymbols[] = $indexsymbol;
        }
        foreach ($this->processSubordinate($DomElement, $position) as $subordinate)
        {
            $this->subordinates[] = $subordinate;
        }

        foreach ($this->processMedia($DomElement, $position) as $media)
        {
            $this->medias[] = $media;
        }
//
        foreach ($this->processContent($DomElement, $position) as $content)
        {
            $this->content .= $content;
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

        $data->additional_attribute = $this->additional_attribute;
        $data->type = $this->type;
        if (!empty($this->content))
        {
            $data->content = $this->content;
            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
        }
        else
        {
            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
        }

        $elementPositions = array();
        $sibling_id = null;

        if (!empty($this->childContents))
        {
            foreach ($this->childContents as $key => $childContent)
            {
                $elementPositions['childContent' . '-' . $key] = $childContent->position;
            }
        }

        if (!empty($this->subordinates))
        {
            foreach ($this->subordinates as $key => $subordinate)
            {
                $elementPositions['subordinate' . '-' . $key] = $subordinate->position;
            }
        }

        if (!empty($this->tables))
        {
            foreach ($this->tables as $key => $table)
            {
                $elementPositions['table' . '-' . $key] = $table->position;
            }
        }

        if (!empty($this->matharrays))
        {
            foreach ($this->matharrays as $key => $matharray)
            {
                $elementPositions['matharray' . '-' . $key] = $matharray->position;
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
            foreach ($this->indexglossarys as $key => $indexglossary)
            {
                $elementPositions['indexglossary' . '-' . $key] = $indexglossary->position;
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
                case(preg_match("/^(childContent.\d+)$/", $element) ? true : false):
                    $childString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $block = $this->childContents[$childString[1]];
                        $block->saveIntoDb($block->position, $parentid);
//                        $sibling_id = $block->compid;
                    }
                    else
                    {
                        $block = $this->childContents[$childString[1]];
                        $block->saveIntoDb($block->position, $parentid, $sibling_id);
//                        $sibling_id = $block->compid;
                    }
                    break;

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

                case(preg_match("/^(table.\d+)$/", $element) ? true : false):
                    $tableString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $table = $this->tables[$tableString[1]];
                        $table->saveIntoDb($table->position, $this->compid);
                        $sibling_id = $table->compid;
                    }
                    else
                    {
                        $table = $this->tables[$tableString[1]];
                        $table->saveIntoDb($table->position, $this->compid, $sibling_id);
                        $sibling_id = $table->compid;
                    }
                    break;

                case(preg_match("/^(matharray.\d+)$/", $element) ? true : false):
                    $matharrayString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $matharray = $this->matharrays[$matharrayString[1]];
                        $matharray->saveIntoDb($matharray->position, $this->compid);
                        $sibling_id = $matharray->compid;
                    }
                    else
                    {
                        $matharray = $this->matharrays[$matharrayString[1]];
                        $matharray->saveIntoDb($matharray->position, $this->compid, $sibling_id);
                        $sibling_id = $matharray->compid;
                    }
                    break;

                case(preg_match("/^(indexauthor.\d+)$/", $element) ? true : false):
                    $indexauthorString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $indexauthor = $this->indexauthors[$indexauthorString[1]];
                        $indexauthor->saveIntoDb($indexauthor->position, $this->compid);
                        $sibling_id = $indexauthor->compid;
                    }
                    else
                    {
                        $indexauthor = $this->indexauthors[$indexauthorString[1]];
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

        $contentRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($contentRecord))
        {
            $this->compid = $compid;
            $this->content = $contentRecord->content;
            $this->type = $contentRecord->type;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        $this->subordinates = array();
        $this->tables = array();
        $this->childs = array();

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            switch ($childtablename)
            {
                case('msm_subordinate'):
                    $subordinate = new Subordinate();
                    $subordinate->loadFromDb($child->unit_id, $child->id);
                    $this->subordinates[] = $subordinate;
                    break;

                case('msm_math_array'):
                    $matharray = new MathArray();
                    $matharray->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $matharray;
                    break;

                case('msm_table'):
                    $table = new Table();
                    $table->loadFromDb($child->unit_id, $child->id);
                    $this->tables[] = $table;
                    break;

                case('msm_media'):
                    $media = new Media();
                    $media->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $media;
                    break;
            }
        }

        return $this;
    }

    function displayhtml()
    {
        $content = '';

        $content .= $this->displayContent($this, $this->content);

        return $content;
    }

}

?>
