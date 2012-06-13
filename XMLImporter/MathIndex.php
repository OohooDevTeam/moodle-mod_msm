<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Index
 *
 * @author User
 */
class MathIndex extends Element
{

    public $position;
    public $term;
    public $symbol;
    public $symbol_type;
    public $sortstring;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->glossarytable = 'msm_index_glossary';
        $this->symboltable = 'msm_index_symbol';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $nameofElement = $DomElement->tagName;

        $this->infos = array();
        $this->names = array();

        switch ($nameofElement)
        {
            case('index.symbol'):
                $this->symbol = $this->getContent($DomElement->getElementsByTagName('symbol')->item(0));
                $this->symbol_type = $DomElement->getElementsByTagName('symbol')->item(0)->getAttribute('type');
                $this->sortstring = $this->getDomAttribute($DomElement->getElementsByTagName('sortstring'));

                $infos = $DomElement->getElementsByTagName('info');

                foreach ($infos as $i)
                {
                    $position = $position + 1;
                    $info = new MathInfo($this->xmlpath);
                    $info->loadFromXml($i, $position);
                    $this->infos[] = $info;
                }

                break;

            case('index.glossary'):
                $this->term = $this->getContent($DomElement->getElementsByTagName('term')->item(0));

                $infos = $DomElement->getElementsByTagName('info');

                foreach ($infos as $i)
                {
                    $position = $position + 1;
                    $info = new MathInfo($this->xmlpath);
                    $info->loadFromXml($i, $position);
                    $this->infos[] = $info;
                }

                break;

            case('index.author'):
                $names = $DomElement->getElementsByTagName('name');

                foreach ($names as $n)
                {
                    $position = $position + 1;
                    $name = new Person($this->xmlpath);
                    $name->loadFromXml($n, $position);
                    $this->names[] = $name;
                }

                $infos = $DomElement->getElementsByTagName('info');

                foreach ($infos as $i)
                {
                    $position = $position + 1;
                    $info = new MathInfo($this->xmlpath);
                    $info->loadFromXml($i, $position);
                    $this->infos[] = $info;
                }

                break;
        }
    }

    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();

        if (!empty($this->symbol))
        {
            $data->symbol = $this->symbol;
            $data->symbol_type = $this->symbol_type;
            $data->sortstring = $this->sortstring;

            $this->id = $DB->insert_record($this->symboltable, $data);
        }

        if (!empty($this->names))
        {
            foreach ($this->names as $key => $name)
            {
                $name->saveIntoDb($name->position, 'index');
            }
        }

        if (!empty($this->term))
        {
            $data->term = $this->term;
            $this->id = $DB->insert_record($this->glossarytable, $data);
        }

        foreach ($this->infos as $info)
        {
            $info->saveIntoDb($info->position);
        }
    }

}

?>
