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
 * This class represents al the pilot XML elements in the legacy document
 * (ie. files in the newXML) and it is called by Step class.
 * Pilot class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Pilot extends Element
{

    public $id;                            // database ID associated with the pilot element in msm_pilot table
    public $compid;                        // database ID associated with the pilot element in msm_compositor table
    public $position;                      // integer that keeps track of order of elements
    public $pilot_content;                 // content elements associated with this pilot elements
    public $indexauthors = array();        // MathIndex associated with the pilot element --> referncing index.author
    public $indexglossarys = array();      // MathIndex associated with the pilot element --> referncing index.glossary
    public $indexsymbols = array();        // MathIndex associated with the pilot element --> referncing index.symbol
    public $subordinates = array();        // Subordinate objects associated with the pilot elements
    public $tables = array();              // Table objects associated with the pilot elements
    public $medias = array();              // Media objects associated with the pilot elements

    /**
     * constructor for this class
     * 
     * @param string $xmlpath           filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_pilot';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (pilot element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        pilot element in XML file
     * @param int $position                 integer that keeps track of order if elements
     */
    function loadFromXml($DomElement, $position = '')
    {
        $doc = new DOMDocument();
        // pilot.step elements exist as a child element of pilot element under answer.ext
        $pilot_step = $DomElement->getElementsByTagName('pilot.step')->item(0);

        if (!empty($pilot_step))
        {
            $element = $doc->importNode($pilot_step, true);
            $this->pilot_content .= $doc->saveXML($element);
        }

        //there are pilot.body child elements for pilot in exercise element under solution.ext elements
        $pilot_bodys = $DomElement->getElementsByTagName('pilot.body');

        if (!empty($pilot_bodys))
        {
            foreach ($pilot_bodys as $pib)
            {
                foreach ($this->processIndexAuthor($pib, $position) as $indexauthor)
                {
                    $this->indexauthors[] = $indexauthor;
                }

                foreach ($this->processIndexGlossary($pib, $position) as $indexglossary)
                {
                    $this->indexglossarys[] = $indexglossary;
                }

                foreach ($this->processIndexSymbols($pib, $position) as $indexsymbol)
                {
                    $this->indexsymbols[] = $indexsymbol;
                }
                foreach ($this->processSubordinate($pib, $position) as $subordinate)
                {
                    $this->subordinates[] = $subordinate;
                }

                foreach ($this->processMedia($pib, $position) as $media)
                {
                    $this->medias[] = $media;
                }

                foreach ($this->processTable($pib, $position) as $table)
                {
                    $this->tables[] = $table;
                }

                foreach ($this->processContent($pib, $position) as $content)
                {
                    $this->pilot_content .= $content;
                }
            }
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of pilot element into
     * msm_pilot database table.
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

        if (!empty($this->pilot_content))
        {
            $data->pilot_content = $this->pilot_content;

            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        }
        else
        {
            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        }
    }

}

?>
