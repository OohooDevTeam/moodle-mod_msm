<?php

/**
 * This class is used to create the index tree for MathIndex class objects.
 * This tree is then displayed in view.php
 *
 * @author Ga Young Kim
 */
class GlossaryNode
{

    public $depth = 0;              // depth of the node used for printing out the nodes in order
    public $parents = array();      // reference to parent node
    public $text = '';              // display text associated with this node
    public $children = array();     // children node(s) linked to this node
    public $infos = array();        // MathInfo associated with the MathIndex object represented by this node
    public $compid = 0;             // database ID of the MathIndex object in msm_compositor

    /**
     * This method adds the given node as a child of the current node.
     * 
     * @param GlossaryNode $node        child node to be added
     */
    public function addChild(GlossaryNode $node)
    {
        $this->children[] = $node;
    }

}

?>
