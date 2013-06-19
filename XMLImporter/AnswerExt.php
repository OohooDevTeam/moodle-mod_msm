<?php

/**
**************************************************************************
**                              MSM                                     **
**************************************************************************
* @package     mod                                                      **
* @subpackage  msm                                                      **
* @name        msm                                                      **
* @copyright   University of Alberta                                    **
* @link        http://ualberta.ca                                       **
* @author      Ga Young Kim                                             **
* @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
**************************************************************************
**************************************************************************/

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
            @$step->loadFromXml($s, $position);
            $this->steps[] = $step;
        }
    }
    
    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();
        
        $data->ext_type = $this->type;
        $data->ext_version = $this->version;
        $data->caption = $this->caption;
        $data->ext_content = null;
        $data->ext_name = $this->ext_name;
        
        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        
        $elementPosition = array();
        foreach ($this->steps as $key => $step)
        {
            $elementPosition['step' . '-' . $key] = $step->position;
        }

        asort($elementPosition);

        foreach ($elementPosition as $element => $value)
        {
            $stepString = explode('-', $element);

            $this->steps[$stepString[1]]->saveIntoDb($this->steps[$stepString[1]]->position, $msmid, $this->compid);
        }
    }

}

?>
