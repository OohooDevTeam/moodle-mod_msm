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

        $this->proof_blocks = array();
       
        $this->string_id = $DomElement->getAttribute('proofid');

        $this->proof_type = $DomElement->getAttribute('type');
        
        $proof_blocks = $DomElement->getElementsByTagName('proof.block');
        
        foreach($proof_blocks as $pb)
        {
            $position = $position+1;
            $proof_block = new ProofBlock($this->xmlpath);
            $proof_block->loadFromXml($pb, $position);
            $this->proof_blocks[] = $proof_block;
        }
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position)
    {
        echo "proof save start";
        $time = time();
        print_object($time);
        
        global $DB;
        $data = new stdClass();

        $data->string_id = $this->string_id;
        $data->proof_type = $this->proof_type;
        
        $this->id = $DB->insert_record($this->tablename, $data);
        
        foreach($this->proof_blocks as $proof_block)
        {
            $proof_block->saveIntoDb($proof_block->position);
        }
    }

}

?>
