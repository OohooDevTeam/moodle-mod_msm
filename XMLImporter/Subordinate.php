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
 * This class represents all the subordinate XML elements in the legacy document
 * (ie. files in the newXML) and the newly formed XML exported by the editor system
 * and it is called by almost all classes with content associated with them.  The Subordinate
 * elements represents the "hot-tagged" terms that can trigger extra information in a form of a 
 * jquery UI dialog window and has an option of having other reference materials linked to it.  When
 * the terms are clicked, the reference material would display on right panel of the view screen.
 * Also it can act same as an anchor element to link to a website along with info about the link
 * given by the popup windows.  Subordinate class inherits from the abstract class Element and for
 * all the methods inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Subordinate extends Element
{

    public $id;                             // database ID associated with the subordinate element in msm_subordinate table
    public $compid;                         // database ID associated with the subordinate element in msm_compositor table
    public $position;                       // integer that keeps track of order of elements
    public $hot;                            // the "hot-tagged" terms that has mouse triggered bound to it to open popups
    public $external_links = array();       // ExternalLink objects associated with the subordinate element
    public $infos = array();                // MathInfo objects associated with the subordinate elements (creates the popups)
    public $companions = array();           // Companion objects associated with the subordinate elements (popup with reference material)
    public $external_refs = array();        // Reference materials(eg. Definiton..etc) linked to the subordinate
    // (in this case, ones that doesn't exist in the same composition as this subordinate)
    public $crossrefs = array();            // Crossref objects associated with the subordinate elements (popup with reference material)
    public $cites = array();                // Cite objects associated with the subordinate elements
    public $childs = array();               // reference materials that the subordinate is associated with --> for load/display from DB

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_subordinate';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (subordinate element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        subordinate elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \Subordinate
     */
    public function loadFromXml($DomElement, $position = '')
    {
        if ($DomElement != null)
        {
            $this->position = $position;

            foreach ($DomElement->childNodes as $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    $childname = $child->tagName;

                    switch ($childname)
                    {
                        case('hot'):
                            $this->hot = $this->getContent($child);
                            break;

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
                        case ("external.ref"):
                            $position++;
                            $crossref = new Crossref($this->xmlpath);
                            $crossref->loadFromXml($child, $position);
                            $this->external_refs[] = $crossref;
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
        }
        
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of subordinate element into
     * msm_subordinate database table.  It calls saveInfoDb method for MathInfo/Companion/
     * Crossref/Cite/ExternalLink classes.
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
        $data->hot = "msm_subordinate_hotword-" . $position . "|| " . $this->hot;

        // checking for duplicate records
        $numOfRecords = $DB->count_records($this->tablename);
        if ($numOfRecords > 0)
        {
            // need the limit to be $numOfRecords+1 to process the last record
            for ($i = 1; $i < $numOfRecords + 1; $i++)
            {
                $string = $DB->get_field($this->tablename, 'hot', array('id' => $i));

                if ($string == $this->hot)
                {
                    $recordID = $i;
                    break;
                }
                else
                {
                    $recordID = false;
                }
            }
        }
        else
        {
            $recordID = false;
        }

        if (empty($recordID))
        {
            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        }
        else //record already exists
        {
            $this->compid = $this->insertToCompositor($recordID, $this->tablename, $msmid, $parentid, $siblingid);
        }

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
     * This method is used to retrieve all relevant data linked with the subordinate element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from MathInfo/ExternalLink/Theorem/
     * Comment/Definition/Pack/Unit classes are also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current subordinate element in msm_subordinate_theorem table
     * @param int $compid                   database ID of the current subordinate element in msm_compositor table
     * @return \Subordinate
     */
    function loadFromDb($id, $compid)
    {
        global $DB;

        $subordinaterecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($subordinaterecord))
        {
            $this->hot = $subordinaterecord->hot;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            switch ($childtablename)
            {
                case 'msm_info':
                    $info = new MathInfo();
                    $info->loadFromDb($child->unit_id, $child->id);
                    $this->infos[] = $info;
                    break;
                case 'msm_external_link':
                    $externallink = new ExternalLink();
                    $externallink->loadFromDb($child->unit_id, $child->id);
                    $this->external_links[] = $externallink;
                    break;

                case 'msm_theorem':
                    $theorem = new Theorem();
                    $theorem->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $theorem;
                    break;
//
                case 'msm_def':
                    $def = new Definition();
                    $def->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $def;
                    break;

                case 'msm_comment':
                    $comment = new MathComment();
                    $comment->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $comment;
                    break;

                case 'msm_unit':
                    $unit = new Unit();
                    $unit->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $unit;
                    break;

                case 'msm_packs':
                    $pack = new Pack();
                    $pack->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $pack;
                    break;
            }
        }

        return $this;
    }

    // display is done by processContent method in Element class
}

?>
