<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Theorem
 *
 * @author User
 */
class Theorem extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_theorem';
    }

    /**
     *
     * @param DOMElement $DomElement 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->theorem_type = $DomElement->getAttribute('type');
        $this->string_id = $DomElement->getAttribute('id');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0)); 
        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));
        $this->description = $this->getDomAttribute($DomElement->getElementsByTagName('description'));

        $this->statements = array();
        $this->associates = array();
        $this->proofs = array();

        foreach ($DomElement->childNodes as $key => $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == 'associate')
                {
                    $position = $position + 1;
                    $associate = new Associate($this->xmlpath);
                    $associate->loadFromXml($child, $position);
                    $this->associates[] = $associate;
                }
                else if ($child->tagName == 'statement.theorem')
                {
                    $position = $position + 1;
                    $statement = new StatementTheorem($this->xmlpath);
                    $statement->loadFromXml($child, $position);
                    $this->statements[] = $statement;
                }
                else if($child->tagName == 'proof')
                {
                    $position = $position + 1;
                    $proof = new Proof($this->xmlpath);
                    $proof->loadFromXml($child, $position);
                    $this->proofs[] = $proof;
                }
            }
        }
    }
    
    function saveIntoDb($position)
    {
        global $DB;
        
        $data = new stdClass();
        $data->theorem_type = $this->theorem_type;
        $data->string_id = $this->string_id;
        $data->caption = $this->caption;
        $data->textcaption = $this->textcaption;
        $data->description = $this->description;
        
        $this->id = $DB->insert_record($this->tablename, $data);
        
        foreach($this->associates as $key=>$associate)
        {
            $associate->saveIntoDb($associate->position);
        }
        
        foreach($this->statements as $key=>$statement)
        {
            $statement->saveIntoDb($statement->position);
        }
        
        foreach($this->proofs as $key=>$proof)
        {
            $proof->saveIntoDb($proof->position);
        }
    }

}

?>
