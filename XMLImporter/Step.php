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
 * Description of Step
 *
 * @author User
 */
class Step extends Element
{

    public $position;
    public $content;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_step';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $this->partref = $DomElement->getAttribute('partref');

        $this->pilots = array();
        $pilots = $DomElement->getElementsByTagName('pilot');
        foreach ($pilots as $p)
        {
            $position = $position + 1;
            $pilot = new Pilot($this->xmlpath);
            $pilot->loadFromXml($p, $position);
            $this->pilots[] = $pilot;
        }

        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->medias = array();
        $this->tables = array();

        $step_bodys = $DomElement->getElementsByTagName('step.body');

        foreach ($step_bodys as $stb)
        {
            foreach ($this->processIndexAuthor($stb, $position) as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processIndexGlossary($stb, $position) as $indexglossary)
            {
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processIndexSymbols($stb, $position) as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }
            foreach ($this->processSubordinate($stb, $position) as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }
            
            foreach($this->processMedia($stb, $position) as $media)
            {
                $this->medias[] = $media;
            }
            
            foreach($this->processTable($stb, $position) as $table)
            {
                $this->tables[] = $table;
            }

            foreach ($this->processContent($stb, $position) as $content)
            {
                $this->content .= $content;
            }
        }
    }

    function saveIntoDb($position, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();

        $data->partref = $this->partref;
        $data->caption = $this->caption;

        $data->step_content = $this->content;
        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);

        $elementPositions = array();
        $sibling_id = null;


        if (!empty($this->pilots))
        {
            foreach ($this->pilots as $key => $pilot)
            {
                $elementPositions['pilot' . '-' . $key] = $pilot->position;
            }
        }

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
                case(preg_match("/^(pilot.\d+)$/", $element) ? true : false):
                    $pilotString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $pilot = $this->pilots[$pilotString[1]];
                        $pilot->saveIntoDb($pilot->position, $this->compid);
                        $sibling_id = $pilot->compid;
                    }
                    else
                    {
                        $pilot = $this->pilots[$pilotString[1]];
                        $pilot->saveIntoDb($pilot->position, $this->compid, $sibling_id);
                        $sibling_id = $pilot->compid;
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
            }
        }
    }

}

?>
