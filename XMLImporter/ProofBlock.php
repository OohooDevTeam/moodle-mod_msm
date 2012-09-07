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
 * Description of ProofBlock
 *
 * @author User
 */
class ProofBlock extends Element
{

    public $position;
//    public $proof_block_content;
    public $logic_type;
    public $proof_logic;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_proof_block';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {


        $this->position = $position;
//        $this->proof_block_bodys = array();

        $this->logic_types = array();
        $this->proof_logics = array();
        $this->proof_block_content = array();
        $this->proof_block_body = array();
        $this->captions = array();

        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->subordinates = array();
        $this->matharrays = array();
        $this->medias = array();
        $this->tables = array();

        $doc = new DOMDocument();
        @$doc->loadXML($DomElement);

        $index = 0;
        $sectionedContent = '';

        foreach ($DomElement->childNodes as $child)
        {

            if ($child->nodeType == XML_ELEMENT_NODE)
            {

                if ($child->tagName == 'logic')
                {
                    $index++;
                    $this->proof_block_content[$index] = $sectionedContent;
                    $sectionedContent = '';
                    $element = $doc->importNode($child, true);
                    $this->logic_types[$index] = $element->getAttribute('type');
                    $this->proof_logics[$index] = $doc->saveXML($element);
                }
                else if ($child->tagName == 'caption')
                {
                    $this->captions[$index] = $this->getContent($child);
                }
                else
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

                    foreach ($this->processMathArray($child, $position) as $matharray)
                    {
                        $this->matharrays[] = $matharray;
                    }

                    foreach ($this->processMedia($child, $position) as $media)
                    {
                        $this->medias[] = $media;
                    }

                    foreach ($this->processTable($child, $position) as $table)
                    {
                        $this->tables[] = $table;
                    }

                    foreach ($this->processContent($child, $position) as $content)
                    {
                        $sectionedContent .= $content;
                    }
                }
            }
            $this->proof_block_content[$index] = $sectionedContent;
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

