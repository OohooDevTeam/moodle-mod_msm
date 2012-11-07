<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once($CFG->libdir . '/formslib.php');

/**
 * Description of authoring_form
 *
 * @author User
 */
class mod_msm_authoring_form extends moodleform
{

    function definition()
    {
        global $CFG;
        $mform =& $this->_form;
       
        require_once('htmltesting.html');
        $mform->addElement('html', '<br />');
    }

}

?>
