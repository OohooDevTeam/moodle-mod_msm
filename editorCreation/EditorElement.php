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

    // This method deals with retrieving appropriate information from form data.
    // The specific implementation differs between classes.
    abstract function getFormData($idNumber);

    // This method deals with inserting the class information to database tables.
    // The specific implementation differs between classes.
    abstract function insertData($parentid, $siblingid, $msmid);

    // This method is used to retrieve information from database to be displayed.
    // The specific implementation differs between classes.
    abstract function loadData($compid);

    /**
     * This function parses a raw HTML string content and looks for the HTML elements considered to be
     * "content" elements (ie. p, header elements, ol, ul, and table).  These "content" HTML elements and their 
     * associated data is passed to appropriate content classes such as EditorPara, EditorInContent and EditorTable
     * to be processed by these class methods.  Then the resulting class objects are pushed into an array which is returned.
     * 
     * @param string $oldContent    raw content HTML string that needs to be processsed as mentioned above
     * @return array                array containing all content class objects
     */
    function processContent($oldContent)
    {
        $doc = new DOMDocument();
        $content = $this->processMath($oldContent);
        @$doc->loadXML($content);

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
     * @return array                an array with all EditorSubordinate objects containing appropriate values
     */
    function processSubordinate($content)
    {
        $subordinates = array();
        $htmlParser = new DOMDocument;

        $htmlParser->loadHTML($content);

        $aElements = $htmlParser->getElementsByTagName('a');
        foreach ($aElements as $a)
        {
            $hotword = new EditorSubordinate();
            $hotword->getFormData($a);
            $subordinates[] = $hotword;
        }

        return $subordinates;
    }

    /**
     * This method is used to process all the media elements in the content bodies.
     * It is called by all class that has content property(ie. EditorDefinition, EditorComment...etc).
     * It looks for image elements in the content and pass the DOMElement to the getFormData function of
     * EditorMedia class to be processed and be inserted into msm_media database table.
     * 
     * @param string $content           content value of the parent class
     * @return array                    an array with all EditorMedia objects containing appropriate values
     */
    function processImage($content)
    {
        $medias = array();
        $htmlParser = new DOMDocument;

        $htmlParser->loadHTML($content);

        $imgElements = $htmlParser->getElementsByTagName('img');
        foreach ($imgElements as $img)
        {
            $media = new EditorMedia();
            $media->getFormData($img);
            $medias[] = $media;
        }

        return $medias;
    }

    /**
     * This method is used to process all the image elements in the content bodies.
     * It is called by all class that has content property(ie. EditorDefinition, EditorComment...etc).
     * It looks for image elements in the content and replaces the img node with relative pathing
     * to the image file to new image node with url to where the moodle is serving the image files.
     * 
     * @param integer $index        integer used to track the order of images as they appear in the content
     * @param Object $imgObj        EditorImage object that is representing this image element
     * @param string $content       content of the parent element
     * @param string $tagName       tagName associated with root element of above content
     * @return string               new processed content
     */
    function replaceImages($index, $imgObj, $content, $tagName)
    {
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

    /**
     * This method is used in getFormData method of any class with content associated (eg. EditorDefinition/EditorComment..etc)
     * and it removes the MathJax code in the $_POST object representing the content.  The MathJax code, if left in the content,
     * interrupts the load process of DOMDocument and throws an error due to wrong syntax that it does not recognize as valid.
     * Therefore, this method detects span tags with class name matheditor and removes all tags except for the last script tag
     * which contains the raw mathjax code.  Then the code takes the raw mathjax code and append to new span tag with matheditor
     * classname and replace it into content.
     * 
     * @param string $content           content from parent to be modified
     * @return string                   new content
     */
    function processMath($content)
    {
        $parser = new DOMDocument();
        // <nobr> tags from mathjax code caues error in loadXML function
        $content = preg_replace("/(?s)(<img(\"[^\"]*\"|'[^']*'|[^'\">\/])*)(>)/", "$1/>", $content);
        $content = str_replace("<nobr>", '', $content);
        $content = str_replace("</nobr>", '', $content);
        $content = preg_replace("/\&nbsp;/", ' ', $content);

        //htmlParseEntityRef: no name in Entity warning is thrown?     
        // changed to loadXML due to encoding issue --> loadHTML doesn't read &nbsp; properly
        @$parser->loadXML($content);
        $divs = $parser->getElementsByTagName("div");

        if ($divs->length > 0)
        {
            $content = $content;
        }
        else
        {
            $content = "<div>$content</div>";
        }
        
       @$parser->loadXML($content);

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

        return $parser->saveXML($parser->importNode($parser->getElementsByTagName("div")->item(0)));
    }

}

?>
