<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Block
 *
 * @author User
 */
class Block extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
    }

    function loadFromXml($DomElement, $position = '')
    {
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

                        case('comment'):
                            $position = $position + 1;
                            $comment = new MathComment($this->xmlpath);
                            $comment->loadFromXml($child, $position);
                            $this->comments[] = $comment;
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
    function saveIntoDb($position, $parentid = '')
    {
        global $DB;

        $elementPositions = array();
        $sibling_id = null;


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
                    break;

                case(preg_match("/^(theorem.\d+)$/", $element) ? true : false):
                    $theoremString = split('-', $element);

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
                    break;

                case(preg_match("/^(comment.\d+)$/", $element) ? true : false):
                    $commentString = split('-', $element);

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
        }
    }

    function loadFromDb($introcompid)
    {
        global $DB;

        $this->childs = array();
          
        $childElements = $DB->get_records('msm_compositor', array('parent_id'=>$introcompid), 'prev_sibling_id');
        
        foreach($childElements as $child)
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
//               
//               case('msm_table'):
//                   $table = new Table();
//                   $table->loadFromDb($child->unit_id, $child->id);
//                   $this->childs[] = $table;
//                   break;
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
