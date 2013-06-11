<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("Unit.php");

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

/**
 * This class has only one purpose and it is to make a table of contents.  It finds all the unit records from the compositor table that
 * are not a reference material(i.e. the field "standalone" is false.) and get the only title data to be displayed into the table of content and also 
 * retrieve the structure of the units themselves as well from the Compositor table.  Then these unit titles are organized into identical structure as 
 * the one in the compositor table then is displayed in form of a unordered list which is styled to be a collapsable tree.  The final product of this class
 * is displayed in view.php
 *
 * @author Ga Young Kim
 */
class TableOfContents
{

    function __construct()
    {
        $this->unittable = 'msm_unit';
        $this->comptable = 'msm_compositor';
        $this->tabletable = 'msm_table_collection';
    }

    function makeToc($msm_id)
    {
        global $DB;

        $this->unitData = array();

        foreach ($this->getTocData('root', $msm_id) as $unitTitleArray)
        {
            $this->unitData[] = $unitTitleArray;
        }


        $content = '';
        $content .= "<h3> Table Of Contents </h3>";
        $content .= '<div id="treecontrol">
                        <a title="Collapse the entire tree below" href="#"> Collapse All</a> | 
                        <a title="Expand the entire tree below" href="#"> Expand All</a> | 
                        <a title="Toggle the tree below, opening closed branches, closing open branches" href="#">Toggle All</a>
                     </div>';
        $content .= '<ul id="tableofcontent" class="treeview-red">';

        $content .= $this->makeTree($this->unitData);
        $content .= '</ul>';

        return $content;
    }

    private function makeTree($TitlesArray)
    {
        $newString = '';

        foreach ($TitlesArray as $key => $unitTitle)
        {
            if (gettype($unitTitle) == 'object')
            {
                if ($key + 1 < sizeof($TitlesArray))
                {
                    if (gettype($TitlesArray[$key + 1]) == 'object')
                    {
                        $newString .= "<li><a onclick='navToPage($unitTitle->id)'>" . trim($unitTitle->title) . "</a></li>";
                    }
                    else
                    {
                        $newString .= "<li><a onclick='navToPage($unitTitle->id)'>" . trim($unitTitle->title) . "</a>";
                    }
                }
                else
                {
                    $newString .= "<li><a onclick='navToPage($unitTitle->id)'>" . trim($unitTitle->title) . "</a></li>";
                }
            }
            elseif (gettype($unitTitle) == 'array')
            {
                $newString .= "<ul>";

                $newString .= $this->makeTree($unitTitle);

                $newString .= "</ul>";
                $newString .= "</li>";
            }
        }
//
        return $newString;
    }

    private function getTocData($compRecord, $msm_id)
    {
        global $DB;
        $unitData = array();

        $unittableid = $DB->get_record($this->tabletable, array('tablename' => 'msm_unit'))->id;

        if ($compRecord == 'root')
        {
            $unitElements = $DB->get_records($this->comptable, array('parent_id' => 0, 'table_id' => $unittableid, 'msm_id'=>$msm_id), 'prev_sibling_id');
        }
        else
        {
            $unitElements = $DB->get_records($this->comptable, array('parent_id' => $compRecord->id, 'table_id' => $unittableid, 'msm_id'=>$msm_id), 'prev_sibling_id');
        }

        foreach ($unitElements as $unit)
        {
            $unitRecord = $DB->get_record($this->unittable, array('id' => $unit->unit_id));

            if ($unitRecord->standalone == 'false')
            {
                $unitData[] = $unitRecord;
            }

            $childElements = $DB->get_records($this->comptable, array('parent_id' => $unit->id, 'table_id' => $unittableid, 'msm_id'=>$msm_id));
            if (sizeof($childElements) > 0)
            {
                $unitData[] = $this->getTocData($unit, $msm_id);
            }
        }

        return $unitData;
    }

}

?>
