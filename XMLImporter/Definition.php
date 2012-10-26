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
 * Description of Definition
 *
 * @author User
 */
class Definition extends Element
{

    public $position;
    public $def_content;
    public $caption;
    public $string_id;

    /**
     *
     * @param String $xmlpath 
     */
    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_def';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->type = $DomElement->getAttribute('type');

        $this->string_id = $DomElement->getAttribute('id');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $this->description = $this->getDomAttribute($DomElement->getElementsByTagName('description'));

        $this->associates = array();
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->medias = array();
        $this->tables = array();
        $this->matharrays = array();

        $associates = $DomElement->getElementsByTagName('associate');

        foreach ($associates as $a)
        {
            $position = $position + 1;
            $associate = new Associate($this->xmlpath);
            $associate->loadFromXml($a, $position);
            $this->associates[] = $associate;
        }
        $defbodys = $DomElement->getElementsByTagName('def.body');

        $doc = new DOMDocument();

        foreach ($defbodys as $d)
        {
            foreach ($this->processMathArray($d, $position) as $matharray)
            {
                $this->matharrays[] = $matharray;
            }

            foreach ($this->processIndexAuthor($d, $position) as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processIndexGlossary($d, $position) as $indexglossary)
            {
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processIndexSymbols($d, $position) as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }
            foreach ($this->processSubordinate($d, $position) as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processMedia($d, $position) as $media)
            {
                $this->medias[] = $media;
            }

            foreach ($this->processTable($d, $position) as $table)
            {
                $this->tables[] = $table;
            }

            foreach ($this->processContent($d, $position) as $content)
            {
                $this->def_content .= $content;
            }
        }
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;
        
        $data = new stdClass();

        $data->def_type = $this->type;
        $data->string_id = $this->string_id;
        if (!empty($this->caption))
        {
            $data->caption = $this->caption;
        }

        $data->description = $this->description;

        if (!empty($this->def_content))
        {
            $data->def_content = $this->def_content;
            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        }
        else // has def.body as child of def
        {
            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        }

        $elementPositions = array();
        $sibling_id = null;

//        if (!empty($this->associates))
//        {
//            foreach ($this->associates as $key => $associate)
//            {
//                $elementPositions['associate' . '-' . $key] = $associate->position;
//            }
//        }

        if (!empty($this->matharrays))
        {
            foreach ($this->matharrays as $key => $matharray)
            {
                $elementPositions['matharray' . '-' . $key] = $matharray->position;
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
//                case(preg_match("/^(associate.\d+)$/", $element) ? true : false):
//                    $associateString = explode('-', $element);
//
//                    if (empty($sibling_id))
//                    {
//                        $associate = $this->associates[$associateString[1]];
//                        $associate->saveIntoDb($associate->position, $msmid, $this->compid);
//                        $sibling_id = $associate->compid;
//                    }
//                    else
//                    {
//                        $associate = $this->associates[$associateString[1]];
//                        $associate->saveIntoDb($associate->position, $msmid, $this->compid, $sibling_id);
//                        $sibling_id = $associate->compid;
//                    }
//                    break;

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

        $sibling_id = 0;

        foreach ($this->associates as $associate)
        {
            $associate->saveIntoDb($associate->position, $msmid, $this->compid, $sibling_id);
            $sibling_id = $associate->compid;
        }
    }

    function loadFromDb($id, $compid, $indexref = false)
    {
        global $DB;

        $defRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($defRecord))
        {
            $this->compid = $compid;
            $this->def_type = $defRecord->def_type;
            $this->caption = $defRecord->caption;
            $this->def_content = $defRecord->def_content;
        }

        $this->associates = array();
        $this->subordinates = array();
        $this->medias = array();
        $this->tables = array();
        $this->matharrays = array();
        $this->childs = array();

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            if (!$indexref)
            {
                switch ($childtablename)
                {
                    case('msm_associate'):
                        $associate = new Associate();
                        $associate->loadFromDb($child->unit_id, $child->id);
                        $this->associates[] = $associate;
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
                        $matharray = new MathArray();
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
            else
            {
                switch ($childtablename)
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

                    case('msm_math_array'):
                        $matharray = new MathArray();
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
        }

        return $this;
    }

    function displayhtml($isindex = false)
    {
        $content = '';
        $content .= "<br />";
        $content .= "<div class='def'>";
        if (!empty($this->caption))
        {
            $content .= "<span class='deftitle'>" . $this->caption . "</span>";
        }

        if (!empty($this->def_type))
        {
            $content .= "<span class='deftype'>" . $this->def_type . "</span>";
        }
        $content .= "<br/>";


        $content .= "<div class='mathcontent'>";
        $content .= $this->displayContent($this, $this->def_content, $isindex);
        $content .= "<br />";
        $content .= "</div>";

        $content .= "<br />";
        
        if (!$isindex)
        {
            if (!empty($this->associates))
            {
                $content .= "<ul class='defminibuttons'>";
                foreach ($this->associates as $key => $associate)
                {
                    $content .= $associate->displayhtml();
                }
                $content .= "</ul>";
            }
        }

        $content .= "</div>";
        $content .= "<br />";

//        print_object($content);

        return $content;
    }

}

?>
