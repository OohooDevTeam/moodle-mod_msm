<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorElement
 *
 * @author User
 */
abstract class EditorElement
{

    abstract function getFormData($idNumber, $position);

    abstract function insertData($parentid, $siblingid, $msmid);

    function processContent($oldContent)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($oldContent);
        
        print_object($oldContent);

        $rootElement = $doc->getElementsByTagName('body')->item(0);

        foreach ($rootElement->childNodes as $child)
        {
            echo "each child" . "\n";
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                print_object($child->tagName);
                print_object($child->nodeValue);
            }
        }


        die;
    }

}

?>