        foreach ($this->proof_block_content as $key => $content)
        {
            if (isset($this->logic_types[$key]))
            {
                $data->logic_type = $this->logic_types[$key];
                $data->proof_logic = $this->proof_logics[$key];
                $data->proof_content = $content;
                $data->caption = $this->captions[$key];

                $this->id = $DB->insert_record('msm_proof_block', $data);
//
                $compid = $this->insertToCompositor($this->id, 'msm_proof_block', $parentid, $siblingid);
                $siblingid = $compid;

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

                if (!empty($this->matharrays))
                {
                    foreach ($this->matharrays as $key => $matharray)
                    {
                        $elementPositions['matharray' . '-' . $key] = $matharray->position;
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
                        case(preg_match("/^(subordinate.\d+)$/", $element) ? true : false):
                            $subordinateString = split('-', $element);

                            if (empty($sibling_id))
                            {
                                $subordinate = $this->subordinates[$subordinateString[1]];
                                $subordinate->saveIntoDb($subordinate->position, $compid);
                                $sibling_id = $subordinate->compid;
                            }
                            else
                            {
                                $subordinate = $this->subordinates[$subordinateString[1]];
                                $subordinate->saveIntoDb($subordinate->position, $compid, $sibling_id);
                                $sibling_id = $subordinate->compid;
                            }
                            break;

                        case(preg_match("/^(indexauthor.\d+)$/", $element) ? true : false):
                            $indexauthorString = split('-', $element);

                            if (empty($sibling_id))
                            {
                                $indexauthor = $this->indexauthors[$indexauthorString[1]];
                                $indexauthor->saveIntoDb($indexauthor->position, $compid);
                                $sibling_id = $indexauthor->compid;
                            }
                            else
                            {
                                $indexauthor = $this->indexauthors[$indexauthorString[1]];
                                $indexauthor->saveIntoDb($indexauthor->position, $compid, $sibling_id);
                                $sibling_id = $indexauthor->compid;
                            }
                            break;

                        case(preg_match("/^(indexsymbol.\d+)$/", $element) ? true : false):
                            $indexsymbolString = split('-', $element);

                            if (empty($sibling_id))
                            {
                                $indexsymbol = $this->indexsymbols[$indexsymbolString[1]];
                                $indexsymbol->saveIntoDb($indexsymbol->position, $compid);
                                $sibling_id = $indexsymbol->compid;
                            }
                            else
                            {
                                $indexsymbol = $this->indexsymbols[$indexsymbolString[1]];
                                $indexsymbol->saveIntoDb($indexsymbol->position, $compid, $sibling_id);
                                $sibling_id = $indexsymbol->compid;
                            }
                            break;

                        case(preg_match("/^(indexglossary.\d+)$/", $element) ? true : false):
                            $indexglossaryString = split('-', $element);

                            if (empty($sibling_id))
                            {
                                $indexglossary = $this->indexglossarys[$indexglossaryString[1]];
                                $indexglossary->saveIntoDb($indexglossary->position, $compid);
                                $sibling_id = $indexglossary->compid;
                            }
                            else
                            {
                                $indexglossary = $this->indexglossarys[$indexglossaryString[1]];
                                $indexglossary->saveIntoDb($indexglossary->position, $compid, $sibling_id);
                                $sibling_id = $indexglossary->compid;
                            }
                            break;

                        case(preg_match("/^(media.\d+)$/", $element) ? true : false):
                            $mediaString = split('-', $element);

                            if (empty($sibling_id))
                            {
                                $media = $this->medias[$mediaString[1]];
                                $media->saveIntoDb($media->position, $compid);
                                $sibling_id = $media->compid;
                            }
                            else
                            {
                                $media = $this->medias[$mediaString[1]];
                                $media->saveIntoDb($media->position, $compid, $sibling_id);
                                $sibling_id = $media->compid;
                            }
                            break;

                        case(preg_match("/^(matharray.\d+)$/", $element) ? true : false):
                            $matharrayString = split('-', $element);

                            if (empty($sibling_id))
                            {
                                $matharray = $this->matharrays[$matharrayString[1]];
                                $matharray->saveIntoDb($matharray->position, $compid);
                                $sibling_id = $matharray->compid;
                            }
                            else
                            {
                                $matharray = $this->matharrays[$matharrayString[1]];
                                $matharray->saveIntoDb($matharray->position, $compid, $sibling_id);
                                $sibling_id = $matharray->compid;
                            }
                            break;

                        case(preg_match("/^(table.\d+)$/", $element) ? true : false):
                            $tableString = split('-', $element);

                            if (empty($sibling_id))
                            {
                                $table = $this->tables[$tableString[1]];
                                $table->saveIntoDb($table->position, $compid);
                                $sibling_id = $table->compid;
                            }
                            else
                            {
                                $table = $this->tables[$tableString[1]];
                                $table->saveIntoDb($table->position, $compid, $sibling_id);
                                $sibling_id = $table->compid;
                            }
                            break;
                    }
                }
            }
            else
            {
                $data->logic_type = null;
                $data->proof_logic = null;
                $data->proof_content = $content;
                $data->caption = null;

                $this->id = $DB->insert_record('msm_proof_block', $data);
//
                $compid = $this->insertToCompositor($this->id, 'msm_proof_block', $parentid, $siblingid);
                $siblingid = $compid;

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

                if (!empty($this->matharrays))
                {
                    foreach ($this->matharrays as $key => $matharray)
                    {
                        $elementPositions['matharray' . '-' . $key] = $matharray->position;
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
                        case(preg_match("/^(subordinate.\d+)$/", $element) ? true : false):
                            $subordinateString = split('-', $element);

                            if (empty($sibling_id))
                            {
                                $subordinate = $this->subordinates[$subordinateString[1]];
                                $subordinate->saveIntoDb($subordinate->position, $compid);
                                $sibling_id = $subordinate->compid;
                            }
                            else
                            {
                                $subordinate = $this->subordinates[$subordinateString[1]];
                                $subordinate->saveIntoDb($subordinate->position, $compid, $sibling_id);
                                $sibling_id = $subordinate->compid;
                            }
                            break;

                        case(preg_match("/^(indexauthor.\d+)$/", $element) ? true : false):
                            $indexauthorString = split('-', $element);

                            if (empty($sibling_id))
                            {
                                $indexauthor = $this->indexauthors[$indexauthorString[1]];
                                $indexauthor->saveIntoDb($indexauthor->position, $compid);
                                $sibling_id = $indexauthor->compid;
                            }
                            else
                            {
                                $indexauthor = $this->indexauthors[$indexauthorString[1]];
                                $indexauthor->saveIntoDb($indexauthor->position, $compid, $sibling_id);
                                $sibling_id = $indexauthor->compid;
                            }
                            break;

                        case(preg_match("/^(indexsymbol.\d+)$/", $element) ? true : false):
                            $indexsymbolString = split('-', $element);

                            if (empty($sibling_id))
                            {
                                $indexsymbol = $this->indexsymbols[$indexsymbolString[1]];
                                $indexsymbol->saveIntoDb($indexsymbol->position, $compid);
                                $sibling_id = $indexsymbol->compid;
                            }
                            else
                            {
                                $indexsymbol = $this->indexsymbols[$indexsymbolString[1]];
                                $indexsymbol->saveIntoDb($indexsymbol->position, $compid, $sibling_id);
                                $sibling_id = $indexsymbol->compid;
                            }
                            break;

                        case(preg_match("/^(indexglossary.\d+)$/", $element) ? true : false):
                            $indexglossaryString = split('-', $element);

                            if (empty($sibling_id))
                            {
                                $indexglossary = $this->indexglossarys[$indexglossaryString[1]];
                                $indexglossary->saveIntoDb($indexglossary->position, $compid);
                                $sibling_id = $indexglossary->compid;
                            }
                            else
                            {
                                $indexglossary = $this->indexglossarys[$indexglossaryString[1]];
                                $indexglossary->saveIntoDb($indexglossary->position, $compid, $sibling_id);
                                $sibling_id = $indexglossary->compid;
                            }
                            break;

                        case(preg_match("/^(media.\d+)$/", $element) ? true : false):
                            $mediaString = split('-', $element);

                            if (empty($sibling_id))
                            {
                                $media = $this->medias[$mediaString[1]];
                                $media->saveIntoDb($media->position, $compid);
                                $sibling_id = $media->compid;
                            }
                            else
                            {
                                $media = $this->medias[$mediaString[1]];
                                $media->saveIntoDb($media->position, $compid, $sibling_id);
                                $sibling_id = $media->compid;
                            }
                            break;

                        case(preg_match("/^(matharray.\d+)$/", $element) ? true : false):
                            $matharrayString = split('-', $element);

                            if (empty($sibling_id))
                            {
                                $matharray = $this->matharrays[$matharrayString[1]];
                                $matharray->saveIntoDb($matharray->position, $compid);
                                $sibling_id = $matharray->compid;
                            }
                            else
                            {
                                $matharray = $this->matharrays[$matharrayString[1]];
                                $matharray->saveIntoDb($matharray->position, $compid, $sibling_id);
                                $sibling_id = $matharray->compid;
                            }
                            break;

                        case(preg_match("/^(table.\d+)$/", $element) ? true : false):
                            $tableString = split('-', $element);

                            if (empty($sibling_id))
                            {
                                $table = $this->tables[$tableString[1]];
                                $table->saveIntoDb($table->position, $compid);
                                $sibling_id = $table->compid;
                            }
                            else
                            {
                                $table = $this->tables[$tableString[1]];
                                $table->saveIntoDb($table->position, $compid, $sibling_id);
                                $sibling_id = $table->compid;
                            }
                            break;
                    }
                }
            }
        }

