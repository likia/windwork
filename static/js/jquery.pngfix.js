/** 
 * jQuery-Plugin "pngFix" 
 * Version: 1.1, 11.09.2007 
 * by Andreas Eberhard, andreas.eberhard@gmail.com 
 *                      http://jquery.andreaseberhard.de/ 
 * 
 * Copyright (c) 2007 Andreas Eberhard 
 * Licensed under GPL (http://www.opensource.org/licenses/gpl-license.php) 
 * -------------------------------------------------------------------- 
 * @example $(function(){$(document).pngFix();}); 
 * @desc Fixes all PNG's in the document on document.ready 
 * 
 * jQuery(function(){jQuery(document).pngFix();}); 
 * @desc Fixes all PNG's in the document on document.ready when using noConflict 
 * 
 * @example $(function(){$('div.examples').pngFix();}); 
 * @desc Fixes all PNG's within div with class examples 
 * 
 * @example $(function(){$('div.examples').pngFix( { blankgif:'ext.gif' } );}); 
 * @desc Fixes all PNG's within div with class examples, provides blank gif for input with png 
 */  
      
(function($) {  
    jQuery.fn.pngFix = function(settings) {  
      
        // Settings  
        settings = jQuery.extend({  
            blankgif: 'blank.gif' //此处是全透明的小图片，配合热区效果  
        }, settings);  
      
        var ie55 = (navigator.appName == "Microsoft Internet Explorer" && parseInt(navigator.appVersion) == 4 && navigator.appVersion.indexOf("MSIE 5.5") != -1);  
        var ie6 = (navigator.appName == "Microsoft Internet Explorer" && parseInt(navigator.appVersion) == 4 && navigator.appVersion.indexOf("MSIE 6.0") != -1);  
      
        if (jQuery.browser.msie && (ie55 || ie6)) {  
            //fix images with png-source  
            jQuery(this).find("img[src$='.png']").each(function() {  
                var Img = this,img=jQuery(this);  
                var id=new Date().getTime();var ImgID='id="' +id+ '" ';  
                var ImgClass = (Img.className) ? "class='" + Img.className + "' " : "";  
                var ImgTitle = (Img.title) ? "title='" + Img.title + "' " : "title='" +img.attr('alt') + "' "  
                var ImgStyle = "display:inline-block;" + Img.style.cssText  
                if (Img.align == "left") ImgStyle = "float:left;" + ImgStyle  
                if (Img.align == "right") ImgStyle = "float:right;" + ImgStyle  
                if (Img.parentElement.href) ImgStyle = "cursor:hand;" + ImgStyle  
                var ImgUsemap=(img.attr('usemap'))? ' usemap="' + img.attr('usemap') + '" ' : '';     
                var strNewHTML = "<span " + ImgID + ImgClass + ImgTitle
                + " style=\"" + "width:" + Img.width + "px; height:" + Img.height + "px;" + ImgStyle + ";"  
                + "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
                + "(src=\'" + Img.src + "\', sizingMethod='scale');\"><img src=\'"+settings.blankgif+"\' width=" + Img.width + " height=" + Img.height +ImgUsemap+"  /></span>";  
                img.hide();
                img.after(strNewHTML);
                var eventStr="click|mousedown|mouseenter|mouseleave|mousemove|mouseout|mouseover|mouseup|dblclick|blur|focus";  
                var events = eventStr.split("|");
                var len=events.length;
                for (i = 0; i < len; i++) {
                    var type=events[i];if(img.attr("on"+type)!=undefined)$("#"+id).bind(type,function(type){ img.trigger(type);});  
                }
            });
      
            // fix css background pngs
            jQuery(this).find("*").each(function(){
                var bgIMG = jQuery(this).css('background-image');
                if(bgIMG.indexOf(".png")!=-1){
                    var iebg = bgIMG.split('url("')[1].split('")')[0];
                    jQuery(this).css('background-image', 'none');
                    jQuery(this).get(0).runtimeStyle.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + iebg + "',sizingMethod='scale')";  
                }
            });
              
            //fix input with png-source
            jQuery(this).find("input[src$='.png']").each(function() {
                var bgIMG = jQuery(this).attr('src');
                jQuery(this).get(0).runtimeStyle.filter = 'progid:DXImageTransform.Microsoft.AlphaImageLoader' + '(src=\'' + bgIMG + '\', sizingMethod=\'scale\');';  
                jQuery(this).attr('src', settings.blankgif);
            });
          
        }
          
        return jQuery;
      
    };
      
})(jQuery);