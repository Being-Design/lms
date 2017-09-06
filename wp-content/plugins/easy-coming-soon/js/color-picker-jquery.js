

/*menu navigation color picker js*/
 jQuery(document).ready(function() {

   
	 
    var f = jQuery.farbtastic('#picker_navigation');
    var p = jQuery('#picker_navigation').css('opacity', 0.25);
    var selected;
    jQuery('.colorwell')
      .each(function () { f.linkTo(this); jQuery(this).css('opacity', 0.75); })
      .focus(function() {
        if (selected) {
         jQuery(selected).css('opacity', 0.75).removeClass('colorwell-selected');
        }
        f.linkTo(this);
        p.css('opacity', 1);
        jQuery(selected = this).css('opacity', 1).addClass('colorwell-selected');
		
      });

 

jQuery(document).mousedown( function() {
			jQuery('#picker_navigation').hide();			
		});
	  

jQuery("input[name='color_navigation']").click(function(){
jQuery('#picker_navigation').show();
		
});

});


/*end of navigation typographi*/

/*Post Title color picker js */
 jQuery(document).ready(function() {
 
    
    var f = jQuery.farbtastic('#picker_post_title');
    var p = jQuery('#picker_post_title').css('opacity', 0.25);
    var selected;
    jQuery('.colorwell')
      .each(function () { f.linkTo(this); jQuery(this).css('opacity', 0.75); })
      .focus(function() {
        if (selected) {
         jQuery(selected).css('opacity', 0.75).removeClass('colorwell-selected');
        }
        f.linkTo(this);
        p.css('opacity', 1);
        jQuery(selected = this).css('opacity', 1).addClass('colorwell-selected');
      });
	  
 
	  
jQuery(document).mousedown( function() {
			jQuery('#picker_post_title').hide();
		});
	  

jQuery("input[name='color_post_title']").click(function(){
jQuery('#picker_post_title').show();
});

});
/*end of Post Title typographi*/

/*Post Excerpt typography color picker */
 jQuery(document).ready(function() {
 
    var f = jQuery.farbtastic('#picker_post_entry');
    var p = jQuery('#picker_post_entry').css('opacity', 0.25);
    var selected;
    jQuery('.colorwell')
      .each(function () { f.linkTo(this); jQuery(this).css('opacity', 0.75); })
      .focus(function() {
        if (selected) {
         jQuery(selected).css('opacity', 0.75).removeClass('colorwell-selected');
        }
        f.linkTo(this);
        p.css('opacity', 1);
        jQuery(selected = this).css('opacity', 1).addClass('colorwell-selected');
      });
	  

	  
jQuery(document).mousedown( function() {
			jQuery('#picker_post_entry').hide();
		});
	  

jQuery("input[name='color_post_entry']").click(function(){
jQuery('#picker_post_entry').show();
});

});
/*end of Post Excerpt typography*/

/*Post Meta typographi color picker js  */
 jQuery(document).ready(function() {
  
    var f = jQuery.farbtastic('#picker_post_meta');
    var p = jQuery('#picker_post_meta').css('opacity', 0.25);
    var selected;
    jQuery('.colorwell')
      .each(function () { f.linkTo(this); jQuery(this).css('opacity', 0.75); })
      .focus(function() {
        if (selected) {
         jQuery(selected).css('opacity', 0.75).removeClass('colorwell-selected');
        }
        f.linkTo(this);
        p.css('opacity', 1);
        jQuery(selected = this).css('opacity', 1).addClass('colorwell-selected');
      });
	  
  
	  
jQuery(document).mousedown( function() {
			jQuery('#picker_post_meta').hide();
		});
	  

jQuery("input[name='color_post_meta']").click(function(){
jQuery('#picker_post_meta').show();
});

});
/*end of Post Meta*/

