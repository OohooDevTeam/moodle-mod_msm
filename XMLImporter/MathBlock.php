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
 * This class represents all the block and block.body XML elements in the legacy document
 * (ie. files in the newXML) and the newly formed XML exported by the editor system
 * and it is called by ExtraInfo/Intro/Unit classes. Block class inherits from the abstract class Element
 * and for all the methods inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Block extends Element
{

    public $id;                             // Database ID of current block element in msm_block table
    public $compid;                         // Database ID of current block element in msm_compositor table
    public $position;                       // integer that keeps track of order of elements
    public $root;                           // storing previous sibling id to order the blocks properly in unit
    public $title;                          // caption element associated with this block element
    public $defs = array();                 // Definition objects assocaited with this block element
    public $theorems = array();             // Theorem objects assocaited with this block element
    public $comments = array();             // MathComment objects assocaited with this block element
    public $paras = array();                // Para objects assocaited with this block element
    public $uls = array();                  // InContent objects assocaited with this block element (in this case representing ul element)
    public $ols = array();                  // InContent objects assocaited with this block element (in this case representing ol element)
    public $math_displays = array();        // InContent objects assocaited with this block element (in this case representing math.display element)
    public $math_arrays = array();          // MathArray objects assocaited with this block element
    public $tables = array();               // Table objects assocaited with this block element
    public $medias = array();               // Media objects assocaited with this block element

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = "msm_block";
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (block element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        block elements
     * @param int $position                 integer that keeps track of order if elements
     * @param string $flag                  flag for if the block element came from intro element as a parent
     *                                      --> if flag is not an empty string, then caption goes to msm_intro database table 
     * @return \Block
     */
    function loadFromXml($DomElement, $position = '', $flag = '')
    {
        $this->position = $position;

        if (empty($flag))
        {
            $caption = $DomElement->getElementsByTagName("caption");

            if ($caption->length > 0)
            {
                $this->title = $caption->item(0)->textContent;
            }
        }
        else
        {
            $this->title = '';
        }

        $blockBodys = $DomElement->getElementsByTagName("block.body");

        foreach ($blockBodys as $blockBody)
        {
            foreach ($blockBody->childNodes as $key => $child)
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
                            $position = $position + 1;
                            $def = new Definition($this->xmlpath);
                            $def->loadFromXml($child, $position);
                            $this->defs[] = $def;
                            break;

                        case ("theorem"):
                            $position++;
                            $theorem = new Theorem($this->xmlpath);
                            $theorem->loadFromXml($child, $position);
                            $this->theorems[] = $theorem;
                            break;

                        case('comment'):
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
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of block element into
     * msm_block database table.  It calls saveInfoDb method for Para, MathArray, Table, InContent,
     * Media, Definition, Theorem and/or Comment classes.
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

        if ((empty($this->defs)) && (empty($this->theorems)) && (empty($this->comments)))
        {
            $data = new stdClass();
            $data->block_caption = $this->title;

            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        }

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
                    $defString = explode('-', $element);
                    if (is_object($this->defs[$defString[1]]))
                    {
                        $defRecord = null;

                        if (!empty($this->defs[$defString[1]]->string_id))
                        {
                            $defRecord = $this->checkForRecord($msmid, $this->defs[$defString[1]]);
                        }
                        else
                        {
                            $defRecord = $this->checkForRecord($msmid, $this->defs[$defString[1]], 'caption');
                        }

                        if (empty($defRecord))
                        {
                            if (empty($sibling_id))
                            {
                                $def = $this->defs[$defString[1]];
                                $def->saveIntoDb($def->position, $msmid, $parentid);
                                $sibling_id = $def->compid;
                            }
                            else
                            {
                                $def = $this->defs[$defString[1]];
                                $def->saveIntoDb($def->position, $msmid, $parentid, $sibling_id);
                                $sibling_id = $def->compid;
                            }
                        }
                        else
                        {
                            $defID = $defRecord->id;
                            $deftableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_def'))->id;

                            $defCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $defID, 'table_id' => $deftableID));
                            $defCompID = $this->insertToCompositor($defID, 'msm_def', $msmid, $parentid, $sibling_id);
                            $sibling_id = $defCompID;

                            foreach ($defCompRecords as $defCompRecord)
                            {
                                $this->grabSubunitChilds($defCompRecord, $defCompID, $msmid);
                            }
                        }
                    }
                    break;

                case(preg_match("/^(theorem.\d+)$/", $element) ? true : false):
                    $theoremString = explode('-', $element);
                    if (is_object($this->theorems[$theoremString[1]]))
                    {
                        $theoremRecord = $this->checkForRecord($msmid, $this->theorems[$theoremString[1]]);

                        if (empty($theoremRecord))
                        {
                            if (empty($sibling_id))
                            {
                                $theorem = $this->theorems[$theoremString[1]];
                                $theorem->saveIntoDb($theorem->position, $msmid, $parentid);
                                $sibling_id = $theorem->compid;
                            }
                            else
                            {
                                $theorem = $this->theorems[$theoremString[1]];
                                $theorem->saveIntoDb($theorem->position, $msmid, $parentid, $sibling_id);
                                $sibling_id = $theorem->compid;
                            }
                        }
                        else
                        {
                            $theoremID = $theoremRecord->id;
                            $theoremtableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_theorem'))->id;

                            $theoremCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $theoremID, 'table_id' => $theoremtableID));
                            $theoremCompID = $this->insertToCompositor($theoremID, 'msm_theorem', $msmid, $parentid, $sibling_id);
                            $sibling_id = $theoremCompID;

                            $parenttableid = $DB->get_record('msm_compositor', array('id' => $parentid))->table_id;
                            $parentElement = $DB->get_record('msm_table_collection', array('id' => $parenttableid))->tablename;

                            // if parent is subordinate, this theorem is a reference material that cannot have associates..etc to prevent self-references
                            if (($parentElement == 'msm_subordinate') || ($parentElement == 'msm_associate'))
                            {
                                foreach ($theoremCompRecords as $theoremCompRecord)
                                {
                                    $this->grabSubunitChilds($theoremCompRecord, $theoremCompID, $msmid);
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
                                    $theorem->saveIntoDb($theorem->position, $msmid, $parentid, '', $theoremCompID);
                                    $sibling_id = $theorem->compid;
                                }
                                else
                                {
                                    $theorem = $this->theorems[$theoremString[1]];
                                    $theorem->saveIntoDb($theorem->position, $msmid, $parentid, $sibling_id, $theoremCompID);
                                    $sibling_id = $theorem->compid;
                                }
                            }
                        }
                    }
                    break;

                case(preg_match("/^(comment.\d+)$/", $element) ? true : false):
                    $commentString = explode('-', $element);

                    if (is_object($this->comments[$commentString[1]]))
                    {
                        $commentRecord = null;
                        if (!empty($this->comments[$commentString[1]]->string_id))
                        {
                            $commentRecord = $this->checkForRecord($msmid, $this->comments[$commentString[1]]);
                        }
                        else
                        {
                            $commentRecord = $this->checkForRecord($msmid, $this->comments[$commentString[1]], 'caption');
                        }

                        if (empty($commentRecord))
                        {
                            if (empty($sibling_id))
                            {
                                $comment = $this->comments[$commentString[1]];
                                $comment->saveIntoDb($comment->position, $msmid, $parentid);
                                $sibling_id = $comment->compid;
                            }
                            else
                            {
                                $comment = $this->comments[$commentString[1]];
                                $comment->saveIntoDb($comment->position, $msmid, $parentid, $sibling_id);
                                $sibling_id = $comment->compid;
                            }
                        }
                        else
                        {
                            $commentID = $commentRecord->id;
                            $commenttableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_comment'))->id;

                            $commentCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $commentID, 'table_id' => $commenttableID));
                            $commentCompID = $this->insertToCompositor($commentID, 'msm_comment', $msmid, $parentid, $sibling_id);
                            $sibling_id = $commentCompID;

                            foreach ($commentCompRecords as $commentCompRecord)
                            {
                                $this->grabSubunitChilds($commentCompRecord, $commentCompID, $msmid);
                            }
                        }
                    }
                    break;

                case(preg_match("/^(media.\d+)$/", $element) ? true : false):
                    $mediaString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $media = $this->medias[$mediaString[1]];
                        $media->saveIntoDb($media->position, $msmid, $this->compid);
                        $sibling_id = $media->compid;
                    }
                    else
                    {
                        $media = $this->medias[$mediaString[1]];
                        $media->saveIntoDb($media->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $media->compid;
                    }
                    break;

                case(preg_match("/^(para.\d+)$/", $element) ? true : false):
                    $paraString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $para = $this->paras[$paraString[1]];
                        $para->saveIntoDb($para->position, $msmid, $this->compid);
                        $sibling_id = $para->compid;
                    }
                    else
                    {
                        $para = $this->paras[$paraString[1]];
                        $para->saveIntoDb($para->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $para->compid;
                    }
                    break;

                case(preg_match("/^(ol.\d+)$/", $element) ? true : false):
                    $olString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $ol = $this->ols[$olString[1]];
                        $ol->saveIntoDb($ol->position, $msmid, $this->compid);
                        $sibling_id = $ol->compid;
                    }
                    else
                    {
                        $ol = $this->ols[$olString[1]];
                        $ol->saveIntoDb($ol->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $ol->compid;
                    }
                    break;

                case(preg_match("/^(ul.\d+)$/", $element) ? true : false):
                    $ulString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $ul = $this->uls[$ulString[1]];
                        $ul->saveIntoDb($ul->position, $msmid, $this->compid);
                        $sibling_id = $ul->compid;
                    }
                    else
                    {
                        $ul = $this->uls[$ulString[1]];
                        $ul->saveIntoDb($ul->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $ul->compid;
                    }
                    break;

                case(preg_match("/^(mathdisplay.\d+)$/", $element) ? true : false):
                    $mathdisplayString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $mathdisplay = $this->math_displays[$mathdisplayString[1]];
                        $mathdisplay->saveIntoDb($mathdisplay->position, $msmid, $this->compid);
                        $sibling_id = $mathdisplay->compid;
                    }
                    else
                    {
                        $mathdisplay = $this->math_displays[$mathdisplayString[1]];
                        $mathdisplay->saveIntoDb($mathdisplay->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $mathdisplay->compid;
                    }
                    break;

                case(preg_match("/^(matharray.\d+)$/", $element) ? true : false):
                    $matharrayString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $matharray = $this->math_arrays[$matharrayString[1]];
                        $matharray->saveIntoDb($matharray->position, $msmid, $this->compid);
                        $sibling_id = $matharray->compid;
                    }
                    else
                    {
                        $matharray = $this->math_arrays[$matharrayString[1]];
                        $matharray->saveIntoDb($matharray->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $matharray->compid;
                    }
                    break;
                case(preg_match("/^(table.\d+)$/", $element) ? true : false):
                    $tableString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $table = $this->tables[$tableString[1]];
                        $table->saveIntoDb($table->position, $msmid, $this->compid);
                        $sibling_id = $table->compid;
                    }
                    else
                    {
                        $table = $this->tables[$tableString[1]];
                        $table->saveIntoDb($table->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $table->compid;
                    }
                    break;
            }
            // to order the blocks properly in unit
            $this->root = $sibling_id;
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the block element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from Para, MathArray, Table, InContent,
     * and/or Media classes are also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current block element in msm_block table
     * @param int $compid                   database ID of the current block element in msm_compositor table
     * @return \Block
     */
    function loadFromDb($id, $compid)
    {
        global $DB;

        $this->childs = array();

        $blockCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

//        $tableName = $DB->get_record('msm_table_collection', array('id' => $blockCompRecord->table_id))->tablename;
//        if ($tableName == 'msm_block')
//        {
        $this->compid = $blockCompRecord->id;
        $this->id = $blockCompRecord->unit_id;
        $blockRecord = $DB->get_record('msm_block', array('id' => $this->id));

        $this->title = $blockRecord->block_caption;

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');

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
//               
                case('msm_table'):
                    $table = new Table();
                    $table->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $table;
                    break;
            }
        }
//        }     
        return $this;
    }

    /**
     * This method produces an HTML code to display the retrieved data from method above and
     * also calls the same method in Para, MathArray, Table, InContent, Media, Definition,
     * Theorem and/or Comment classes to display the data from these classes.
     * 
     * @param bool $isindex             flag variable to indicate if this method was called by MathIndex object
     * @param type $flag                flag indicating if the caption exists in msm_intro table
     * @return type
     */
    function displayhtml($isindex = false, $flag = false)
    {
        $content = '';

        if (!empty($this->title))
        {
            // first title is same as intro title
            if (!$flag)
            {
                $content .= "<div style='text-align:center;'><h3>$this->title</h3></div>";
            }
        }

        foreach ($this->childs as $child)
        {
            $content .= $child->displayhtml($isindex);
        }

        return $content;
    }

}

?>
