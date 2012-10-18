<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                      **
 * @subpackage  msm                                                      **
 * @name        msm                                                      **
 * @copyright   University of Alberta                                    **
 * @link        http://ualberta.ca                                       **
 * @author      Ga Young Kim                                             **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * *************************************************************************
 * ************************************************************************ */
require_once('GlossaryNode.php');

/**
 * Description of Index
 *
 * @author User
 */
class MathIndex extends Element
{

    public $position;
    public $term;
    public $symbol;
    public $symbol_type;
    public $sortstring;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->glossarytable = 'msm_index_glossary';
        $this->symboltable = 'msm_index_symbol';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $nameofElement = $DomElement->tagName;

        $this->infos = array();
        $this->names = array();

        switch ($nameofElement)
        {
            case('index.symbol'):
                $this->symbol = $this->getContent($DomElement->getElementsByTagName('symbol')->item(0));
                $this->symbol_type = $DomElement->getElementsByTagName('symbol')->item(0)->getAttribute('type');
                $this->sortstring = $this->getDomAttribute($DomElement->getElementsByTagName('sortstring'));

                $infos = $DomElement->getElementsByTagName('info');

                foreach ($infos as $i)
                {
                    $position = $position + 1;
                    $info = new MathInfo($this->xmlpath);
                    $info->loadFromXml($i, $position);
                    $this->infos[] = $info;
                }

                break;

            case('index.glossary'):
                $terms = $DomElement->getElementsByTagName('term');
                $string = '';

                foreach ($terms as $t)
                {
                    $term = $this->getContent($t);
                    $string = $string . trim($term) . '/';
                    $this->term = $string;
                }

                $infos = $DomElement->getElementsByTagName('info');

                foreach ($infos as $i)
                {
                    $position = $position + 1;
                    $info = new MathInfo($this->xmlpath);
                    $info->loadFromXml($i, $position);
                    $this->infos[] = $info;
                }

                break;

            case('index.author'):

                $position = $position + 1;
                $name = new Person($this->xmlpath);
                $name->loadFromXml($DomElement, $position);
                $this->names[] = $name;

                $infos = $DomElement->getElementsByTagName('info');

                foreach ($infos as $i)
                {
                    $position = $position + 1;
                    $info = new MathInfo($this->xmlpath);
                    $info->loadFromXml($i, $position);
                    $this->infos[] = $info;
                }

                break;
        }
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();
        $sibling_id = $siblingid;

        if (!empty($this->symbol))
        {
            $data->symbol_type = $this->symbol_type;
            $data->sortstring = $this->sortstring;
            $data->symbol = trim($this->symbol);

            $numOfRecords = $DB->count_records('msm_index_symbol');
            if ($numOfRecords > 0)
            {
                // need the limit to be $numOfRecords+1 to process the last record
                for ($i = 1; $i < $numOfRecords + 1; $i++)
                {
                    $string = $DB->get_field('msm_index_symbol', 'symbol', array('id' => $i));

                    if (trim($string) == trim($this->symbol))
                    {
                        $recordID = $i;
                        break;
                    }
                    else
                    {
                        $recordID = false;
                    }
                }
            }
            else
            {
                $recordID = false;
            }

            if (empty($recordID))
            {
                $this->id = $DB->insert_record($this->symboltable, $data);
                $this->compid = $this->insertToCompositor($this->id, $this->symboltable, $msmid, $parentid, $sibling_id);
                $sibling_id = $this->compid;
            }
            else
            {
                $this->compid = $this->insertToCompositor($recordID, $this->symboltable, $msmid, $parentid, $sibling_id);
                $sibling_id = $this->compid;
            }
        }