/*Sidebar Widget Titles typographi color picker js */
 jQuery(document).ready(function() {
   
    var f = jQuery.farbtastic('#picker_sidebar_widget_titles');
    var p = jQuery('#picker_sidebar_widget_titles').css('opacity', 0.25);
    var selected;
    jQuery('.colorwell')
      .each(function () { f.linkTo(this); jQuery(this).css('opacity', 0.75); })
      .focus(function() {
        if (selected) {
         jQuery(selected).css('opacity', 0.75).removeClass('colorwell-selected');
        }
        f.linkTo(this);
        p.css('opacity', 1);
        jQuery(selected = this).css('opacity', 1).addClass('colorwell-selected');
      });
	  
	  
	  
jQuery(document).mousedown( function() {
			jQuery('#picker_sidebar_widget_titles').hide();
		});
	  

jQuery("input[name='color_sidebar_widget_titles']").click(function(){
jQuery('#picker_sidebar_widget_titles').show();
});

});

/*end of Sidebar Widget Titles*/

/*typographi Footer Widget Titles color picker js */
jQuery(document).ready(function() {
   
    var f = jQuery.farbtastic('#picker_footer_widget_titles');
    var p = jQuery('#picker_footer_widget_titles').css('opacity', 0.25);
    var selected;
    jQuery('.colorwell')
      .each(function () { f.linkTo(this); jQuery(this).css('opacity', 0.75); })
      .focus(function() {
        if (selected) {
         jQuery(selected).css('opacity', 0.75).removeClass('colorwell-selected');
        }
        f.linkTo(this);
        p.css('opacity', 1);
        jQuery(selected = this).css('opacity', 1).addClass('colorwell-selected');
      });
	  
	  
	  
jQuery(document).mousedown( function() {
			jQuery('#picker_footer_widget_titles').hide();
		});
	  

jQuery("input[name='home_color_title']").click(function(){
jQuery('#picker_footer_widget_titles').show();
});

});
/*end typographi Footer Widge*/

/*general option color picker js */

 jQuery(document).ready(function() {
  
    var f = jQuery.farbtastic('#picker_title');
    var p = jQuery('#picker_title').css('opacity', 0.25);
    var selected;
    jQuery('.colorwell')
      .each(function () { f.linkTo(this); jQuery(this).css('opacity', 0.75); })
      .focus(function() {
        if (selected) {
         jQuery(selected).css('opacity', 0.75).removeClass('colorwell-selected');
        }
        f.linkTo(this);
        p.css('opacity', 1);
        jQuery(selected = this).css('opacity', 1).addClass('colorwell-selected');
      });
	  
	  
	  
jQuery(document).mousedown( function() {
			jQuery('#picker_title').hide();
		});

jQuery("input[name='color_title']").click(function(){
jQuery('#picker_title').show();
});

});
/*end of site title*/
/*typographi Footer Widget Titles color picker js */
jQuery(document).ready(function() {
   
    var f = jQuery.farbtastic('#picker_footer_widget_titles');
    var p = jQuery('#picker_footer_widget_titles').css('opacity', 0.25);
    var selected;
    jQuery('.colorwell')
      .each(function () { f.linkTo(this); jQuery(this).css('opacity', 0.75); })
      .focus(function() {
        if (selected) {
         jQuery(selected).css('opacity', 0.75).removeClass('colorwell-selected');
        }
        f.linkTo(this);
        p.css('opacity', 1);
        jQuery(selected = this).css('opacity', 1).addClass('colorwell-selected');
      });
	  
	  
	  
jQuery(document).mousedown( function() {
			jQuery('#picker_footer_widget_titles').hide();
		});
	  

jQuery("input[name='home_color_title']").click(function(){
jQuery('#picker_footer_widget_titles').show();
});

});
/*end typographi Footer Widge*/



/* Porduct Titles color picker js on homepage 
jQuery(document).ready(function() {
   
    var f = jQuery.farbtastic('#picker_product_titles');
    var p = jQuery('#picker_product_titles').css('opacity', 0.25);
    var selected;
    jQuery('.colorwell')
      .each(function () { f.linkTo(this); jQuery(this).css('opacity', 0.75); })
      .focus(function() {
        if (selected) {
         jQuery(selected).css('opacity', 0.75).removeClass('colorwell-selected');
        }
        f.linkTo(this);
        p.css('opacity', 1);
        jQuery(selected = this).css('opacity', 1).addClass('colorwell-selected');
      });
	  
	  
	  
jQuery(document).mousedown( function() {
			jQuery('#picker_product_titles').hide();
		});
	  

jQuery("input[name='product_color_title']").click(function(){
alert(123);
jQuery('#picker_product_titles').show();
});

});*/




