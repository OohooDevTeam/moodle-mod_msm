<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GlossaryNode
 *
 * @author User
 */
class GlossaryNode
{

    public $depth = 0;          //for printing
    public $parentNode = null;  //reference to parent node
    public $text = '';          //display text
    public $children = array(); //children node(s)
    public $infos = array();

    
    public function addChild(GlossaryNode $node)
    {
        $this->children[] = $node;
    }

}

?>
