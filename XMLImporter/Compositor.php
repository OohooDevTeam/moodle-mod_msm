<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

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
        $this->childs = array();
        $childs = array();

        array_push($childs, $DomElement);

        $unittableid = $DB->get_record('msm_table_collection', array('tablename' => 'msm_unit'))->id;

        $unitRecords = $DB->get_records('msm_compositor', array('table_id' => $unittableid, 'parent_id' => $DomElement->id), 'prev_sibling_id');
        // prev_sibling_id not the best way to order things... may need to process this to get the order

        foreach ($unitRecords as $unitRecord)
        {
            foreach ($this->makeStack($unitRecord) as $child)
            {
                array_push($childs, $child);
            }
        }

        return $childs;
    }

    function loadAndDisplay($string)
    {
//        echo "in loadAndDisplay";
        global $DB;

//        $this->displayunits = array();

        $newstring = '';
        $stack = array();

        //recreating stack from string
        $eachRecordString = explode(',', $string);

        $recordLength = count($eachRecordString) - 1;

        // foreach not used because need to eliminate empty array at the end due to ending comma when the string is made
        for ($i = 0; $i < $recordLength; $i++)
        {
            array_push($stack, $eachRecordString[$i]);
        }

        $recordValue = array_pop($stack);

        $recordids = explode('/', $recordValue);

        $unitRecord = $DB->get_record('msm_unit', array('id' => $recordids[1]));

        $unitid = $unitRecord->id;
        $unitcompid = $recordids[0];

        $unit = new Unit();
        $unit->loadFromDb($unitid, $unitcompid);
        $this->unit = $unit;
//        $this->displayunits[] = $unit;
//        end($this->displayunits);
//        $displayunitkey = key($this->displayunits);
        $content = '';

        $content .= "<div class=unit>";
        $content .= $this->unit->displayhtml();

        foreach ($stack as $key => $record)
        {
            $newstring .= $record . ",";
        }
        // passing contents of stack to ajax call by putting it into an hidden input field
        ?>

        <script type="text/javascript">
            $(document).ready(function() {
                var stackstring = "<?php echo $newstring; ?>";
                $('.unit').append('<input id="stack" type="text" name="stackstring"/>');
                $('#stack').val(stackstring); 
            });
                                            
        </script>

        <?php
        $content .= "</div>";
        return $content;
    }

}
?>
