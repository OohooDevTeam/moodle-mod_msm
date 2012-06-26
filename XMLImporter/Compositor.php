<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Compositor
 *
 * @author User
 */
class Compositor
{

    function __construct()
    {
        $this->tablename = "msm_compositor";
    }

    function loadFromUnit($unit)
    {
        foreach ($unit as $key => $value)
        {
            echo "key";
            print_object($key);

            echo "value";
            print_object($value);
        }


//        $unitChilds = array();
//        foreach($unit as $key=>$child)
//        {
//          switch($key)
//          {
//              case('intro'):
//                  if(!empty($child))
//                  {
//                      $unitChilds['intro'][0] = $child->position;
//                      $unitChilds['intro'][1] = $child->id;
//                  }
//                  break;
//                  case('authors'):
//                      if(!empty($child))
//                      {
//                          foreach($child as $noAuthors=>$author)
//                          {
//                             
//                          }
//                      }
//                  
//          }
//        }
    }

}

?>
