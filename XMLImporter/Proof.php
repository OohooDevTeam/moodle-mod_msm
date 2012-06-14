<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Proof
 *
 * @author User
 */
class Proof extends Element
{

    public $position;
    public $logic_type;
    public $proof_logic;
    public $caption;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_proof';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->proof_block_body = array();
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexauthors = array();

        $this->string_id = $DomElement->getAttribute('id');

        $this->proof_type = $DomElement->getAttribute('type');

// may not be a best code to deal with proof...
// to make up one record, need $this->logic_type[1],
// $this->proof_logic[1]...etc 
        $proof_blocks = $DomElement->getElementsByTagName('proof.block');
        foreach ($proof_blocks as $pb)
        {
            foreach ($pb->childNodes as $key => $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    if ($child->tagName == 'logic')
                    {
                        $this->logic_type = $child->getAttribute('type');
                        $this->proof_logic = $this->getContent($child->getElementsByTagName('part.ref')->item(0));
                    }
                    if ($child->tagName == 'caption')
                    {
                        $this->caption = $this->getContent($child);
                    }
                }
            }

            $proofbodys = $pb->getElementsByTagName('proof.block.body');
            $doc = new DOMDocument();

            foreach ($proofbodys as $proofbody)
            {
                foreach ($this->processIndexAuthor($proofbody, $position) as $indexauthor)
                {
                    $this->indexauthors[] = $indexauthor;
                }

                foreach ($this->processIndexGlossary($proofbody, $position) as $indexglossary)
                {
                    $this->indexglossarys[] = $indexglossary;
                }

                foreach ($this->processIndexSymbols($proofbody, $position) as $indexsymbol)
                {
                    $this->indexsymbols[] = $indexsymbol;
                }
                foreach ($this->processSubordinate($proofbody, $position) as $subordinate)
                {
                    $this->subordinates[] = $subordinate;
                }

                foreach ($this->processContent($proofbody, $position) as $content)
                {
                    $this->proof_block_body[] = $content;
                }
            }
        }
    }
    
    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();
        
        $data->string_id = $this->string_id;
        $data->proof_type = $this->proof_type;
        $data->logic_type = $this->logic_type;
        $data->proof_logic = $this->proof_logic;
        $data->caption = $this->caption;
        
        if(!empty($this->proof_block_body))
        {
            foreach($this->proof_block_body as $proof_block_body)
            {
                $data->proof_content = $proof_block_body;
                $this->id = $DB->insert_record($this->tablename, $data);
            }
        }
        else
        {
             $this->id = $DB->insert_record($this->tablename, $data);
        }
    }

}

?>
