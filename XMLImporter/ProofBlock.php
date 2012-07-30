<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProofBlock
 *
 * @author User
 */
class ProofBlock extends Element
{

    public $position;
    public $proof_block_body;
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

        $logic = $DomElement->getElementsByTagName('logic')->item(0);

        if (!empty($logic))
        {
            $doc = new DOMDocument();
            $element = $doc->importNode($logic, true);
            $this->logic_type = $element->getAttribute('type');
            $this->proof_logic = $doc->saveXML($element);
        }

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $proof_block_bodys = $DomElement->getElementsByTagName('proof.block.body');

        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->medias = array();

        foreach ($proof_block_bodys as $pbb)
        {
            foreach ($this->processIndexAuthor($pbb, $position) as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processIndexGlossary($pbb, $position) as $indexglossary)
            {
                $this->indexglossarys[] = $indexglossary;
            }

            foreach ($this->processIndexSymbols($pbb, $position) as $indexsymbol)
            {
                $this->indexsymbols[] = $indexsymbol;
            }
            foreach ($this->processSubordinate($pbb, $position) as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processMedia($pbb, $position) as $media)
            {
                $this->medias[] = $media;
            }

            foreach ($this->processContent($pbb, $position) as $content)
            {
                $this->proof_block_body .= $content;
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
        if (!empty($this->proof_logic))
        {
            $data->logic_type = $this->logic_type;
            $data->proof_logic = $this->proof_logic;
        }

        $data->caption = $this->caption;

        $data->proof_content = $this->proof_block_body;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);

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
        
        $proofBlockRecord = $DB->get_record($this->tablename, array('id'=>$id));
        
        if(!empty($proofBlockRecord))
        {
            $this->compid = $compid;
            $this->proof_content = $proofBlockRecord->proof_content;
        }
        
        $this->subordinates = array();
        $childElements = $DB->get_records('msm_compositor', array('parent_id'=>$compid), 'prev_sibling_id');
        
        foreach($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id'=>$child->table_id))->tablename;
            
            if($childtablename == 'msm_subordinate')
            {
                $subordinate = new Subordinate();
                $subordinate->loadFromDb($child->unit_id, $child->id);
                $this->subordinates[]= $subordinate;
            }
        }
        
        return $this;
    }

    function displayhtml()
    {
        $content = '';
        
        $content .= $this->displaySubordinate($this, $this->proof_content);
        
        return $content;
    }
}

?>
