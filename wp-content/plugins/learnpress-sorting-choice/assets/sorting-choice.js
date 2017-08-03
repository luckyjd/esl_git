;(function($) {
    function updateAnswers($element){
        $rows = $('.lpr-question-option tbody', $element).children();
        if( $rows.length == 1 ){
            $rows.addClass('lpr-disabled');
        }else{
            $rows.filter(function(){
                if( $('.lpr-answer-text', this).val().length == 0 ) {
                    $(this).addClass('lpr-disabled');
                }else{
                    $(this).removeClass('lpr-disabled');
                }
            })
        }
        $element.trigger('lpr_update_item_index')
    }
    function inputKeyEvent(evt){
        var $input = $(this),
            $wrap = ( evt.data || {} ).wrap;

        if (evt.type == 'focusin') {
            $input.data('value', $input.val());
        } else if (evt.type == 'focusout') {
            if ($input.val().length == 0) {
                var $row = $input.closest('tr');
                if (!$row.is(':last-child')) $row.remove();
            }
            return false;
        } else if(evt.type == 'keydown'){
            var $row = $input.closest('tr'),
                $rows = $row.parent().children(),
                index = $rows.index($row);

            switch (evt.keyCode) {
                default:
                    if ($row.is(':last-child') && $input.val().length) {
                        $('.lpr-button-add-answer', $wrap).trigger('click');
                        $('.lpr-question-answer tr:last input:last', $wrap).val('')
                        $input.focus();
                    }
                    break;
                case 13: // enter
                    if ($input.val().length) {
                        var $nextrow = $row.next();
                        if (!$nextrow.get(0)) {
                            $('.lpr-button-add-answer', $wrap).trigger('click')
                            $nextrow = $row.next();
                            $('input.lpr-answer-text', $nextrow).val('')
                        }
                        $('input.lpr-answer-text', $nextrow).focus();
                    }
                    evt.preventDefault();
                    return false;
                case 27: // esc
                    $input.val($input.data('value'));
                    $input.closest('.lpr-question').focus();
                    evt.preventDefault();
                    break;
                case 9: // tab

                    $('input.lpr-answer-text', $row.next()).focus();
                    evt.preventDefault();
                    return false;
                case 8: // back space
                case 46: // delete
                    if ($input.val().length == 0) {
                        $newrow = $row.prev();

                        if ($rows.length > 1) {
                            try{ $row.remove(); }catch(ex){}
                        }
                        if (!$newrow.get(0)) {
                            $newrow = $rows.last();
                        }
                        $newrow.find('input.lpr-answer-text').focus();
                        updateAnswers($wrap)
                        evt.preventDefault();
                        return false;
                    }
            }
        }
    }
    // Sorting Choice
    $.lprSortingChoice = function (elem) {
        var $element = $(elem);

        $('.lpr-sortable tbody', $element).sortable({
            handle: '.lpr-sortable-handle',
            placeholder: 'placeholder',
            axis: 'y',
            helper: function (e, helper) {
                helper.children().each(function (i) {
                    var td = $(this),
                        w = td.innerWidth() - parseInt(td.css("padding-left")) - parseInt(td.css("padding-right"));
                    td.width(w + 1);
                });

                return helper;
            },
            start: function (e, ui) {
                ui.placeholder.height(ui.item.height() - 2);

            },
            update: function (e, ui) {
                $(this).trigger('lpr_update_item_index');
            },
            stop: function (e, ui) {
                ui.item.children().removeAttr('style');
            }
        }).on('lpr_update_item_index', function () {
            $(this).children().each(function (i) {
                var $row = $(this),
                    $inputs = $('input[name^="lpr_question"]', this);
                $inputs.each(function () {
                    var name = $(this).attr('name')
                    name = name.replace(/\[__INDEX__([0-9]?)\]/, '[__INDEX__' + i + ']');
                    $(this).attr('name', name);


                })
            })
        });
        $element.on('click', '.lpr-button-add-answer', function(){
            if( $('.lpr-sortable tbody tr:last input:last', $element).val().length == 0 ){
                $('.lpr-sortable tbody tr:last input:last', $element).focus();
                return;
            }
            var $parent = $(this).parent(),
                tpl = wp.template("sorting-choice-question-answer");
            var $item = $(tpl({
                question_id: $element.data('id') || 0
            }));
            $('.lpr-sortable tbody', $element).append( $item ).trigger('lpr_update_item_index');
            $('input[name*="text"]', $item).focus().val('Question answer');
            updateAnswers($element);
        }).on('click', '.lpr-remove-answer', function(){
            var $row = $(this).closest('tr');

            $row.remove();
        }).on('keydown keypress focus blur', '.lpr-answer-text', {wrap: $element}, inputKeyEvent)
            .on('focus', function(){$(this).addClass('selected')})
            .on('blur', function(){$(this).removeClass('selected')})
            .attr('tabindex', 0);

        updateAnswers($element);
    }
    $.fn.lprSortingChoice = function () {
        return $.each(this, function () {
            var q = $(this).data('lprSortingChoice');
            if (!q) {
                q = new $.lprSortingChoice(this);
                $(this).data('lprSortingChoice', q)
            }
            return this;
        })
    }
})(jQuery);