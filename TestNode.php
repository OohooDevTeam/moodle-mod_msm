<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TestNode
 *
 * @author User
 */
class TestNode
{

    function buildTree()
    {
        $rootDocument = new DOMDocument();

        $root = $rootDocument->createElement('one');
        $two = $rootDocument->createElement('two');
        $three = $rootDocument->createElement('three');
        $four = $rootDocument->createElement('four');
        $five = $rootDocument->createElement('five');
        $six = $rootDocument->createElement('six');
        $seven = $rootDocument->createElement('seven');
        $eight = $rootDocument->createElement('eight');
        $nine = $rootDocument->createElement('nine');
        $ten = $rootDocument->createElement('ten');
        $eleven = $rootDocument->createElement('eleven');
        $twelve = $rootDocument->createElement('twleve');

        $three->appendChild($four);
        $three->appendChild($five);
        $three->appendChild($six);
        $two->appendChild($three);

        $nine->appendChild($ten);
        $eight->appendChild($nine);
        $seven->appendChild($eight);

        $eleven->appendChild($twelve);
        
        $root->appendChild($two);
        $root->appendChild($seven);
        $root->appendChild($eleven);
        
        $rootDocument->appendChild($root);
        
       return $rootDocument;
    }
    
    function trasverse($DomElement)
    {
        $stack = array();
        array_push($stack, $DomElement->tagName);
//        if(!($DomElement->hasChildNodes()))
//        {
//            echo "no childnode";
//            array_push($stack, $DomElement->tagName);
//            return $stack;
//        }
//        else
//        {
            echo "childNode";
            foreach($DomElement->childNodes as $child)
            {
                foreach($this->trasverse($child) as $stackElement)
                {
                    array_push($stack, $stackElement);
                }
//                return $stack;
            }
//        }
        return $stack;
    }

}

?>
