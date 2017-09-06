/* multistep form js */
jQuery(window).ready(function() {
				jQuery('#multipage').multipage({transitionFunction:transition,stateFunction: textpages});
	jQuery('form').submit(function(){return true;});
});

(function(jQuery) {


		var curpage = 1;
		var id = null;
		var settings = null;
		
		jQuery.fn.transitionPage = function(from,to) {
		
			if (settings.transitionFunction) {
				settings.transitionFunction(from,to);
			} else {
				jQuery(from).hide();
				jQuery(to).show();
			}
			jQuery(id + ' fieldset').removeClass('active');
			jQuery(to).addClass('active');		
		}
		
		jQuery.fn.showState = function(page) { 
			
			if (settings.stateFunction) { 
				return settings.stateFunction(id+"_nav .multipage_state",page,settings.pages.length);
			}
			var state = '';
			for (x = 1; x <= settings.pages.length; x++) {
				if(x==page) {
					state = state + settings.activeDot;
				} else {
					state = state + settings.inactiveDot;
				}
			}
			jQuery(id+"_nav .multipage_state").html(state);	
		}

		
		jQuery.fn.gotopage = function(page) {
			jQuery(id + '_nav .multipage_next').html('Next');				
			
			if (isNaN(page)) { 
				q = page;
				page = 1;
				jQuery(id+' fieldset').each(function(index) {
					if ('#'+jQuery(this).attr('id')==q) { 
						curpage = page = index+1;
					}
				});
			}

			var np = null;
			var cp = jQuery(id+' fieldset.active');
			// show the appropriate page.
			jQuery(id+' fieldset').each(function(index) {
				index++;
				if (index==page) {		
					np = this;
				}
			});
			
			jQuery(this).transitionPage(cp,np);
			
			jQuery(this).showState(page);

			jQuery(id + '_nav .multipage_next').removeClass('submit');				
			
			// is there a legend tag for this fieldset?
			// if so, pull it out.
			page_title = settings.pages[page-1].title;
			
			if (settings.stayLinkable) { 
				hashtag = '#' + settings.pages[page-1].id;
				document.location.hash = hashtag;
			}
			if (page==1) {
				// set up for first page
				jQuery(id + '_nav .multipage_back').hide();
				jQuery(id + '_nav .multipage_next').show();
                                if(page==settings.pages.length)
                                {
                                    jQuery(id + '_nav .multipage_next').addClass('submit');				
                                    jQuery(id + '_nav .multipage_next').html(settings.submitLabel);	
                                }
                                else
                                {
				if (settings.pages[page].title) {
					jQuery(id + '_nav .multipage_next').html('Next: ' + settings.pages[page].title);
				} else {
					jQuery(id + '_nav .multipage_next').html('Next');
				}
                            }

			} else if (page==settings.pages.length) { 
				// set up for last page
				jQuery(id + '_nav .multipage_back').show();
				jQuery(id + '_nav .multipage_next').show();

				if (settings.pages[page-2].title) { 
					jQuery(id + '_nav .multipage_back').html('Back: ' + settings.pages[page-2].title);
				} else {
					jQuery(id + '_nav .multipage_back').html('Back');				
				}
                                 
				jQuery(id + '_nav .multipage_next').addClass('submit');				
				jQuery(id + '_nav .multipage_next').html(settings.submitLabel);				
				
			} else {
				if (settings.pages[page-2].title) { 
					jQuery(id + '_nav .multipage_back').html('Back: ' + settings.pages[page-2].title);
				} else {
					jQuery(id + '_nav .multipage_back').html('Back');				
				}
				if (settings.pages[page].title) {
					jQuery(id + '_nav .multipage_next').html('Next: ' + settings.pages[page].title);
				} else {
					jQuery(id + '_nav .multipage_next').html('Next');
				}

				jQuery(id + '_nav .multipage_back').show();
				jQuery(id + '_nav .multipage_next').show();				

			}
			
			jQuery(id + ' fieldset.active input:first').focus();
			curpage=page;
			return false;
			
		}
		
	jQuery.fn.validatePage = function(page) { 
			return true;
		}

		
	jQuery.fn.validateAll = function() { 
		for (x = 1; x <= settings.pages.length; x++) {
			if (!jQuery(this).validatePage(x)) {
				jQuery(this).gotopage(x);
				return false;
			}
		}
		return true;
	}	

		
	jQuery.fn.gotofirst = function() {
		curpage = 1;
		jQuery(this).gotopage(curpage);
		return false;
	}
	jQuery.fn.gotolast = function() {
		curpage = settings.pages.length;
		jQuery(this).gotopage(curpage);
		return false;
	}

	jQuery.fn.nextpage = function() {
			// validate the current page
			curfieldset = jQuery(this).children("fieldset:nth-child("+ curpage+")");
			if(profile_magic_multistep_form_validation(curfieldset))
			{
				if (jQuery(this).validatePage(curpage)) { 
					curpage++;
		
					if (curpage > settings.pages.length) {
						// submit!
						jQuery(this).submit();
						 curpage = settings.pages.length;
						 return false;
					}
					jQuery(this).gotopage(curpage);
				}
				return false;
			}
		
	}
	
	jQuery.fn.getPages = function() {
		return settings.pages;
	}
		
	jQuery.fn.prevpage = function() {

		curpage--;

		if (curpage < 1) {
			 curpage = 1;
		}
		jQuery(this).gotopage(curpage);
		return false;
		
	}
	
	
	jQuery.fn.multipage = function(options) { 
		
		settings = jQuery.extend({stayLinkable:false,submitLabel:'Submit',hideLegend:false,hideSubmit:true,generateNavigation:true,activeDot:'&nbsp;&#x25CF;',inactiveDot:'&nbsp;&middot;'},options);
		id = '#' + jQuery(this).attr('id');
		var form = jQuery(this);			
		
		form.addClass('multipage');
		
		form.submit(function(e) {
			if (!jQuery(this).validateAll()) {
				e.preventDefault()
			};
		});
		
		// hide all the pages 
		jQuery(id +' fieldset').hide();
			if (settings.hideSubmit) { 
				jQuery(id+' input[type="submit"]').hide();
			}		
			
			if (jQuery(id+' input[type="submit"]').val()!='') { 
				settings.submitLabel = jQuery(id+' input[type="submit"]').val();
			}
			
			settings.pages = new Array();
			
			jQuery(this).children('fieldset').each(function(index) { 
				label = jQuery(this).children('legend').html();
				settings.pages[index] = {number:index+1,title:label,id:jQuery(this).attr('id')};
			});
			
			
			if (settings.hideLegend) { 
				// hide legend tags
				jQuery(id+' fieldset legend').hide();
			}
			
			// show the first page.
			jQuery(id+' fieldset:first').addClass('active');

			jQuery(id+' fieldset:first').show();
									
			if (settings.generateNavigation) { 
				if (settings.navigationFunction) { 
					settings.navigationFunction(jQuery(this).getPages());
				} else {
					// insert navigation
                                        var id_name = jQuery(this).attr('id');
                                        jQuery('<div class="multipage_nav" id="'+id_name+'_nav"><a href="#" class="multipage_back" onclick="return  jQuery(\''+id+'\').prevpage();">Back</a><a href="#"  class="multipage_next" onclick="return jQuery(\''+id+'\').nextpage();">Next</a><span class="multipage_state"></span><div class="clearer"></div></div>').insertAfter(this);
				}
			}				
			
			if (document.location.hash) { 
				jQuery(this).gotopage('#'+document.location.hash.substring(1,document.location.hash.length));
			} else {
				jQuery(this).gotopage(1);			
			}	
			return false;
		
		}
		

})(jQuery);



/* multistep form js old js start */
/*var current_fs, next_fs, previous_fs; //fieldsets
var left, opacity, scale; //fieldset properties which we will animate
var animating; //flag to prevent quick multi-click glitches

jQuery(document).ready(function($){
  jQuery('.pm_next').click(function(){
	current_fs = jQuery(this).parent();
	validation = profile_magic_multistep_form_validation(current_fs);
	if(validation)
	{
		next_fs = jQuery(this).parent().next();
		//activate next step on progressbar using the index of next_fs
		jQuery("#progressbar li").eq(jQuery("fieldset").index(next_fs)).addClass("active");
		current_fs.hide();
		next_fs.show(500); 
	}
  });
 });
 
jQuery(document).ready(function($){
	jQuery('.pm_previous').click(function(){	
	current_fs = jQuery(this).parent();
	previous_fs = jQuery(this).parent().prev();
	//de-activate current step on progressbar
	jQuery("#progressbar li").eq(jQuery("fieldset").index(current_fs)).removeClass("active"); 	
	//show the previous fieldset
	current_fs.hide();
	previous_fs.show(500); 
	
	});
});*/