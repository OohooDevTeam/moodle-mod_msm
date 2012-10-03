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
require_once("Element.php");
require_once("Person.php");
require_once("Unit.php");

/**
 * Description of Compositor
 *
 * @author User
 */
class Compositor
{

    public $unit;

//    public $displayunits = array();
//    public $theorem;

    function __construct()
    {
        $this->tablename = "msm_compositor";
    }

    /**
     * This is a method to create a stack that has all the unit elements in compositor table in order they are specified in the compositor.  
     * The returned stack is needed to dynamically load each unit element for display.
     * 
     * @global moodle_database $DB
     * @param DOMElement $DomElement
     * @return array 
     */
    function makeStack($DomElement)
    {
        global $DB;

        // stack that will have all the unit records in order given by the compositor table
//        $this->childs = array();
        $childs = array();

        array_push($childs, $DomElement);

        $unittableid = $DB->get_record('msm_table_collection', array('tablename' => 'msm_unit'))->id;

        $unitRecords = $DB->get_records('msm_compositor', array('table_id' => $unittableid, 'parent_id' => $DomElement->id), 'prev_sibling_id');
        // prev_sibling_id not the best way to order things... may need to process this to get the order

        foreach ($unitRecords as $unitRecord)
        {
            $checkedUnit = $DB->get_record('msm_unit', array('id' => $unitRecord->unit_id));
            if ($checkedUnit->standalone == 'false')
            {
                foreach ($this->makeStack($unitRecord) as $child)
                {
                    array_push($childs, $child);
                }
            }
        }

        return $childs;
    }

//    function loadAndDisplay($prevstring, $string)
    function loadAndDisplay($previousstring, $string, $current, $functionString)
    {
        global $DB;

        $newstring = '';
        $beforeString = '';
        $content = '';
        $stack = array();
        $prevstack = array();

        //recreating stack from string
        $eachRecordString = explode(',', $string);

        $eachPrevRecordString = explode(',', $previousstring);

        if (empty($string))
        {
            $recordLength = 0;
        }
        else
        {
            $recordLength = count($eachRecordString);
        }

        if (empty($previousstring))
        {
            $prevRecordLength = 0;
        }
        else
        {
            $prevRecordLength = count($eachPrevRecordString);
        }

        // foreach not used because need to eliminate empty array at the end due to ending comma when the string is made
        for ($i = 0; $i < $recordLength; $i++)
        {
            array_push($stack, $eachRecordString[$i]);
        }

        for ($i = 0; $i < $prevRecordLength; $i++)
        {
            array_push($prevstack, $eachPrevRecordString[$i]);
        }
        
        if ($functionString == 'previous')
        {
            $recordValue = array_pop($prevstack);
            if (!empty($recordValue))
            {               
                array_push($stack, $current);
            }
        }
        
        else if($functionString == 'newprev')
        {
            $recordValue = array_pop($prevstack);
            $tempprevstack = array();
            $tempprevstack[0] = $current;
            $i = 1;
            foreach($prevstack as $prevrecord)
            {
                $tempprevstack[$i] = $prevrecord;
                $i++;
            }
            
            $prevstack = array();
            foreach($tempprevstack as $tempRecord)
            {
                $prevstack[] = $tempRecord;
            }
        }
        // it's the first page?
        else if (empty($functionString))
        {
//            echo "in this condition";
            $recordValue = array_pop($stack);
        }
        else if ($functionString == 'next')
        {
            $recordValue = array_pop($stack);

//            print_object($recordValue);
            if (!empty($recordValue))
            {
                array_push($prevstack, $current);
            }
        }
//
//        echo "stack: ";
//        print_object($stack);
//
//        echo "prev: ";
//        print_object($prevstack);

        $recordids = explode('/', $recordValue);

        $unitRecord = $DB->get_record('msm_unit', array('id' => $recordids[1]));

        $unitid = $unitRecord->id;
        $unitcompid = $recordids[0];


        $unit = new Unit();
        $unit->loadFromDb($unitid, $unitcompid);
        $this->unit = $unit;
//                $content = '';
        $content .= "<div class='unit'>";
        $content .= $this->unit->displayhtml();

        for ($i = 0; $i < sizeof($stack) - 1; $i++)
        {
            $newstring .= $stack[$i] . ",";
        }
        if (sizeof($stack) > 0)
        {
            $newstring .= $stack[sizeof($stack) - 1];
        }

        for ($i = 0; $i < sizeof($prevstack) - 1; $i++)
        {
            $beforeString .= $prevstack[$i] . ",";
        }
        if (sizeof($prevstack) > 0)
        {
            $beforeString .= $prevstack[sizeof($prevstack) - 1];
        }
        // passing contents of stack to ajax call by putting it into an hidden input field
        ?>

        <script type="text/javascript">
            $(document).ready(function() {
                var stackstring = "<?php echo $newstring; ?>";
                $('.unit').append('<input id="stack" type="text" name="stackstring"/>');
                $('#stack').val(stackstring); 
                                                                                                
                var currentString = "<?php echo $recordValue; ?>";
                $('.unit').append('<input id="current" type="text" name="currentvalue"/>');
                $('#current').val(currentString);
                                                                                                                                                                                                
                var prevString = "<?php echo $beforeString; ?>";
                $('.unit').append('<input id="prevstack" type="text" name="prevstackstring"/>');
                $('#prevstack').val(prevString);
                                                                                                                                                
                var functionstring = "";
                $('.unit').append('<input id="functioninput" type="text" name="functionstring"/>');
                $('#functioninput').val(functionstring); 
            });
                                                                                                                                                                                                                                                                                                                                                                    
        </script>

        <?php
        $content .= "</div>";
        return $content;

        return $content;
    }

}
?>
