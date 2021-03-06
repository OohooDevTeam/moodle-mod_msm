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
require_once("Element.php");
require_once("Person.php");
require_once("Unit.php");

/**
 * This class is the only class that does not inherit from the Element abstract class.
 * Compositor class is responsible for keeping the structure specified by the XML files
 * when retriveing data from database and displaying the result.  It creates a stack
 * that contains all the Unit objects in order which contains information about its child
 * elements.  Then the stack is used to display prev and next slides in view.php by popping
 * and pushing elements from and into the stack.  The values in these stacks are stored in 
 * hidden input field in view.php.
 *
 * @author Ga Young Kim
 */
class Compositor
{
    public $unit;                   // Unit objects that are associated with this composition

    /**
     * constructor for this class
     */
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

    /**
     * This method is called by an AJAX call in jquery.jshowoff.js whenever the user triggers either the prev or the next button 
     * in the slideshow plugin.  It takes the values in hidden input fields representing previous/next/current units to navigate through the 
     * strcuture given by the XML file.  This method calls loadFromDb and displayhtml functions from Unit class which starts the process of
     * displaying the composition.
     * 
     * @global moodle_database $DB
     * @param string $previousString            hidden input values representing previous units (eg. compid/unitid, compid/unitid...etc format)
     * @param string $nextString                hidden input values representing units that come after current unit in same format as above
     * @param string $current                   hidden input value that has database ID in msm_compositor and database ID in msm_unit of current unit
     * @param string $functionString            hidden input value that specifies the action user chose in slideshow plugin (eg. previous/ next triggered);
     * @return string                           HTML code that is used to view current unit data and associated child elements
     */
    function loadAndDisplay($previousString, $nextString, $current, $functionString)
    {
        global $DB;

        $prevStack = array();
        $nextStack = array();

        $content = '';
        $nextRecordString = '';
        $prevRecordString = '';
        // grabbing each compositor record passed in input field
        $eachPrevString = explode(',', $previousString);
        $eachNextString = explode(',', $nextString);

        if (empty($previousString))
        {
            $prevStackLength = 0;
        }
        else
        {
            $prevStackLength = count($eachPrevString);
        }

        if (empty($nextString))
        {
            $nextStackLength = 0;
        }
        else
        {
            $nextStackLength = count($eachNextString);
        }

        for ($i = 0; $i < $prevStackLength; $i++)
        {
            array_push($prevStack, $eachPrevString[$i]);
        }

        for ($i = 0; $i < $nextStackLength; $i++)
        {
            array_push($nextStack, $eachNextString[$i]);
        }

        if ($functionString == 'previous')
        {
            // add the current value into the next stack
            if (!empty($current))
            {
                array_push($nextStack, $current);
            }
            // if the previous stack is empty then we are at the beginning of the document
            // which means we must navigate to the end of the book.
            // Therefore, the stack containing next record ids must be reversed and stored as previous record values
            if (empty($prevStack))
            {
                $tempArray = array_reverse($nextStack);
                $currentRecord = array_pop($tempArray);

                $prevStack = null;

                foreach ($tempArray as $recordCopy)
                {
                    $prevStack[] = $recordCopy;
                }

                $nextStack = null;
            }
            else
            {
                $currentRecord = array_pop($prevStack);
            }
        }
        else if ($functionString == 'next')
        {
            if (!empty($current))
            {
                array_push($prevStack, $current);
            }

            if (empty($nextStack))
            {
                $tempArray = array_reverse($prevStack);

                $currentRecord = array_pop($tempArray);

                // has an empty record in it, so reinitializing it
                $nextStack = null;

                foreach ($tempArray as $recordCopy)
                {
                    $nextStack[] = $recordCopy;
                }

                $prevStack = null;
            }
            else
            {
                $currentRecord = array_pop($nextStack);
            }
        }
        //the first time the app is opened, no function string given
        else if (empty($functionString))
        {
            $currentRecord = array_pop($nextStack);
        }
        else if ($functionString == 'toc')
        {
            $currentRecord = $current;
        }

        $recordIds = explode('/', $currentRecord);
        $unitRecord = $DB->get_record('msm_unit', array('id' => $recordIds[1]));

        $unitId = $unitRecord->id;
        $compId = $recordIds[0];

        $unit = new Unit();
        $unit->loadFromDb($unitId, $compId);
        $this->unit = $unit;

        $content .= "<div class='unit'>";
        $content .= $this->unit->displayhtml();

        $nextStackSize = count($nextStack);
        $prevStackSize = count($prevStack);

        for ($i = 0; $i < $nextStackSize - 1; $i++)
        {
            $nextRecordString .= $nextStack[$i] . ',';
        }

        if ($nextStackSize - 1 >= 0)
        {
            if (!empty($nextStack[$nextStackSize - 1]))
            {
                $nextRecordString .= $nextStack[$nextStackSize - 1];
            }
        }

        for ($i = 0; $i < $prevStackSize - 1; $i++)
        {
            $prevRecordString .= $prevStack[$i] . ',';
        }

        if ($prevStackSize - 1 >= 0)
        {
            if (!empty($prevStack[$prevStackSize - 1]))
            {
                $prevRecordString .= $prevStack[$prevStackSize - 1];
            }
        }
        ?>

        <script type="text/javascript">
            $(document).ready(function() {
                var stackstring = "<?php echo $nextRecordString; ?>";
                $('.unit').append('<input id="stack" type="text" name="stackstring" style="visibility:hidden;"/>');

                //                                $('.unit').append('<input id="stack" type="text" name="stackstring"/>'); 
                $('#stack').val(stackstring); 
                                                                                                                                                                                                        
                var currentString = "<?php echo $currentRecord; ?>";
                $('.unit').append('<input id="current" type="text" name="currentvalue" style="visibility:hidden;"/>');
                //                                $('.unit').append('<input id="current" type="text" name="currentvalue"/>');
                $('#current').val(currentString);
                                                                                                                                                                                                                                                                                                        
                var prevString = "<?php echo $prevRecordString; ?>";
                $('.unit').append('<input id="prevstack" type="text" name="prevstackstring" style="visibility:hidden;"/>');
                //                                $('.unit').append('<input id="prevstack" type="text" name="prevstackstring"/>');
                $('#prevstack').val(prevString);
                                                                                                                                                                                                                                                        
                var functionstring = "<?php echo $functionString; ?>";
                $('.unit').append('<input id="functioninput" type="text" name="functionstring" style="visibility:hidden;"/>');
                //                                $('.unit').append('<input id="functioninput" type="text" name="functionstring"/>');
                $('#functioninput').val(functionstring); 
            });
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
        </script>

        <?php
        $content .= "</div>";
        return $content;
    }

}
?>
