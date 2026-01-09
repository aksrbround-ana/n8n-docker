
// 1. Открытие/закрытие по клику на триггер
$(document).on('click', '.custom-select-trigger', function (e) {
    $(this).parent('.custom-select').toggleClass('open');
});

// 2. Выбор опции
$(document).on('click', '.custom-option', function (e) {
    let value = $(this).data('value');
    let text = $(this).text();

    // Обновляем текст в триггере и скрытый инпут
    $('.custom-select-trigger').text(text);
    $('#real-input').val(value);

    // Закрываем список
    $('.custom-select').removeClass('open');
});

// 3. Закрытие списка, если кликнули вне его области
$(window).on('click', function (e) {
    if (!$(e.target).closest('.custom-select').length) {
        $('.custom-select').removeClass('open');
    }
});
