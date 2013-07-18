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
 * This class represents all the index.author/index.glossary/index.symbol XML elements in the legacy document
 * (ie. files in the newXML) and it is generally called by all classes with content by Element class methods when
 * processing contents.  These elements represented by this class give extra information about author/glossary terms/symbols hot linked 
 * to trigger a popup when mouse is hovered and if it has reference material attached to it, then it will appear on the
 * right panel to be viewed. MathIndex class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class MathIndex extends Element
{

    public $id;                     // database ID associated with this index element in its appropriate table 
    // (author --> msm_person, glossary--> msm_index_glossary, symbol--> msm_index_symbol)
    public $compid;                 // database ID associated with this index element in msm_compositor table
    public $position;               // integer that keeps track of order of elements
    public $term;                   // term element associated with index.glossary element
    public $symbol;                 // symbol element associated with index.symbol element
    public $symbol_type;            // type of symbols associated with index.symbol element
    public $sortstring;             // text associted with index.symbol element that aid the sorting of symbols
    public $infos = array();        // MathInfo objects associated with this index element
    public $names = array();        // First/miidle/initials/last names associated with the index.author

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->glossarytable = 'msm_index_glossary';
        $this->symboltable = 'msm_index_symbol';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (index.author/index.glossary/index.symbol
     * element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        index.author/index.glossary/index.symbol elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \MathIndex
     */
    public function loadFromXml($DomElement, $position = '')
    {
        if ($DomElement != null)
        {
            $this->position = $position;

            $nameofElement = $DomElement->tagName;

            switch ($nameofElement)
            {
                // the element that was parsed is index.symbol
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
                // the element that was parsed is index.symbol
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
                // the element that was parsed is index.symbol
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
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of index.author/index.glossary/index.symbol element into
     * msm_person/msm_index_glossary/msm_index_symbol database tables, respectively.  
     * 
     * @global moodle_databse $DB
     * @param int $position              integer that keeps track of order if elements
     * @param int $msmid                 MSM instance ID
     * @param int $parentid              ID of the parent element from msm_compositor
     * @param int $siblingid             ID of the previous sibling element from msm_compositor
     */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();
        $sibling_id = $siblingid;

        // processing index.symbol elements
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

        // processing index.author elements
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
        // processing index.glossary elements
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
            }
            $sibling_id = $this->compid;
        }

        $sibling_id = 0;
        foreach ($this->infos as $info)
        {
            $numOfRecords = $DB->count_records('msm_info');
            if ($numOfRecords > 0)
            {
                // need the limit to be $numOfRecords+1 to process the last record
                for ($i = 1; $i < $numOfRecords + 1; $i++)
                {
                    $string = $DB->get_field('msm_info', 'info_content', array('id' => $i));

                    if (trim($string) == trim($info->info_content))
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
                $sibling_id = $info->compid;
            }
            else
            {
                $info->compid = $this->insertToCompositor($recordID, $info->tablename, $msmid, $this->compid, $sibling_id);
                $sibling_id = $info->compid;
            }
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the index.glossary element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from Defintion/Theorem/MathInfo/Unit classes
     * are also called by this method.  This method also calls the private method createTree and starts the 
     * recursive process of making the glossary node tree.  findParentUnit method is also called to get the parent unit object
     * of current glossary index.  For more information read the comments below about these methods.
     * 
     * @global moodle_database $DB
     * @param int $msmid                    // MSM module instance ID
     * @return \GlossaryNode
     */
    function loadGlossaryFromDb($msmid)
    {
        global $DB;

        $glossaryTable = $DB->get_record('msm_table_collection', array('tablename' => 'msm_index_glossary'))->id;
        $glossaryCompRecords = $DB->get_records('msm_compositor', array('table_id' => $glossaryTable, 'msm_id' => $msmid), 'unit_id');

        $rootNode = new GlossaryNode();
        $rootNode->text = 'root';
        $rootNode->depth = 0;

        $infos = array();
        $parents = array();

        foreach ($glossaryCompRecords as $glossaryComp)
        {
            $glossaryUnit = $DB->get_record('msm_index_glossary', array('id' => $glossaryComp->unit_id));
            $infotable = $DB->get_record('msm_table_collection', array('tablename' => 'msm_info'))->id;
            $childElements = $DB->get_records('msm_compositor', array('parent_id' => $glossaryComp->id, 'table_id' => $infotable));
            $parentElements = $DB->get_records('msm_compositor', array('id' => $glossaryComp->parent_id));
            if (!empty($childElements))
            {
                foreach ($childElements as $child)
                {
                    $info = new MathInfo();
                    $info->loadFromDb($child->unit_id, $child->id);
                    $infos[] = $info;
                }
            }


            if (!empty($parentElements))
            {
                foreach ($parentElements as $parent)
                {
                    $parentTablename = $DB->get_record('msm_table_collection', array('id' => $parent->table_id))->tablename;

                    if ($parentTablename == 'msm_def')
                    {
                        $def = new Definition();
                        $def->loadFromDb($parent->unit_id, $parent->id, true);
                        $parents[] = $def;
                    }
                    else if ($parentTablename == 'msm_theorem')
                    {
                        $theorem = new Theorem();
                        $theorem->loadFromDb($parent->unit_id, $parent->id, true);
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
                                $unit->loadFromDb($parentUnit->unit_id, $parentUnit->id, true);
                                $parents[] = $unit;
                            }
                            else if ($parentUnit->table_id == $theoremTable)
                            {
                                $theorem = new Theorem();
                                $theorem->loadFromDb($parentUnit->unit_id, $parentUnit->id, true);
                                $parents[] = $theorem;
                            }
                            else if ($parentUnit->table_id == $statementTheoremTable)
                            {
                                $TheoremElement = $DB->get_record('msm_compositor', array('id' => $parentUnit->parent_id));

                                $theorem = new Theorem();
                                $theorem->loadFromDb($TheoremElement->unit_id, $TheoremElement->id, true);
                                $parents[] = $theorem;
                            }
                            else
                            {
                                $parentRecord = $this->findParentUnit($unitTable, $parentUnit->parent_id);

                                $unit = new Unit();
                                $unit->loadFromDb($parentRecord->unit_id, $parentRecord->id, true);
                                $parents[] = $unit;
                            }
                        }
                    }
                }
            }

            $termpath = explode('/', $glossaryUnit->term);
            array_pop($termpath);

            if ((!empty($infos)) && (!empty($parents)))
            {
                $this->createTree($glossaryComp->id, $infos, $parents, $rootNode, $rootNode, $termpath);
                unset($infos);
                unset($parents);
            }
            else if ((!empty($infos)) && (empty($parents)))
            {
                $this->createTree($glossaryComp->id, $infos, '', $rootNode, $rootNode, $termpath);
                unset($infos);
            }
            else if ((empty($infos)) && (!empty($parents)))
            {
                $this->createTree($glossaryComp->id, '', $parents, $rootNode, $rootNode, $termpath);
                unset($parents);
            }
        }

        $this->sortTree($rootNode, 'text');
        return $rootNode;
    }

    /**
     * This recursive function creates node tree that preserves stucture of each index.glossary elements and its child elements
     * while ordering each index in alphabetical order in tree form.  The categorizing of the glossary
     * indices were done by sortstring values which is a string in similar format to file pathing but
     * it provides pathing to the term in categories.  Another recursive private method sortTree is called to sort the nodes using the
     * sortstring values.
     * 
     * @param int $currentCompid                Database ID of current index element in msm_compositor
     * @param array $children                   An array containing all the child elements of current index element
     * @param array $ancestors                  An array containing all the parent elements of current index element 
     *                                          (ie. has names of the categories)
     * @param GlossaryNode $rootNode            GlossaryNode object that represents the root node of the tree
     * @param GlossaryNode $parentNode          GlossaryNode object that represents the parent node of the current index element
     * @param array $termArray                  Array that contains all the terms of glossary
     */
    private function createTree($currentCompid, $children, $ancestors, $rootNode, $parentNode, $termArray)
    {
        if (!empty($termArray))
        {
            $term = array_shift($termArray);
            $isfound = false;
            if (!empty($parentNode->children)) // parent is not the leaf of the tree
            {
                foreach ($parentNode->children as $currentNode)
                {
                    if ($currentNode->text == $term)
                    {
                        // if end of the array then need to add the infos/parents associated with it
                        if (sizeof($termArray) == 0)
                        {
                            if (!empty($children))
                            {
                                // adding info associated with the term
                                foreach ($children as $child)
                                {
                                    if (!empty($currentNode->infos))
                                    {
                                        foreach ($currentNode->infos as $prevInfo)
                                        {
                                            if ($prevInfo->id == $child->id)
                                            {
                                                break 2;
                                            }
                                        }
                                        $currentinfosize = sizeof($currentNode->infos);
                                        $currentNode->infos[$currentinfosize] = $child;
                                    }
                                    else
                                    {
                                        $currentNode->infos[] = $child;
                                    }
                                }
                            }

                            if (!empty($ancestors))
                            {
                                // adding parent elements associated with the term
                                foreach ($ancestors as $ancestor)
                                {
                                    if (!empty($currentNode->parents))
                                    {
                                        foreach ($currentNode->parents as $prevParent)
                                        {
                                            if ($prevParent->id == $ancestor->id)
                                            {
                                                break 2;
                                            }
                                        }
                                        $currentParentSize = sizeof($currentNode->parents);
                                        $currentNode->parents[$currentParentSize] = $ancestor;
                                    }
                                    else
                                    {
                                        $currentNode->parents[] = $ancestor;
                                    }
                                }
                            }
                        }
                        $this->createTree($currentCompid, $children, $ancestors, $rootNode, $currentNode, $termArray);
                        $isfound = true;
                        break;
                    }
                }
                if (!$isfound)
                {
                    $currentNode = new GlossaryNode();
                    $currentNode->compid = $currentCompid;
                    $currentNode->text = $term;
                    $parentNode->children[] = $currentNode;

                    // if end of the array then need to add the infos/parents associated with it
                    if (sizeof($termArray) == 0)
                    {
                        if (!empty($children))
                        {
                            // adding info associated with the term
                            foreach ($children as $child)
                            {
                                if (!empty($currentNode->infos))
                                {
                                    foreach ($currentNode->infos as $prevInfo)
                                    {
                                        if ($prevInfo->id == $child->id)
                                        {
                                            break 2;
                                        }
                                    }
                                    $currentinfosize = sizeof($currentNode->infos);
                                    $currentNode->infos[$currentinfosize] = $child;
                                }
                                else
                                {
                                    $currentNode->infos[] = $child;
                                }
                            }
                        }

                        if (!empty($ancestors))
                        {
                            // adding parent elements associated with the term
                            foreach ($ancestors as $ancestor)
                            {
                                if (!empty($currentNode->parents))
                                {
                                    foreach ($currentNode->parents as $prevParent)
                                    {
                                        if ($prevParent->id == $ancestor->id)
                                        {
                                            break 2;
                                        }
                                    }
                                    $currentParentSize = sizeof($currentNode->parents);
                                    $currentNode->parents[$currentParentSize] = $ancestor;
                                }
                                else
                                {
                                    $currentNode->parents[] = $ancestor;
                                }
                            }
                        }
                    }

                    $this->createTree($currentCompid, $children, $ancestors, $rootNode, $currentNode, $termArray);
                }
            }
            else // parentNode is a leaf so create new GlossaryNode instance as child of parentNode
            {
                $currentNode = new GlossaryNode();
                $currentNode->compid = $currentCompid;
                $currentNode->text = $term;
                if (!empty($children))
                {
                    foreach ($children as $child)
                    {
                        if (!empty($currentNode->infos))
                        {
                            foreach ($currentNode->infos as $prevInfo)
                            {
                                if ($prevInfo->id == $child->id)
                                {
                                    break 2;
                                }
                            }
                            $currentinfosize = sizeof($currentNode->infos);
                            $currentNode->infos[$currentinfosize] = $child;
                        }
                        else
                        {
                            $currentNode->infos[] = $child;
                        }
                    }
                }

                if (!empty($ancestors))
                {
                    foreach ($ancestors as $ancestor)
                    {
                        if (!empty($currentNode->parents))
                        {
                            foreach ($currentNode->parents as $prevParent)
                            {
                                if ($prevParent->id == $ancestor->id)
                                {
                                    break 2;
                                }
                            }
                            $currentParentSize = sizeof($currentNode->parents);
                            $currentNode->parents[$currentParentSize] = $ancestor;
                        }
                        else
                        {
                            $currentNode->parents[] = $ancestor;
                        }
                    }
                }
                $parentNode->children[] = $currentNode;
                $this->createTree($currentCompid, $children, $ancestors, $rootNode, $currentNode, $termArray);
            }
        }
        $this->sortTree($parentNode, 'text');
    }

    /**
     * This method is used to retrieve all relevant data linked with the index.symbol element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from Defintion/Theorem/Unit/MathInfo classes
     * are also called by this method.  findParentUnit method is also called to get the parent unit object
     * of current glossary index.  For more information read the comments below about this method.
     * 
     * @global moodle_database $DB
     * @param int $msmid                    MSM module instance ID
     * @return \GlossaryNode
     */
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
                        $def->loadFromDb($parent->unit_id, $parent->id, true);
                        $currentSymbol->parents[] = $def;
                    }
                    else if ($parentTablename == 'msm_theorem')
                    {
                        $theorem = new Theorem();
                        $theorem->loadFromDb($parent->unit_id, $parent->id, true);
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
                                $unit->loadFromDb($parentUnit->unit_id, $parentUnit->id, true);
                                $currentSymbol->parents[] = $unit;
                            }
                            else if ($parentUnit->table_id == $theoremTable)
                            {
                                $theorem = new Theorem();
                                $theorem->loadFromDb($parentUnit->unit_id, $parentUnit->id, true);
                                $currentSymbol->parents[] = $theorem;
                            }
                            else if ($parentUnit->table_id == $statementTheoremTable)
                            {
                                $TheoremElement = $DB->get_record('msm_compositor', array('id' => $parentUnit->parent_id));

                                $theorem = new Theorem();
                                $theorem->loadFromDb($TheoremElement->unit_id, $TheoremElement->id, true);
                                $currentSymbol->parents[] = $theorem;
                            }
                            else
                            {
                                $parentRecord = $this->findParentUnit($unitTable, $parentUnit->parent_id);

                                $unit = new Unit();
                                $unit->loadFromDb($parentRecord->unit_id, $parentRecord->id, true);
                                $currentSymbol->parents[] = $unit;
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

    /**
     * This method is used to retrieve all relevant data linked with the index.author element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from Defintion/Theorem/Unit/MathInfo classes
     * are also called by this method.  This method also calls private method sortTree to order the resulting index
     * tree correctly.  findParentUnit method is also called to get the parent unit object
     * of current glossary index.  For more information read the comments below about these methods.
     * 
     * @global moodle_database $DB
     * @param int $msmid                    MSM module instance ID
     * @return \GlossaryNode
     */
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
                            $def->loadFromDb($parent->unit_id, $parent->id, true);
                            $currentAuthor->parents[] = $def;
                        }
                        else if ($parentTablename == 'msm_theorem')
                        {
                            $theorem = new Theorem();
                            $theorem->loadFromDb($parent->unit_id, $parent->id, true);
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
                                    $unit->loadFromDb($parentUnit->unit_id, $parentUnit->id, true);
                                    $currentAuthor->parents[] = $unit;
                                }
                                else if ($parentUnit->table_id == $theoremTable)
                                {
                                    $theorem = new Theorem();
                                    $theorem->loadFromDb($parentUnit->unit_id, $parentUnit->id, true);
                                    $currentAuthor->parents[] = $theorem;
                                }
                                else if ($parentUnit->table_id == $statementTheoremTable)
                                {
                                    $TheoremElement = $DB->get_record('msm_compositor', array('id' => $parentUnit->parent_id));

                                    $theorem = new Theorem();
                                    $theorem->loadFromDb($TheoremElement->unit_id, $TheoremElement->id, true);
                                    $currentAuthor->parents[] = $theorem;
                                }
                                else
                                {
                                    $parentRecord = $this->findParentUnit($unitTable, $parentUnit->parent_id);

                                    $unit = new Unit();
                                    $unit->loadFromDb($parentRecord->unit_id, $parentRecord->id, true);
                                    $currentAuthor->parents[] = $unit;
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

    /**
     * This method is called by loadGlossaryFromDb, loadSymbolFromDb, loadAuthorFromDb methods to find 
     * the parent Unit object of specified ID by parentId paramter.  This unit is then loaded by loadFromDb
     * and added as a parent in the current index GlossaryNode object.
     * 
     * @global moodle_database $DB      
     * @param int $unittableid              Database ID of msm_unit in msm_table_collection
     * @param int $parentId                 Database ID of unit object in msm_compositor
     * @return stdClass                     record from msm_compositor
     */
    private function findParentUnit($unittableid, $parentId)
    {
        global $DB;

        $parentElement = $DB->get_record('msm_compositor', array('id' => $parentId));

        if ($parentElement->table_id == $unittableid)
        {
            return $parentElement;
        }
        else
        {
            $parentElement = $this->findParentUnit($unittableid, $parentElement->parent_id);
        }
        return $parentElement;
    }

    /**
     * This method is called by makeGlossaryPanel function and its main role is to 
     * take the data loaded and organized by above methods and create an HTML code that
     * is consistent with jsTree plugin syntax to create file-tree-like index tree.
     * 
     * @param GlossaryNode $glossaryTree          The GlossaryNode tree that has all the index.glossary data in order
     * @return string
     */
    function displayGlossary($glossaryTree)
    {
        $content = '';

        // current node is a root element, which is ignored
        if ($glossaryTree->text != 'root')
        {
            $content .= "<li><span>" . $glossaryTree->text . "      </span>";

            if ((sizeof($glossaryTree->infos) > 0) && (sizeof($glossaryTree->parents) > 0))
            {
                // takes care of processing when info is larger/equal size to parent array
                foreach ($glossaryTree->infos as $info)
                {
                    $content .= "<a id='glossaryinfo-" . $info->compid . "' class='msm_infobutton' onmouseover='infoopen(" . $info->compid . ")'>i</a>";
                    $content .= $info->displayhtml();

                    // checks that parent array is not empty
                    if (sizeof($glossaryTree->parents) > 0)
                    {
                        $parent = array_shift($glossaryTree->parents);
                        $content .= "<div class='glossaryrefcontent' id='glossaryrefcontent-" . $info->compid . "' style='display:none;'>";
                        $content .= $parent->displayhtml(true);
                        $content .= "</div>";
                    }
                }
                // if the size of the parent array is larger than the info array then there are some left over parents to be processed
                if (sizeof($glossaryTree->parents) > 0)
                {
                    foreach ($glossaryTree->parents as $parent)
                    {
                        $content .= "<a id='glossaryinfo-" . $glossaryTree->compid . "' class='msm_infobutton' onmouseover='infoopen(" . $glossaryTree->compid . ")'>i</a>";
                        $content .= "<div class='glossaryrefcontent' id='glossaryrefcontent-" . $glossaryTree->compid . "' style='display:none;'>";
                        $content .= $parent->displayhtml(true);
                        $content .= "</div>";
                    }
                }
            }
            // in case where parent array is empty
            else if ((sizeof($glossaryTree->infos) > 0) && (sizeof($glossaryTree->parents) == 0))
            {
                foreach ($glossaryTree->infos as $info)
                {
                    $content .= "<a id='glossaryinfo-" . $info->compid . "' class='msm_infobutton' onmouseover='infoopen(" . $info->compid . ")'>i</a>";
                    $content .= $info->displayhtml();
                }
            }
            // in case where info array is empty, then an icon would be created but it will have no associating info box 
            else if ((sizeof($glossaryTree->infos) == 0) && (sizeof($glossaryTree->parents) > 0))
            {
                foreach ($glossaryTree->parents as $parent)
                {
                    $content .= "<a id='glossaryinfo-" . $glossaryTree->compid . "' class='msm_infobutton' onmouseover='infoopen(" . $glossaryTree->compid . ")'>i</a>";
                    $content .= "<div class='glossaryrefcontent' id='glossaryrefcontent-" . $glossaryTree->compid . "' style='display:none;'>";
                    $content .= $parent->displayhtml(true);
                    $content .= "</div>";
                }
            }
            // if the current node of the tree has children, then need to display them in the tree
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
            // no children for the current node, so just close the list item tag
            else
            {
                $content .= "</li>";
            }
        }
        // it is not the root node therefore, process its children
        else
        {
            foreach ($glossaryTree->children as $child)
            {
                $content .= $this->displayGlossary($child);
            }
        }

        return $content;
    }

    /**
     * This method is called by makeSymbolPanel function and its main role is to 
     * take the data loaded and organized by above methods and create an HTML code that
     * is consistent with jsTree plugin syntax to create file-tree-like index tree.
     * 
     * @param GlossaryNode $symbolNode         the GlossaryNode tree that has all the index.symbol data in order
     * @return string
     */
    function displaySymbol($symbolNode)
    {
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
                    $content .= $parent->displayhtml(true);
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

    /**
     * This method is called by makeAuthorPanel function and its main role is to 
     * take the data loaded and organized by above methods and create an HTML code that
     * is consistent with jsTree plugin syntax to create file-tree-like index tree.
     * 
     * @param GlossaryNode $authorNode         the GlossaryNode tree that has all the index.author data in order
     * @return string
     */
    function displayAuthor($authorNode)
    {
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
                    $content .= $parent->displayhtml(true);
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

    /**
     * This method is called by the loadSymbol.php script that creates an
     * HTML file that will be loaded upon trigger by an user in view.php.
     * This method creates the content that will be in the new HTML file
     * and is in format of unordered list type that will be in a file
     * tree structure by jsTree jQuery plugin.  The loadSymbolFromDb and 
     * displaySymbol functions are called by this method.
     * 
     * @param int $msmid                MSM module instance ID
     * @return string
     */
    function makeSymbolPanel($msmid)
    {
        $content = '';
        $content .= "<h3> S Y M B O L S </h3>";
        $content .= '<ul id="symbolindex" class="treeview-red">';
        $symbolList = $this->loadSymbolFromDb($msmid);
        $content .= $this->displaySymbol($symbolList);
        $content .= "</ul>";
        return $content;
    }

    /**
     * This method is called by the loadGlossary.php script that creates an
     * HTML file that will be loaded upon trigger by an user in view.php.
     * This method creates the content that will be in the new HTML file
     * and is in format of unordered list type that will be in a file
     * tree structure by jsTree jQuery plugin.  The loadGlossaryFromDb and 
     * displayGlossary functions are called by this method.
     * 
     * @param int $msmid               MSM module instance ID
     * @return string
     */
    function makeGlossaryPanel($msmid)
    {
        $content = '';
        $content .= "<h3> G L O S S A R Y </h3>";
        $content .= '<ul id="glossaryindex" class="treeview-red">';
        $glossaryTree = $this->loadGlossaryFromDb($msmid);
        $content .= $this->displayGlossary($glossaryTree);
        $content .= "</ul>";
        return $content;
    }

    /**
     * This method is called by the loadAuthor.php script that creates an
     * HTML file that will be loaded upon trigger by an user in view.php.
     * This method creates the content that will be in the new HTML file
     * and is in format of unordered list type that will be in a file
     * tree structure by jsTree jQuery plugin.  The loadAuthorFromDb and 
     * displayAuthor functions are called by this method.
     * 
     * @param int $msmid               MSM module instance ID
     * @return string
     */
    function makeAuthorPanel($msmid)
    {
        $content = '';
        $content .= "<h3> A U T H O R S </h3>";
        $content .= '<ul id="authorindex" class="treeview-red">';
        $authorTree = $this->loadAuthorFromDb($msmid);
        $content .= $this->displayAuthor($authorTree);
        $content .= "</ul>";
        return $content;
    }

    /**
     * This method sorts the given tree to order specified by sortstring or by alphabetical order of
     * chosen object property represented as compareObject parameter.
     * 
     * @param GlossaryNode $finalTree       represents the root element of the tree created in loadGlossaryFromDb function
     *                                           using createTree method.
     * @param mixed $compareObject           property in GlossaryNode object that will be sorted and matched to created Tree
     *                                           represented by above variable
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

}

?>
