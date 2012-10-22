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
//                $sibling_id = $this->compid;
            }
            else
            {
                $this->compid = $this->insertToCompositor($recordID, $this->symboltable, $msmid, $parentid, $sibling_id);

                $symbolTableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_index_symbol'))->id;
                $sameSymbolRecords = $DB->get_records('msm_compositor', array('unit_id' => $recordID, 'table_id' => $symbolTableID));

                foreach ($sameSymbolRecords as $symbolrecord)
                {
                    $this->grabSubunitChilds($symbolrecord, $this->compid, $msmid);
                }
            }
            $sibling_id = $this->compid;
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
            }
            else
            {
                $this->compid = $this->insertToCompositor($recordID, $this->glossarytable, $msmid, $parentid, $sibling_id);

//                $glossaryTableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_index_glossary'))->id;
//                $sameGlossaryRecords = $DB->get_records('msm_compositor', array('unit_id' => $recordID, 'table_id' => $glossaryTableID));
//
//                foreach ($sameGlossaryRecords as $glossaryrecord)
//                {
//                    $this->grabSubunitChilds($glossaryrecord, $this->compid, $msmid);
//                }
            }
            $sibling_id = $this->compid;
        }

        $sibling_id=0;
        foreach ($this->infos as $info)
        {
            $info->saveIntoDb($info->position, $msmid, $this->compid, $sibling_id);
            $sibling_id = $info->compid;
//            $numOfRecords = $DB->count_records('msm_info');
//            if ($numOfRecords > 0)
//            {
//                // need the limit to be $numOfRecords+1 to process the last record
//                for ($i = 1; $i < $numOfRecords + 1; $i++)
//                {
//                    $string = $DB->get_field('msm_info', 'info_content', array('id' => $i));
//
//                    if (trim($string) == trim($info->content))
//                    {
//                        $recordID = $i;
//                        break;
//                    }
//                    else
//                    {
//                        $recordID = false;
//                    }
//                }
//            }
//            else
//            {
//                $recordID = false;
//            }
//
//            if (empty($recordID))
//            {
//                $info->saveIntoDb($info->position, $msmid, $this->compid);
//                $sibling_id = $info->compid;
//            }
//            else
//            {
//                $info->compid = $this->insertToCompositor($recordID, $info->tablename, $msmid, $parentid, $sibling_id);
//                $sibling_id = $info->compid;
//            }
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

                $childElements = $DB->get_records('msm_compositor', array('parent_id' => $glossaryComp->id));

                $infotable = $DB->get_record('msm_table_collection', array('tablename' => 'msm_info'))->id;

                $infos = array();
                if (!empty($childElements))
                {
                    foreach ($childElements as $child)
                    {
                        if ($child->table_id == $infotable)
                        {
                            $info = new MathInfo();
                            $info->loadFromDb($child->unit_id, $child->id);
                            $infos[] = $info;
                        }
                    }
                }

                $parents = array();
                $parentElements = $DB->get_records('msm_compositor', array('id' => $glossaryComp->parent_id));

                if (!empty($parentElements))
                {
                    foreach ($parentElements as $parent)
                    {
                        $parentTablename = $DB->get_record('msm_table_collection', array('id' => $parent->table_id))->tablename;

                        if ($parentTablename == 'msm_def')
                        {
                            $def = new Definition();
                            $def->loadFromDb($parent->unit_id, $parent->id);
                            $parents[] = $def;
                        }
                        else if ($parentTablename == 'msm_theorem')
                        {
                            $theorem = new Theorem();
                            $theorem->loadFromDb($parent->unit_id, $parent->id);
                            $parents[] = $theorem;
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
                                    $parents[] = $unit;
                                }
                                else if ($parentUnit->table_id == $theoremTable)
                                {
                                    $theorem = new Theorem();
                                    $theorem->loadFromDb($parentUnit->unit_id, $parentUnit->id);
                                    $parents[] = $theorem;
                                }
                                else if ($parentUnit->table_id == $statementTheoremTable)
                                {
                                    $TheoremElement = $DB->get_record('msm_compositor', array('id' => $parentUnit->parent_id));

                                    $theorem = new Theorem();
                                    $theorem->loadFromDb($TheoremElement->unit_id, $TheoremElement->id);
                                    $parents[] = $theorem;
                                }
                            }
                        }
                    }
                }

                $termpath = explode('/', $glossaryUnit->term);

                if ((isset($infos)) && (isset($parents)))
                {
                    $this->createTree($infos, $parents, $rootNode, $termpath);
                    unset($infos);
                    unset($parents);
                }
                else if ((isset($infos)) && (!isset($parents)))
                {
                    $this->createTree($infos, '', $rootNode, $termpath);
                    unset($infos);
                }
                else if ((!isset($infos)) && (isset($parents)))
                {
                    $this->createTree('', $parents, $rootNode, $termpath);
                    unset($parents);
                }
            }
            $prevUnitId = $glossaryComp->unit_id;
        }

        $this->sortTree($rootNode, 'text');
