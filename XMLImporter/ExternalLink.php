<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExternalLink
 *
 * @author User
 */
class ExternalLink extends Element
{
     public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_external_link';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->href = $DomElement->getAttribute('href');
        $this->type = $DomElement->getAttribute('type');
        $this->target = $DomElement->getAttribute('target');
        
        $infos = $DomElement->getElementsByTagName('info');
        $this->infos = array();
        
        foreach($infos as $i)
        {
            $position = $position+1;
            $info = new MathInfo($this->xmlpath);
            $info->loadFromXml($i, $position);
            $this->infos[]= $info;
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
        $data->href = $this->href;
        $data->type = $this->type;
        $data->target =$this->target;
        
        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
        
        $elementPosition = array();
        foreach ($this->infos as $key => $info)
        {
            $elementPosition['info' . '-' . $key] = $info->position;
        }

        asort($elementPosition);

        foreach ($elementPosition as $element => $value)
        {
            $infoString = split('-', $element);

            $this->infos[$infoString[1]]->saveIntoDb($this->infos[$infoString[1]]->position, $this->compid);
        }
        
//        foreach($this->infos as $info)
//        {
//            $info->saveIntoDb($info->position);
//        }
    }
}

?>
