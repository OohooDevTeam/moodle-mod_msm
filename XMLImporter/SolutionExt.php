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

    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();
        $data->caption = $this->caption;
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
            $stepString = split('-', $element);

            $this->steps[$stepString[1]]->saveIntoDb($this->steps[$stepString[1]]->position, $msmid, $this->compid);
        }
    }

}

?>
