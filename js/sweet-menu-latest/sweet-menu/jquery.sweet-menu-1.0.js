jQuery(document).ready(function()
{ 

    jQuery.fn.sweetMenu = function(options)
    {
        // setup any options that weren't passed in
        var settings = jQuery.extend({
            top: 80,
            position: 'fixed',
            iconSize: 0,
            iconSizeWidth: 0,
            iconSizeHeight: 0,
            padding: 10,
            verticalSpacing: 10,
            duration: 200,
            easing: 'linear',
            anchorClass: 'sweetMenuAnchor',
            openFinished: function(){},
            closeFinished: function(){},
            icons: []
        }, options);
		
        // add our class
        jQuery(this).children('li').children('a').addClass(settings.anchorClass);
		
        // add our span to our class object for additional styling, if needed
        jQuery(this).children('li').children('a').wrapInner('<span>');
		
        // figure out the proper iconSize
        if(settings.iconSize > 0){
            settings.iconSizeWidth = settings.iconSize;
            settings.iconSizeHeight = settings.iconSize;
        }
		
        // figure out all of the math that we need to figure out, mainly padding
        settings.tabWidth = settings.iconSizeWidth + (settings.padding * 2);
        settings.height = settings.iconSizeHeight + (settings.padding * 2);
        settings.paddingString =
            settings.padding + 'px ' +
            settings.tabWidth + 'px ' +
            settings.padding + 'px ' +
            settings.padding + 'px';
		
        // setup our ul css properties
        jQuery(this).css({
            'padding': '0',
            'margin': '0',
            'right': '0px',
            'top': settings.top + 'px'
        });
		
        // setup our ul css property position
        if(settings.position == 'fixed' || settings.position == 'absolute'){
            jQuery(this).css({
                'position': settings.position
            });
        } else {
            jQuery(this).css({
                'position': 'fixed'
            });
        }
		
        // setup our li css properties
        jQuery(this).children('li').css({
            'padding': '0',
            'margin': '0',
            'list-style': 'none',
            'display': 'block',
            'position': 'relative',
            'margin-bottom': settings.verticalSpacing,
            'height': settings.height
        });
		
        // figure out the width that all of the anchors need to be (so they are all the same)
        settings.width = 0;
        jQuery(this).children('li').children('a').each(function()
        {
            if(jQuery(this).width() > settings.width){
                settings.width = jQuery(this).width();
            }
        });
		
        // setup our anchor css properties
        jQuery(this).children('li').children('a').css({
            'display': 'block',
            'padding': settings.paddingString,
            'min-height': settings.iconSizeHeight,
            'white-space': 'nowrap',
            'width': settings.width
        });
		
        // figure out our margin for when the tab is "hidden"
        settings.marginLeft = jQuery(this).outerWidth() - settings.tabWidth;
		
        // apply our margin left to our anchors
        jQuery(this).children('li').children('a').css({
            'position': 'absolute',
            'right': '-' + settings.marginLeft + 'px'
        });
		
        // figure out our background position, for if we have icons
        var bgPosX = (settings.width + (settings.padding * 2));
        //                var bgPosX = settings.width;
        var bgPosY = settings.padding;
        //settings.backgroundPosition = bgPosX + 'px ' + bgPosY + 'px';
        settings.backgroundPosition = bgPosX + 'px ' - '50%';
		
        // setup our event listeners and our icons (if they exist)
        jQuery(this).children('li').children('a').each(function(index)
        {
            if(settings.icons[index] != 'undefined'){
                jQuery(this).css({
                    'background-image': 'url("' + settings.icons[index] + '")',
                    'background-repeat': 'no-repeat',
                    'background-position': settings.backgroundPosition
					
                });
            }
            jQuery(this).mouseover(function()
            {
                jQuery(this).stop().animate(
                {
                    'right': '0px'
                },
                settings.duration,
                settings.easing,
                function(){
                    if(typeof settings.openFinished == 'function'){
                        settings.openFinished();
                    }
                }
            );
            });
			
            jQuery(this).mouseout(function()
            {
                jQuery(this).stop().animate(
                {
                    'right': '-' + settings.marginLeft + 'px'
                },
                settings.duration,
                settings.easing,
                function(){
                    if(typeof settings.closeFinished == 'function'){
                        settings.closeFinished();
                    }
                }
            );
            });
            $('#toc').toggle(function()
            { //adding a toggle function to the #tab

                $('#tocpanel').stop().animate({
                    width:"400px", 
                    opacity:0.8
                }, 500, function() {//sliding the #panel to 400px
                    $('.slidepanelcontent').fadeIn('slow'); //slides the content into view.
                                    
                });  
            },
            function(){ //when the #tab is next cliked
                $('.slidepanelcontent').fadeOut('slow', function() { //fade out the content 
                    $('#tocpanel').stop().animate({
                        width:"0", 
                        opacity:0.1
                    }, 500); //slide the #panel back to a width of 0
                });
            });
            $('#symbol').toggle(function()
            { //adding a toggle function to the #tab

                $('#symbolpanel').stop().animate({
                    width:"400px", 
                    opacity:0.8
                }, 500, function() {//sliding the #panel to 400px
                    $('.slidepanelcontent').fadeIn('slow'); //slides the content into view.
                });  
            },
            function(){ //when the #tab is next cliked
                $('.slidepanelcontent').fadeOut('slow', function() { //fade out the content 
                    $('#symbolpanel').stop().animate({
                        width:"0", 
                        opacity:0.1
                    }, 500); //slide the #panel back to a width of 0
                });
            });
            $('#glossary').toggle(function()
            { //adding a toggle function to the #tab

                $('#glossarypanel').stop().animate({
                    width:"400px", 
                    opacity:0.8
                }, 500, function() {//sliding the #panel to 400px
                    $('.slidepanelcontent').fadeIn('slow'); //slides the content into view.
                });  
            },
            function(){ //when the #tab is next cliked
                $('.slidepanelcontent').fadeOut('slow', function() { //fade out the content 
                    $('#glossarypanel').stop().animate({
                        width:"0", 
                        opacity:0.1
                    }, 500); //slide the #panel back to a width of 0
                });
            });
        });
		
		
        return jQuery(this);
    };

});