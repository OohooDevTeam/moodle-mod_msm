/**
**************************************************************************
**                              MSM                                     **
**************************************************************************
* @package     mod                                                      **
* @subpackage  msm                                                      **
* @name        msm                                                      **
* @copyright   University of Alberta                                    **
* @link        http://ualberta.ca                                       **
* @author      Ga Young Kim(code modified by)                           **
* @author      Jakub Jankiewicz(original author)                        **
* @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
**************************************************************************
**************************************************************************/

/*!
 * JQuery Spliter Plugin
 * Copyright (C) 2010 Jakub Jankiewicz <http://jcubic.pl> 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
(function($, undefined) {
    var count = 0;    
    var spliter_id = null;
    var spliters = [];
    var current_spliter = null;
    var splitFlag =false;
    $.fn.split = function(options) {
        var panel_1;
        var panel_2;     
        var settings = $.extend({
            limit: 100,
            orientation: 'horizontal',
            position: '50%',
            onDragStart: $.noop,
            onDragEnd: $.noop,
            onDrag: $.noop
        }, options || {});
        var cls;
        var children = this.children();
        if (settings.orientation == 'vertical') {
            panel_1 = children.first().addClass('left_panel');
            panel_2 = panel_1.next().addClass('right_panel');
            cls = 'vspliter';
        } else if (settings.orientation == 'horizontal') {
            panel_1 = children.first().addClass('top_panel')
            panel_2 = panel_1.next().addClass('bottom_panel');
            cls = 'hspliter';
        }
        var width = this.width();
        var height = this.height();
        var id = count++;
        this.addClass('spliter_panel');
        var spliter = $('<div/>').addClass(cls).mouseenter(function() {
            spliter_id = id;
        }).mouseleave(function() {
            spliter_id = null;
        }).insertAfter(panel_1);
        var position;
        var self = $.extend(this, {
            position: (function() {
                if (settings.orientation == 'vertical') {
                    return function(n) {
                        if (n === undefined) {
                            return position;
                        } else {
                            position = n;
                            var sw = spliter.width()/2;
                            spliter.css('left', n-sw);
                            panel_1.width(n-sw);
                            panel_2.width(self.width()-n-sw);
                        }
                    };
                } else if (settings.orientation == 'horizontal') {
                    return function(n) {
                        if (n === undefined) {
                            return position;
                        } else {
                            var sw = spliter.height()/2;
                            spliter.css('top', n-sw);
                            panel_1.height(n-sw);
                            panel_2.height(self.height()-n-sw);
                            position = n;
                        }
                    };
                } else {
                    return null;
                }
            })(),
            orientation: settings.orientation,
            limit: settings.limit,
            isActive: function() {
                return spliter_id === id;
            },
            destroy: function() {
                spliter.unbind('mouseenter');
                spliter.unbind('mouseleave');
                if (settings.orientation == 'vertical') {
                    panel_1.removeClass('left_panel');
                    panel_2.removeClass('right_panel');
                } else if (settings.orientation == 'horizontal') {
                    panel_1.removeClass('top_panel');
                    panel_2.removeClass('bottom_panel');
                }
                self.unbind('spliter.resize');
                spliters[id] = null;
                spliter.remove();
                var not_null = false;
                for (var i=spliters.length; i--;) {
                    if (spliters[i] !== null) {
                        not_null = true;
                        break;
                    }
                }
                //remove document events when no spliters
                if (!not_null) {
                    $(document.documentElement).unbind('.spliter');
                    spliters = [];
                }
            }
        });
        
        self.bind('spliter.resize', function() {
            var pos = self.position();
            if (self.orientation == 'vertical' && 
                pos > self.width()) {
                pos = self.width() - self.limit-1;
            } else if (self.orientation == 'horizontal' && 
                pos > self.height()) {
                pos = self.height() - self.limit-1;
            }
            if(pos < self.limit) pos = self.limit + 1;
            self.position(pos);
        });
        //inital position of spliter
        var m = settings.position.match(/^([0-9]+)(%)?$/);
        var pos;
        if (settings.orientation == 'vertical') {
            if (m[2]) {
                pos = (width * +m[1]) / 100;
            } else {
                pos = settings.position;
            }
            if (pos > width-settings.limit) {
                pos = width-settings.limit;
            }
        } else if (settings.orientation == 'horizontal') {
            //position = height/2;
            if (m[2]) {
                pos = (height * +m[1]) / 100;
            } else {
                pos = settings.position;
            }
            if (pos > height-settings.limit) {
                pos = height-settings.limit;
            }
        }
        if (pos < settings.limit) {
            pos = settings.limit;
        }
        self.position(pos);
        // added code by Ga Young Kim to deal with window resizing
        $(window).resize(function() {
            var pos = self.position();
            if (self.orientation == 'vertical' && 
                pos > self.width()) {
                pos = self.width() - self.limit-1;
            } else if (self.orientation == 'horizontal' && 
                pos > self.height()) {
                pos = self.height() - self.limit-1;
            }
            if(pos < self.limit) pos = self.limit + 1;
            self.position(pos);
        });
        
        if (spliters.length == 0) { // first time bind events to document
            $(document.documentElement).bind('mousedown.spliter', function(e) {
                if (spliter_id !== null) {
                    current_spliter = spliters[spliter_id];
                    $('<div class="splitterMask"></div>').insertAfter(current_spliter);
                    if (current_spliter.orientation == 'horizontal') {
                        $('body').css('cursor', 'row-resize');
                    } else if (current_spliter.orientation == 'vertical') {
                        $('body').css('cursor', 'col-resize');
                    }
                    settings.onDragStart(e);
                    return false;
                }
            }).bind('mouseup.spliter', function(e) {
                current_spliter = null;
                $('div.splitterMask').remove();
                $('body').css('cursor', 'auto');
                settings.onDragEnd(e);
            }).bind('mousemove.spliter', function(e) {
                if (current_spliter !== null) {
                    var limit = current_spliter.limit;
                    var offset = current_spliter.offset();
                    if (current_spliter.orientation == 'vertical') {
                        splitFlag = true;
                        var x = e.pageX - offset.left;
                        if(x <= current_spliter.limit) {
                            x = current_spliter.limit + 1;
                        }
                        else if (x >= current_spliter.width() - limit) {
                            x = current_spliter.width() - limit - 1;
                        }
                        if (x > current_spliter.limit &&
                            x < current_spliter.width()-limit) {
                            current_spliter.position(x);
                            current_spliter.find('.spliter_panel').trigger('spliter.resize');
                            return false;
                        }
                    } else if (current_spliter.orientation == 'horizontal') {
                        var y = e.pageY-offset.top;
                        if(y <= current_spliter.limit) {
                            y = current_spliter.limit + 1;
                        }
                        else if (y >= current_spliter.height() - limit) {
                            y = current_spliter.height() - limit - 1;
                        }
                        if (y > current_spliter.limit &&
                            y < current_spliter.height()-limit) {
                            current_spliter.position(y);
                            current_spliter.trigger('spliter.resize');
                            return false;
                        }
                    }
                    settings.onDrag(e);
                }
            });
        }
        spliters.push(self);
        return self;
    };
})(jQuery);

//
//function scrollbarWidth() { 
//    var wide_scroll_html = '<div id="wide_scroll_div_one" style="width:50px;height:50px;overflow-y:scroll;position:absolute;top:-200px;left:-200px;"><div id="wide_scroll_div_two" style="height:100px;width:100%"></div></div>'; 
//    $("body").append(wide_scroll_html); // Append our div and add the hmtl to your document for calculations
//    var scroll_w1 = $("#wide_scroll_div_one").width(); // Getting the width of the surrounding(parent) div - we already know it is 50px since we styled it but just to make sure.
//    var scroll_w2 = $("#wide_scroll_div_two").innerWidth(); // Find the inner width of the inner(child) div.
//    var scroll_bar_width = scroll_w1 - scroll_w2; // subtract the difference
//    $("#wide_scroll_div_one").remove(); // remove the html from your document
//    
//    return scroll_bar_width; 
//}