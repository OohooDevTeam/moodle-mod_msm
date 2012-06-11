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

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->glossarytable = 'msm_index_glossary';
        $this->symboltable = 'msm_index_symbol';

        //for index author
        $this->name = 'msm_name';
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
                $this->symbol = $this->getDomAttribute($DomElement->getElementsByTagName('symbol'));
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
                $this->term = $this->getDomAttribute($DomElement->getElementsByTagName('term'));

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

}

?>
