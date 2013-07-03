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

        foreach ($blocks as $key => $b)
        {
            $position = $position + 1;
            $block = new Block($this->xmlpath);
            // to make sure that the first block caption goes to intro caption instead of going to the block
            if ($key == 0)
            {
                $block->loadFromXml($b, $position, "intro-$key");
            }
            else
            {
                $block->loadFromXml($b, $position);
            }
            $this->blocks[] = $block;
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
        $data->string_id = $this->string_id;
        $data->intro_caption = $this->caption;

        $this->id = $DB->insert_record($this->tablename, $data);

        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        $sibling_id = 0;
        foreach ($this->blocks as $key => $block)
        {
            $block->saveIntoDb($position, $msmid, $this->compid, $sibling_id);
            $sibling_id = $block->compid;
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

                // these conditional statements are to accomodate for the difference in db structure between legacy material import and the newly
                // exported materials --> only a temporary solution till the legacy material importer is fixed
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
