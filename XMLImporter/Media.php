<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

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

    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();
        $data->string_id = $this->string_id;
        $data->active = $this->active;
        $data->inline = $this->inline;
        $data->type = $this->type;
        $data->img = $this->img;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->img->saveIntoDb($this->img->position);
    }

}

?>
