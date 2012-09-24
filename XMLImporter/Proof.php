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
 * Description of Proof
 *
 * @author User
 */
class Proof extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_proof';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->proof_blocks = array();

        $this->string_id = $DomElement->getAttribute('proofid');

        $this->proof_type = $DomElement->getAttribute('type');

        $proof_blocks = $DomElement->getElementsByTagName('proof.block');

        foreach ($proof_blocks as $key => $pb)
        {
            $position = $position + 1;
            $proof_block = new ProofBlock($this->xmlpath);
            $proof_block->loadFromXml($pb, $position);
            $this->proof_blocks[] = $proof_block;
        }
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();

        $sibling_id = $siblingid;

        $data->string_id = $this->string_id;
        $data->proof_type = $this->proof_type;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);

        foreach ($this->proof_blocks as $proof_block)
        {
//            print_object($proof_block);

            if (empty($sibling_id))
            {
                $proof_block->saveIntoDb($proof_block->position, $this->compid);
                $sibling_id = $proof_block->compid;
            }
            else
            {
                $proof_block->saveIntoDb($proof_block->position, $this->compid, $sibling_id);
                $sibling_id = $proof_block->compid;
            }
        }
    }

    function loadFromDb($id, $compid)
    {
        global $DB;

        $proofRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($proofRecord))
        {
            $this->compid = $compid;
            $this->proof_type = $proofRecord->proof_type;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        $this->childs = array();

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            if ($childtablename == 'msm_proof_block')
            {
                $proofblock = new ProofBlock();
                $proofblock->loadFromDb($child->unit_id, $child->id);
                $this->childs[] = $proofblock;
            }
        }

        return $this;
    }

    function displayhtml()
    {
        $content = '';

        $content .= "<li class='proofminibutton' id='proofminibutton-" . $this->compid . "' onclick='showRightpage(" . $this->compid . ")'>";
        $content .= "<span style='cursor:pointer'>";
        if (!empty($this->proof_type))
        {
            $content .= trim($this->proof_type);
        }
        else
        {
            $content .= "Proof";
        }
        $content .= "</span>";
        $content .= "</li>";

        $content .= "<div class='proof' id='proof-" . $this->compid . "' style='display:none;'>";

        foreach ($this->childs as $childComponent)
        {
            $content .= $childComponent->displayhtml();
        }
        $content .= "</div>";

        return $content;
    }

}

?>
