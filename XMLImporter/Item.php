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
 * Cite class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Item extends Element
{

    public $id;
    public $compid;
    public $position;
    public $length;
    public $citekey;
    public $content = array();
    public $indexauthors = array();
    public $indexglossarys = array();
    public $indexsymbols = array();
    public $subordinates = array();
    public $medias = array();

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_item';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->content = array();
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
     *
     * @global moodle_database $DB
     * @param int $position 
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
