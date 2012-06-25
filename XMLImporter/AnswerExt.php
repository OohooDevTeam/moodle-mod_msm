<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AnswerExt
 *
 * @author User
 */
class AnswerExt extends Element
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
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->type = $DomElement->getAttribute('type');
        $this->version = $DomElement->getAttribute('version');
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));
        $this->ext_name = $DomElement->tagName;
        
        $steps = $DomElement->getElementsByTagName('step');
        $this->steps = array();
        foreach($steps as $s)
        {
            $position = $position+1;
            $step = new Step($this->xmlpath);
            $step->loadFromXml($s, $position);
            $this->steps[] = $step;
        }
    }
    
    function saveIntoDb($position)
    {
//         echo "answerext save start";
//        $time = time();
//        print_object($time);
        
        global $DB;
        $data = new stdClass();
        
        $data->ext_type = $this->type;
        $data->ext_version = $this->version;
        $data->caption = $this->caption;
        $data->ext_content = null;
        $data->ext_name = $this->ext_name;
        
        $this->id = $DB->insert_record($this->tablename, $data);
        
        foreach($this->steps as $step)
        {
            $step->saveIntoDb($step->position);
        }
    }

}

?>
