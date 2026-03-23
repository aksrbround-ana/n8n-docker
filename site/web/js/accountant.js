$(document).on('click', '#accountant-edit-button', function (e) {
  const id = $(this).data('id');
  const user = getUser();
  const data = {
    token: user.token,
    id: id
  };
  $.ajax({
    url: '/accountant/change',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $('#accountant-map').html(response.data);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      showError(dictionaryLookup('error', user.lang), 'Unknown error');
    }
  });
  return false;
});

$(document).on('click', '#accountant-cancel-button', function (e) {
  const id = $(this).data('id');
  if (id) {
    const user = getUser();
    const data = {
      token: user.token,
    };
    $.ajax({
      url: '/accountant/page/' + id,
      type: 'POST',
      data: data,
      success: function (response) {
        if (response.status === 'success') {
          $('#accountant-map').html(response.data);
        } else if (response.status === 'logout') {
          clearUser();
          loadContent();
        } else {
          showError(dictionaryLookup('error', user.lang), response.message);
        }
      },
      error: function () {
        showError(dictionaryLookup('error', user.lang), 'Unknown error');
      }
    });
  } else {
    $('#accountant-map').empty();
  }
  return false;
});

$(document).on('click', '#accountant-save-button', function (e) {
  const table = $('#accountant-map table.accountant-page');
  const id = $(this).data('id');
  const email = $(table).find('input[name=accountant-email]').val();
  const firstname = $(table).find('input[name=accountant-firstname]').val();
  const lastname = $(table).find('input[name=accountant-lastname]').val();
  const role = getSelectWidgetValue('accountant-role');
  const lang = getSelectWidgetValue('accountant-lang');
  const user = getUser();
  const data = {
    token: user.token,
    id: id,
    email: email,
    firstname: firstname,
    lastname: lastname,
    role: role,
    lang: lang
  };
  if (!id) {
    data.password = $(table).find('input[name=accountant-password]').val();
  }
  $.ajax({
    url: '/accountant/write/',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $('#accountant-map').html(response.data);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      showError(dictionaryLookup('error', user.lang), 'Unknown error');
    }
  });
  return false;
});
