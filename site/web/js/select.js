
function getSelectWidgetValue(id) {
    let widget = $('#' + id);
    if (widget.length === 0) {
        return null;
    } else {
        return widget.find('input.select-widget-value').val();
    }
}

// 1. Открытие/закрытие по клику на триггер
$(document).on('click', '.select-widget-trigger', function (e) {
    $(this).parent('.select-widget').toggleClass('open');
});

// 2. Выбор опции
$(document).on('click', '.select-widget-option', function (e) {
    let value = $(this).data('value');
    let text = $(this).text();
    let wrapper = $(this).closest('.select-widget-wrapper');

    // Обновляем текст в триггере и скрытый инпут
    $(wrapper).find('div.select-widget-trigger').text(text);
    $(wrapper).find('input.select-widget-value').val(value);

    // Закрываем список
    $(wrapper).find('.select-widget').removeClass('open');
});

// 3. Закрытие списка, если кликнули вне его области
$(window).on('click', function (e) {
    if (!$(e.target).closest('.select-widget').length) {
        $('.select-widget').removeClass('open');
    }
});

$(document).on('keydown', 'div.select-widget-wrapper', function (e) {
    // console.dir(e.originalEvent.key)
    let selected;
    switch (e.originalEvent.key) {
        case 'Enter':
            selected = $(this).find('div.select-widget-options span.select-widget-option.selected');
            if (selected.length != 0) {
                $(this).find('input.select-widget-value').val($(selected).data('value'));
                $(this).find('div.select-widget-trigger').text($(selected).text());
            }
            $(this).find('div.select-widget').toggleClass('open');
            break;
        case 'ArrowDown':
            if (!$(this).find('div.select-widget').hasClass('open')) {
                $(this).find('div.select-widget').addClass('open');
            } else {
                selected = $(this).find('div.select-widget-options span.select-widget-option.selected');
                if (selected.length == 0) {
                    selected = $(this).find('div.select-widget-options span.select-widget-option').first();
                } else {
                    selected = selected.next();
                }
                if (selected.length) {
                    $(this).find('div.select-widget-options span.select-widget-option.selected').removeClass('selected');
                    selected.addClass('selected');
                }
            }
            break;
        case 'ArrowUp':
            if ($(this).find('div.select-widget').hasClass('open')) {
                selected = $(this).find('div.select-widget-options span.select-widget-option.selected');
                if (selected.length == 0) {
                    selected = $(this).find('div.select-widget-options span.select-widget-option').last();
                } else {
                    selected = selected.prev();
                }
                if (selected.length) {
                    $(this).find('div.select-widget-options span.select-widget-option.selected').removeClass('selected');
                    selected.addClass('selected');
                }
            }
            break;
        default:
            if (e.originalEvent.key.length == 1) {
                console.log('symbol');
            }
            break;
    }

});
