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
 * Description of Block
 *
 * @author User
 */
class Block extends Element
{

    public $position;
    public $root;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
    }

    function loadFromXml($DomElement, $position = '')
    {
        global $DB;

        $this->position = $position;

        $this->defs = array();
        $this->theorems = array();
        $this->comments = array();

        $this->paras = array();
        $this->uls = array();
        $this->ols = array();
        $this->math_displays = array();
        $this->math_arrays = array();
        $this->tables = array();
        $this->medias = array();

        $this->caption = $this->getDomAttribute($DomElement->getElementsByTagName('caption'));

        $blockbody = $DomElement->getElementsByTagName('block.body');

        foreach ($blockbody as $b)
        {
            foreach ($b->childNodes as $key => $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    $name = $child->tagName;

                    switch ($name)
                    {
                        case('para'):
                            $position = $position + 1;
                            $para = new Para($this->xmlpath);
                            $para->loadFromXml($child, $position);
                            $this->paras[] = $para;
                            break;

                        case('ol'):
                            $position = $position + 1;
                            $ol = new InContent($this->xmlpath);
                            $ol->loadFromXml($child, $position);
                            $this->ols[] = $ol;
                            break;

                        case('ul'):
                            $position = $position + 1;
                            $ul = new InContent($this->xmlpath);
                            $ul->loadFromXml($child, $position);
                            $this->uls[] = $ul;
                            break;

                        case('math.display'):
                            $position = $position + 1;
                            $mathdisplay = new InContent($this->xmlpath);
                            $mathdisplay->loadFromXml($child, $position);
                            $this->math_displays[] = $mathdisplay;
                            break;

                        case('math.array'):
                            $position = $position + 1;
                            $matharray = new MathArray($this->xmlpath);
                            $matharray->loadFromXml($child, $position);
                            $this->math_arrays[] = $matharray;
                            break;

                        case('table'):
                            $position = $position + 1;
                            $table = new Table($this->xmlpath);
                            $table->loadFromXml($child, $position);
                            $this->tables[] = $table;
                            break;

                        case('xi:include'):
                            $position = $position + 1;
                            $href = $child->getAttribute('href');

                            $xiParser = new DOMDocument();
                            @$xiParser->load($this->xmlpath . '/' . $href);

                            $xiElement = $xiParser->documentElement;

                            if ($xiElement->tagName == 'theorem')
                            {
                                $theoremID = $child->getAttribute('id');
                                $position = $position + 1;
                                $theorem = new Theorem(dirname($this->xmlpath . '/' . $href));
                                $theorem->loadFromXml($xiElement, $position);
                                $this->theorems[] = $theorem;
                            }
                            else
                            {
                                echo "missing tagName in xi:include in block.body";
                                echo $xiElement->tagName;
                            }
                            break;

                        case('def'):
                            $defID = $child->getAttribute('id');
                            $position = $position + 1;
                            $def = new Definition($this->xmlpath);
                            $def->loadFromXml($child, $position);
                            $this->defs[] = $def;
                            break;

                        case('comment'):
                            $commentID = $child->getAttribute('id');
                            $position = $position + 1;
                            $comment = new MathComment($this->xmlpath);
                            $comment->loadFromXml($child, $position);
                            $this->comments[] = $comment;
                            break;

                        case('media'):
                            $position = $position + 1;
                            $media = new Media($this->xmlpath);
                            $media->loadFromXml($child, $position);
                            $this->medias[] = $media;
                            break;
                    }
                }
            }
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

        $elementPositions = array();
        $sibling_id = $siblingid;


        if (!empty($this->defs))
        {
            foreach ($this->defs as $key => $def)
            {
                $elementPositions['def' . '-' . $key] = $def->position;
            }
        }

        if (!empty($this->theorems))
        {
            foreach ($this->theorems as $key => $theorem)
            {
                $elementPositions['theorem' . '-' . $key] = $theorem->position;
            }
        }

        if (!empty($this->comments))
        {
            foreach ($this->comments as $key => $comment)
            {
                $elementPositions['comment' . '-' . $key] = $comment->position;
            }
        }

        if (!empty($this->medias))
        {
            foreach ($this->medias as $key => $media)
            {
                $elementPositions['media' . '-' . $key] = $media->position;
            }
        }

        if (!empty($this->paras))
        {
            foreach ($this->paras as $key => $para)
            {
                $elementPositions['para' . '-' . $key] = $para->position;
            }
        }

        if (!empty($this->ols))
        {
            foreach ($this->ols as $key => $ol)
            {
                $elementPositions['ol' . '-' . $key] = $ol->position;
            }
        }

        if (!empty($this->uls))
        {
            foreach ($this->uls as $key => $ul)
            {
                $elementPositions['ul' . '-' . $key] = $ul->position;
            }
        }

        if (!empty($this->math_displays))
        {
            foreach ($this->math_displays as $key => $mathdisplay)
            {
                $elementPositions['mathdisplay' . '-' . $key] = $mathdisplay->position;
            }
        }

        if (!empty($this->math_arrays))
        {
            foreach ($this->math_arrays as $key => $matharray)
            {
                $elementPositions['matharray' . '-' . $key] = $matharray->position;
            }
        }

        if (!empty($this->tables))
        {
            foreach ($this->tables as $key => $table)
            {
                $elementPositions['table' . '-' . $key] = $table->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(def.\d+)$/", $element) ? true : false):
                    $defString = split('-', $element);

                    if (is_object($this->defs[$defString[1]]))
                    {
                        if (!empty($this->defs[$defString[1]]->string_id))
                        {
                            $defRecord = $this->checkForRecord($this->defs[$defString[1]]);
//                            echo "string_id def";
//                            print_object($this->defs[$defString[1]]);
//                            print_object($defRecord);
                        }
                        else
                        {
                            $defRecord = $this->checkForRecord($this->defs[$defString[1]], 'caption');
                        }

                        if (empty($defRecord))
                        {
                            if (empty($sibling_id))
                            {
                                $def = $this->defs[$defString[1]];
                                $def->saveIntoDb($def->position, $parentid);
                                $sibling_id = $def->compid;
                            }
                            else
                            {
                                $def = $this->defs[$defString[1]];
                                $def->saveIntoDb($def->position, $parentid, $sibling_id);
                                $sibling_id = $def->compid;
                            }
                        }
                        else
                        {
                            $defID = $defRecord->id;
                            $deftableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_def'))->id;

                            $defCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $defID, 'table_id' => $deftableID));
                            $defCompID = $this->insertToCompositor($defID, 'msm_def', $parentid, $sibling_id);
                            $sibling_id = $defCompID;

                            foreach ($defCompRecords as $defCompRecord)
                            {
                                $this->grabSubunitChilds($defCompRecord, $defCompID);
                            }
                        }
                    }
                    break;

                case(preg_match("/^(theorem.\d+)$/", $element) ? true : false):
                    $theoremString = split('-', $element);
                    if (is_object($this->theorems[$theoremString[1]]))
                    {
                        $theoremRecord = $this->checkForRecord($this->theorems[$theoremString[1]]);

                        if (empty($theoremRecord))
                        {
                            if (empty($sibling_id))
                            {
                                $theorem = $this->theorems[$theoremString[1]];
                                $theorem->saveIntoDb($theorem->position, $parentid);
                                $sibling_id = $theorem->compid;
                            }
                            else
                            {
                                $theorem = $this->theorems[$theoremString[1]];
                                $theorem->saveIntoDb($theorem->position, $parentid, $sibling_id);
                                $sibling_id = $theorem->compid;
                            }
                        }
                        else
                        {
                            $theoremID = $theoremRecord->id;
                            $theoremtableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_theorem'))->id;

                            $theoremCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $theoremID, 'table_id' => $theoremtableID));
                            $theoremCompID = $this->insertToCompositor($theoremID, 'msm_theorem', $parentid, $sibling_id);
                            $sibling_id = $theoremCompID;

                            $parenttableid = $DB->get_record('msm_compositor', array('id' => $parentid))->table_id;
                            $parentElement = $DB->get_record('msm_table_collection', array('id' => $parenttableid))->tablename;

                            // if parent is subordinate, this theorem is a reference material that cannot have associates..etc to prevent self-references
                            if (($parentElement == 'msm_subordinate') || ($parentElement == 'msm_associate'))
                            {
                                foreach ($theoremCompRecords as $theoremCompRecord)
                                {
                                    $this->grabSubunitChilds($theoremCompRecord, $theoremCompID);
                                }
                            }
                            // if parent is not a subordinate/associate, this is a theorem element that needs to have associate...etc 
                            // Therefore, it cannot use grabSubunitChilds because the other theorem existing in the db might be a reference material which
                            // will not have any associate elements.  
                            else
                            {
                                if (empty($sibling_id))
                                {
                                    $theorem = $this->theorems[$theoremString[1]];
                                    $theorem->saveIntoDb($theorem->position, $parentid, '', $theoremCompID);
                                    $sibling_id = $theorem->compid;
                                }
                                else
                                {
                                    $theorem = $this->theorems[$theoremString[1]];
                                    $theorem->saveIntoDb($theorem->position, $parentid, $sibling_id, $theoremCompID);
                                    $sibling_id = $theorem->compid;
                                }
                            }
                        }
                    }
                    break;

                case(preg_match("/^(comment.\d+)$/", $element) ? true : false):
                    $commentString = split('-', $element);

                    if (is_object($this->comments[$commentString[1]]))
                    {
                        if (!empty($this->comments[$commentString[1]]->string_id))
                        {
                            $commentRecord = $this->checkForRecord($this->comments[$commentString[1]]);
                        }
                        else
                        {
                            $commentRecord = $this->checkForRecord($this->comments[$commentString[1]], 'caption');
                        }

                        if (empty($commentRecord))
                        {
                            if (empty($sibling_id))
                            {
                                $comment = $this->comments[$commentString[1]];
                                $comment->saveIntoDb($comment->position, $parentid);
                                $sibling_id = $comment->compid;
                            }
                            else
                            {
                                $comment = $this->comments[$commentString[1]];
                                $comment->saveIntoDb($comment->position, $parentid, $sibling_id);
                                $sibling_id = $comment->compid;
                            }
                        }
                        else
                        {
                            $commentID = $commentRecord->id;
                            $commenttableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_comment'))->id;

                            $commentCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $commentID, 'table_id' => $commenttableID));
                            $commentCompID = $this->insertToCompositor($commentID, 'msm_comment', $parentid, $sibling_id);
                            $sibling_id = $commentCompID;

                            foreach ($commentCompRecords as $commentCompRecord)
                            {
                                $this->grabSubunitChilds($commentCompRecord, $commentCompID);
                            }
                        }
                    }
                    break;

                case(preg_match("/^(media.\d+)$/", $element) ? true : false):
                    $mediaString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $media = $this->medias[$mediaString[1]];
                        $media->saveIntoDb($media->position, $parentid);
                        $sibling_id = $media->compid;
                    }
                    else
                    {
                        $media = $this->medias[$mediaString[1]];
                        $media->saveIntoDb($media->position, $parentid, $sibling_id);
                        $sibling_id = $media->compid;
                    }
                    break;

                case(preg_match("/^(para.\d+)$/", $element) ? true : false):
                    $paraString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $para = $this->paras[$paraString[1]];
                        $para->saveIntoDb($para->position, $parentid);
                        $sibling_id = $para->compid;
                    }
                    else
                    {
                        $para = $this->paras[$paraString[1]];
                        $para->saveIntoDb($para->position, $parentid, $sibling_id);
                        $sibling_id = $para->compid;
                    }
                    break;

                case(preg_match("/^(ol.\d+)$/", $element) ? true : false):
                    $olString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $ol = $this->ols[$olString[1]];
                        $ol->saveIntoDb($ol->position, $parentid);
                        $sibling_id = $ol->compid;
                    }
                    else
                    {
                        $ol = $this->ols[$olString[1]];
                        $ol->saveIntoDb($ol->position, $parentid, $sibling_id);
                        $sibling_id = $ol->compid;
                    }
                    break;

                case(preg_match("/^(ul.\d+)$/", $element) ? true : false):
                    $ulString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $ul = $this->uls[$ulString[1]];
                        $ul->saveIntoDb($ul->position, $parentid);
                        $sibling_id = $ul->compid;
                    }
                    else
                    {
                        $ul = $this->uls[$ulString[1]];
                        $ul->saveIntoDb($ul->position, $parentid, $sibling_id);
                        $sibling_id = $ul->compid;
                    }
                    break;

                case(preg_match("/^(mathdisplay.\d+)$/", $element) ? true : false):
                    $mathdisplayString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $mathdisplay = $this->math_displays[$mathdisplayString[1]];
                        $mathdisplay->saveIntoDb($mathdisplay->position, $parentid);
                        $sibling_id = $mathdisplay->compid;
                    }
                    else
                    {
                        $mathdisplay = $this->math_displays[$mathdisplayString[1]];
                        $mathdisplay->saveIntoDb($mathdisplay->position, $parentid, $sibling_id);
                        $sibling_id = $mathdisplay->compid;
                    }
                    break;

                case(preg_match("/^(matharray.\d+)$/", $element) ? true : false):
                    $matharrayString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $matharray = $this->math_arrays[$matharrayString[1]];
                        $matharray->saveIntoDb($matharray->position, $parentid);
                        $sibling_id = $matharray->compid;
                    }
                    else
                    {
                        $matharray = $this->math_arrays[$matharrayString[1]];
                        $matharray->saveIntoDb($matharray->position, $parentid, $sibling_id);
                        $sibling_id = $matharray->compid;
                    }
                    break;
                case(preg_match("/^(table.\d+)$/", $element) ? true : false):
                    $tableString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $table = $this->tables[$tableString[1]];
                        $table->saveIntoDb($table->position, $parentid);
                        $sibling_id = $table->compid;
                    }
                    else
                    {
                        $table = $this->tables[$tableString[1]];
                        $table->saveIntoDb($table->position, $parentid, $sibling_id);
                        $sibling_id = $table->compid;
                    }
                    break;
            }
            // to order the blocks properly in unit
            $this->root = $sibling_id;
        }
    }

    function loadFromDb($id, $compid)
    {
        global $DB;

        $this->childs = array();

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            switch ($childtablename)
            {

                case('msm_para'):
                    $para = new Para();
                    $para->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $para;
                    break;

                case('msm_content'):
                    $incontent = new InContent();
                    $incontent->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $incontent;
                    break;

                case('msm_math_array'):
                    $matharray = new MathArray();
                    $matharray->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $matharray;
                    break;

                case('msm_media'):
                    $media = new Media();
                    $media->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $media;
                    break;

                case('msm_comment'):
                    $comment = new MathComment();
                    $comment->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $comment;
                    break;
//               
                case('msm_table'):
                    $table = new Table();
                    $table->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $table;
                    break;
            }
        }

        return $this;
    }

    function displayhtml()
    {
        $content = '';  
        
        foreach ($this->childs as $child)
        {
            $content .= $child->displayhtml();
        }
        
        return $content;
    }

}

?>
