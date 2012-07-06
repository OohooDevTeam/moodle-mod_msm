<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cite
 *
 * @author User
 */
class Cite extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_cite';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->cite_label = $DomElement->getAttribute('label');
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $items = $DomElement->getElementsByTagName('item');
        $this->items = array();
        foreach ($items as $i)
        {
            $position = $position + 1;
            $item = new Item($this->xmlpath);
            $item->loadFromXml($i, $position);
            $this->items[] = $item;
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
        $data->cite_label = $this->cite_label;
        $data->caption = $this->caption;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);

        $elementPosition = array();
        foreach ($this->items as $key => $item)
        {
            $elementPosition['item' . '-' . $key] = $item->position;
        }

        asort($elementPosition);

        foreach ($elementPosition as $element => $value)
        {
            $itemString = split('-', $element);

            $this->items[$itemString[1]]->saveIntoDb($this->items[$itemString[1]]->position, $this->compid);
        }
    }

}

?>
