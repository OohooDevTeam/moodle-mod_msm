<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Proof
 *
 * @author User
 */
class Proof extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_proof';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->proof_block_body = array();

        $this->string_id = $DomElement->getAttribute('id');

        $this->proof_type = $DomElement->getAttribute('type');

        // may not be a best code to deal with proof...
        // to make up one record, need $this->logic_type[1],
        // $this->proof_logic[1]...etc 
        $proof_blocks = $DomElement->getElementsByTagName('proof.block');
        foreach ($proof_blocks as $pb)
        {
            foreach ($pb->childNodes as $key => $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    if ($child->tagName == 'logic')
                    {
                        $this->logic_type[] = $child->getAttribute('type');
                        $this->proof_logic[] = $child->nodeValue;
                    }
                    if ($child->tagName == 'caption')
                    {
                        $this->caption[] = $child->nodeValue;
                    }
//                    if ($child->tagName == 'proof.block.body')
//                    {
//                        $position = $position + 1;
//                        $content = new stdClass();
//                        $content = $this->getContent($child, $position, $this->xmlpath);
//                        $this->proof_block_body[] = $content->content;
//                    }
                }
            }
            $proofbodys = $pb->getElementsByTagName('proof.block.body');
            $doc = new DOMDocument();

            foreach ($proofbodys as $proofbody)
            {
                $position = $position + 1;


                $subordinates = $proofbody->getElementsByTagName('subordinate');

                foreach ($subordinates as $s)
                {
                    $hot = $s->getElementsByTagName('hot')->item(0);

                    $position = $position + 1;
                    $subordinate = new Subordinate($this->xmlpath);
                    $subordinate->loadFromXml($s, $position);
                    $this->subordinates[] = $subordinate;

                    $s->parentNode->replaceChild($hot, $s);
                }

                $indexauthors = $proofbody->getElementsByTagName('index.author');
                foreach ($indexauthors as $ia)
                {
                    $position = $position + 1;
                    $indexauthor = new MathIndex($this->xmlpath);
                    $indexauthor->loadFromXml($ia, $position);
                    $this->indexauthors[] = $indexauthor;

                    $ia->parentNode->removeChild($ia);
                }

                $indexglossarys = $proofbody->getElementsByTagName('index.glossary');
                foreach ($indexglossarys as $ig)
                {
                    $position = $position + 1;
                    $indexglossary = new MathIndex($this->xmlpath);
                    $indexglossary->loadFromXml($ig, $position);
                    $this->indexglossarys[] = $indexglossary;

                    $ig->parentNode->removeChild($ig);
                }

                $indexsymbols = $proofbody->getElementsByTagName('index.symbol');
                foreach ($indexsymbols as $is)
                {
                    $position = $position + 1;
                    $indexsymbol = new MathIndex($this->xmlpath);
                    $indexsymbol->loadFromXml($is, $position);
                    $this->indexsymbols[] = $indexsymbol;

                    $is->parentNode->removeChild($is);
                }

                $element = $doc->importNode($proofbody, true);
                $this->content[] = $doc->saveXML($element);
            }
        }
    }

}

?>
