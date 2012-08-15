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
 * Description of Media
 *
 * @author User
 */
class Media extends Element
{

    public $position;
    public $string_id;
    public $inline;
    public $type;
    public $img;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_media';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->string_id = $DomElement->getAttribute('id');
        $this->active = $DomElement->getAttribute('active');
        $this->inline = $DomElement->getAttribute('inline');
        $this->type = $DomElement->getAttribute('type');

        $img = $DomElement->getElementsByTagName('img')->item(0);

        $position = $position + 1;
        $image = new MathImg($this->xmlpath);
        $image->loadFromXml($img, $position);
        $this->img = $image;
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
        $data->string_id = $this->string_id;
        $data->active = $this->active;
        $data->inline = $this->inline;
        $data->media_type = $this->type;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);

       $this->img->saveIntoDb($this->img->position, $this->compid);
        
    }
    
    function loadFromDb($id, $compid)
    {
        global $DB;
        
        $mediaRecord = $DB->get_record('msm_media', array('id'=>$id));
        
        if(!empty($mediaRecord))
        {
            $this->compid = $compid;
            $this->active = $mediaRecord->active;
            $this->inline = $mediaRecord->inline;
            $this->media_type = $mediaRecord->media_type;            
        }
        
//        print_object($mediaRecord);
        
        $childElements = $DB->get_records('msm_compositor', array('parent_id'=>$compid), 'prev_sibling_id');
        
        $this->childs = array();
        
        foreach($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id'=>$child->table_id))->tablename;
            
            if($childtablename == 'msm_img')
            {
                $img = new MathImg();
                $img->loadFromDb($child->unit_id, $child->id);
                $this->childs[] = $img;
            }
        }        
        
        return $this;
    }
    
    function displayhtml()
    {
        $content = '';
        $content .= "<div class='picture'>";
        
        foreach($this->childs as $childComponent)
        {
            $content .= $childComponent->displayhtml($this->inline);
        }
        $content .= "</div>";
        
        return $content;
    }

}

?>
