/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(function() {
//    var d=300;
//    $('#navigation a').each(function(){
//        $(this).stop().animate({
//            'marginBottom':'-80px'
//        });
//    });
//
//    $('#navigation > li').hover(
//        function () {
//            $('a',$(this)).stop().animate({
//                'marginBottom':'-2px'
//            });
//        },
//        function () {
//            $('a',$(this)).stop().animate({
//                'marginBottom':'-80px'
//            });
//        }
//        );
            
    $('#toc').toggle(function () {
        $('#tocpanel').stop().animate({
            height:'90%', 
            opacity:0.8
        }, 500, function() {
            $('#toccontent').fadeIn('slow');
                                    
        });  
    },
    function(){
        $('#toccontent').fadeOut('slow', function() {
            $('#tocpanel').stop().animate({
                height:'0px', 
                opacity:0.1
            }, 500); //slide the #panel back to a width of 0
        });
    });
  
});