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

$(document).on('click', '#save-user', function (e) {
  const form = $(this).closest('form');
  const formData = form.serializeArray();
  let data = {};
  formData.forEach(item => {
    data[item.name] = item.value;
  });
  let user = getUser();
  data.token = user.token;
  $.ajax({
    url: form.attr('action'),
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        loadPage('/accountant/profile', {}, false);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка при сохранении данных пользователя.');
    }
  });
  return false;
});

$(document).on('click', '#accountant-company-button', function (e) {
  let id = $(this).data('item-id');
  const title = $(this).closest('tr').find('.calendar-text').text();
  const type = $(this).data('type');
  companyListModal = new Modal('modal-overlay', title);
  companyListModal.setDoUrl('/accountant/update-company/');
  loadCompanyListModal(id, '/accountant/company-list', makeCompanyListTr, type);
  companyListModal.open(this);
});

