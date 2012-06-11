<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Definition
 *
 * @author User
 */
class Definition extends Element
{

    public $position;

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
      
        $this->content = array();
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();


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
            foreach ($this->processSubordinate($d, $position)->subordinates as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processSubordinate($d, $position)->indexauthors as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processSubordinate($d, $position)->indexglossarys as $indexglossary)
            {
                $this->indexglossarys[] = $subordinate;
            }

            foreach ($this->processSubordinate($d, $position)->indexsymbols as $indexsymbol)
            {
                $this->indexsymbols[] = $subordinate;
            }

            foreach ($this->processSubordinate($d, $position)->content as $content)
            {
                $this->content[] = $content;
            }
        }
    }

    function saveIntoDb($position)
    {
        global $DB;

        $data->def_type = $this->type;
        $data->string_id = $this->string_id;
        if (!empty($this->caption))
        {
            $data->caption = $this->caption;
        }

        $data->description = $this->description;

        if (!empty($this->content))
        {
            foreach ($this->content as $content)
            {
                $data->def_content = $content;
              
                $this->id = $DB->insert_record($this->tablename, $data);
            }
        }
        else // has def.body as child of def
        {
            $this->id = $DB->insert_record($this->tablename, $data);
        }

        foreach ($this->associates as $key => $associate)
        {
            $associate->saveIntoDb($associate->position);
        }
    }

}

?>
