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
    function saveIntoDb($position)
    {
        echo "proofblock save start";
        $time = time();
        print_object($time);
        
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

        foreach ($this->subordinates as $key => $subordinate)
        {
            $subordinate->saveIntoDb($subordinate->position);
        }

        foreach ($this->indexglossarys as $key => $indexglossary)
        {
            $indexglossary->saveIntoDb($indexglossary->position);
        }

        foreach ($this->indexsymbols as $key => $indexsymbol)
        {
            $indexsymbol->saveIntoDb($indexsymbol->position);
        }

        foreach ($this->indexauthors as $key => $indexauthor)
        {
            $indexauthor->saveIntoDb($indexauthor->position);
        }

        foreach ($this->medias as $key => $media)
        {
            $media->saveIntoDb($media->position);
        }
    }

}

?>
