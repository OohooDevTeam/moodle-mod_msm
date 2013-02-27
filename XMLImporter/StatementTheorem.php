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
 * Description of Statement
 *
 * @author User
 */
class StatementTheorem extends Element
{

    public $position;
    public $content;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_statement_theorem';
    }

    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        // $this->content = array();
        $this->part_theorems = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->subordinates = array();
        $this->medias = array();
        $this->tables = array();
        $this->matharrays = array();

        foreach ($DomElement->childNodes as $key => $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == 'part.theorem')
                {
                    $position = $position + 1;
                    $parttheorem = new PartTheorem($this->xmlpath);
                    $parttheorem->loadFromXml($child, $position);
                    $this->part_theorems[] = $parttheorem;
                }
                else
                {
                    //give a root node to child element being passed so tables
                    $doc = new DOMDocument();
                    $childElement = $doc->importNode($child, true);
                    $newNode = $doc->createElement('wrapper');
                    $newNode->appendChild($childElement);

                    $element = $doc->importNode($newNode, true);

                    foreach ($this->processIndexAuthor($element, $position) as $indexauthor)
                    {
                        $this->indexauthors[] = $indexauthor;
                    }

                    foreach ($this->processIndexGlossary($element, $position) as $indexglossary)
                    {
                        $this->indexglossarys[] = $indexglossary;
                    }

                    foreach ($this->processIndexSymbols($element, $position) as $indexsymbol)
                    {
                        $this->indexsymbols[] = $indexsymbol;
                    }
                    foreach ($this->processSubordinate($element, $position) as $subordinate)
                    {
                        $this->subordinates[] = $subordinate;
                    }

                    foreach ($this->processMedia($element, $position) as $media)
                    {
                        $this->medias[] = $media;
                    }

                    foreach ($this->processMathArray($element, $position) as $matharray)
                    {
                        $this->matharrays[] = $matharray;
                    }

                    foreach ($this->processTable($element, $position) as $table)
                    {
                        $this->tables[] = $table;
                    }

                    foreach ($this->processContent($child, $position) as $content)
                    {
                        $this->content .= $content;
                    }
                }
            }
        }
    }

    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();

        // need to group all the children of statement.theorem for loadXML function in displaySubordinate function later...
        $data->statement_content = "<statement.theorem>" . $this->content . "</statement.theorem>";
        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $elementPositions = array();
        $sibling_id = null;

        if (!empty($this->part_theorems))
        {
            foreach ($this->part_theorems as $key => $part_theorem)
            {
                $elementPositions['parttheorem' . '-' . $key] = $part_theorem->position;
            }
        }

        if (!empty($this->subordinates))
        {
            foreach ($this->subordinates as $key => $subordinate)
            {
                $elementPositions['subordinate' . '-' . $key] = $subordinate->position;
            }
        }

        if (!empty($this->matharrays))
        {
            foreach ($this->matharrays as $key => $matharray)
            {
                $elementPositions['matharray-' . $key] = $matharray->position;
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

        if (!empty($this->tables))
        {
            foreach ($this->tables as $key => $table)
            {
                $elementPositions['table' . '-' . $key] = $table->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(parttheorem.\d+)$/", $element) ? true : false):
                    $parttheoremString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $partTheorem = $this->part_theorems[$parttheoremString[1]];
                        $partTheorem->saveIntoDb($partTheorem->position, $msmid, $this->compid);
                        $sibling_id = $partTheorem->compid;
                    }
                    else
                    {
                        $partTheorem = $this->part_theorems[$parttheoremString[1]];
                        $partTheorem->saveIntoDb($partTheorem->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $partTheorem->compid;
                    }
                    break;

                case(preg_match("/^(subordinate.\d+)$/", $element) ? true : false):
                    $subordinateString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $subordinate = $this->subordinates[$subordinateString[1]];
                        $subordinate->saveIntoDb($subordinate->position, $msmid, $this->compid);
                        $sibling_id = $subordinate->compid;
                    }
                    else
                    {
                        $subordinate = $this->subordinates[$subordinateString[1]];
                        $subordinate->saveIntoDb($subordinate->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $subordinate->compid;
                    }
                    break;

                case(preg_match("/^(matharray.\d+)$/", $element) ? true : false):
                    $matharrayString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $matharray = $this->matharrays[$matharrayString[1]];
                        $matharray->saveIntoDb($matharray->position, $msmid, $this->compid);
                        $sibling_id = $matharray->compid;
                    }
                    else
                    {
                        $matharray = $this->matharrays[$matharrayString[1]];
                        $matharray->saveIntoDb($matharray->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $matharray->compid;
                    }
                    break;

                case(preg_match("/^(indexauthor.\d+)$/", $element) ? true : false):
                    $indexauthorString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $indexauthor = $this->indexauthors[$indexauthorString[1]];
                        $indexauthor->saveIntoDb($indexauthor->position, $msmid, $this->compid);
                        $sibling_id = $indexauthor->compid;
                    }
                    else
                    {
                        $indexauthor = $this->indexauthors[$indexauthorString[1]];
                        $indexauthor->saveIntoDb($indexauthor->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $indexauthor->compid;
                    }
                    break;

                case(preg_match("/^(indexsymbol.\d+)$/", $element) ? true : false):
                    $indexsymbolString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $indexsymbol = $this->indexsymbols[$indexsymbolString[1]];
                        $indexsymbol->saveIntoDb($indexsymbol->position, $msmid, $this->compid);
                        $sibling_id = $indexsymbol->compid;
                    }
                    else
                    {
                        $indexsymbol = $this->indexsymbols[$indexsymbolString[1]];
                        $indexsymbol->saveIntoDb($indexsymbol->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $indexsymbol->compid;
                    }
                    break;

                case(preg_match("/^(indexglossary.\d+)$/", $element) ? true : false):
                    $indexglossaryString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $indexglossary = $this->indexglossarys[$indexglossaryString[1]];
                        $indexglossary->saveIntoDb($indexglossary->position, $msmid, $this->compid);
                        $sibling_id = $indexglossary->compid;
                    }
                    else
                    {
                        $indexglossary = $this->indexglossarys[$indexglossaryString[1]];
                        $indexglossary->saveIntoDb($indexglossary->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $indexglossary->compid;
                    }
                    break;

                case(preg_match("/^(media.\d+)$/", $element) ? true : false):
                    $mediaString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $media = $this->medias[$mediaString[1]];
                        $media->saveIntoDb($media->position, $msmid, $this->compid);
                        $sibling_id = $media->compid;
                    }
                    else
                    {
                        $media = $this->medias[$mediaString[1]];
                        $media->saveIntoDb($media->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $media->compid;
                    }
                    break;

                case(preg_match("/^(table.\d+)$/", $element) ? true : false):
                    $tableString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $table = $this->tables[$tableString[1]];
                        $table->saveIntoDb($table->position, $msmid, $this->compid);
                        $sibling_id = $table->compid;
                    }
                    else
                    {
                        $table = $this->tables[$tableString[1]];
                        $table->saveIntoDb($table->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $table->compid;
                    }
                    break;
            }
        }
    }

    function loadFromDb($id, $compid)
    {
        global $DB;

        $statementTheoremRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($statementTheoremRecord))
        {
            $this->compid = $compid;
            $this->statement_content = $statementTheoremRecord->statement_content;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        $this->childs = array();
        $this->subordinates = array();
        $this->medias = array();
        $this->tables = array();
        $this->matharrays = array();

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            switch ($childtablename)
            {
                case('msm_part_theorem'):
                    $partTheorem = new PartTheorem();
                    $partTheorem->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $partTheorem;
                    break;

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

                case('msm_math_array'):
                    $matharray = new Matharray();
                    $matharray->loadFromDb($child->unit_id, $child->id);
                    $this->matharrays[] = $matharray;
                    break;

                case('msm_table'):
                    $table = new Table();
                    $table->loadFromDb($child->unit_id, $child->id);
                    $this->tables[] = $table;
                    break;
            }
        }

        return $this;
    }

    function displayhtml($isindex = false)
    {
        $content = '';
        $content .= $this->displayContent($this, $this->statement_content, $isindex);

        $content .= "<ol class='parttheorem' style='list-style-type:lower-roman;'>";
        foreach ($this->childs as $childComponent)
        {
            $content .= $childComponent->displayhtml($isindex);
        }
        $content .= "</ol>";

        return $content;
    }

}

?>
