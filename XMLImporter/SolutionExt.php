<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SolutionExt
 *
 * @author User
 */
class SolutionExt extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_ext';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));
        $this->ext_name = $DomElement->tagName;

        $this->steps = array();
        $steps = $DomElement->getElementsByTagName('step');
        foreach ($steps as $s)
        {
            $position = $position + 1;
            $step = new Step($this->xmlpath);
            $step->loadFromXml($s, $position);
            $this->steps[] = $step;
        }
    }

    function saveIntoDb($position, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();
        $data->caption = $this->caption;
        $data->ext_name = $this->ext_name;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);

        $elementPosition = array();
        foreach ($this->steps as $key => $step)
        {
            $elementPosition['step' . '-' . $key] = $step->position;
        }

        asort($elementPosition);

        foreach ($elementPosition as $element => $value)
        {
            $stepString = split('-', $element);

            $this->steps[$stepString[1]]->saveIntoDb($this->steps[$stepString[1]]->position, $this->compid);
        }

//        foreach($this->steps as $step)
//        {
//            $step->saveIntoDb($step->position);
//        }
    }

}

?>
