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

/**
 * This class represents all the img/image XML elements in the legacy document
 * (ie. files in the newXML) and just img elements in the new XML documents 
 * created from the editor and this class is called by Media class.
 * MathImg class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class MathImg extends Element
{

    public $id;                         // database ID associated with current img element in msm_img table
    public $compid;                     // database ID associated with current img element in msm_compositor table
    public $position;                   // integer that keeps track of order of elements
    public $string_id;                  // unique string ID given to img element by the user in legacy XML (none in new XML)
    public $src;                        // src attribute value with path to image (in database, it's in moodle file serving format--> ../pluginfile.php/..)
    public $height;                     // height attribute associated with this image element
    public $width;                      // width attribute associated with this image element
    public $description;                // description associated with this image element
    public $extended_caption;           // extra information associated with this image
    public $msm_id;                     // MSM module instance
    public $itemid;                     // database ID associated with this image that is served by moodle in mdl_files table
    public $imageareas = array();       // ImgArea objects associated with this image for image mapping

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_img';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (image element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        image elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \MathImg
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->string_id = $DomElement->getAttribute('id');
        $this->src = $DomElement->getAttribute('src');
        $this->height = $DomElement->getAttribute('height');
        $this->width = $DomElement->getAttribute('width');
        $this->description = $this->getDomAttribute($DomElement->getElementsByTagName('description'));
        $this->extended_caption = $this->getContent($DomElement->getElementsByTagName('extended.caption')->item(0));

        $this->imageareas = array();
        $imageMappings = $DomElement->getElementsByTagName('image.mapping');

        foreach ($imageMappings as $im)
        {
            $areas = $im->getElementsByTagName('area');

            foreach ($areas as $a)
            {
                $position = $position + 1;
                $imgArea = new ImgArea($this->xmlpath);
                $imgArea->loadFromXml($a, $position);
                $this->imageareas[] = $imgArea;
            }
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of img element into
     * msm_img database table.  It calls saveInfoDb method for ImgArea class.
     * 
     * @global moodle_database $DB
     * @global type $CFG
     * @param int $position              integer that keeps track of order if elements
     * @param int $msmid                 MSM instance ID
     * @param int $parentid              ID of the parent element from msm_compositor
     * @param int $siblingid             ID of the previous sibling element from msm_compositor
     */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB, $CFG;

        $msm = $DB->get_record('msm', array('id' => $msmid), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);
        $context = context_module::instance($cm->id);


        $data = new stdClass();
        $data->string_id = $this->string_id;

        $sourcefolders = explode('/', $this->src);

        $storedFile = null;
        $fileurlname = '';

        if (count($sourcefolders) == 2)
        {
            $fs = get_file_storage();
            $file_record = array('contextid' => $context->id, 'component' => 'mod_msm', 'filearea' => 'editor',
                'itemid' => $msmid, 'filepath' => '/', 'filename' => $sourcefolders[1],
                'timecreated' => time(), 'timemodified' => time());

            if ($fs->file_exists($context->id, "mod_msm", "editor", $msmid, "/", $sourcefolders[1]))
            {
                $existingFile = $fs->get_file($context->id, "mod_msm", "editor", $msmid, "/", $sourcefolders[1]);
                $existingFile->delete();
            }

            if (basename(dirname($this->xmlpath)) == "LinearAlgebraRn")
            {
                $path = dirname(dirname(__FILE__)) . '/newXML/' . basename(dirname($this->xmlpath)) . '/'
                        . basename($this->xmlpath) . '/' . $sourcefolders[0] . '/' . $sourcefolders[1];
            }
            else if (basename(dirname(dirname($this->xmlpath))) == "Calculus")
            {
                $path = dirname(dirname(__FILE__)) . '/newXML/' . basename(dirname(dirname($this->xmlpath))) . '/' . basename(dirname($this->xmlpath)) . '/'
                        . basename($this->xmlpath) . '/' . $sourcefolders[0] . '/' . $sourcefolders[1];
            }

            $storedFile = $fs->create_file_from_pathname($file_record, $path);
            $fileurlname = str_replace(' ', '%20', $sourcefolders[1]);
        }
        else if (count($sourcefolders) == 1) // to account for src in xml that does not include the ims folder in its path
        {
            $fs = get_file_storage();
            $file_record = array('contextid' => $context->id, 'component' => 'mod_msm', 'filearea' => 'editor',
                'itemid' => $msmid, 'filepath' => '/', 'filename' => $sourcefolders[0],
                'timecreated' => time(), 'timemodified' => time());

            if ($fs->file_exists($context->id, "mod_msm", "editor", $msmid, "/", $sourcefolders[0]))
            {
                $existingFile = $fs->get_file($context->id, "mod_msm", "editor", $msmid, "/", $sourcefolders[0]);
                $existingFile->delete();
            }

            if (basename(dirname($this->xmlpath)) == "LinearAlgebraRn")
            {
                $path = dirname(dirname(__FILE__)) . '/newXML/' . basename(dirname($this->xmlpath)) . '/'
                        . basename($this->xmlpath) . '/ims/' . $sourcefolders[0];
            }
            else if (basename(dirname(dirname($this->xmlpath))) == "Calculus")
            {
                $path = dirname(dirname(__FILE__)) . '/newXML/' . basename(dirname(dirname($this->xmlpath))) . '/' . basename(dirname($this->xmlpath)) . '/'
                        . basename($this->xmlpath) . '/ims/' . $sourcefolders[0];
            }

            $storedFile = $fs->create_file_from_pathname($file_record, $path);
            $fileurlname = str_replace(' ', '%20', $sourcefolders[0]);
        }
        else if (count($sourcefolders) == 3) // for new XML formed by the editor --> in ../pic/filename.ext format
        {
            $pathofImg = "$CFG->dataroot/temp/msmtempfiles/$this->src";
            $fs = get_file_storage();
            $file_record = array('contextid' => $context->id, 'component' => 'mod_msm', 'filearea' => 'editor',
                'itemid' => $msmid, 'filepath' => "/", 'filename' => $sourcefolders[2], 'timecreated' => time(), 'timemodified' => time());


            if ($fs->file_exists($context->id, "mod_msm", "editor", $msmid, "/", $sourcefolders[2]))
            {
                $existingFile = $fs->get_file($context->id, "mod_msm", "editor", $msmid, "/", $sourcefolders[2]);
                $existingFile->delete();
            }

            $storedFile = $fs->create_file_from_pathname($file_record, $pathofImg);
            $fileurlname = str_replace(' ', '%20', $sourcefolders[2]);
        }

        $url = "{$CFG->wwwroot}/pluginfile.php/{$context->id}/mod_msm/editor";
        $fileurl = $url . $storedFile->get_filepath() . $storedFile->get_itemid() . '/' . $fileurlname;
        $this->src = $fileurl . "||" . $msmid;

        $data->src = $this->src;

        $data->height = $this->height;
        $data->width = $this->width;
        $data->description = $this->description;
        $data->extended_caption = $this->extended_caption;

        if (!empty($this->src))
        {
            $this->id = $DB->insert_record($this->tablename, $data);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        }

        $elementPositions = array();
        $sibling_id = null;

        if (!empty($this->imageareas))
        {
            foreach ($this->imageareas as $key => $imagearea)
            {
                $elementPositions['imgarea' . '-' . $key] = $imagearea->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(imgarea.\d+)$/", $element) ? true : false):
                    $imageareaString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $imagearea = $this->imageareas[$imageareaString[1]];
                        $imagearea->saveIntoDb($imagearea->position, $msmid, $this->compid);
                        $sibling_id = $imagearea->compid;
                    }
                    else
                    {
                        $imagearea = $this->imageareas[$imageareaString[1]];
                        $imagearea->saveIntoDb($imagearea->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $imagearea->compid;
                    }
            }
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the img element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from ImgArea classes are
     * also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current image element in msm_img table
     * @param int $compid                   database ID of the current image element in msm_compositor table
     * @return \MathImg
     */
    function loadFromDb($id, $compid)
    {
        global $DB;

        $imgCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));

        $imgRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($imgRecord))
        {
            $srcInfo = explode("||", $imgRecord->src);
            $this->id = $imgRecord->id;
            $this->msm_id = $imgCompRecord->msm_id;
            $this->compid = $compid;
            $this->src = $srcInfo[0];
            if (sizeof($srcInfo) > 1)
            {
                $this->itemid = $srcInfo[1];
            }
            else
            {
                $this->itemid = null;
            }
            $this->height = $imgRecord->height;
            $this->width = $imgRecord->width;
        }

        $childElements = $DB->get_records('msm_compositor', array('msm_id' => $this->msm_id, 'parent_id' => $compid), 'prev_sibling_id');

        $this->imageareas = array();

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            if ($childtablename == 'msm_image_mapping')
            {
                $imagearea = new ImgArea();
                $imagearea->loadFromDb($child->unit_id, $child->id);
                $this->imageareas[] = $imagearea;
            }
        }

        return $this;
    }

    /**
     * This method produces an HTML code to display the retrieved data from method above and
     * also calls the same method in ImgArea classes to display the data from these classes.
     * 
     * @global moodle_database $DB
     * @global type $CFG
     * @param bool $inline                  indicating if the image should be inline(if $inline=1) with content or be centered to body
     * @param type $isindex                 flag variable to indicate if this method was called by MathIndex object
     * @return string
     */
    function displayhtml($inline, $isindex = false)
    {
        global $DB, $CFG;

        $content = '';
        $imageinfo = '';

        //getting the name of the image file to tag each image with name
        $srcfile = explode('/', $this->src);

        $src = '';

        if (!empty($this->itemid))
        {
            $fs = get_file_storage();

            $msm = $DB->get_record('msm', array('id' => $this->msm_id), '*', MUST_EXIST);
            $course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
            $cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);

            $files = $fs->get_area_files($context->id, 'mod_msm', 'editor', $msm->id);

            foreach ($files as $file)
            {
                $filesize = $file->get_filesize();
                if ($filesize > 0)
                {
                    if (($file->get_itemid() == $this->itemid) && ($file->get_filename()) == end($srcfile))
                    {
                        $filename = $file->get_filename();
                        $url = "{$CFG->wwwroot}/pluginfile.php/{$file->get_contextid()}/mod_msm/editor";
                        $fileurlname = str_replace(' ', '%20', $filename);
                        $fileurl = $url . $file->get_filepath() . $file->get_itemid() . '/' . $fileurlname;
                        $this->src = $fileurl;
                        $imageinfo = "newcontent";
                    }
                }
            }
        }
        else
        {
            $imageinfo = getimagesize($this->src);
        }

        $filename = explode('.', end($srcfile));

        // getting the "natural size" of the image

        if (!empty($imageinfo))
        {
            if ((empty($this->width)) && (empty($this->height)))
            {
                $width = $imageinfo[0];
                $height = $imageinfo[1];

                $hTowRatio = 1;

                // to prevent division by zero warning
                if ($width != 0)
                {
                    $hTowRatio = $height / $width;
                }

                // longer height than width
                if ($hTowRatio >= 1)
                {
                    //fix the height and adjust the width using the ratio to keep
                    // the height to width proportion
                    $height = 350;
                    $width = 350 / $hTowRatio;
                }
                //longer width
                else
                {
                    //fix the width and adjust the height using the ratio to keep
                    // the height to width proportion
                    $width = 350;
                    $height = 350 * $hTowRatio;
                }
            }
            // height is defined but not width
            else if ((empty($this->width)) && (!empty($this->height)))
            {
                //need to resize the width and keep the natural height to width ratio
                $naturalwidth = $imageinfo[0];
                $naturalheight = $imageinfo[1];

                // calculating the amount that has been shrunk/expanded
                $amountResized = $this->height / $naturalheight;

                // adjusting the width accordingly
                $width = $naturalwidth * $amountResized;
                $height = $this->height;
            }
            // width defined but not height
            else if ((empty($this->height)) && (!empty($this->width)))
            {
                //need to resize the height and keep the natural height to width ratio
                $naturalwidth = $imageinfo[0];
                $naturalheight = $imageinfo[1];

                // calculating the amount that has been shrunk/expanded
                $amountResized = $this->width / $naturalwidth;

                // adjusting the width accordingly
                $height = $naturalheight * $amountResized;
                $width = $this->width;
            }
            else if ((!empty($this->height)) && (!empty($this->width)))
            {
                $height = $this->height;
                $width = $this->width;
            }

            if (empty($this->imageareas))
            {
                $content .= "<img class='mathimage' src='" . $this->src . "' height='" . $height . "' width='" . $width . "'/>";
            }
            else if (!empty($this->imageareas))
            {
                if (!$isindex)
                {
                    $content .= "<img id='image-" . $this->compid . "' class='mathimagemap' src='" . $this->src . "' height='" . $height . "' width='" . $width . "' usemap='#" . $filename[0] . "'/>";
                    $content .= "<map name='" . $filename[0] . "'>";
                    foreach ($this->imageareas as $imagearea)
                    {
                        $content .= $imagearea->displayhtml();
                    }
                    $content .= "</map>";
                }
                else
                {
                    $content .= "<img class='mathimage' src='" . $this->src . "' height='" . $height . "' width='" . $width . "'/>";
                }
            }
        }

        return $content;
    }

}

?>
