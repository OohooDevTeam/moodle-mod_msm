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
 * Description of Theorem
 *
 * @author User
 */
class Theorem extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_theorem';
    }

    /**
     *
     * @param DOMElement $DomElement 
     */
    function loadFromXml($DomElement, $position = '', $isRef = 'false')
    {
        $this->position = $position;

        $this->theorem_type = $DomElement->getAttribute('type');
        $this->string_id = $DomElement->getAttribute('id');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));
        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));
        $this->description = $this->getDomAttribute($DomElement->getElementsByTagName('description'));

        $this->statements = array();
        $this->associates = array();
        $this->proofs = array();

        if ($isRef == 'false')
        {
            foreach ($DomElement->childNodes as $key => $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    if ($child->tagName == 'associate')
                    {
                        $position = $position + 1;
                        $associate = new Associate($this->xmlpath);
                        $associate->loadFromXml($child, $position);
                        $this->associates[] = $associate;
                    }
                    else if ($child->tagName == 'statement.theorem')
                    {
                        $position = $position + 1;
                        $statement = new StatementTheorem($this->xmlpath);
                        $statement->loadFromXml($child, $position);
                        $this->statements[] = $statement;
                    }
                    else if ($child->tagName == 'proof')
                    {
                        $position = $position + 1;
                        $proof = new Proof($this->xmlpath);
                        $proof->loadFromXml($child, $position);
                        $this->proofs[] = $proof;
                    }
                }
            }
        }
        else if ($isRef == 'true')
        {
            foreach ($DomElement->childNodes as $key => $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    if ($child->tagName == 'statement.theorem')
                    {
                        $position = $position + 1;
                        $statement = new StatementTheorem($this->xmlpath);
                        $statement->loadFromXml($child, $position);
                        $this->statements[] = $statement;
                    }
                }
            }
        }
    }

    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '', $theoremCompid = '')
    {
        global $DB;

        $sibling_id = $siblingid;

        if (empty($theoremCompid))
        {
            $data = new stdClass();
            $data->theorem_type = $this->theorem_type;
            $data->string_id = $this->string_id;
            $data->caption = $this->caption;
            $data->textcaption = $this->textcaption;
            $data->description = $this->description;

            $this->id = $DB->insert_record($this->tablename, $data, true, true);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        }
        else
        {
            $this->compid = $theoremCompid;
            $this->id = $DB->get_record('msm_compositor', array('id' => $this->compid))->unit_id;
        }

        $elementPosition = array();

        if (!empty($this->statements))
        {
            foreach ($this->statements as $key => $statement)
            {
                $elementPosition['statement' . '-' . $key] = $statement->position;
            }
        }

        if (!empty($this->proofs))
        {
            foreach ($this->proofs as $key => $proof)
            {
                $elementPosition['proof' . '-' . $key] = $proof->position;
            }
        }

        asort($elementPosition);

        foreach ($elementPosition as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(statement.\d+)$/", $element) ? true : false):
                    $statementString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $statement = $this->statements[$statementString[1]];
                        $statement->saveIntoDb($statement->position, $msmid, $this->compid);
                        $sibling_id = $statement->compid;
                    }
                    else
                    {
                        $statement = $this->statements[$statementString[1]];
                        $statement->saveIntoDb($statement->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $statement->compid;
                    }
                    break;

                case(preg_match("/^(proof.\d+)$/", $element) ? true : false):
                    $proofString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $proof = $this->proofs[$proofString[1]];
                        $proof->saveIntoDb($proof->position, $msmid, $this->compid);
                        $sibling_id = $proof->compid;
                    }
                    else
                    {
                        $proof = $this->proofs[$proofString[1]];
                        $proof->saveIntoDb($proof->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $proof->compid;
                    }
                    break;
            }
        }

        $sibling_id = 0;

        foreach ($this->associates as $associate)
        {
            $associate->saveIntoDb($associate->position, $msmid, $this->compid, $sibling_id);
            $sibling_id = $associate->compid;
        }
    }

    function loadFromDb($id, $compid, $indexref = false)
    {
        global $DB;

        $theoremRecord = $DB->get_record('msm_theorem', array('id' => $id));

        if (!empty($theoremRecord))
        {
            $this->compid = $compid;
            $this->theorem_type = $theoremRecord->theorem_type;
            $this->caption = $theoremRecord->caption;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        $this->childs = array();
        $this->proofs = array();
        $this->associates = array();

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            if (!$indexref)
            {
                if ($childtablename == 'msm_associate')
                {
                    $associate = new Associate();
                    $associate->loadFromDb($child->unit_id, $child->id);
                    $this->associates[] = $associate;
                }
                if ($childtablename == 'msm_statement_theorem')
                {
                    $statement = new StatementTheorem();
                    $statement->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $statement;
                }
                if ($childtablename == 'msm_proof')
                {
                    $proof = new Proof();
                    $proof->loadFromDb($child->unit_id, $child->id);
                    $this->proofs[] = $proof;
                }
            }
            else
            {
                if ($childtablename == 'msm_statement_theorem')
                {
                    $statement = new StatementTheorem();
                    $statement->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $statement;
                }
            }
        }

        return $this;
    }

    function displayhtml($isindex = false)
    {
        $content = '';
        $content .= "<br />";
        $content .= "<div class='theorem'>";
        if (!empty($this->caption))
        {
            $content .= "<span class='theoremtitle'>" . $this->caption . "</span>";
        }

        if (!empty($this->theorem_type))
        {
            $content .= "<span class='theoremtype'>" . $this->theorem_type . "</span>";
        }
        $content .= "<br/>";

        $content .= "<div class='mathcontent'>";
        foreach ($this->childs as $child)
        {
            $content .= $child->displayhtml($isindex);
        }
        $content .= "</div>";

        $content .= "<br />";

        if (!$isindex)
        {
            if ((!empty($this->associates)) && (!empty($this->proofs)))
            {
                $content .= "<ul class='minibuttons'>";
                foreach ($this->associates as $associate)
                {
                    $content .= $associate->displayhtml();
                }

                foreach ($this->proofs as $proof)
                {
                    $content .= $proof->displayhtml();
                }

                $content .= "</ul>";
            }
            else if ((!empty($this->associates)) && (empty($this->proofs)))
            {
                $content .= "<ul class='minibuttons'>";
                foreach ($this->associates as $associate)
                {
                    $content .= $associate->displayhtml();
                }
                $content .= "</ul>";
            }
            else if ((empty($this->associates)) && (!empty($this->proofs)))
            {
                $content .= "<ul class='minibuttons'>";
                foreach ($this->proofs as $proof)
                {
                    $content .= $proof->displayhtml();
                }
                $content .= "</ul>";
            }
        }

        $content .= "</div>";
        $content .= "<br />";
//        print_object($content);

        return $content;
    }

}

?>
