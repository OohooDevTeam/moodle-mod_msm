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
 * Description of Pilot
 *
 * @author User
 */
class Pilot extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_pilot';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $doc = new DOMDocument();
        $pilot_step = $DomElement->getElementsByTagName('pilot.step')->item(0);

        if (!empty($pilot_step))
        {
            $element = $doc->importNode($pilot_step, true);
            $this->pilot_content .= $doc->saveXML($element);
        }

        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->subordinates = array();
        $this->tables = array();
        $this->medias = array();

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
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position
     * @param int $parentid
     * @param int $siblingid 
     */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();

        if (!empty($this->pilot_content))
        {
            $data->pilot_content = $this->pilot_content;

            $this->id = $DB->insert_record($this->tablename, $data, true, true);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        }
        else
        {
            $this->id = $DB->insert_record($this->tablename, $data, true, true);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        }
    }

}

?>
