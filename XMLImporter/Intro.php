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
 * Description of Intro
 *
 * @author User
 */
class Intro extends Element
{

    public $blocks = array();

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_intro';
    }

    /**
     *
     * @param DOMElement $DomElement 
     * @param int $position
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->string_id = $DomElement->getAttribute('id');
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $blocks = $DomElement->getElementsByTagName('block');

        $this->blocks = array();

        foreach ($blocks as $b)
        {
            $position = $position + 1;
            $block = new Block($this->xmlpath);
            $block->loadFromXml($b, $position);
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
        $data->string_id = $this->string_id;
        $data->intro_caption = $this->caption;

        $this->id = $DB->insert_record($this->tablename, $data);

        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);


        $elementPosition = array();
        foreach ($this->blocks as $key => $block)
        {
            $elementPosition['block' . '-' . $key] = $block->position;
        }

        asort($elementPosition);

        $sibling_id = 0;
        foreach ($elementPosition as $element => $value)
        {
            $blockString = explode('-', $element);

            $this->blocks[$blockString[1]]->saveIntoDb($this->blocks[$blockString[1]]->position, $msmid, $this->compid, $sibling_id);
            $sibling_id = $this->blocks[$blockString[1]]->compid;
        }
    }

    function loadFromDb($id, $compid)
    {
        global $DB;

        $introrecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($introrecord))
        {
            $this->compid = $compid;
            $this->id = $introrecord->id;
            $this->caption = $introrecord->intro_caption;

            $childElements = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');

            foreach ($childElements as $child)
            {
                $childTable = $DB->get_record("msm_table_collection", array('id' => $child->table_id));

                if ($childTable->tablename == 'msm_block')
                {
                    $block = new Block();
                    $block->loadFromDb($child->unit_id, $child->id); //this should be compositor id
                    $this->blocks[] = $block;
                }
                else
                {
                    $block = new Block();
                    $block->loadFromDb('', $child->id); //this should be compositor id
                    $this->blocks[] = $block;
                }
            }

            if (empty($childElements))
            {
                $block = new Block();
                $block->loadFromDb('', $compid); //this should be compositor id
                $this->blocks[] = $block;
            }
        }
        
        return $this;
    }

    function displayhtml($isindex = false)
    {
        $content = '';

        if (!empty($this->caption))
        {
            $content .= "<h3>$this->caption</h3>";
        }

        foreach ($this->blocks as $key => $block)
        {
            if ($key == 0)
            {
                $content .= $block->displayhtml($isindex, true);
            }
            else
            {
                $content .= $block->displayhtml($isindex);
            }
        }


        return $content;
    }

}

?>
