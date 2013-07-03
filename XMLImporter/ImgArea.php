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
 * @author      Ga Young Kim                                             
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * *************************************************************************
 * ************************************************************************ */

/**
 * Description of ImageMapping
 *
 * @author User
 */
class ImgArea extends Element
{

    public $position;
    public $shape;
    public $coords;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_image_mapping';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->shape = $DomElement->getAttribute('shape');
        $this->coords = $DomElement->getAttribute('coords');

        $this->infos = array();
        $this->companions = array();
        $this->external_refs = array();
        $this->crossrefs = array();
        $this->cites = array();
        $this->external_links = array();

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                $childname = $child->tagName;

                switch ($childname)
                {
                    case('info'):
                        $position++;
                        $info = new MathInfo($this->xmlpath);
                        $info->loadFromXml($child, $position);
                        $this->infos[] = $info;
                        break;

                    case('companion'):
                        $position++;
                        $companion = new Companion($this->xmlpath);
                        $companion->loadFromXml($child, $position);
                        $this->companions[] = $companion;
                        break;

                    // external ref has the same children as crossref
                    case('crossref'):
                        $position++;
                        $crossref = new Crossref($this->xmlpath);
                        $crossref->loadFromXml($child, $position);
                        $this->crossrefs[] = $crossref;
                        break;

                    case('cite'):
                        $position = $position + 1;
                        $cite = new Cite($this->xmlpath);
                        $cite->loadFromXml($child, $position);
                        $this->cites[] = $cite;
                        break;
                    case('external.link'):
                        $position = $position + 1;
                        $link = new ExternalLink($this->xmlpath);
                        $link->loadFromXml($child, $position);
                        $this->external_links[] = $link;
                        break;
                }
            }
        }
         return $this;
    }

    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;

        $data = new stdClass();
        $data->shape = $this->shape;
        $data->coordinates = $this->coords;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $elementPositions = array();
        $sibling_id = null;


        if (!empty($this->infos))
        {
            foreach ($this->infos as $key => $info)
            {
                $elementPositions['info' . '-' . $key] = $info->position;
            }
        }

        if (!empty($this->companions))
        {
            foreach ($this->companions as $key => $companion)
            {
                $elementPositions['companion' . '-' . $key] = $companion->position;
            }
        }

        if (!empty($this->crossrefs))
        {
            foreach ($this->crossrefs as $key => $crossref)
            {
                $elementPositions['crossref' . '-' . $key] = $crossref->position;
            }
        }

        if (!empty($this->external_refs))
        {
            foreach ($this->external_refs as $key => $external_ref)
            {
                $elementPositions['externalref' . '-' . $key] = $external_ref->position;
            }
        }

        if (!empty($this->external_links))
        {
            foreach ($this->external_links as $key => $external_link)
            {
                $elementPositions['externallink' . '-' . $key] = $external_link->position;
            }
        }

        if (!empty($this->cites))
        {
            foreach ($this->cites as $key => $cite)
            {
                $elementPositions['cite' . '-' . $key] = $cite->position;
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

                case(preg_match("/^(companion.\d+)$/", $element) ? true : false):
                    $companionString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $companion = $this->companions[$companionString[1]];
                        $companion->saveIntoDb($companion->position, $msmid, $this->compid);
                    }
                    else
                    {
                        $companion = $this->companions[$companionString[1]];
                        $companion->saveIntoDb($companion->position, $msmid, $this->compid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(crossref.\d+)$/", $element) ? true : false):
                    $crossrefString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $crossref = $this->crossrefs[$crossrefString[1]];
                        $crossref->saveIntoDb($crossref->position, $msmid, $this->compid);
                    }
                    else
                    {
                        $crossref = $this->crossrefs[$crossrefString[1]];
                        $crossref->saveIntoDb($crossref->position, $msmid, $this->compid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(externalref.\d+)$/", $element) ? true : false):
                    $externalrefString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $externalref = $this->external_refs[$externalrefString[1]];
                        $externalref->saveIntoDb($externalref->position, $msmid, $this->compid);
                    }
                    else
                    {
                        $externalref = $this->external_refs[$externalrefString[1]];
                        $externalref->saveIntoDb($externalref->position, $msmid, $this->compid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(externallink.\d+)$/", $element) ? true : false):
                    $externallinkString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $externallink = $this->external_links[$externallinkString[1]];
                        $externallink->saveIntoDb($externallink->position, $msmid, $this->compid);
                    }
                    else
                    {
                        $externallink = $this->external_links[$externallinkString[1]];
                        $externallink->saveIntoDb($externallink->position, $msmid, $this->compid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(cite.\d+)$/", $element) ? true : false):
                    $citeString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $cite = $this->cites[$citeString[1]];
                        $cite->saveIntoDb($cite->position, $msmid, $this->compid);
                    }
                    else
                    {
                        $cite = $this->cites[$citeString[1]];
                        $cite->saveIntoDb($cite->position, $msmid, $this->compid, $sibling_id);
                    }
                    break;
            }
        }
    }

    function loadFromDb($id, $compid)
    {
        global $DB;

        $imgAreaRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($imgAreaRecord))
        {
            $this->shape = $imgAreaRecord->shape;
            $this->coordinates = $imgAreaRecord->coordinates;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        $this->infos = array();

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            switch ($childtablename)
            {
                case('msm_info'):
                    $info = new MathInfo();
                    $info->loadFromDb($child->unit_id, $child->id);
                    $this->infos[] = $info;
                    break;
            }
        }

        return $this;
    }

    function displayhtml()
    {
        $content = '';

        $content .= "<area id='pic-" . $this->infos[0]->compid . "' coords='" . $this->coordinates . "' shape='" . $this->shape . "' href='#' onmouseover='popup(" . $this->infos[0]->compid . ")'>";

        foreach ($this->infos as $info)
        {
            $content .= $info->displayhtml();
        }
        $content .= "</area>";


        return $content;
    }

}

?>
