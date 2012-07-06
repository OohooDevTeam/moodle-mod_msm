<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MathImg
 *
 * @author User
 */
class MathImg extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_img';
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
        $this->src = $DomElement->getAttribute('src');
        $this->height = $DomElement->getAttribute('height');
        $this->width = $DomElement->getAttribute('width');
        $this->description = $this->getDomAttribute($DomElement->getElementsByTagName('description'));
        $this->extended_caption = $this->getContent($DomElement->getElementsByTagName('extended.caption')->item(0));

        $this->imagemappings = array();
        $imageMappings = $DomElement->getElementsByTagName('image.mapping');

        $doc = new DOMDocument();

        foreach ($imageMappings as $im)
        {
            $position = $position + 1;
            $element = $doc->importNode($im, true);
            $this->imagemappings[] = $doc->saveXML($element);
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
        $data->string_id = $this->string_id;
        $data->src = $this->src;
        $data->height = $this->height;
        $data->width = $this->width;
        $data->description = $this->description;
        $data->extended_caption = $this->extended_caption;

        if (!empty($this->imagemappings))
        {
            foreach ($this->imagemappings as $imagemapping)
            {
                $data->image_mapping = $imagemapping;
                $this->id = $DB->insert_record($this->tablename, $data);
//                $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
            }
        }
        else
        {
            $this->id = $DB->insert_record($this->tablename, $data);
//            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
        }
    }

}

?>
