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
        $this->proof_block_bodys = array();

        // initial declaration of the standard class
        $proofblockbody = new stdClass();

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == 'logic')
                {
                    // for first item, it can be empty
                    // the array proof_block_bodys stores the parsed content before reading anothe logic
                    // which is used to divide between proofblock and proof.block.body contents.
                    if (!empty($proofblockbody->proof_block_content))
                    {
                        // this is a "work-around" solution for the transformed XML that does not have the proof.block.body
                        // inserted due to difficulty of transforming them to proper schema.
                        $this->proof_block_bodys[] = $proofblockbody;
                    }

                    // need to make a new instance of the standard class to reset all the properties
                    $proofblockbody = new stdClass();

                    $doc = new DOMDocument();
                    $element = $doc->importNode($child, true);
                    $proofblockbody->logic_type = $element->getAttribute('type');
                    $proofblockbody->proof_logic = $doc->saveXML($element);
                }
                else if ($child->tagName == 'caption')
                {
                    $proofblockbody->caption = $this->getContent($child);
                }
                else// block elements
                {
                    foreach ($this->processIndexAuthor($child, $position) as $indexauthor)
                    {
                        $proofblockbody->indexauthors[] = $indexauthor;
                    }

                    foreach ($this->processIndexGlossary($child, $position) as $indexglossary)
                    {
                        $proofblockbody->indexglossarys[] = $indexglossary;
                    }

                    foreach ($this->processIndexSymbols($child, $position) as $indexsymbol)
                    {
                        $proofblockbody->indexsymbols[] = $indexsymbol;
                    }
                    foreach ($this->processSubordinate($child, $position) as $subordinate)
                    {
                        $proofblockbody->subordinates[] = $subordinate;
                    }

                    foreach ($this->processMedia($child, $position) as $media)
                    {
                        $proofblockbody->medias[] = $media;
                    }

                    foreach ($this->processMathArray($child, $position) as $matharray)
                    {
                        $proofblockbody->matharrays[] = $matharray;
                    }

                    foreach ($this->processTable($child, $position) as $table)
                    {
                        $proofblockbody->tables[] = $table;
                    }

                    foreach ($this->processContent($child, $position) as $content)
                    {
                        $proofblockbody->proof_block_content .= $content;
                    }
                }
            }
        }
        // storing the last contents of proof block that was read
        $this->proof_block_bodys[] = $proofblockbody;
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position, $parentid = '', $siblingid = '')
    {
        global $DB;

        foreach ($this->proof_block_bodys as $pbb)
        {
            $data = new stdClass();
            if (!empty($pbb->proof_logic))
            {
                $data->proof_logic = $pbb->proof_logic;
            }
            if (!empty($pbb->logic_type))
            {
                $data->logic_type = $pbb->logic_type;
            }
            if (!empty($pbb->caption))
            {
                $data->caption = $pbb->caption;
            }
            if (!empty($pbb->proof_block_content))
            {
                $data->proof_content = $pbb->proof_block_content;
            }

            $this->id = $DB->insert_record('msm_proof_block', $data);

            $this->compid = $this->insertToCompositor($this->id, 'msm_proof_block', $parentid, $siblingid);
            $siblingid = $this->compid;
            $elementPositions = array();
            $sibling_id = null;

            if (!empty($pbb->subordinates))
            {
                foreach ($pbb->subordinates as $key => $subordinate)
                {
                    $elementPositions['subordinate' . '-' . $key] = $subordinate->position;
                }
            }

            if (!empty($pbb->indexauthors))
            {
                foreach ($pbb->indexauthors as $key => $indexauthor)
                {
                    $elementPositions['indexauthor' . '-' . $key] = $indexauthor->position;
                }
            }

            if (!empty($pbb->indexglossarys))
            {
                foreach ($pbb->indexglossarys as $key => $indexglosary)
                {
                    $elementPositions['indexglossary' . '-' . $key] = $indexglosary->position;
                }
            }

            if (!empty($pbb->indexsymbols))
            {
                foreach ($pbb->indexsymbols as $key => $indexsymbol)
                {
                    $elementPositions['indexsymbol' . '-' . $key] = $indexsymbol->position;
                }
            }

            if (!empty($pbb->medias))
            {
                foreach ($pbb->medias as $key => $media)
                {
                    $elementPositions['media' . '-' . $key] = $media->position;
                }
            }

            if (!empty($pbb->matharrays))
            {
                foreach ($pbb->matharrays as $key => $matharray)
                {
                    $elementPositions['matharray' . '-' . $key] = $matharray->position;
                }
            }

            if (!empty($pbb->tables))
            {
                foreach ($pbb->tables as $key => $table)
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
                            $subordinate = $pbb->subordinates[$subordinateString[1]];
                            $subordinate->saveIntoDb($subordinate->position, $this->compid);
                            $sibling_id = $subordinate->compid;
                        }
                        else
                        {
                            $subordinate = $pbb->subordinates[$subordinateString[1]];
                            $subordinate->saveIntoDb($subordinate->position, $this->compid, $sibling_id);
                            $sibling_id = $subordinate->compid;
                        }
                        break;

                    case(preg_match("/^(indexauthor.\d+)$/", $element) ? true : false):
                        $indexauthorString = split('-', $element);

                        if (empty($sibling_id))
                        {
                            $indexauthor = $pbb->indexauthors[$indexauthorString[1]];
                            $indexauthor->saveIntoDb($indexauthor->position, $this->compid);
                            $sibling_id = $indexauthor->compid;
                        }
                        else
                        {
                            $indexauthor = $pbb->indexauthors[$indexauthorString[1]];
                            $indexauthor->saveIntoDb($indexauthor->position, $this->compid, $sibling_id);
                            $sibling_id = $indexauthor->compid;
                        }
                        break;

                    case(preg_match("/^(indexsymbol.\d+)$/", $element) ? true : false):
                        $indexsymbolString = split('-', $element);

                        if (empty($sibling_id))
                        {
                            $indexsymbol = $pbb->indexsymbols[$indexsymbolString[1]];
                            $indexsymbol->saveIntoDb($indexsymbol->position, $this->compid);
                            $sibling_id = $indexsymbol->compid;
                        }
                        else
                        {
                            $indexsymbol = $pbb->indexsymbols[$indexsymbolString[1]];
                            $indexsymbol->saveIntoDb($indexsymbol->position, $this->compid, $sibling_id);
                            $sibling_id = $indexsymbol->compid;
                        }
                        break;

                    case(preg_match("/^(indexglossary.\d+)$/", $element) ? true : false):
                        $indexglossaryString = split('-', $element);

                        if (empty($sibling_id))
                        {
                            $indexglossary = $pbb->indexglossarys[$indexglossaryString[1]];
                            $indexglossary->saveIntoDb($indexglossary->position, $this->compid);
                            $sibling_id = $indexglossary->compid;
                        }
                        else
                        {
                            $indexglossary = $pbb->indexglossarys[$indexglossaryString[1]];
                            $indexglossary->saveIntoDb($indexglossary->position, $this->compid, $sibling_id);
                            $sibling_id = $indexglossary->compid;
                        }
                        break;

                    case(preg_match("/^(media.\d+)$/", $element) ? true : false):
                        $mediaString = split('-', $element);

                        if (empty($sibling_id))
                        {
                            $media = $pbb->medias[$mediaString[1]];
                            $media->saveIntoDb($media->position, $this->compid);
                            $sibling_id = $media->compid;
                        }
                        else
                        {
                            $media = $pbb->medias[$mediaString[1]];
                            $media->saveIntoDb($media->position, $this->compid, $sibling_id);
                            $sibling_id = $media->compid;
                        }
                        break;

                    case(preg_match("/^(matharray.\d+)$/", $element) ? true : false):
                        $matharrayString = split('-', $element);

                        if (empty($sibling_id))
                        {
                            $matharray = $pbb->matharrays[$matharrayString[1]];
                            $matharray->saveIntoDb($matharray->position, $this->compid);
                            $sibling_id = $matharray->compid;
                        }
                        else
                        {
                            $matharray = $pbb->matharrays[$matharrayString[1]];
                            $matharray->saveIntoDb($matharray->position, $this->compid, $sibling_id);
                            $sibling_id = $matharray->compid;
                        }
                        break;

                    case(preg_match("/^(table.\d+)$/", $element) ? true : false):
                        $tableString = split('-', $element);

                        if (empty($sibling_id))
                        {
                            $table = $pbb->tables[$tableString[1]];
                            $table->saveIntoDb($table->position, $this->compid);
                            $sibling_id = $table->compid;
                        }
                        else
                        {
                            $table = $pbb->tables[$tableString[1]];
                            $table->saveIntoDb($table->position, $this->compid, $sibling_id);
                            $sibling_id = $table->compid;
                        }
                        break;
                }
            }
        }
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
