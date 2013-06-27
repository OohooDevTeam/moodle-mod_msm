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
 * Description of Table
 *
 * @author User
 */
class Table extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_table';
    }

    //put your code here
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->string_id = $DomElement->getAttribute('id');
        $this->table_class = $DomElement->getAttribute('class');
        $this->table_summary = $DomElement->getAttribute('summary');
        $this->table_title = $DomElement->getAttribute('title');

        $this->content = array();
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->medias = array();
        $this->matharrays = array();

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

        foreach ($this->processTable($DomElement, $position) as $table)
        {
            $this->tables[] = $table;
        }

        foreach ($this->processMathArray($DomElement, $position) as $matharray)
        {
            $this->matharrays[] = $matharray;
        }

        foreach ($this->processContent($DomElement, $position) as $content)
        {
            $this->content[] = $content;
        }
    }

    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;

        $ids = array();
        $data = new stdClass();
        $data->string_id = $this->string_id;
        $data->table_class = $this->table_class;
        $data->table_summary = $this->table_summary;
        $data->table_title = $this->table_title;

        if (!empty($this->content))
        {
            foreach ($this->content as $content)
            {
                $data->table_content = $content;
                $this->id = $DB->insert_record($this->tablename, $data);
                $ids[] = $this->id;
                $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
            }
        }
        else
        {
            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
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

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
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
            }
        }
        if (!empty($this->medias))
        {
            foreach ($ids as $key => $id)
            {
                $newdata = new stdClass();
                $newdata->string_id = $this->string_id;
                $newdata->table_class = $this->table_class;
                $newdata->table_summary = $this->table_summary;
                $newdata->table_title = $this->table_title;
                $newdata->id = $id;
                $newdata->table_content = $this->processDbContent("<div>" . $this->content[$key] . "</div>", $this);

                $DB->update_record($this->tablename, $newdata);
            }
        }
    }

    function loadFromDb($id, $compid)
    {
        global $DB;

        $tableRecord = $DB->get_record('msm_table', array('id' => $id));

        if (!empty($tableRecord))
        {
            $this->compid = $compid;
            $this->table_class = $tableRecord->table_class;
            $this->table_summary = $tableRecord->table_summary;
            $this->table_title = $tableRecord->table_title;
            $this->table_content = $tableRecord->table_content;
        }

        $this->subordinates = array();
        $this->tables = array();
        $this->medias = array();
        $this->matharrays = array();

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');

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

                case('msm_table'):
                    $table = new Table();
                    $table->loadFromDb($child->unit_id, $child->id);
                    $this->tables[] = $table;
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
            }
        }
        return $this;
    }

    function displayhtml($isindex = false)
    {
        $newtablecontent = $this->displayContent($this, $this->table_content, $isindex);

        $tablecontent = "<body>" . $newtablecontent . "</body>";

        $content = $this->processTableContent($tablecontent);

        return $content;
    }

    function processTableContent($tablecontent)
    {
        $content = '';
        $doc = new DOMDocument;

        @$doc->loadHTML($tablecontent);

        $dialogs = $doc->getElementsByTagName('div');

        $table = $doc->getElementsByTagName('table')->item(0);

        if ($table->hasAttribute('border'))
        {
            $border = $table->getAttribute('border');
        }
        else
        {
            $border = 0;
        }
        if ($table->hasAttribute('cellpadding'))
        {
            $cellpadding = $table->getAttribute('cellpadding');
        }
        else
        {
            $cellpadding = 0;
        }

        if (empty($border))
        {
            $border = 0;
        }
        if (empty($cellpadding))
        {
            $cellpadding = 0;
        }

        $tbody = $table->getElementsByTagName("tbody");

//        print_object($tablecontent);

        if (sizeof($tbody) > 0)
        {
            $table->setAttribute("class", "mathtable");
            $atags = $doc->getElementsByTagName('a');
//
            foreach ($atags as $atag)
            {
                // getting the associated info's compid
                $tagID = $atag->getAttribute('id');
                $idArray = explode('-', $tagID);

                foreach ($dialogs as $dialog)
                {
                    $divclass = $dialog->getAttribute('class');
                    if ($divclass == 'dialogs')
                    {
                        $divID = $dialog->getAttribute('id');
                        $divIDArray = explode('-', $divID);

                        if ((isset($idArray[1])) && (isset($divIDArray[1])))
                        {
                            if ($idArray[1] == $divIDArray[1])
                            {
                                $divNode = $doc->importNode($dialog, true);
                                $atag->parentNode->appendChild($divNode);
                                break;
                            }
                        }
                    }
                }
            }
            $content .= $doc->saveHTML($doc->importNode($table, true));
        }
//        else
//        {
//            echo "no tbody";
//            $content .= "<table class='mathtable' border='" . $border . "' cellpadding='" . $cellpadding . "' style='width:100% !important;'>";
//
//            foreach ($table->childNodes as $row)
//            {
//                if ($row->nodeType == XML_ELEMENT_NODE)
//                {
//                    if ($row->tagName == 'tr')
//                    {
//                        $content .= "<tr>";
//                        foreach ($row->childNodes as $grandChild)
//                        {
//                            if ($grandChild->nodeType == XML_ELEMENT_NODE)
//                            {
//                                if ($grandChild->tagName == 'td')
//                                {
//                                    $content .= "<td style='border-width:" . $border . "px !important;'>";
//                                    foreach ($grandChild->childNodes as $contentElement)
//                                    {
//                                        if ($contentElement->nodeType == XML_ELEMENT_NODE)
//                                        {
//                                            $atags = $contentElement->getElementsByTagName('a');
////
//                                            foreach ($atags as $atag)
//                                            {
//                                                // getting the associated info's compid
//                                                $tagID = $atag->getAttribute('id');
//                                                $idArray = explode('-', $tagID);
//
//                                                foreach ($dialogs as $dialog)
//                                                {
//                                                    $divclass = $dialog->getAttribute('class');
//                                                    if ($divclass == 'dialogs')
//                                                    {
//                                                        $divID = $dialog->getAttribute('id');
//                                                        $divIDArray = explode('-', $divID);
//
//                                                        if ((isset($idArray[1])) && (isset($divIDArray[1])))
//                                                        {
//                                                            if ($idArray[1] == $divIDArray[1])
//                                                            {
//                                                                $divNode = $doc->importNode($dialog, true);
//                                                                $atag->parentNode->appendChild($divNode);
//                                                                break;
//                                                            }
//                                                        }
//                                                    }
//                                                }
//                                            }
//                                            $content .= $doc->saveHTML($contentElement);
//                                        }
//                                        else
//                                        {
//                                            $content .= $doc->saveHTML($contentElement);
//                                        }
//                                    }
//                                    $content .= "</td>";
//                                }
//                            }
//                            else
//                            {
//                                $content .= $doc->saveHTML($grandChild);
//                            }
//                        }
//                        $content .= "</tr>";
//                    }
//                }
//            }
//
//            $content .= "</table>";
//        }
//        print_object($content);
        return $content;
    }

}

?>