        if (!empty($this->names))
        {
            foreach ($this->names as $key => $name)
            {
                $firststring = $name->name["first"];
                $laststring = $name->name["last"];
                $middlestring = $name->name["middle"];

                $numOfRecords = $DB->count_records('msm_person');
                if ($numOfRecords > 0)
                {
                    // need the limit to be $numOfRecords+1 to process the last record
                    for ($i = 1; $i < $numOfRecords + 1; $i++)
                    {
                        $first = $DB->get_field('msm_person', 'firstname', array('id' => $i));
                        $last = $DB->get_field('msm_person', 'lastname', array('id' => $i));
                        $middle = $DB->get_field('msm_person', 'middlename', array('id' => $i));

                        if ((!empty($first)) && ($first == $firststring))
                        {
                            if ((!empty($last)) && ($last == $laststring))
                            {
                                if ((!empty($middle)) && ($middle == $middlestring))
                                {
                                    $recordID = $i;
                                    break;
                                }
                                else if ((empty($middle)) && (empty($middlestring))) //record exists where first and last name match but middle is empty
                                {
                                    $recordID = $i;
                                    break;
                                }
                                else
                                {
                                    $recordID = false;
                                }
                            }
                            else //first name matched but not lastname
                            {
                                $recordID = false;
                            }
                        }
                        else // first name did not match
                        {
                            $recordID = false;
                        }
                    }
                }
                else // the number of record in the table is less or equal to zero
                {
                    $recordID = false;
                }

                if (empty($recordID))
                {
                    $name->saveIntoDb($name->position, $msmid, '', '', 'index');
                    $name->compid = $this->insertToCompositor($name->id, $name->tablename, $msmid, $parentid, $sibling_id);
                    $sibling_id = $name->compid;
                    $this->compid = $name->compid;
                }
                else
                {
                    $name->compid = $this->insertToCompositor($recordID, $name->tablename, $msmid, $parentid, $sibling_id);
                    $sibling_id = $name->compid;
                    $this->compid = $name->compid;
                }
            }
        }

        if (!empty($this->term))
        {
            $data->term = trim($this->term);

            // cannot compare the this->term and term field of a record due to XML nature of the two content
            $numOfRecords = $DB->count_records('msm_index_glossary');
            if ($numOfRecords > 0)
            {
                // need the limit to be $numOfRecords+1 to process the last record
                for ($i = 1; $i < $numOfRecords + 1; $i++)
                {
                    $string = $DB->get_field('msm_index_glossary', 'term', array('id' => $i));

                    if (trim($string) == trim($this->term))
                    {
                        $recordID = $i;
                        break;
                    }
                    else
                    {
                        $recordID = false;
                    }
                }
            }
            else
            {
                $recordID = false;
            }



            if (empty($recordID))
            {
                $this->id = $DB->insert_record($this->glossarytable, $data);
                $this->compid = $this->insertToCompositor($this->id, $this->glossarytable, $msmid, $parentid, $sibling_id);
                $sibling_id = $this->compid;
            }
            else
            {
                $this->compid = $this->insertToCompositor($recordID, $this->glossarytable, $msmid, $parentid, $sibling_id);
                $sibling_id = $this->compid;
            }
        }

