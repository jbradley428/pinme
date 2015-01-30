/**
 * Plugin Name: Page Management
 * Plugin URI: http://www.wpshowcase.net/plugins/page-management-plugin/ 
 * Description: Page Management is a great tool which allows you to do loads of useful things including to hide/expand subpages when viewing the page hierarchy, to display all pages/posts on the same page, to reorder pages using drag and drop, to show the current page's media files by default and to add a shortcode dropdown of all the site's shortcodes.
 * Version: 2.4
 * Author: WPShowCase
 * Author URI: http://www.wpshowcase.net
 * Text Domain: pagemanagementlang
 * Domain Path: /lang
 *
 * @package PageManagement
 * @version 2.4
 * @author WPShowCase <admin@wpshowcase.net>
 * @copyright Copyright (c) 2014, WPShowCase.net
 * @link http://www.wpshowcase.net/plugins/page-management-plugin/
 */
 
 (function() {
	tinymce.create('tinymce.plugins.ShortCodeDropDown', {
		
		init : function(ed, url) {
			var t = this;
			t.editor = ed;
		},
			
		//Creates the dropdown
		createControl : function(n, cm) {
            if(n=='shortcodedropdown'){
                var mlb = cm.createListBox('shortcode_dropdown', {
                     title : '[...]',
                     onselect : function(v) {
						if(tinyMCE.activeEditor.selection.getContent() == ''){//If nothing selected, just add the shortcode
                            tinyMCE.activeEditor.selection.setContent( '['+v+']' )
                        }
						else {//If text is selected, add the shortcode around the text
							tinyMCE.activeEditor.execCommand('mceReplaceContent', false, '['+v+']{$selection}[/'+v+']');
						}
						return false;
                     }
                });
	
				//Add the options to the dropdown
				for(var cnt=0; cnt<shortcode_options_for_dropdown.length; cnt++)
                	mlb.add('['+shortcode_options_for_dropdown[cnt]+']',shortcode_options_for_dropdown[cnt]);
 
                return mlb;
            }
            return null;
        }

	});

	tinymce.PluginManager.add('shortcodedropdown', tinymce.plugins.ShortCodeDropDown);
		
})();