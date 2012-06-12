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
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexauthors = array();

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
                        $this->caption[] = $this->getContent($child->getElementsByTagName('caption')->item(0));
                    }
                }
            }

            $proofbodys = $pb->getElementsByTagName('proof.block.body');
            $doc = new DOMDocument();

            foreach ($proofbodys as $proofbody)
            {

                foreach ($this->processSubordinate($proofbody, $position)->subordinates as $subordinate)
                {
                    $this->subordinates[] = $subordinate;
                }

                foreach ($this->processSubordinate($proofbody, $position)->indexauthors as $indexauthor)
                {
                    $this->indexauthors[] = $indexauthor;
                }

                foreach ($this->processSubordinate($proofbody, $position)->indexglossarys as $indexglossary)
                {
                    $this->indexglossarys[] = $indexglossary;
                }

                foreach ($this->processSubordinate($proofbody, $position)->indexsymbols as $indexsymbol)
                {
                    $this->indexsymbols[] = $indexsymbol;
                }

                foreach ($this->processSubordinate($proofbody, $position)->content as $content)
                {
                    $this->proof_block_body[] = $content;
                }
//                $position = $position + 1;
//
//                $subordinates = $proofbody->getElementsByTagName('subordinate');
//
//                $length=0;
//                
//              foreach($subordinates as $s)
//              {
//                  if($s->parentNode->parentNode->parentNode->tagName != 'info')
//                  {
//                      $length++;
//                  }
//              }
//
//                for ($i = 0; $i < $length; $i++)
//                {
//                      echo "length";
//                print_object($length);
//                    echo "subordinates";
//                    print_object($i);
//                    print_object($subordinates->item(0)->nodeValue);
//                    
//                    $hot = $subordinates->item(0)->getElementsByTagName('hot')->item(0);
//                    
//                    echo "hot";
//                    print_object($hot);
//
//                    $position = $position + 1;
//                    $subordinate = new Subordinate($this->xmlpath);
//                    $subordinate->loadFromXml($subordinates->item(0), $position);
//                    $this->subordinates[] = $subordinate;
//
//                    $subordinates->item(0)->parentNode->replaceChild($hot, $subordinates->item(0));
//                    
//                }
//
//
//                $indexauthors = $proofbody->getElementsByTagName('index.author');
//                foreach ($indexauthors as $ia)
//                {
//                    $position = $position + 1;
//                    $indexauthor = new MathIndex($this->xmlpath);
//                    $indexauthor->loadFromXml($ia, $position);
//                    $this->indexauthors[] = $indexauthor;
//
//                    $ia->parentNode->removeChild($ia);
//                }
//
//                $indexglossarys = $proofbody->getElementsByTagName('index.glossary');
//                foreach ($indexglossarys as $ig)
//                {
//                    $position = $position + 1;
//                    $indexglossary = new MathIndex($this->xmlpath);
//                    $indexglossary->loadFromXml($ig, $position);
//                    $this->indexglossarys[] = $indexglossary;
//
//                    $ig->parentNode->removeChild($ig);
//                }
//
//                $indexsymbols = $proofbody->getElementsByTagName('index.symbol');
//                foreach ($indexsymbols as $is)
//                {
//                    $position = $position + 1;
//                    $indexsymbol = new MathIndex($this->xmlpath);
//                    $indexsymbol->loadFromXml($is, $position);
//                    $this->indexsymbols[] = $indexsymbol;
//
//                    $is->parentNode->removeChild($is);
//                }
//
//                $element = $doc->importNode($proofbody, true);
//                $this->proof_block_body[] = $doc->saveXML($element);
            }
        }

    }

}

?>
