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

        $sibling_id = null;
        $record = $this->checkForRecord($this->img, 'src');
        if (empty($record))
        {
            if (empty($sibling_id))
            {
                $this->img->saveIntoDb($this->img->position);
                $this->img->compid = $this->insertToCompositor($this->img->id, $this->img->tablename, $this->compid);
                $sibling_id = $this->img->compid;
            }
            else
            {
                $this->img->saveIntoDb($this->img->position);
                $this->img->compid = $this->insertToCompositor($this->img->id, $this->img->tablename, $this->compid, $sibling_id);
                $sibling_id = $this->img->compid;
            }
        }
        else
        {
            if (empty($sibling_id))
            {
                $recordID = $record->id;
                $this->img->compid = $this->insertToCompositor($recordID, $this->img->tablename, $this->compid);
                $sibling_id = $this->img->compid;
            }
            else
            {
                $recordID = $record->id;
                $this->img->compid = $this->insertToCompositor($recordID, $this->img->tablename, $this->compid, $sibling_id);
                $sibling_id = $this->img->compid;
            }
        }
    }

}

?>