        $this->compid = $compid;

//        foreach ($this->proof_block_bodys as $pbb)
//        {
//            $data = new stdClass();
//            if (!empty($pbb->proof_logic))
//            {
//                $data->proof_logic = $pbb->proof_logic;
//            }
//            if (!empty($pbb->logic_type))
//            {
//                $data->logic_type = $pbb->logic_type;
//            }
//            if (!empty($pbb->caption))
//            {
//                $data->caption = $pbb->caption;
//            }
//            if (!empty($pbb->proof_block_content))
//            {
//                $data->proof_content = $pbb->proof_block_content;
//            }
//
//            $this->id = $DB->insert_record('msm_proof_block', $data);
//
//            $this->compid = $this->insertToCompositor($this->id, 'msm_proof_block', $parentid, $siblingid);
//            $siblingid = $this->compid;
//        $elementPositions = array();
//        $sibling_id = null;
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
//        if (!empty($this->matharrays))
//        {
//            foreach ($this->matharrays as $key => $matharray)
//            {
//                $elementPositions['matharray' . '-' . $key] = $matharray->position;
//            }
//        }
//
//        if (!empty($this->tables))
//        {
//            foreach ($this->tables as $key => $table)
//            {
//                $elementPositions['table' . '-' . $key] = $table->position;
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
//
//                case(preg_match("/^(matharray.\d+)$/", $element) ? true : false):
//                    $matharrayString = split('-', $element);
//
//                    if (empty($sibling_id))
//                    {
//                        $matharray = $this->matharrays[$matharrayString[1]];
//                        $matharray->saveIntoDb($matharray->position, $this->compid);
//                        $sibling_id = $matharray->compid;
//                    }
//                    else
//                    {
//                        $matharray = $this->matharrays[$matharrayString[1]];
//                        $matharray->saveIntoDb($matharray->position, $this->compid, $sibling_id);
//                        $sibling_id = $matharray->compid;
//                    }
//                    break;
//
//                case(preg_match("/^(table.\d+)$/", $element) ? true : false):
//                    $tableString = split('-', $element);
//
//                    if (empty($sibling_id))
//                    {
//                        $table = $this->tables[$tableString[1]];
//                        $table->saveIntoDb($table->position, $this->compid);
//                        $sibling_id = $table->compid;
//                    }
//                    else
//                    {
//                        $table = $this->tables[$tableString[1]];
//                        $table->saveIntoDb($table->position, $this->compid, $sibling_id);
//                        $sibling_id = $table->compid;
//                    }
//                    break;
//            }
//        }
//        }
    }

    function loadFromDb($id, $compid)
    {
        global $DB;

        $proofBlockRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($proofBlockRecord))
        {
            $this->compid = $compid;
            $this->caption = $proofBlockRecord->caption;
            $this->proof_content = $proofBlockRecord->proof_content;
        }

        $this->subordinates = array();
        $this->matharrays = array();
        $this->medias = array();
        $this->tables = array();

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

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
                    $this->matharrays[] = $matharray;
                    break;

                case('msm_media'):
                    $media = new Media();
                    $media->loadFromDb($child->unit_id, $child->id);
                    $this->medias[] = $media;
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

    function displayhtml()
    {
        $content = '';

        $content .="<div class='proofblock'>";

        $content .= "<div class='proofblocktitle'>";
        $content .= $this->caption;
        $content .= "</div>";
        // this content needs proof.block.body tags to be added due to it lacking a root element
        // (without it, it will error out in displayContent due to loadXML method needing a root element)
        $content .= $this->displayContent($this, "<proof.block.body>$this->proof_content</proof.block.body>");

        $content .="</div>";

        return $content;
    }

}

?>
