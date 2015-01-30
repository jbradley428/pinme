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

jQuery(document).ready(function($) {

    $('.edit-php #the-list tr').each(function() {
        addPageManagementToRow($(this), true);
    });

    if (page_management_settings.expandpages == 'false') {
        $('.edit-php #the-list tr[class*="parent-is-"]').each(function() {
            $(this).hide(0);
        });
        $('.edit-php #the-list tr[class*="parent-is-0"]').each(function() {
            $(this).show(0);
        });
    }

    function changeRowPosition(rowId, newParent, postBefore) {
        $('#the-list').css({'opacity': '0.5', 'filter': 'alpha(opacity=50)'});
        jQuery.post(ajaxurl,
                {
                    'action': 'update_post_position',
                    'newParent': newParent,
                    'postBefore': postBefore,
                    'thisPost': postid,
                    'query': window.location.href.slice(window.location.href.indexOf('?') + 1)
                },
        function(response) {
            $('#the-list').html(response);
            $('.edit-php #the-list tr').each(function() {
                addPageManagementToRow($(this), true);
            });
            if (page_management_settings.expandpages == 'false') {
                $('.edit-php #the-list tr[class*="parent-is-"]').each(function() {
                    $(this).hide(0);
                });
                $('.edit-php #the-list tr[class*="parent-is-0"]').each(function() {
                    $(this).show(0);
                });
            }
            $('#the-list').css({'opacity': '1', 'filter': 'alpha(opacity=100)'});
        }
        );
    }

    function addClasses(id, classes) {
        for (var i = 0; i < classes.length; i++) {
            $('#' + id).addClass(classes[i]);
        }
    }

    function moveRows(postid, newParent, postBefore, movepost) {
        var insertAfter = 'post-' + newParent;
        if (movepost)
            $('#' + insertAfter).insertAfter($('#post-' + postid));
        var prevousElement = 'post-' + postid;
        $('#descendent-' + postid).each(function() {
            $('#' + previousElement).insertAfter($(this));
            previousElement = $(this).attr(id);
        });
    }

    function removePageManagementFromRow(key) {
        $('#post-' + key).removeClass(function(index, css) {
            var classesToRemove = (css.match(/\b(parent-is-|descendent-|menu-order-|level-)\d+\b/g)).join(' ') + ' parent';
            return classesToRemove;
        });
        $('#post-' + key).find('.showhidetreecontainer').remove();
    }

    function addPageManagementToRow(row, removePad) {
        var level = row.attr('class').match(/level-(\d+)/)[1];
        var levelIndent = (level) * 20;
        if (!row.hasClass('parent')) {
            levelIndent = (level - 1) * 20;
        }
        var titleWithPad = row.find('.row-title').html();
        if (removePad)
            row.find('.row-title').html(titleWithPad.substring(level * 2));
        row.find('.column-title').prepend('<div class="showhidetreecontainer">&nbsp;</div>');
        if (row.hasClass('parent')) {
            row.find('.showhidetreecontainer').html('<button class="showhidetree">-</button>');
            if (page_management_settings.expandpages == 'false' && row.hasClass('parent-is-0')) {
                row.find('.showhidetreecontainer').html('<button class="showhidetree">+</button>');
            }
        }
        row.find('.showhidetreecontainer').css('margin-left', levelIndent + 'px');
    }

    $('.showhidetree').live('click', function() {
        var id = $(this).parent().parent().parent().attr('id');
        id = id.slice(id.indexOf('-') + 1);
        if ($(this).html() == '-') {
            $(this).html('+');
            $('.descendent-' + id).toggle(1000);
        }
        else {
            $(this).html('-');
            $('.descendent-' + id).toggle(1000);
        }
        return false;
    });

    function getRowAtPosition(offsetX, offsetY)
    {
        $('.edit-php #the-list .hentry:visible, .ui-state-highlight').each(function() {
            if ($(this).offset().left < offsetX
                    && $(this).offset().top < offsetY
                    && $(this).offset().left + $(this).width() > offsetX
                    && $(this).offset().top + $(this).height() > offsetY) {
                $(this).css('background', '#9cc');
            }
            else {
                if ($(this).data('oldbackground') == null || $(this).data('oldbackground').text == null)
                    $(this).css('background', 'none');
                else
                    $(this).css('background', $(this).data('oldbackground').text);
            }
        });
    }

    var postid = -1;
    var newIndex = '';
    var oldIndex = '';
    $("#the-list").sortable({
        handle: 'td',
        placeholder: "ui-state-highlight",
        stop: function(event, ui) {
            sortableStop(event, ui);
        },
        start: function(event, ui) {
            sortableStart(event, ui);
        },
        sort: function(event, ui) {
            newIndex += ' ' + ui.item.index();
            getRowAtPosition(event.pageX, event.pageY);
        },
        update: function(e, ui) {
            $('.parent-' + postid).show(1000);
            $('.sortabledragged').removeClass('sortabledragged');
            $('.edit-php #the-list .hentry:visible, ui-state-highlight').each(function() {
                if ($(this).data('oldbackground') == 'undefined' || $(this).data('oldbackground').text == 'undefined')
                    $(this).css('background', 'none');
                else
                    $(this).css('background', $(this).data('oldbackground').text);
            });
            var postBeforeHighlight = '';
            $('.edit-php #the-list .hentry:visible, .ui-state-highlight').each(function() {
                if ($(this).offset().top < event.pageY && $(this).attr('id') != 'post-' + postid)
                    postBeforeHighlight = $(this).attr('id');
                if ($(this).offset().left < event.pageX
                        && $(this).offset().top < event.pageY
                        && $(this).offset().left + $(this).width() > event.pageX
                        && $(this).offset().top + $(this).height() > event.pageY) {
                    var newParent = -1;
                    var postBefore = -1;
                    newIndex = $(this).parent().children().index($(this));
                    if ($(this).attr('class').indexOf('ui-state-highlight') >= 0) {
                    } else {
                        if ($(this).attr('id') != 'post-' + postid)
                            newParent = $(this).attr('class').match(/post-(\d+)/)[1];
                    }
                    if (newParent == -1 && postBefore == -1) {
                        if (postBeforeHighlight == '')
                        {
                        }
                        else {
                            postBefore = postBeforeHighlight.match(/post-(\d+)/)[1];
                            newParent = $('#' + postBeforeHighlight).attr('class').match(/parent-is-(\d+)/)[1];
                        }
                    }
                    changeRowPosition(postid, newParent, postBefore);
                }
            });
            return false;
        }
    }).disableSelection();

    function sortableStop(event, ui) {
    }

    function sortableStart(event, ui) {
        $('.edit-php #the-list .hentry, ui-state-highlight').each(function() {
            $(this).data('oldbackground', {text: $(this).css('background')});
        });
        oldIndex = ui.item.index();
        postid = ui.item[0].id.slice(ui.item[0].id.indexOf('-') + 1);
        $('.descendent-' + postid).hide(1000);
    }

    function save(id) {
        var params, fields, page = $('.post_status_page').val() || '';

        if (typeof (id) == 'object')
            id = this.getId(id);

        $('table.widefat .spinner').show();

        params = {
            action: 'inline-save',
            post_type: typenow,
            post_ID: id,
            edit_date: 'true',
            post_status: page
        };

        fields = $('#edit-' + id + ' :input').serialize();
        params = fields + '&' + $.param(params);

        // make ajax request
        $.post(ajaxurl, params,
                function(r) {
                    $('table.widefat .spinner').hide();

                    if (r) {
                        if (-1 != r.indexOf('<tr')) {
                            $(inlineEditPost.what + id).remove();
                            $('#edit-' + id).before(r).remove();
                            $(inlineEditPost.what + id).hide().fadeIn();
                        } else {
                            r = r.replace(/<.[^<>]*?>/g, '');
                            $('#edit-' + id + ' .inline-edit-save .error').html(r).show();
                        }
                    } else {
                        $('#edit-' + id + ' .inline-edit-save .error').html(inlineEditL10n.error).show();
                    }
                }
        , 'html');
        return false;
    }

    $(".edit-php").append('<div class="ajax-background"></div>');
});