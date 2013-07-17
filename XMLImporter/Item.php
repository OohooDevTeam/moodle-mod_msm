<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                       **
 * @subpackage  msm                                                       **
 * @name        msm                                                       **
 * @copyright   University of Alberta                                     **
 * @link        http://ualberta.ca                                        **
 * @author      Ga Young Kim                                              **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************* */

/**
 * his class represents al the item XML elements in the legacy document
 * (ie. files in the newXML) and it is called by Cite class.
 * Item class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Item extends Element
{

    public $id;                             // database ID of current item element in msm_item table
    public $compid;                         // database ID of current item element in msm_compositor table
    public $position;                       // integer that keeps track of order of elements
    public $citekey;                        // key element associated with cite item element
    public $content = array();              // content elements associated with this item element
    public $indexauthors = array();         // MathIndex objects associated with this item element (about authors in this case)
    public $indexglossarys = array();       // MathIndex objects associated with this item element (about glossarys in this case)
    public $indexsymbols = array();         // MathIndex objects associated with this item element (about symbols in this case)
    public $subordinates = array();         // Subordinate objects associated with this item element
    public $medias = array();               // Media objects associated with this item element

    /**
     * constructor for this class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_item';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (item element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param moodle_database $DomElement           item element
     * @param int $position                         integer that keeps track of order if elements
     * @return \Item
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $flag = false;
        foreach ($DomElement->childNodes as $key => $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName != 'citekey')
                {
                    if ($flag == 'true')
                    {
                        foreach ($this->processIndexAuthor($child, $position) as $indexauthor)
                        {
                            $this->indexauthors[] = $indexauthor;
                        }

                        foreach ($this->processIndexGlossary($child, $position) as $indexglossary)
                        {
                            $this->indexglossarys[] = $indexglossary;
                        }

                        foreach ($this->processIndexSymbols($child, $position) as $indexsymbol)
                        {
                            $this->indexsymbols[] = $indexsymbol;
                        }
                        foreach ($this->processSubordinate($child, $position) as $subordinate)
                        {
                            $this->subordinates[] = $subordinate;
                        }

                        foreach ($this->processMedia($child, $position) as $media)
                        {
                            $this->medias[] = $media;
                        }

                        foreach ($this->processContent($child, $position) as $content)
                        {
                            $this->content[1] .= $content;
                        }
                    }
                    else
                    {
                        foreach ($this->processIndexAuthor($child, $position) as $indexauthor)
                        {
                            $this->indexauthors[] = $indexauthor;
                        }

                        foreach ($this->processIndexGlossary($child, $position) as $indexglossary)
                        {
                            $this->indexglossarys[] = $indexglossary;
                        }

                        foreach ($this->processIndexSymbols($child, $position) as $indexsymbol)
                        {
                            $this->indexsymbols[] = $indexsymbol;
                        }
                        foreach ($this->processSubordinate($child, $position) as $subordinate)
                        {
                            $this->subordinates[] = $subordinate;
                        }

                        foreach ($this->processMedia($child, $position) as $media)
                        {
                            $this->medias[] = $media;
                        }

                        foreach ($this->processContent($child, $position) as $content)
                        {
                            $this->content[2] .= $content;
                        }
                    }
                }
                if ($child->tagName == 'citekey')
                {
                    $doc = new DOMDocument();
                    $element = $doc->importNode($child, true);
                    $this->citekey = $doc->saveXML($element);
                    $flag = true;
                }
            }
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of item element into
     * msm_item database table.  
     * 
     * @global moodle_database $DB
     * @param int $position              integer that keeps track of order if elements
     * @param int $msmid                 MSM instance ID
     * @param int $parentid              ID of the parent element from msm_compositor
     * @param int $siblingid             ID of the previous sibling element from msm_compositor
     */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();

        $data->citekey = $this->citekey;
        if (!empty($this->content[1]))
        {
            $data->item_content = $this->content[1];
            $data->position = 1;
        }

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        if (!empty($this->content[2]))
        {
            $data->citekey = $this->citekey;
            $data->item_content = $this->content[2];
            $data->position = 2;
            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        }
    }

}

?>