        foreach ($this->infos as $info)
        {
            $numOfRecords = $DB->count_records('msm_info');
            if ($numOfRecords > 0)
            {
                // need the limit to be $numOfRecords+1 to process the last record
                for ($i = 1; $i < $numOfRecords + 1; $i++)
                {
                    $string = $DB->get_field('msm_info', 'info_content', array('id' => $i));

                    if (trim($string) == trim($info->content))
                    {
                        $recordID = $i;
                        break;
                    }
                    else
                    {
                        $recordID = false;
                    }
                }
            }
            else
            {
                $recordID = false;
            }

            if (empty($recordID))
            {
                $info->saveIntoDb($info->position, $msmid, $this->compid);
            }
            else
            {
                $info->compid = $this->insertToCompositor($recordID, $info->tablename, $msmid, $parentid, $sibling_id);
                $sibling_id = $info->compid;
            }
        }
    }

    /**
     * 
     * @global moodle_database $DB
     * @return \MathIndex
     */
    function loadGlossaryFromDb($msmid)
    {
        global $DB;

        $glossaryTable = $DB->get_record('msm_table_collection', array('tablename' => 'msm_index_glossary'))->id;
        $glossaryCompRecords = $DB->get_records('msm_compositor', array('table_id' => $glossaryTable, 'msm_id' => $msmid), 'unit_id');

        $prevUnitId = 0;
        $rootNode = new GlossaryNode();
        $rootNode->text = 'root';
        $rootNode->depth = 0;

        foreach ($glossaryCompRecords as $glossaryComp)
        {
            if ($prevUnitId == $glossaryComp->unit_id)
            {
                // only processing the first occurence of the specified glofssary record
                continue;
            }
            else
            {
                $glossaryUnit = $DB->get_record('msm_index_glossary', array('id' => $glossaryComp->unit_id));

                $childElement = $DB->get_record('msm_compositor', array('parent_id' => $glossaryComp->id));

                $infotable = $DB->get_record('msm_table_collection', array('tablename' => 'msm_info'))->id;

                if (!empty($childElement))
                {
                    if ($childElement->table_id == $infotable)
                    {
                        $info = new MathInfo();
                        $info->loadFromDb($childElement->unit_id, $childElement->id);
                    }
                }

                $termpath = explode('/', $glossaryUnit->term);

                if (isset($info))
                {
                    $this->createTree($info, $rootNode, $termpath);
                }
            }
            $prevUnitId = $glossaryComp->unit_id;
        }

        $this->sortTree($rootNode);
        return $rootNode;
    }

    /**
     * 
     * @param GlossaryNode $finalTree       represents the root element of the tree created in loadGlossaryFromDb function
     *                                      using createTree method.
     */
    function sortTree($finalTree)
    {
        $comparisonArray = array();
        foreach ($finalTree->children as $child)
        {
            $comparisonArray[] = strtolower($child->text);
        }

        sort($comparisonArray);

        foreach ($comparisonArray as $key => $text)
        {
            foreach ($finalTree->children as $index => $subNode)
            {
                if (strtolower($subNode->text) == $text)
                {
                    $temp = $finalTree->children[$key];
                    $finalTree->children[$key] = $subNode;
                    $finalTree->children[$index] = $temp;
                }
            }
        }
    }

    function createTree($child, $parentNode, $termArray)
    {
        $termLength = sizeof($termArray);

        if (!empty($parentNode->children))
        {
            $term = array_shift($termArray);

            $foundCatergory = false;

            foreach ($parentNode->children as $parentChild)
            {
                if ($parentChild->text == $term)
                {
                    $this->createTree($child, $parentChild, $termArray);
                    $foundCatergory = true;
                    break;
                }
                else
                {
                    continue;
                }
            }

            if (!$foundCatergory)
            {
                $childNode = new GlossaryNode();
                $childNode->text = $term;
                $parentNode->children[] = $childNode;

                $this->createTree($child, $childNode, $termArray);
            }
        }
        else
        {
            if ($termLength >= 1)
            {
                $term = array_shift($termArray);

                if (!empty($term))
                {
                    if ($parentNode->text != $term)
                    {
                        $childNode = new GlossaryNode();
                        $childNode->text = $term;
                        $parentNode->children[] = $childNode;

                        $this->createTree($child, $childNode, $termArray);
                    }
                    else
                    {
                        $parentNode->infos[] = $child;
                    }
                }
                else
                {
                    $parentNode->infos[] = $child;
                }
            }
            else
            {
                return false;
            }
        }
        $child = null;
    }

    function loadSymbolFromDb($id, $compid)
    {
        global $DB;

        $symbolRecord = $DB->get_record($this->symboltable, array('id' => $id));

        $this->id = $symbolRecord->id;
        $this->symbol = $symbolRecord->symbol;
        $this->symbol_type = $symbolRecord->symbol_type;
        $this->sortstring = $symbolRecord->sortstring;

        $this->infos = array();
        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childTablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            if ($childTablename == 'msm_info')
            {
                $info = new MathInfo();
                $info->loadFromDb($child->unit_id, $child->id);
                $this->infos[] = $info;
            }
        }

        $this->parents = array();
        $currentRecord = $DB->get_record('msm_compositor', array('id' => $compid));
        $parentElements = $DB->get_records('msm_compositor', array('id' => $currentRecord->parent_id));

        foreach ($parentElements as $parent)
        {
            $parentTablename = $DB->get_record('msm_table_collection', array('id' => $parent->table_id))->tablename;

            if ($parentTablename == 'msm_def')
            {
                $def = new Definition();
                $def->loadFromDb($parent->unit_id, $parent->id);
                $this->parents[] = $def;
            }
            else if ($parentTablename == 'msm_theorem')
            {
                $theorem = new Theorem();
                $theorem->loadFromDb($parent->unit_id, $parent->id);
                $this->parents[] = $theorem;
            }
            else
            {
                $parentUnitElement = $DB->get_records('msm_compositor', array('id' => $parent->parent_id));
                $unitTable = $DB->get_record('msm_table_collection', array('tablename' => 'msm_unit'))->id;
                $theoremTable = $DB->get_record('msm_table_collection', array('tablename' => 'msm_theorem'))->id;
                $statementTheoremTable = $DB->get_record('msm_table_collection', array('tablename' => 'msm_statement_theorem'))->id;

                foreach ($parentUnitElement as $parentUnit)
                {
                    if ($parentUnit->table_id == $unitTable)
                    {
                        $unit = new Unit();
                        $unit->loadFromDb($parentUnit->unit_id, $parentUnit->id);
                        $this->parents[] = $unit;
                    }
                    else if ($parentUnit->table_id == $theoremTable)
                    {
                        $theorem = new Theorem();
                        $theorem->loadFromDb($parentUnit->unit_id, $parentUnit->id);
                        $this->parents[] = $theorem;
                    }
                    else if ($parentUnit->table_id == $statementTheoremTable)
                    {
                        $TheoremElement = $DB->get_record('msm_compositor', array('id' => $parentUnit->parent_id));

                        $theorem = new Theorem();
                        $theorem->loadFromDb($TheoremElement->unit_id, $TheoremElement->id);
                        $this->parents[] = $theorem;
                    }
                }
            }
        }

        return $this;
    }

    function displayGlossary($glossaryTree)
    {
        global $CFG;
        $content = '';

        foreach ($glossaryTree->children as $key => $term)
        {
            if (!empty($term->text))
            {
                // it has a info box associated with the word
                if (sizeof($term->infos) > 0)
                {
                    if (sizeof($term->infos) > 1)
                    {
                        print_object($term);
                    }
                    // this word has a child category
                    if (sizeof($term->children) > 0)
                    {
                        $content .= "<li><span>" . $term->text . "     </span><a id='glossaryinfo-" . $term->infos[0]->compid . "' class='msm_infobutton' onmouseover='infoopen(" . $term->infos[0]->compid . ")'>i</a>";
                        $content .= "<ul>";
                        $content .= $this->displayGlossary($term);
                        $content .= "</ul>";
                        $content .= "<li>";
                        $content .= $term->infos[0]->displayhtml();
                    }
                    // empty children
                    else
                    {
                        $content .= "<li><span>" . $term->text . "   </span><a id='glossaryinfo-" . $term->infos[0]->compid . "' class='msm_infobutton' onmouseover='infoopen(" . $term->infos[0]->compid . ")'>i</a></li>";
                        $content .= $term->infos[0]->displayhtml();
                    }
                }
                else // no info box associated with this word
                {
                    // this word has a child category
                    if (sizeof($term->children) > 0)
                    {
                        $content .= "<li><span>" . $term->text . "</span>";
                        $content .= "<ul>";
                        $content .= $this->displayGlossary($term);
                        $content .= "</ul>";
                        $content .= "<li>";
                    }
                    // empty children
                    else
                    {
                        $content .= "<li><span>" . $term->text . "</span></li>";
                    }
                }
            }
        }
        return $content;
    }

    function displaySymbol()
    {
        global $CFG;

        $content = '';

        if (sizeof($this->infos) != 0)
        {
            $content .= "<li><span>" . $this->symbol . "     </span><a id='symbolinfo-" . $this->infos[0]->compid . "' class='msm_infobutton' onmouseover='infoopen(" . $this->infos[0]->compid . ")'>i</a></li>";

            $content .= $this->infos[0]->displayhtml();

            $content .= "<div class='symbolrefcontent' id='symbolrefcontent-" . $this->infos[0]->compid . "' style='display:none;'>";
            foreach ($this->parents as $parent)
            {
                $content .= $parent->displayhtml();
            }
            $content .= "</div>";
        }
        else
        {
            $content .= "<li><span>" . $this->symbol . "</span></li>";
        }

        return $content;
    }

    function makeSymbolPanel($msmid)
    {
        global $DB;

        $content = '';

        $content .= "<div id='symbolpanel' class='panel'>";
        $content .="<div class='slidepanelcontent' id='symbolcontent'>";
        $content .= "<h3> S Y M B O L S </h3>";
        $content .= '<ul id="symbolindex" class="treeview-red">';

        $symbolUnitRecords = $DB->get_records('msm_index_symbol');
        $symbolTable = $DB->get_record('msm_table_collection', array('tablename' => 'msm_index_symbol'))->id;
        foreach ($symbolUnitRecords as $symbolRecord)
        {
            $symbolRecords = $DB->get_records('msm_compositor', array('table_id' => $symbolTable, 'unit_id' => $symbolRecord->id, 'msm_id' => $msmid));
            $firstitem = array_shift(array_values($symbolRecords));

            $this->loadSymbolFromDb($firstitem->unit_id, $firstitem->id);

            $content .= $this->displaySymbol();
        }

        $content .= "</ul>";
        $content .="</div>"; // end of slidepanelcontent
        $content .= "</div>"; // end of panel

        return $content;
    }

    function makeGlossaryPanel($msmid)
    {
        global $DB;

        $content = '';

        $content .= "<div id='glossarypanel' class='panel'>";
        $content .="<div class='glossarypanelcontent' id='glossarycontent'>";
        $content .= "<h3> G L O S S A R Y </h3>";
        $content .= '<ul id="glossaryindex" class="treeview-red">';

        $glossaryTree = $this->loadGlossaryFromDb($msmid);

//        print_object($glossaryTree);

        $content .= $this->displayGlossary($glossaryTree);

        $content .= "</ul>";
        $content .="</div>"; // end of slidepanelcontent
        $content .= "</div>"; // end of panel

        return $content;
    }

}

?>
