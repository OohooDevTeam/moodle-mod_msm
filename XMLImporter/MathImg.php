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
        global $DB, $CFG;
        $data = new stdClass();
        $data->string_id = $this->string_id;
        $data->src = $CFG->wwwroot . '/mod/msm/newxml/' . basename(dirname($this->xmlpath)) . '/'
                . basename($this->xmlpath) . '/' . $this->src;
        $data->height = $this->height;
        $data->width = $this->width;
        $data->description = $this->description;
        $data->extended_caption = $this->extended_caption;

        if (!empty($this->imagemappings))
        {
            foreach ($this->imagemappings as $imagemapping)
            {
                $data->image_mapping = $imagemapping;
                if (!empty($this->src))
                {
                    $this->id = $DB->insert_record($this->tablename, $data);
                }
            }
        }
        else
        {
            if (!empty($this->src))
            {
                $this->id = $DB->insert_record($this->tablename, $data);
            }
        }
    }

    function loadFromDb($id, $compid)
    {
        global $DB;

        $imgRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($imgRecord))
        {
            $this->image_mapping = $imgRecord->image_mapping;
            $this->src = $imgRecord->src;
            $this->height = $imgRecord->height;
            $this->width = $imgRecord->width;
        }

        return $this;
    }

    function displayhtml()
    {
        $content = '';

        $imagename = explode('/', $this->src);

        $usemap = explode('.', end($imagename));
        $usemapname = $usemap[0];

        if ((!empty($this->height)) || (!empty($this->width)))
        {
            if (!empty($this->image_mapping))
            {
                $content .= "<img src='" . $this->src . "' height='" . $this->height . "' width='" . $this->width . "' usemap='#" . $usemapname . "'/>";
                $imagemapping = str_replace("<image.mapping>", "<map name='" . $usemapname . "'>", $this->image_mapping);
                $finalimagemapping = str_replace("</image.mapping>", "</map name>", $imagemapping);
                $content .= $finalimagemapping;
            }
            else
            {
                $content .= "<img src='" . $this->src . "' height='" . $this->height . "' width='" . $this->width . "'/>";
            }
        }
        else
        {
            if (!empty($this->image_mapping))
            {
                $content .= "<img src='" . $this->src . "' usemap='#" . $usemapname . "' height200' width='300'/>";
                $imagemapping = str_replace("<image.mapping>", "<map name='" . $usemapname . "'>", $this->image_mapping);
                $finalimagemapping = str_replace("</image.mapping>", "</map name>", $imagemapping);
                $content .= $finalimagemapping;
            }
            else
            {
                $content .= "<img src='" . $this->src . "' height='200' width='300'/>";
            }
        }



        return $content;
    }

}

?>
