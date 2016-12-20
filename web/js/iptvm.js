/*!
 * Start Bootstrap - SB Admin 2 v3.3.7+1 (http://startbootstrap.com/template-overviews/sb-admin-2)
 * Copyright 2013-2016 Start Bootstrap
 * Licensed under MIT (https://github.com/BlackrockDigital/startbootstrap/blob/gh-pages/LICENSE)
 */
$(function() {
    $('#side-menu').metisMenu();
});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        var topOffset = 50;
        var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    function GetQueryString(url,name)
    {    
    	var index = url.indexOf("?");
         var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
         var r = url.substr(index+1).match(reg);
         if(r!=null)return  unescape(r[2]); return null;
    }
    var url = window.location;
    // var element = $('ul.nav a').filter(function() {
    //     return this.href == url;
    // }).addClass('active').parent().parent().addClass('in').parent();
    var element = $('ul.nav a').filter(function() {
    	var uhref = GetQueryString(url.href,"r");
    	var thref = GetQueryString(this.href,"r");
    	if(thref == "" || thref == undefined || thref == null) return false;
    	if(uhref == thref){
    		return true;
    	}else{
    		if(uhref.substring(0, uhref.indexOf('/')) == thref.substring(0, thref.indexOf('/'))){
    			var uri = uhref.substring(uhref.indexOf('/')+1);
    			var category = (uri == 'index') || (uri == 'create') || (uri == 'update') || (uri == 'view') || (uri == 'import');
    			if(category){
    				return true;
    			}else{
    				return uhref.indexOf(thref)>=0;
    			}
    		}
    	}
    }).addClass('active').parent();

    while (true) {
        if (element.is('li')) {
            element = element.parent().addClass('in').parent();
        } else {
            break;
        }
    }
});
