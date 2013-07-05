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
 * *************************************************************************
 */

/**
 * This class is representing all the media elements(eg. img and video elements) that needs to be exported as XML document.
 * ExportMedia class is called directly by ExportElement but all the other classes with content indirectly calls on this class
 * by createXmlContent function in ExportElement.
 * It inherits methods from the abstract class ExportElement including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @todo need to implement methods to deal with image mapping
 * 
 * @author Ga Young Kim
 */
class ExportMedia extends ExportElement
{
    public $id;                 // ID of the current media element in msm_media database table
    public $compid;             // ID of the current media element in msm_compositor database table
    public $msmid;              // MSM instance ID
    public $img;                // ExportImage object that is associated with the current media element
    public $active;             // whether or not the image maps are associated with the image of the current media element
    public $inline;             // whether or not the media element should be displayed inline or centered
    public $type;               // type of media element --> eg) image or video
    
    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with media element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Molecules.xsd.  This method also calls the exportData method
     * from ExportImage class.  The DOMElement object that is returned from exportData calls from classes mentioned
     * above is then appended to the media DOMElement and is returned to replace the existing img or video tags
     * in the content.
     * 
     * @return DOMElement
     */
    public function exportData()
    {
        $mediaCreator = new DOMDocument();
        $mediaNode = $mediaCreator->createElement("media");
        $mediaNode->setAttribute("id", "$this->msmid-$this->compid");
        $mediaNode->setAttribute("active", $this->active);
        $mediaNode->setAttribute("inline", $this->inline);
        $mediaNode->setAttribute("type", $this->type);
        
        if(!empty($this->img))
        {
            $imgNode = $this->img->exportData();
            $newimgNode = $mediaCreator->importNode($imgNode, true);
            $mediaNode->appendChild($newimgNode);
        }
        
        return $mediaNode;
    }

    /**
     * This method is used to pull all relevant data linked with media elements from the database table
     * "msm_media".  It also calls the loadDbData method from the ExportImage class.
     *
     * @global moodle_database $DB
     * @param int $compid                   ID of the current media element in msm_compositor database table
     * @return \ExportMedia
     */
    public function loadDbData($compid)
    {
        global $DB;
        
        $mediaCompRecord = $DB->get_record("msm_compositor", array("id"=>$compid));
        $mediaUnitRecord = $DB->get_record("msm_media", array("id"=>$mediaCompRecord->unit_id));
        
        $this->id = $mediaUnitRecord->id;
        $this->compid = $compid;
        $this->msmid = $mediaCompRecord->msm_id;
        $this->active = $mediaUnitRecord->active;
        $this->inline = $mediaUnitRecord->inline;
        $this->type = $mediaUnitRecord->media_type;
        
        $imgRecord = $DB->get_record("msm_compositor", array("parent_id"=>$this->compid));
        
        if(!empty($imgRecord))
        {
            $image = new ExportImage();
            $image->loadDbData($imgRecord->id);
            $this->img = $image;
        }
        
        return $this;
    }
}

?>
