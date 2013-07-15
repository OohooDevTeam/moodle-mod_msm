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
 * This class represents all the image.mapping XML elements in the legacy document
 * (ie. files in the newXML) and the newly formed XML exported by the editor system
 * and it is called by MathImg class. ImgArea class inherits from the abstract class
 * Element and for all the methods inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class ImgArea extends Element
{

    public $id;                             // database ID associated with this image.mapping element in msm_image_mapping
    public $compid;                         // database ID associated with this image.mapping element in msm_compositor
    public $position;                       // integer that keeps track of order of elements
    public $shape;                          // shape of the image map
    public $coords;                         // coordinates of the points to make the shape defined above
    public $infos = array();                // MathInfo objects associated with one of the  coordinates in image map (content of this appears as popup)
    public $companions = array();           // Companion objects associated with one of the coordinates in image map (popup + reference material)
    public $external_refs = array();        // TODO: not processed yet
    public $crossrefs = array();            // Crossref objects assocaited with one of the  coordinates in image map(popup + reference material)
    public $cites = array();                // Cite objects assocaited with this image map
    public $exteranl_links = array();       // ExternalLink object associated with one of the  coordinates in image map

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_image_mapping';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (image.mapping element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        image.mapping elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \ImgArea
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->shape = $DomElement->getAttribute('shape');
        $this->coords = $DomElement->getAttribute('coords');

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

    /**
     * This method saves the extracted information from the XML files of image.mapping element into
     * msm_image_mapping database table.  It calls saveInfoDb method for MathInfo, Companion,
     * CrossRef, Cite, and ExternalLink classes.
     * 
     * @global moodle_databse $DB
     * @param int $position              integer that keeps track of order if elements
     * @param int $msmid                 MSM instance ID
     * @param int $parentid              ID of the parent element from msm_compositor
     * @param int $siblingid             ID of the previous sibling element from msm_compositor
     */
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

    /**
     * This method is used to retrieve all relevant data linked with the image.mapping element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from MathInfo, Companion, Crossref,
     * Cite,and ExternalLink(other then mathinfo, TODO: implement the rest if needed! --> legacy material do not have examples of ones with reference material) classes are also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                   ID of the current associate element from msm_image_mapping
     * @param int $compid               ID of the current associate element from msm_compositor
     * @return \ImgArea
     */
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

    /**
     * This method produces an HTML code from retrieved data from above and creates a 
     * image map that shows popup for each coordinates with contents from MathInfo objects.
     * 
     * @return string
     */
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
