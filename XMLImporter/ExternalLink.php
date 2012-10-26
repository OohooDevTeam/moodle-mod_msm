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
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {        
        global $DB;
        $data = new stdClass();
        $data->href = $this->href;
        $data->type = $this->type;
        $data->target =$this->target;
        
        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        
        $elementPosition = array();
        foreach ($this->infos as $key => $info)
        {
            $elementPosition['info' . '-' . $key] = $info->position;
        }

        asort($elementPosition);

        foreach ($elementPosition as $element => $value)
        {
            $infoString = explode('-', $element);

            $this->infos[$infoString[1]]->saveIntoDb($this->infos[$infoString[1]]->position, $msmid, $this->compid);
        }
        
    }
    
    function loadFromDb($id, $compid)
    {
        global $DB;
        
        $linkRecord = $DB->get_record($this->tablename, array('id'=>$id));
        
        if(!empty($linkRecord))
        {
            $this->compid = $compid;
            $this->href = $linkRecord->href;
            $this->type = $linkRecord->type;
            $this->target = $linkRecord->target;
        }
        
        $childElements = $DB->get_records('msm_compositor', array('parent_id'=>$compid), 'prev_sibling_id');
        
        $this->infos = array();
        
        foreach($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id'=>$child->table_id))->tablename;
            
            if($childtablename == 'msm_info')
            {
                $info = new MathInfo();
                $info->loadFromDb($child->unit_id, $child->id);
                $this->infos[] = $info;
            }
        }
        
        return $this;
    }
    
    function displayhtml($isindex = false)
    {
        $content ='';
        
        $content .= "<a target='" . $this->target . "' href='" . $this->href . "'>";
        $content .= $this->href;
        $content .= "</a>";
        
        return $content;
    }
}

?>
