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
                            $comment = new Comment($this->xmlpath);
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

        if (!empty($this->math_diplays))
        {
            foreach ($this->math_diplays as $key => $mathdisplay)
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
            }
        }
    }

}

?>
