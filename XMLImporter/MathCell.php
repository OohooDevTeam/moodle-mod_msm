<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MathCell
 *
 * @author User
 */
class MathCell extends Element
{

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_math_cell';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->colspan = $DomElement->getAttribute('colspan');
        $this->halign = $DomElement->getAttribute('halign');
        $this->valign = $DomElement->getAttribute('valign');
        $this->bgcolor = $DomElement->getAttribute('bgcolor');
        $this->fontcolor = $DomElement->getAttribute('fontcolor');

//        $this->infos = array();
        $this->companion = array(); // if the ref already exists inside db table, then store in here as id number
//        $this->packs = array();
//        $this->comments = array();
//        $this->defs = array();
//        $this->theorems = array();
//        $this->subunits = array();

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                $childname = $child->tagName;
                $doc = new DOMDocument;

                switch ($childname)
                {
                    case('math'):
                        foreach ($this->processContent($child, $position) as $content)
                        {
                            $this->content .= $content;
                        }
                        break;

                    case('text'):
                        $element = $doc->importNode($child, true);
                        $this->content .= $doc->saveXML($element);
                        break;

                    case('companion'):
                        $position++;
                        $companion = new Companion();
                        $companion->loadFromXml($child, $position);
                        $this->companion[] = $companion;
                }
            }
        }
    }

    function saveIntoDb($position, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();
        $data->colspan = $this->colspan;
        $data->halign = $this->halign;
        $data->valign = $this->valign;
        $data->bgcolor = $this->bgcolor;
        $data->fontcolor = $this->fontcolor;
        $data->content = $this->content;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $parentid, $siblingid);
        
        $elementPositions = array();
        $sibling_id = null;

        if (!empty($this->companion))
        {
            foreach ($this->companion as $key => $companion)
            {
                $elementPositions['companion' . '-' . $key] = $companion->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(companion.\d+)$/", $element) ? true : false):
                    $companionString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $companion = $this->companion[$companionString[1]];
                        $companion->saveIntoDb($companion->position, $this->compid);
                    }
                    else
                    {
                        $companion = $this->companion[$companionString[1]];
                        $companion->saveIntoDb($companion->position, $this->compid, $sibling_id);
                    }
                    break;
            }
        }
    }

    function loadFromDb($id, $compid)
    {
        global $DB;

        $mathcellRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($mathcellRecord))
        {
            $this->compid = $compid;
            $this->colspan = $mathcellRecord->colspan;
            $this->halign = $mathcellRecord->halign;
            $this->valign = $mathcellRecord->valign;
            $this->bgcolor = $mathcellRecord->bgcolor;
            $this->fontcolor = $mathcellRecord->fontcolor;
            $this->content = $mathcellRecord->content;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');
        $this->refchilds = array();
        $this->childs = array();

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            switch ($chidltablename)
            {
                case('msm_comment'):
                    $comment = new MathComment();
                    $comment->loadFromDb($child->unit_id, $child->id);
                    $this->refchilds[] = $comment;
                    break;
                case('msm_def'):
                    $def = new Definition();
                    $def->loadFromDb($child->unit_id, $child->id);
                    $this->refchilds[] = $def;
                    break;
                case('msm_theorem'):
                    $theorem = new Theorem();
                    $theorem->loadFromDb($child->unit_id, $child->id);
                    $this->refchilds[] = $theorem;
                    break;

                // depending on what to do with showme/quiz, it maybe included here or not
                case('msm_unit'):
                    $unit = new Unit();
                    $unit->loadFromDb($child->unit_id, $child->id);
                    $this->refchilds[] = $unit;
                    break;

                case('msm_info'):
                    $info = new MathInfo();
                    $info->loadFromDb($child->unit_id, $child->id);
                    $this->childs[$this->content] = $info;
            }
        }

        return $this;
    }

    function displayhtml($rowspan)
    {
        $content = '';

        $content .= "<td class='matharraycell' colspan='" . $this->colspan . "' rowspan='" . $rowspan . "' align='" . $this->halign . "' valign='" . $this->valign . "'>";

        // if info exists then need to set up the dialog popup window, otherwise, just show the content
        if (empty($this->childs))
        {
            $content .= $this->content;
        }
        else
        {
            $content .= "<a id='hottag-" . $this->compid . "' class='hottag' onmouseover='popup(" . $this->compid . ")'>";
            $content .= $this->content;
            $content .= "</a>";

            $content .= '<div id="dialog-' . $this->compid . '" class="dialogs" title="' . $this->childs[$this->content]->caption . '">';
            $content .= $this->childs[$this->content]->info_content;
            $content .= "</div>";
        }

        $content .= "</td>";

        return $content;
    }

}

?>
