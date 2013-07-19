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
 * This class represents all the media XML elements in the legacy document
 * (ie. files in the newXML) and the newly formed XML exported by the editor system
 * and it is called by almost all the classes with any content associated with them.
 * The media element can be representing either images or videos that are included
 * in the content.
 * Media class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Media extends Element
{

    public $id;                     // Database ID associated with this media element in msm_media table
    public $compid;                 // Database ID associated with this media element in msm_compositor table
    public $position;               // integer that keeps track of order of elements
    public $string_id;              // unique string given to element that is either user-defined(legacy XML) or same as compid above(new XML)
    public $active;                 // flag indicating if the media element is active (ie. has an image map attched to it) or inactive
    public $inline;                 // flag indicating if the media element is displayed inline with text or in center
    public $type;                   // type of media element (either image/video)
    public $imgs = array();         // MathImg objects associated with this media element
    public $infos = array();        // MathInfo objects associated with this media element

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */
    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_media';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (media element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        media elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \Media
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->string_id = $DomElement->getAttribute('id');
        $this->active = $DomElement->getAttribute('active');
        $this->inline = $DomElement->getAttribute('inline');
        $this->type = $DomElement->getAttribute('type');

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
         return $this;
    }

    /**
     * This method saves the extracted information from the XML files of media element into
     * msm_media database table.  It calls saveInfoDb method for MathImg and MathInfo class.
     * 
     * @global moodle_database $DB
     * @global type $CFG
     * @param int $position              integer that keeps track of order if elements
     * @param int $msmid                 MSM instance ID
     * @param int $parentid              ID of the parent element from msm_compositor
     * @param int $siblingid             ID of the previous sibling element from msm_compositor
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

    /**
     * This method is used to retrieve all relevant data linked with the media element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from MathImg and MathInfo
     * classes are also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current media element in msm_media table
     * @param int $compid                   database ID of the current media element in msm_compositor table
     * @return \Media
     */
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
        
        $mediaCompRecord = $DB->get_record("msm_compositor", array("id"=>$compid));

        $childElements = $DB->get_records('msm_compositor', array("msm_id"=>$mediaCompRecord->msm_id, 'parent_id' => $compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            if ($childtablename == 'msm_img')
            {
                $img = new MathImg();
                $img->loadFromDb($child->unit_id, $child->id);
                $this->imgs[] = $img;
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

    /**
     * This method produces an HTML code to display the retrieved data from method above and
     * also calls the same method in MathImg and MathInfo classes to display the data from these classes.
     * 
     * @param type $isindex                 flag variable to indicate if this method was called by MathIndex object
     * @return string
     */
    function displayhtml($isindex = false)
    {
        $content = '';
        $content .= "<div class='picture'>";

        if (empty($this->infos))
        {
            foreach ($this->imgs as $childComponent)
            {
                $content .= $childComponent->displayhtml($this->inline, $isindex);
            }
        }
        else
        {
            $imagehtml = '';
            foreach ($this->imgs as $childComponent)
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