//        print_object($rootNode);
        return $rootNode;
    }

    function loadSymbolFromDb($msmid)
    {
        global $DB;

        $prevUnitID = 0;

        $symbols = new GlossaryNode();

        $symboltableid = $DB->get_record('msm_table_collection', array('tablename' => 'msm_index_symbol'))->id;

        $symbolCompRecords = $DB->get_records('msm_compositor', array('table_id' => $symboltableid, 'msm_id' => $msmid), 'unit_id');

        foreach ($symbolCompRecords as $key => $symbolComp)
        {
            $currentSymbol = new MathIndex();
            if ($prevUnitID != $symbolComp->unit_id)
            {
                $SymbolUnit = $DB->get_record('msm_index_symbol', array('id' => $symbolComp->unit_id));

                $currentSymbol->id = $SymbolUnit->id;
                $currentSymbol->symbol = $SymbolUnit->symbol;
                $currentSymbol->symbol_type = $SymbolUnit->symbol_type;
                $currentSymbol->sortstring = $SymbolUnit->sortstring;

                $currentSymbol->infos = array();
                $childElements = $DB->get_records('msm_compositor', array('parent_id' => $symbolComp->id));

                foreach ($childElements as $child)
                {
                    $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;
                    if ($childtablename == 'msm_info')
                    {
                        $info = new MathInfo();
                        $info->loadFromDb($child->unit_id, $child->id);
                        $currentSymbol->infos[] = $info;
                    }
                }

                $currentSymbol->parents = array();
                $parentElements = $DB->get_records('msm_compositor', array('id' => $symbolComp->parent_id));

                foreach ($parentElements as $parent)
                {
                    $parentTablename = $DB->get_record('msm_table_collection', array('id' => $parent->table_id))->tablename;

                    if ($parentTablename == 'msm_def')
                    {
                        $def = new Definition();
                        $def->loadFromDb($parent->unit_id, $parent->id);
                        $currentSymbol->parents[] = $def;
                    }
                    else if ($parentTablename == 'msm_theorem')
                    {
                        $theorem = new Theorem();
                        $theorem->loadFromDb($parent->unit_id, $parent->id);
                        $currentSymbol->parents[] = $theorem;
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
                                $currentSymbol->parents[] = $unit;
                            }
                            else if ($parentUnit->table_id == $theoremTable)
                            {
                                $theorem = new Theorem();
                                $theorem->loadFromDb($parentUnit->unit_id, $parentUnit->id);
                                $currentSymbol->parents[] = $theorem;
                            }
                            else if ($parentUnit->table_id == $statementTheoremTable)
                            {
                                $TheoremElement = $DB->get_record('msm_compositor', array('id' => $parentUnit->parent_id));

                                $theorem = new Theorem();
                                $theorem->loadFromDb($TheoremElement->unit_id, $TheoremElement->id);
                                $currentSymbol->parents[] = $theorem;
                            }
                        }
                    }
                }
                $symbols->children[] = $currentSymbol;
            }
            $prevUnitID = $symbolComp->unit_id;
        }
        return $symbols;
    }

    function loadAuthorFromDb($msmid)
    {
        global $DB;

        $authors = new GlossaryNode();
        $prevUnitID = 0;

        $authorTableid = $DB->get_record('msm_table_collection', array('tablename' => 'msm_person'))->id;

        $authorCompRecords = $DB->get_records('msm_compositor', array('table_id' => $authorTableid, 'msm_id' => $msmid), 'unit_id');

        foreach ($authorCompRecords as $authorComp)
        {
            $currentAuthor = new Person();
            if ($prevUnitID != $authorComp->unit_id)
            {
                $authorUnit = $DB->get_record('msm_person', array('id' => $authorComp->unit_id, 'type' => 'index'));

                if (!empty($authorUnit))
                {
                    $currentAuthor->id = $authorUnit->id;
                    $currentAuthor->firstname = $authorUnit->firstname;
                    $currentAuthor->lastname = $authorUnit->lastname;
                    $currentAuthor->middlename = $authorUnit->middlename;
                    $currentAuthor->initials = $authorUnit->initials;

                    $currentAuthor->infos = array();
                    $childElements = $DB->get_records('msm_compositor', array('parent_id' => $authorComp->id));

                    foreach ($childElements as $child)
                    {
                        $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;
                        if ($childtablename == 'msm_info')
                        {
                            $info = new MathInfo();
                            $info->loadFromDb($child->unit_id, $child->id);
                            $currentAuthor->infos[] = $info;
                        }
                    }

                    $currentAuthor->parents = array();
                    $parentElements = $DB->get_records('msm_compositor', array('id' => $authorComp->parent_id));

                    foreach ($parentElements as $parent)
                    {
                        $parentTablename = $DB->get_record('msm_table_collection', array('id' => $parent->table_id))->tablename;

                        if ($parentTablename == 'msm_def')
                        {
                            $def = new Definition();
                            $def->loadFromDb($parent->unit_id, $parent->id);
                            $currentAuthor->parents[] = $def;
                        }
                        else if ($parentTablename == 'msm_theorem')
                        {
                            $theorem = new Theorem();
                            $theorem->loadFromDb($parent->unit_id, $parent->id);
                            $currentAuthor->parents[] = $theorem;
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
                                    $currentAuthor->parents[] = $unit;
                                }
                                else if ($parentUnit->table_id == $theoremTable)
                                {
                                    $theorem = new Theorem();
                                    $theorem->loadFromDb($parentUnit->unit_id, $parentUnit->id);
                                    $currentAuthor->parents[] = $theorem;
                                }
                                else if ($parentUnit->table_id == $statementTheoremTable)
                                {
                                    $TheoremElement = $DB->get_record('msm_compositor', array('id' => $parentUnit->parent_id));

                                    $theorem = new Theorem();
                                    $theorem->loadFromDb($TheoremElement->unit_id, $TheoremElement->id);
                                    $currentAuthor->parents[] = $theorem;
                                }
                            }
                        }
                    }
                    $authors->children[] = $currentAuthor;
                }
            }
            $prevUnitID = $authorComp->unit_id;
        }
        $this->sortTree($authors, 'lastname');

        return $authors;
    }

    function displayGlossary($glossaryTree)
    {
        global $DB;
//        
//        print_object($glossaryTree);
//        die;

        $content = '';

        if ($glossaryTree->text != 'root')
        {
            $content .= "<li><span>" . $glossaryTree->text . "     </span>";

            if (sizeof($glossaryTree->infos) > 0)
            {
                foreach ($glossaryTree->infos as $info)
                {
                    $content .= "<a id='glossaryinfo-" . $info->compid . "' class='msm_infobutton' onmouseover='infoopen(" . $info->compid . ")'>i</a>";
                    $content .= $info->displayhtml();
                }
                foreach ($glossaryTree->parents as $parent)
                {
                    $content .= "<div class='glossaryrefcontent' id='glossaryrefcontent-" . $info->compid . "' style='display:none;'>";
                    $content .= $parent->displayhtml();
                    $content .= "</div>";
                }
            }
            else
            {
                foreach ($glossaryTree->parents as $parent)
                {
                    $content .= "<a id='glossaryinfo-" . $parent->compid . "' class='msm_infobutton' onmouseover='infoopen(" . $parent->compid . ")'>i</a>";
                    $content .= "<div class='glossaryrefcontent' id='glossaryrefcontent-" . $parent->compid . "' style='display:none;'>";
                    $content .= $parent->displayhtml();
                    $content .= "</div>";
                }
            }

            if (sizeof($glossaryTree->children) > 0)
            {
                $content .= "<ul class='chilren'>";
                foreach ($glossaryTree->children as $child)
                {
                    $content .= $this->displayGlossary($child);
                }
                $content .= "</ul>";
                $content .= "</li>";
            }
            else
            {
                $content .= "</li>";
            }
        }
        else
        {
            foreach ($glossaryTree->children as $child)
            {
                $content .= $this->displayGlossary($child);
            }
        }



//        $content = '';
//
//        foreach ($glossaryTree->children as $key => $term)
//        {
//            if (!empty($term->text))
//            {
//                // it has a info box associated with the word
//                if (sizeof($term->infos) > 0)
//                {
//                    if (sizeof($term->infos) > 1)
//                    {
//                        $content .= "<li><span>" . $term->text . "     </span>";
//                        foreach ($term->infos as $info)
//                        {
//                            $currentIndexCompid = $DB->get_record('msm_compositor', array('id' => $info->compid))->parent_id;
//                            $content .= "<a id='glossaryinfo-" . $currentIndexCompid . "' class='msm_infobutton' onmouseover='infoopen(" . $currentIndexCompid . ")'>i</a>";
//                            $content .= $info->displayhtml();
//                        }
//
//                        if (!empty($term->parents))
//                        {
//                            $content .= "<div class='glossaryrefcontent' id='glossaryrefcontent-" . $currentIndexCompid . "' style='display:none;'>";
//                            foreach ($term->parents as $parent)
//                            {
//                                $content .= $parent->displayhtml();
//                            }
//                            $content .= "</div>";
//                        }
//
//
//                        $content .= "<ul>";
//                        $content .= $this->displayGlossary($term);
//                        $content .= "</ul>";
//                        $content .= "<li>";
//                    }
//                     $currentIndexCompid = $DB->get_record('msm_compositor', array('id' => $term->infos[0]->compid))->parent_id;
//                    // this word has a child category
//                    if (sizeof($term->children) > 0)
//                    {
//                        $content .= "<li><span>" . $term->text . "     </span><a id='glossaryinfo-" . $currentIndexCompid . "' class='msm_infobutton' onmouseover='infoopen(" . $currentIndexCompid . ")'>i</a>";
//                        $content .= "<ul>";
//                        $content .= $this->displayGlossary($term);
//                        $content .= "</ul>";
//                        $content .= "<li>";
//                        $content .= $term->infos[0]->displayhtml();
//
//                        if (!empty($term->parents))
//                        {
//                            $content .= "<div class='glossaryrefcontent' id='glossaryrefcontent-" . $currentIndexCompid . "' style='display:none;'>";
//                            foreach ($term->parents as $parent)
//                            {
//                                $content .= $parent->displayhtml();
//                            }
//                            $content .= "</div>";
//                        }
//                    }
//                    // empty children
//                    else
//                    {
//                        $content .= "<li><span>" . $term->text . "   </span><a id='glossaryinfo-" . $currentIndexCompid . "' class='msm_infobutton' onmouseover='infoopen(" . $currentIndexCompid . ")'>i</a></li>";
//                        $content .= $term->infos[0]->displayhtml();
//
//                        if (!empty($term->parents))
//                        {
//                            $content .= "<div class='glossaryrefcontent' id='glossaryrefcontent-" . $currentIndexCompid . "' style='display:none;'>";
//                            foreach ($term->parents as $parent)
//                            {
//                                $content .= $parent->displayhtml();
//                            }
//                            $content .= "</div>";
//                        }
//                    }
//                }
//                else // no info box associated with this word
//                {
//                    // this word has a child category
//                    if (sizeof($term->children) > 0)
//                    {
//                        $content .= "<li><span>" . $term->text . "</span>";
//
//                        if (!empty($term->parents))
//                        {
//                            $content .= "<div class='glossaryrefcontent' id='glossaryrefcontent-" . $term->parents[0]->compid . "' style='display:none;'>";
//                            foreach ($term->parents as $parent)
//                            {
//                                $content .= $parent->displayhtml();
//                            }
//                            $content .= "</div>";
//                        }
//
//                        $content .= "<ul>";
//                        $content .= $this->displayGlossary($term);
//                        $content .= "</ul>";
//                        $content .= "<li>";
//                    }
//                    // empty children
//                    else
//                    {
//                        $content .= "<li><span>" . $term->text . "</span></li>";
//                        if (!empty($term->parents))
//                        {
//                            $content .= "<div class='glossaryrefcontent' id='glossaryrefcontent-" . $term->parents[0]->compid . "' style='display:none;'>";
//                            foreach ($term->parents as $parent)
//                            {
//                                $content .= $parent->displayhtml();
//                            }
//                            $content .= "</div>";
//                        }
//                    }
//                }
//            }
//        }
        return $content;
    }

    function displaySymbol($symbolNode)
    {
        global $DB;

        $content = '';
        foreach ($symbolNode->children as $symbol)
        {
            if (sizeof($symbol->infos) != 0)
            {
                $content .= "<li><span>" . $symbol->symbol . "     </span><a id='symbolinfo-" . $symbol->infos[0]->compid . "' class='msm_infobutton' onmouseover='infoopen(" . $symbol->infos[0]->compid . ")'>i</a></li>";

                $content .= $symbol->infos[0]->displayhtml();

                $content .= "<div class='symbolrefcontent' id='symbolrefcontent-" . $symbol->infos[0]->compid . "' style='display:none;'>";
                foreach ($symbol->parents as $parent)
                {
                    $content .= $parent->displayhtml();
                }
                $content .= "</div>";
            }
            else
            {
                $content .= "<li><span>" . $symbol->symbol . "</span></li>";
            }
        }
        return $content;
    }

    function displayAuthor($authorNode)
    {
        global $DB;

        $content = '';
        foreach ($authorNode->children as $author)
        {
            if (sizeof($author->infos) != 0)
            {
                if (empty($author->middlename))
                {
                    $author->middlename = '';
                }
                $content .= "<li><span>" . $author->lastname . ", " . $author->firstname . " " . $author->middlename . "     </span><a id='authorinfo-" . $author->infos[0]->compid . "' class='msm_infobutton' onmouseover='infoopen(" . $author->infos[0]->compid . ")'>i</a></li>";

                $content .= $author->infos[0]->displayhtml();

                $content .= "<div class='authorrefcontent' id='authorrefcontent-" . $author->infos[0]->compid . "' style='display:none;'>";
                foreach ($author->parents as $parent)
                {
                    $content .= $parent->displayhtml();
                }
                $content .= "</div>";
            }
            else
            {
                $content .= "<li><span>" . $author->lastname . ", " . $author->firstname . " " . $author->middlename . "</span></li>";
            }
        }
        return $content;
    }

    function makeSymbolPanel($msmid)
    {
        $content = '';

        $content .= "<div id='symbolpanel' class='panel'>";
        $content .="<div class='symbolpanelcontent' id='symbolcontent'>";
        $content .= "<h3> S Y M B O L S </h3>";
        $content .= '<ul id="symbolindex" class="treeview-red">';

        $symbolList = $this->loadSymbolFromDb($msmid);
        $content .= $this->displaySymbol($symbolList);

        $content .= "</ul>";
        $content .="</div>"; // end of slidepanelcontent
        $content .= "</div>"; // end of panel

        return $content;
    }

    function makeGlossaryPanel($msmid)
    {
        $content = '';

        $content .= "<div id='glossarypanel' class='panel'>";
        $content .="<div class='glossarypanelcontent' id='glossarycontent'>";
        $content .= "<h3> G L O S S A R Y </h3>";
        $content .= '<ul id="glossaryindex" class="treeview-red">';

        $glossaryTree = $this->loadGlossaryFromDb($msmid);
        $content .= $this->displayGlossary($glossaryTree);

        $content .= "</ul>";
        $content .="</div>"; // end of slidepanelcontent
        $content .= "</div>"; // end of panel

        return $content;
    }

    function makeAuthorPanel($msmid)
    {
        $content = '';

        $content .= "<div id='authorpanel' class='panel'>";
        $content .="<div class='authorpanelcontent' id='authorcontent'>";
        $content .= "<h3> A U T H O R S </h3>";
        $content .= '<ul id="authorindex" class="treeview-red">';

        $authorTree = $this->loadAuthorFromDb($msmid);
        $content .= $this->displayAuthor($authorTree);

        $content .= "</ul>";
        $content .="</div>"; // end of slidepanelcontent
        $content .= "</div>"; // end of panel

        return $content;
    }

    /**
     * 
     * @param GlossaryNode $finalTree       represents the root element of the tree created in loadGlossaryFromDb function
     *                                      using createTree method.
     */
    private function sortTree($finalTree, $compareObject)
    {
        $comparisonArray = array();
        foreach ($finalTree->children as $child)
        {
            $comparisonArray[] = strtolower($child->$compareObject);
        }

        sort($comparisonArray);

        foreach ($comparisonArray as $key => $text)
        {
            foreach ($finalTree->children as $index => $subNode)
            {
                if (strtolower($subNode->$compareObject) == $text)
                {
                    $temp = $finalTree->children[$key];
                    $finalTree->children[$key] = $subNode;
                    $finalTree->children[$index] = $temp;
                }
            }
        }
    }

    private function createTree($children, $ancestors, $parentNode, $termArray)
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
                    $this->createTree($children, $ancestors, $parentChild, $termArray);
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

                $this->createTree($children, $ancestors, $childNode, $termArray);
            }
        }
        else
        {
            if ($termLength >= 1)
            {
                $term = array_shift($termArray);

                if (!empty($term))
                {
                    echo "term and nodetext\n";
                    print_object($parentNode->text);
                    print_object($term);
                    
                    if ($parentNode->text != $term)
                    {
                        echo "not the same node\n";
                        $childNode = new GlossaryNode();
                        $childNode->text = $term;
                        $parentNode->children[] = $childNode;

                        $this->createTree($children, $ancestors, $childNode, $termArray);
                    }
                    else
                    {
                        echo "same text and term\n";
                        
                        foreach ($children as $child)
                        {
                            $parentNode->infos[] = $child;
                        }

                        foreach ($ancestors as $ancestor)
                        {
                            $parentNode->parents[] = $ancestor;
                        }
                    }
                }
                else
                {
                    foreach ($children as $child)
                    {
                        $parentNode->infos[] = $child;
                    }
                    foreach ($ancestors as $ancestor)
                    {
                        $parentNode->parents[] = $ancestor;
                    }
                }
            }
            else
            {
                return false;
            }
        }
        $child = null;
        $ancestor = null;
    }

}

?>
