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
 * For inserting data into msm_extra_info table from preface,trailer,summary and/or historical.notes elements.
 *
 * @author User
 */
class ExtraInfo extends Element
{

    public $name;
    public $blocks = array();
    
    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_extra_info';
    }

    /**
     *
     * @param DOMElement $DomElement 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $nameofElement = $DomElement->tagName;
        switch ($nameofElement)
        {
            case ('acknowledgements'):
                $this->name = 'Acknowledgements';
                break;
            case('preface'):
                $this->name = 'Preface';
                break;
            case('trailer'):
                $this->name = 'Trailer';
                break;
            case('summary'):
                $this->name = 'Summary';
                break;
            case('historical.notes'):
                $this->name = 'Historical';
                break;
        }
        
        $blocks = $DomElement->getElementsByTagName("block");
        foreach($blocks as $b)
        {
            $block = new Block();
            $block->loadFromXml($b);
            $this->blocks[] = $block;
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
        $data->extra_info_name = $this->name;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);


        $elementPositions = array();
        $sibling_id = null;

        if (!empty($this->blocks))
        {
            foreach ($this->blocks as $key=>$block)
            {
                $elementPositions["block-$key"] = $block->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(block.\d+)$/", $element) ? true : false):
                    $blockString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $block = $this->blocks[$blockString[1]];
                        $block->saveIntoDb($block->position, $msmid, $this->compid);
                        $sibling_id = $block->compid;
                    }
                    else
                    {
                        $block = $this->blocks[$blockString[1]];
                        $block->saveIntoDb($block->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $block->compid;
                    }
                    break;
            }
        }
    }

    function loadFromDb($id, $compid)
    {
        global $DB;

        $extrainfoRecord = $DB->get_record('msm_extra_info', array('id' => $id));

        if (!empty($extrainfoRecord))
        {
            $this->extra_info_name = $extrainfoRecord->extra_info_name;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        $this->blocks = array();

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            if ($childtablename->tablename == "msm_block")
            {
                $block = new Block();
                $block->loadFromDb($child->unit_id, $child->id);
                $this->blocks[] = $block;
            }

        }

        return $this;
    }

    function displayhtml($isindex = false)
    {
        $content = '';
        $content .= "<div class='extrainfo'>";

        $content .= "<span class='extrainfoname'>" . $this->extra_info_name . "</span>";

        $content .= "<div class='mathcontent'>";
         foreach($this->blocks as $block)
        {
            $content .= $block->displayhtml();
        }
        $content .= "</div>";

        $content .= "</div>";
        return $content;
    }

}

?>
