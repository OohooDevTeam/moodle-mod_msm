<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Quiz
 *
 * @author User
 */
class Quiz extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_quiz';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->string_id = $DomElement->getAttribute('id');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));
        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));
        $this->questions = array();        
        
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();

        $questions = $DomElement->getElementsByTagName('question');
        foreach ($questions as $q)
        {
            foreach ($this->processSubordinate($q, $position)->subordinates as $subordinate)
            {
                $this->subordinates['question'][] = $subordinate;
            }

            foreach ($this->processSubordinate($q, $position)->indexauthors as $indexauthor)
            {
                $this->indexauthors['question'][] = $indexauthor;
            }

            foreach ($this->processSubordinate($q, $position)->indexglossarys as $indexglossary)
            {
                $this->indexglossarys['question'][] = $subordinate;
            }

            foreach ($this->processSubordinate($q, $position)->indexsymbols as $indexsymbol)
            {
                $this->indexsymbols['question'][] = $subordinate;
            }

            foreach ($this->processSubordinate($q, $position)->content as $content)
            {
                $this->questions[] = $content;
            }
        }

        //may not even need this... only needed if hint has text without info element
        $this->hints = array();
        $this->hintinfos = array();
        $hints = $DomElement->getElementsByTagName('hint');

        foreach ($hints as $h)
        {
            foreach ($h->childNodes as $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    if ($child->tagName == 'info')
                    {
                        $position = $position + 1;
                        $info = new MathInfo($this->xmlpath);
                        $info->loadFromXml($child, $position);
                        $this->hintinfos[] = $info;
                    }
                }
                else
                {
                    $this->hints[] = $child;
                }
            }
        }

        $this->answers = array();
        $this->choiceinfos = array();

        $choices = $DomElement->getElementsByTagName('choice');
        foreach ($choices as $c)
        {
            foreach ($c->childNodes as $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    if ($child->tagName == 'answer')
                    {
                        foreach ($this->processSubordinate($child, $position)->subordinates as $subordinate)
                        {
                            $this->subordinates['answer'][] = $subordinate;
                        }

                        foreach ($this->processSubordinate($child, $position)->indexauthors as $indexauthor)
                        {
                            $this->indexauthors['answer'][] = $indexauthor;
                        }

                        foreach ($this->processSubordinate($child, $position)->indexglossarys as $indexglossary)
                        {
                            $this->indexglossarys['answer'][] = $subordinate;
                        }

                        foreach ($this->processSubordinate($child, $position)->indexsymbols as $indexsymbol)
                        {
                            $this->indexsymbols['answer'][] = $subordinate;
                        }

                        foreach ($this->processSubordinate($child, $position)->content as $content)
                        {
                            $this->answers[] = $content;
                        }
                    }
                    else if ($child->tagName == 'info')
                    {
                        $position = $position + 1;
                        $info = new MathInfo($this->xmlpath);
                        $info->loadFromXml($child, $position);
                        $this->choiceinfos[] = $info;
                    }
                }
            }
        }

        //$this->parts = array();
        $this->partchoiceinfos = array();
        $this->partchoiceanswers = array();
        $this->parthints = array();
        $this->parthintinfos = array();
        $this->partquestions = array();

        $parts = $DomElement->getElementsByTagName('part');

        $i = 0;
        foreach ($parts as $p)
        {
            foreach ($p->childNodes as $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    if ($child->tagName == 'question')
                    {
                        foreach ($this->processSubordinate($child, $position)->subordinates as $subordinate)
                        {
                            $this->subordinates['question'][] = $subordinate;
                        }

                        foreach ($this->processSubordinate($child, $position)->indexauthors as $indexauthor)
                        {
                            $this->indexauthors['question'][] = $indexauthor;
                        }

                        foreach ($this->processSubordinate($child, $position)->indexglossarys as $indexglossary)
                        {
                            $this->indexglossarys['question'][] = $subordinate;
                        }

                        foreach ($this->processSubordinate($child, $position)->indexsymbols as $indexsymbol)
                        {
                            $this->indexsymbols['question'][] = $subordinate;
                        }

                        foreach ($this->processSubordinate($child, $position)->content as $content)
                        {
                            $this->partquestions[] = $content;
                        }
                    }
                    else if ($child->tagName == 'hint')
                    {
                        foreach ($child->childNodes as $grandchild)
                        {
                            if ($grandchild->nodeType == XML_ELEMENT_NODE)
                            {
                                if ($grandchild->tagName == 'info')
                                {
                                    $position = $position + 1;
                                    $info = new MathInfo($this->xmlpath);
                                    $info->loadFromXml($grandchild, $position);
                                    $this->parthintinfos[$i] = $info;
                                }
                            }
                            else
                            {
                                $this->parthints[$i] = $grandchild;
                            }
                        }
                    }
                    else if ($child->tagName == 'choice')
                    {
                        foreach ($child->childNodes as $grandchild)
                        {
                            if ($grandchild->nodeType == XML_ELEMENT_NODE)
                            {
                                if ($grandchild->tagName == 'answer')
                                {
                                    foreach ($this->processSubordinate($grandchild, $position)->subordinates as $subordinate)
                                    {
                                        $this->subordinates['partanswer'][] = $subordinate;
                                    }

                                    foreach ($this->processSubordinate($grandchild, $position)->indexauthors as $indexauthor)
                                    {
                                        $this->indexauthors['partanswer'][] = $indexauthor;
                                    }

                                    foreach ($this->processSubordinate($grandchild, $position)->indexglossarys as $indexglossary)
                                    {
                                        $this->indexglossarys['partanswer'][] = $subordinate;
                                    }

                                    foreach ($this->processSubordinate($grandchild, $position)->indexsymbols as $indexsymbol)
                                    {
                                        $this->indexsymbols['partanswer'][] = $subordinate;
                                    }

                                    foreach ($this->processSubordinate($grandchild, $position)->content as $content)
                                    {
                                        $this->partchoiceanswers[] = $content;
                                    }
                                }
                                else
                                {
                                    $position = $position + 1;
                                    $info = new MathInfo($this->xmlpath);
                                    $info->loadFromXml($grandchild, $position);
                                    $this->parthintinfos[$i] = $info;
                                }
                            }
                        }
                    }
                }
            }
            $i++;
        }
        // when saving into db, if hint/choice/answer are from part element, then add the same number to the part field
    }

}

?>
