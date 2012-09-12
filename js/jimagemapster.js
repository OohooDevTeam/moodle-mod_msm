/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function activateArea(i)
{
    $('#image-'+i).ready(function() {
        $('#image-'+i).mapster('snapshot');
        $('#image-'+i).mapster('rebind', {
                fillColor: 'ff0000',
                fillOpacity: 0.5
            });
    });
    
    $('#copyimage-'+i).ready(function() {
        $('#copyimage-'+i).mapster('snapshot');
        $('#copyimage-'+i).mapster('rebind', {
                fillColor: 'ff0000',
                fillOpacity: 0.5
            });
    });
}