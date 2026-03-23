$(document).on('click', '.company_open_profile', function (e) {
  e.preventDefault();
  setSearchHistory();
  let id = $(this).data('id');
  data = {
    id: id
  }
  loadPage('/company/profile', data, true);
  return false;
});

$(document).on('click', '#company-edit-button, #company-add-button', function (e) {
  let user = getUser();
  let token = user ? user?.token : '';
  let id = $(this).data('id') ?? null;
  data = {
    token: token,
    id: id
  };
  loadPage('/company/edit', data, true);
});

$(document).on('click', '#company-save-button', function (e) {
  let user = getUser();
  let token = user ? user?.token : '';
  let data = {
    token: token,
    id: $('#company-edit-table input[name=id]').val(),
    name: $('#company-edit-table input[name=name]').val(),
    name_tg: $('#company-edit-table input[name=name_tg]').val(),
    type_id: getSelectWidgetValue('type'),
    pib: $('#company-edit-table input[name=pib]').val(),
    status: getSelectWidgetValue('status'),
    is_pdv: $('#company-edit-table input[name=is_pdv]').prop('checked') ? 1 : 0,
    activity_id: getSelectWidgetValue('activity'),
    accountant_id: getSelectWidgetValue('accountant'),
  };
  if (!data.pib) {
    showError(dictionaryLookup('pibIsRequired', user.lang), '');
    $('#company-edit-table input[name=pib]').focus();
    return false;
  }
  $.ajax({
    url: '/company/save',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        let data = {
          id: response.id
        }
        loadPage('/company/profile', data, 1);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      showError(dictionaryLookup('error', user.lang));
    }
  });
  return false;
});

$(document).on('click', '.company_open_tasks', function (e) {
  e.preventDefault();
  let id = $(this).find('input.company').val();
  let data = {
    id: id
  }
  loadPage('/company/tasks', data);
  return false;
});

$(document).on('click', '.company_open_docs', function (e) {
  e.preventDefault();
  let id = $(this).find('input.company').val();
  let data = {
    id: id
  }
  loadPage('/company/docs', data);
  return false;
});

$(document).on('click', '#add_note_button', function (e) {
  let companyId = $(this).data('company-id');
  let noteText = $('#note_textarea').val();
  let user = getUser();
  let token = user ? user?.token : '';
  $.ajax({
    url: '/company/add-note',
    type: 'POST',
    data: {
      token: token,
      company_id: companyId,
      note_text: noteText
    },
    success: function (response) {
      if (response.status === 'success') {
        $('#company-content-notes').html(response.data);
        $('#note_textarea').val('');
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
});

$(document).on('click', '#sendComment', function (e) {
  const taskId = $(this).data('task-id');
  const commentText = $('#commentInput').val();
  const user = getUser();
  const data = {
    token: user.token,
    id: taskId,
    comment_text: commentText
  };
  $.ajax({
    url: '/task/comment',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $('#commentInput').val('');
        $('#task-comment-list').html(response.data);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function (e) {
      showError(dictionaryLookup('error', user.lang), e.message);
    }
  });
  return false;
});

