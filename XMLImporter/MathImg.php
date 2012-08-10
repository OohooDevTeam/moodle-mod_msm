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

        $this->imageareas = array();
        $imageMappings = $DomElement->getElementsByTagName('image.mapping');

        foreach ($imageMappings as $im)
        {
            $areas = $im->getElementsByTagName('area');

            foreach ($areas as $a)
            {
                $position = $position + 1;
                $imgArea = new ImgArea($this->xmlpath);
                $imgArea->loadFromXml($a, $position);
                $this->imageareas[] = $imgArea;
            }
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

        $sourcefolders = explode('/', $this->src);

        if (count($sourcefolders) == 2)
        {
            $data->src = $CFG->wwwroot . '/mod/msm/newxml/' . basename(dirname($this->xmlpath)) . '/'
                    . basename($this->xmlpath) . '/' . $sourcefolders[0] . '/' . $sourcefolders[1];
        }
        else if (count($sourcefolders) == 1) // to account for src in xml that does not include the ims folder in its path
        {
            $data->src = $CFG->wwwroot . '/mod/msm/newxml/' . basename(dirname($this->xmlpath)) . '/'
                    . basename($this->xmlpath) . '/ims/' . $sourcefolders[0];
        }
        else
        {
            print_object(count($sourcefolders));
        }

        $data->height = $this->height;
        $data->width = $this->width;
        $data->description = $this->description;
        $data->extended_caption = $this->extended_caption;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);

        $elementPositions = array();
        $sibling_id = null;


        if (!empty($this->imageareas))
        {
            foreach ($this->imageareas as $key => $imagearea)
            {
                $elementPositions['imgarea' . '-' . $key] = $imagearea->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element)
        {
            switch ($element)
            {
                case(preg_match("/^(imgarea.\d+)$/", $element) ? true : false):
                    $imageareaString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $imagearea = $this->imageareas[$imageareaString[1]];
                        $imagearea->saveIntoDb($imagearea->position, $this->compid);
                        $sibling_id = $imagearea->compid;
                    }
                    else
                    {
                        $imagearea = $this->imageareas[$imageareaString[1]];
                        $imagearea->saveIntoDb($imagearea->position, $this->compid, $sibling_id);
                        $sibling_id = $imagearea->compid;
                    }
            }
        }
    }

    function loadFromDb($id, $compid)
    {
        global $DB;

        $imgRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($imgRecord))
        {
            $this->src = $imgRecord->src;
            $this->height = $imgRecord->height;
            $this->width = $imgRecord->width;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        $this->imageareas = array();

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            if ($childtablename == 'msm_image_mapping')
            {
                $imagearea = new ImgArea();
                $imagearea->loadFromDb($child->unit_id, $child->id);
                $this->imageareas[] = $imagearea;
            }
        }

        return $this;
    }

    function displayhtml($inline)
    {
        $content = '';

        //getting the name of the image file to tag each image with name
        $srcfile = explode('/', $this->src);
        $filename = explode('.', end($srcfile));

        if ((!empty($this->width)) && (!empty($this->height)) && ($inline == '0'))
        {
            $content .= "<img class='mathimage' src='" . $this->src . "' height='" . $this->height . "' width='" . $this->width . "' name='#" . $filename[0] . "'/>";
        }
        else if((!empty($this->width)) && (!empty($this->height)) && ($inline == '1'))
        {
            $content .= "<img src='" . $this->src . "' height='" . $this->height . "' width='" . $this->width . "' name='#" . $filename[0] . "'/>";
        }
        else if ((!empty($this->width)) && ($inline == '0'))
        {
            $content .= "<img class='mathimage' src='" . $this->src . "' height='200' width='" . $this->width . "' name='#" . $filename[0] . "'/>";
        }
        else if((!empty($this->width)) && ($inline == '1'))
        {
            $content .= "<img src='" . $this->src . "' height='200' width='" . $this->width . "' name='#" . $filename[0] . "'/>";
        }
        else if ((!empty($this->height)) && ($inline == '0'))
        {
            $content .= "<img class='mathimage' src='" . $this->src . "' height='" . $this->height . "' width='350' name='#" . $filename[0] . "'/>";
        }
        else if((!empty($this->height)) && ($inline == '1'))
        {
             $content .= "<img src='" . $this->src . "' height='" . $this->height . "' width='350' name='#" . $filename[0] . "'/>";
        }
        else if ($inline == '0')
        {
            $content .= "<img class='mathimage' src='" . $this->src . "' height='200' width='350'/>";
        }
        else
        {
            $content .= "<img src='" . $this->src . "' height='200' width='350'/>";
        }

        foreach ($this->imageareas as $imagearea)
        {
            $content .= $imagearea->displayhtml();
        }

        return $content;
    }

}

?>
