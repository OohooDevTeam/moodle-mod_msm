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
 * *************************************************************************
 */

/**
 * ExportElement is an abstract class that all the other classes in the XMLExporter folder inherits from.
 * This class has 2 abstract functions, loadDbData and exportData, that the classes define, one function that is
 * only used within the ExportElement class (function exportMath) and 3 other functions that is called by all the child
 * classes.  For details on each of these functions read information below.
 *
 * @author Ga Young Kim
 */
abstract class ExportElement
{

    // this abstrac function allows the objects of the child classes to retrieve desired data from the database tables
    // (for more informations, read the documentations written in each of the classes)
    abstract function loadDbData($compid);

    // this abstrac function converts the database information to XML files
    // (for more informations, read the documentations written in each of the classes)
    abstract function exportData();

    /**
     * This method is called by ExportComment, ExportDefinition, ExportStatementTheorem, ExportPartTheorem, ExportInfo and ExportInContent
     * to process the content associated with each of these classes to prepare for conversion to XML.  It removes any Mathjax javascript
     * that maybe integrated into the content, any anchored elements that represents subordinate is substituted to subordinate elements and all img
     * tags are replaced with media tags.  After all the content processing is done, the final product is appended to the DOMElement representing the element that called
     * this method.  This method also calls the exportData of ExportMedia and ExportSubordinate classes and use the exportMath method in this class to process math contents.
     * 
     * @param DOMDocument $DomDocument          DOMDocument that created the DOMElement representing the object that called this method
     * @param string $content                   content associated with the object
     * @param DOMElement $DomNode               DOMElement representing the object that called this method
     * @param Object $object                    Instace of the class that called this method
     * @return DOMElement
     */
    function createXmlContent($DomDocument, $content, $DomNode, $object = '')
    {
        $contentDoc = new DOMDocument();
        $contentDoc->formatOutput = true;
        $contentDoc->preserveWhiteSpace = false;

        $DomDocument->formatOutput = true;
        $DomDocument->preserveWhiteSpace = false;
       
        $content = preg_replace("/(?s)(<img(\"[^\"]*\"|'[^']*'|[^'\">\/])*)(>)/", "$1/>", $content); // need the self closing tag to prevent mismatching tag error from loadXML
        $content = str_replace("<br>", "<br/>", $content); // need the self closing tag to prevent mismatching tag error from loadXML
        $content = str_replace("<em>", "<emphasis>", $content);
        $content = str_replace("<b>", "<strong>", $content);
        $content = str_replace("</em>", "</emphasis>", $content);
        $content = str_replace("</b>", "</strong>", $content);
        $content = preg_replace('/<ol style="list-style-type:([a-zA-Z-]+);">/', '<ol type="$1">', $content);
        $content = preg_replace('/<ul style="list-style-type:([a-zA-Z-]+);">/', '<ul bullet="$1">', $content);

        $contentDoc->loadXML($content);
        $divNode = $contentDoc->documentElement;

        foreach ($divNode->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == "p")
                {
                    if (!empty($object))
                    {
                        // replace all anchored elements as subordinates
                        $atags = $child->getElementsByTagName("a");

                        $alength = $atags->length;

                        for ($i = 0; $i < $alength; $i++)
                        {
                            $a = $atags->item(0);
                            $targetSub = null;
                            if (!empty($a))
                            {
                                $id = $a->getAttribute("id");

                                foreach ($object->subordinates as $subordinate)
                                {
                                    $hotInfo = explode("||", $subordinate->hot);

                                    if (trim($id) == trim($hotInfo[0]))
                                    {
                                        $targetSub = $subordinate;
                                        break;
                                    }
                                }

                                if (!empty($targetSub))
                                {
                                    $initsubordinateNode = $targetSub->exportData();
                                    $subordinateNode = $contentDoc->importNode($initsubordinateNode, true);
                                    if (!empty($a->parentNode))
                                    {
                                        $a->parentNode->replaceChild($subordinateNode, $a);
                                    }
                                }
                            }
                        }

                        // replace all img tags with media
                        $imgTags = $child->getElementsByTagName("img");

                        foreach ($imgTags as $key => $img)
                        {
                            if (isset($object->medias))
                            {
                                if (sizeof($object->medias) > 0)
                                {
                                    foreach ($object->medias as $media)
                                    {
                                        $imgAttr = $img->getAttribute('src');
                                        $imgFileNameInfo = explode("/", $imgAttr);

                                        $mediaSrcInfo = explode("||", $media->img->src);

                                        $srcInfo = explode("/", $mediaSrcInfo[0]);

                                        if ($imgFileNameInfo[sizeof($imgFileNameInfo) - 1] == $srcInfo[sizeof($srcInfo) - 1])
                                        {
                                            $mediaNode = $media->exportData();
                                            $mediaElement = $contentDoc->importNode($mediaNode, true);
                                            $img->parentNode->replaceChild($mediaElement, $img);
                                        }
                                    }
                                }
                            }
                        }

                        $this->exportMath($child, $contentDoc);
                    }
                    $newChildNode = $this->replacePTags($DomDocument, $child);
                    $childNode = $DomDocument->importNode($newChildNode, true);
                }
                else
                {
                    $ptags = $contentDoc->getElementsByTagName("p");

                    foreach ($ptags as $p)
                    {
                        $newpNode = $this->replacePTags($DomDocument, $p);
                        $pNode = $contentDoc->importNode($newpNode, true);
                        $p->parentNode->replaceChild($pNode, $p);
                    }

                    // li needs to be processed separately as the p tags in li are not detected by the 
                    // getElementByTagName method above (when there are more than one, it only detects first one for some reason)
                    $litags = $child->getElementsByTagName("li");

                    $newPTag = null;
                    foreach ($litags as $li)
                    {
                        $liDoc = new DOMDocument();
                        $liDoc->formatOutput = true;
                        $liDoc->preserveWhiteSpace = false;
                        $newliTag = $liDoc->createElement("li");

                        foreach ($li->childNodes as $liChild)
                        {
                            if ($liChild->nodeType == XML_ELEMENT_NODE)
                            {
                                if ($liChild->tagName == "para") // could have para if the above method detected it
                                {
                                    $newPTag = $liDoc->importNode($liChild, true);
                                }
                                else if ($liChild->tagName == "p")
                                {
                                    $newpNode = $this->replacePTags($liDoc, $liChild);
                                    $newPTag = $liDoc->importNode($newpNode, true);
                                }
                            }
                            // if the DOMText is not blank, then add para tag --> the content created from 
                            // the authoring tool TinyMCE editor does not have p tags as default so need to add it
                            // when exporting
                            else if (!preg_match("/\s+/", $liChild->wholeText))
                            {
                                $newPTag = $liDoc->createElement("para");
                                $newPbodyTag = $liDoc->createElement("para.body");
                                $lichildNode = $liDoc->importNode($liChild, true);
                                $newPbodyTag->appendChild($lichildNode);
                                $newPTag->appendChild($newPbodyTag);
                            }
                            // can have DOMText with whitespace so in this case, no need for the para tag
                            else if (preg_match("/\s+/", $liChild->wholeText))
                            {
                                $newPTag = $liDoc->importNode($liChild, true);
                            }

                            if (!empty($newPTag))
                            {
                                $newliTag->appendChild($newPTag);
                            }
                        }

                        $newLi = $contentDoc->importNode($newliTag, true);

                        $li->parentNode->replaceChild($newLi, $li);
                    }
                    if (!empty($object))
                    {
                        $atags = $child->getElementsByTagName("a");

                        $alength = $atags->length;

//                        foreach ($anchorArray as $a) {
                        for ($i = 0; $i < $alength; $i++)
                        {
                            $a = $atags->item(0);
                            $targetSub = null;
                            if (!empty($a))
                            {
                                $id = $a->getAttribute("id");

                                foreach ($object->subordinates as $subordinate)
                                {
                                    $hotInfo = explode("||", $subordinate->hot);

                                    if (trim($id) == trim($hotInfo[0]))
                                    {
                                        $targetSub = $subordinate;
                                        break;
                                    }
                                }

                                if (!empty($targetSub))
                                {
                                    $initsubordinateNode = $targetSub->exportData();
                                    $subordinateNode = $contentDoc->importNode($initsubordinateNode, true);
                                    if (!empty($a->parentNode))
                                    {
                                        $a->parentNode->replaceChild($subordinateNode, $a);
                                    }
                                }
                            }
                        }
                    }

                    $imgTags = $child->getElementsByTagName("img");

                    foreach ($imgTags as $key => $img)
                    {
                        if (isset($object->medias))
                        {
                            if (sizeof($object->medias) > 0)
                            {
                                foreach ($object->medias as $media)
                                {
                                    $imgAttr = $img->getAttribute('src');
                                    $imgFileNameInfo = explode("/", $imgAttr);

                                    $srcInfo = explode("/", $media->img->src);

                                    if ($imgFileNameInfo[sizeof($imgFileNameInfo) - 1] == $srcInfo[sizeof($srcInfo) - 1])
                                    {
                                        $mediaNode = $media->exportData();
                                        $mediaElement = $contentDoc->importNode($mediaNode, true);
                                        $img->parentNode->replaceChild($mediaElement, $img);
                                    }
                                }
                            }
                        }
                    }
                    $this->exportMath($child, $contentDoc);

                    $childNode = $DomDocument->importNode($child, true);
                }
                $DomNode->appendChild($childNode);
            }
            else
            { // info captions saved from imported XML does not have any tags other than top div tags --> b/c only takes textContent
                $childNode = $DomDocument->importNode($child, true);
                $DomNode->appendChild($childNode);
            }
        }

        return $DomNode;
    }

    /**
     * This method is only used internally by ExportElement and it deals with all mathjax elements in the contents.
     * It detects the <span class="matheditor"> \(MathjaxContent\)</span> and converts it into <math><latex> MathjaxContent </latex></math>.
     * 
     * @param DOMElement $DomElement        DOMElement representation of the class that called the createXmlContent originially
     * @param DOMDocument $DomDocument      DOMDocument that created the DOMElement representation of the class that called the createXmlContent originially
     */
    function exportMath($DomElement, $DomDocument)
    {
        $DomDocument->formatOutput = true;
        $DomDocument->preserveWhiteSpace = false;

        $mathSpans = $DomElement->getElementsByTagName("span");

        // when each of the spans with className of matheditor is replaced, the size of the NodeList above
        // decreases so need a temporary array that is a copy of the NodeList above
        $tempArray = array();
        foreach ($mathSpans as $mathspan)
        {
            $tempArray[] = $mathspan;
        }

        foreach ($tempArray as $math)
        {
            if ($math->getAttribute("class") == "matheditor")
            {
                $mathNode = $DomDocument->createElement("math");
                $latexNode = $DomDocument->createElement("latex");

                // remove \( and )\ at the end of each math element
                $modifiedMathText = substr(trim($math->textContent), 2, -2);

                $innerTextNode = $DomDocument->createTextNode($modifiedMathText);
                $latexNode->appendChild($innerTextNode);
                $mathNode->appendChild($latexNode);

                $math->parentNode->replaceChild($mathNode, $math);
            }
        }
    }

    /**
     * This method is called by ExportElement and by ExportPara classes to replace all p tags
     * with <para><para.body>content</para.body></para> structure and returns the para element to be replaced with the p tags
     * 
     * @param DOMDocument $DomDocument  DOMDocument that created the DOMElement representation of the class that called the createXmlContent originially
     * @param DOMElement $DomElement    DOMElement representation of the class that called the createXmlContent originially
     * @return DOMElement
     */
    function replacePTags($DomDocument, $DomElement)
    {
        $DomDocument->formatOutput = true;
        $DomDocument->preserveWhiteSpace = false;

        $paraNode = $DomDocument->createElement("para");
        $style = $DomElement->getAttribute("style");
        $align = '';

        $styleProperites = explode(";", $style);
        foreach ($styleProperites as $property)
        {
            $propertyValue = explode(":", $property);

            if ($propertyValue[0] == "text-align")
            {
                $align = $propertyValue[1];
            }
        }

        if (!empty($align))
        {
            $paraNode->setAttribute("align", trim($align));
        }
        else
        {
            $paraNode->setAttribute("align", "left");
        }
        $parabody = $DomDocument->createElement("para.body");

        foreach ($DomElement->childNodes as $child)
        {
            $childNode = $DomDocument->importNode($child, true);
            $paraContentNode = $childNode->cloneNode(true);
            $parabody->appendChild($paraContentNode);
        }
        $paraNode->appendChild($parabody);

        return $paraNode;
    }

    /**
     * This function is called by ExportDefinition, ExportTheorem, and ExportComment to create an XML file
     * when the instaces of the classes above are reference materials.  All reference materials are create in the folder 
     * "standalone" as its own XML file.  This function creates the standalone folder in the temporary folder if it does not exist
     * already and create a new XML file with given content.  Then later when all the relevant database information is converted to XML
     * files, then the whole folder is compresssed into a zip file and user is prompted to download the folder.
     * 
     * @global moodle_database $DB
     * @global type $CFG
     * @param type $obj
     * @param type $elementContent
     */
    function createXMLFile($obj, $elementContent)
    {
        global $DB, $CFG;

        $msmRecord = $DB->get_record("msm", array("id" => $obj->msmid));
        $msmtrimName = preg_replace("/\s+/", '', $msmRecord->name);
        $CompDir = $CFG->dataroot . "/temp/msmtempfiles/$msmtrimName$msmRecord->id/standalones/";

        $elementType = '';
        switch (get_class($obj))
        {
            case "ExportDefinition":
                $elementType = "definition";
                break;
            case "ExportTheorem":
                $elementType = "theorem";
                break;
            case "ExportComment":
                $elementType = "comment";
                break;
        }

        // standalone folder already exists
        if (file_exists($CompDir))
        {
            if (!empty($obj->caption))
            {
                $filename = $CompDir . $obj->caption . "-$elementType" . $obj->compid . ".xml";
            }
            else if (!empty($obj->type))
            {
                $filename = $CompDir . $obj->type . "-$elementType" . $obj->compid . ".xml";
            }
        }
        else
        {
            // need to make a standalone folder
            if (mkdir($CompDir))
            {
                if (!empty($obj->caption))
                {
                    $filename = $CompDir . $obj->caption . "-$elementType" . $obj->compid . ".xml";
                }
                else if (!empty($obj->type))
                {
                    $filename = $CompDir . $obj->type . "-$elementType" . $obj->compid . ".xml";
                }
            }
            else
            {
                echo "error with creating standalone folder";
            }
        }

        if ($xmlfile = fopen($filename, "w"))
        {
            fwrite($xmlfile, $elementContent);
            fclose($xmlfile);
        }
        else
        {
            echo json_encode("error");
        }
    }

    //need a function to look for subordinate and math
    // need a function to convert some HTML stuff to XML
}

?>
