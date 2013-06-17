<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                       **
 * @subpackage  msm                                                       **
 * @name        msm                                                       **
 * @copyright   University of Alberta                                     **
 * @link        http://ualberta.ca                                        **
 * @author      Ga Young Kim                                              **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************* */

/**
 * EditorElement class is the top element in class hierarchy and all the classes in the
 * editorCreation folder inherits from this class.  It has of four abstract methods
 * and two methods, which deals with general content processing, that all classes inherit.
 * 
 */
abstract class EditorElement
{

    // This method deals with retrieving appropriate information from form data.  The
    // specific implementation differs between classes.
    abstract function getFormData($idNumber);

    // This method deals with inserting the class information to database tables.  The
    // specific implementation differs between classes.
    abstract function insertData($parentid, $siblingid, $msmid);

    // This method is used to retrieve information from database to be displayed.  The
    // specific implementation differs between classes.
    abstract function loadData($compid);

    // This method is generates the HTML code with appropriate clas information to display the data.  The
    // specific implementation differs between classes.
//    abstract function displayData();

    /**
     * This function parses a raw HTML string content and looks for the HTML elements considered to be
     * "content" elements (ie. p, header elements, ol, ul, and table).  These "content" HTML elements and their 
     * associated data is passed to appropriate content classes such as EditorPara, EditorInContent and EditorTable
     * to be processed by these class methods.  Then the resulting class objects are pushed into an array which is returned.
     * 
     * @param string $oldContent  raw content HTML string that needs to be processsed as mentioned above
     * @return array              array containing all content class objects
     */
    function processContent($oldContent)
    {
        $doc = new DOMDocument();
        $content = $this->processMath($oldContent);
        $doc->loadHTML($content);
        
        $rootElement = $doc->getElementsByTagName('div')->item(0);

        $newContent = array();

        foreach ($rootElement->childNodes as $key => $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if (($child->tagName == "p") || (preg_match('/h\d/', $child->tagName) === 1))
                {
                    $para = new EditorPara();
                    $para->getFormData($child);
                    $newContent[] = $para;
                }
                else if (($child->tagName == "ol") || ($child->tagName == "ul"))
                {
                    $inContent = new EditorInContent();
                    $inContent->getFormData($child);
                    $newContent[] = $inContent;
                }
                else if ($child->tagName == "table")
                {
                    $table = new EditorTable();
                    $table->getFormData($child);
                    $newContent[] = $table;
                }
//                else if ($child->tagName == "img")
//                {
//                    $image = new EditorImage();
//                    $image->getFormData($child);
//                    $newContent[] = $image;
//                }
            }
        }

        return $newContent;
    }

    /**
     * This method is used to process all the subordinate elements in the content bodies.
     * It is called by all class that has content property(ie. EditorDefinition, EditorComment...etc).
     * It looks for anchored elements in the content and pass the DOMElement to the getFormData function of
     * EditorSubordinate class to be processed and be inserted into msm_subordinate database table.
     * 
     * @param string $content       content value of the parent class
     * @return array                an array with all subordinate objects containing appropriate values
     */
    function processSubordinate($content)
    {
        $subordinates = array();
        $htmlParser = new DOMDocument;

        $htmlParser->loadHTML($content);

        $aElements = $htmlParser->getElementsByTagName('a');
        foreach ($aElements as $key => $a)
        {
            $hotword = new EditorSubordinate();
            $hotword->getFormData($a);
            $subordinates[] = $hotword;
        }

        return $subordinates;
    }

    function processImage($content)
    {
        $medias = array();
        $htmlParser = new DOMDocument;

        $htmlParser->loadHTML($content);

        $imgElements = $htmlParser->getElementsByTagName('img');
        foreach ($imgElements as $key => $img)
        {
            $media = new EditorMedia();
            $media->getFormData($img);
            $medias[] = $media;
        }

        return $medias;
    }

    function replaceImages($index, $imgObj, $content, $tagName)
    {
        global $DB;

        $htmlParser = new DOMDocument();
        $htmlParser->loadHTML($content);

        $imgNodes = $htmlParser->getElementsByTagName("img");

        $newImgNode = $htmlParser->createElement("img");
        $newImgNode->setAttribute("src", $imgObj->src);
        $newImgNode->setAttribute("alt", $imgObj->string_id);
        $newImgNode->setAttribute("height", $imgObj->height);
        $newImgNode->setAttribute("width", $imgObj->width);

        foreach ($imgNodes as $key => $imgNode)
        {
            if ($key == $index)
            {
                $imgNode->parentNode->replaceChild($newImgNode, $imgNode);
            }
        }

        $newcontent = $htmlParser->saveXML($htmlParser->importNode($htmlParser->getElementsByTagName($tagName)->item(0), true));

        return $newcontent;
    }

    function processMath($content)
    {
        $parser = new DOMDocument();
        $parser->loadHTML($content);
         $divs = $parser->getElementsByTagName("div");

        if ($divs->length > 0)
        {
            $content = $content;
        }
        else
        {
             $content = "<div>$content</div>";
        }
        $parser->loadHTML($content);

        $spans = $parser->getElementsByTagName("span");

        foreach ($spans as $s)
        {
            $classAttr = $s->getAttribute("class");

            if ($classAttr == "matheditor")
            {
                foreach ($s->childNodes as $child)
                {
                    if ($child->nodeType == XML_TEXT_NODE)
                    {
                        $newSpan = $parser->createElement("span");
                        $newSpan->setAttribute("class", "matheditor");
                        $newSpan->appendChild($child);

                        $s->parentNode->replaceChild($newSpan, $s);
                    }
                    else if ($child->nodeType == XML_ELEMENT_NODE)
                    {
                        if ($child->tagName == "script")
                        {                            
                            $textNode = $child->textContent;
                                    
                            $newSpan = $parser->createElement("span");
                            $newSpan->setAttribute("class", "matheditor");
                            
                            $newTextNode = $parser->createTextNode("\($textNode\)");
                            $newSpan->appendChild($newTextNode);
                            
                            $s->parentNode->replaceChild($newSpan, $s);
                        }
                    }
                }
            }
        }
        return $parser->saveHTML($parser->importNode($parser->getElementsByTagName("div")->item(0)));
    }

}

?>
