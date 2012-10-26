<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                      **
 * @subpackage  msm                                                      **
 * @name        msm                                                      **
 * @copyright   University of Alberta                                    **
 * @link        http://ualberta.ca                                       **
 * @author      Ga Young Kim                                             **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * *************************************************************************
 * ************************************************************************ */

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

//    public $img;

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

        $this->imgs = array();
        $this->infos = array();

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == 'img')
                {
                    $position = $position + 1;
                    $image = new MathImg($this->xmlpath);
                    $image->loadFromXml($child, $position);
                    $this->imgs[] = $image;
                }
                else if ($child->tagName == 'info')
                {
                    $position = $position + 1;
                    $info = new MathInfo($this->xmlpath);
                    $info->loadFromXml($child, $position);
                    $this->infos[] = $info;
                }
            }
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
        $data->string_id = $this->string_id;
        $data->active = $this->active;
        $data->inline = $this->inline;
        $data->media_type = $this->type;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $elementPositions = array();
        $sibling_id = null;

        if (!empty($this->infos))
        {
            foreach ($this->infos as $key => $info)
            {
                $elementPositions['info-' . $key] = $info->position;
            }
        }

        if (!empty($this->imgs))
        {
            foreach ($this->imgs as $key => $img)
            {
                $elementPositions['img-' . $key] = $img->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(info.\d+)$/", $element) ? true : false):
                    $infoString = explode('-', $element);
                    if (empty($sibling_id))
                    {
                        $info = $this->infos[$infoString[1]];
                        $info->saveIntoDb($info->position, $msmid, $this->compid);
                        $sibling_id = $info->compid;
                    }
                    else
                    {
                        $info = $this->infos[$infoString[1]];
                        $info->saveIntoDb($info->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $info->compid;
                    }
                    break;

                case(preg_match("/^(img.\d+)$/", $element) ? true : false):
                    $imgString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $image = $this->imgs[$imgString[1]];
                        $image->saveIntoDb($image->position, $msmid, $this->compid);
                        $sibling_id = $image->compid;
                    }
                    else
                    {
                        $image = $this->imgs[$imgString[1]];
                        $image->saveIntoDb($image->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $image->compid;
                    }
                    break;
            }
        }
    }

    function loadFromDb($id, $compid)
    {
        global $DB;

        $mediaRecord = $DB->get_record('msm_media', array('id' => $id));

        if (!empty($mediaRecord))
        {
            $this->compid = $compid;
            $this->active = $mediaRecord->active;
            $this->inline = $mediaRecord->inline;
            $this->media_type = $mediaRecord->media_type;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        $this->childs = array();
        $this->infos = array();

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            if ($childtablename == 'msm_img')
            {
                $img = new MathImg();
                $img->loadFromDb($child->unit_id, $child->id);
                $this->childs[] = $img;
            }
            else
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
        $content = '';
        $content .= "<div class='picture'>";

        if (empty($this->infos))
        {
            foreach ($this->childs as $childComponent)
            {
                $content .= $childComponent->displayhtml($this->inline, $isindex);
            }
        }
        else
        {
            $imagehtml = '';
            foreach ($this->childs as $childComponent)
            {
                $imagehtml .= $childComponent->displayhtml($this->inline, $isindex);
            }
            if (!$isindex)
            {
                $content .= "<a id='hottag-" . $this->infos[0]->compid . "' class='hottag' onmouseover='popup(" . $this->infos[0]->compid . ")'>";
                $content .= $imagehtml;
                $content .= "</a>";

                $content .= $this->infos[0]->displayhtml();
            }
        }

        $content .= "</div>";

        return $content;
    }

}

?>
