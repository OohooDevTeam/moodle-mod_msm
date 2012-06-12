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

    function saveIntoDb($position)
    {
        global $DB;

        foreach ($this->defs as $key => $def)
        {
            $def->saveIntoDb($def->position);
        }

        foreach ($this->theorems as $key => $theorem)
        {
            $theorem->saveIntoDb($theorem->position);
        }

        foreach ($this->comments as $key => $comment)
        {
            $comment->saveIntoDb($comment->position);
        }

        foreach ($this->paras as $key => $para)
        {
            $para->saveIntoDb($para->position);
        }

        foreach ($this->ols as $key => $ol)
        {
            $ol->saveIntoDb($ol->position);
        }

        foreach ($this->uls as $key => $ul)
        {
            $ul->saveIntoDb($ul->position);
        }

        foreach ($this->math_displays as $key => $math_display)
        {
            $math_display->saveIntoDb($math_display->position);
        }

        foreach ($this->math_arrays as $key => $math_array)
        {
            $math_array->saveIntoDb($math_array->position);
        }

        foreach ($this->tables as $key => $table)
        {
            $table->saveIntoDb($table->position);
        }
    }

}

?>
