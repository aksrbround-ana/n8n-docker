$(document).on('click', '#task-edit-button, #task-add-button', function (e) {
  let user = getUser();
  let token = user ? user?.token : '';
  let id = $(this).data('id') ?? null;
  data = {
    token: token,
    id: id
  };
  loadPage('/task/edit', data, true, function () {
    if (id) {
      $('main h1').text(dictionaryLookup('taskEditing', user.lang));
    } else {
      $('main h1').text(dictionaryLookup('taskCreation', user.lang));
    }
  });
});

$(document).on('click', '#task-save-button', function (e) {
  $('#task-edit-table td').removeClass('error-input');
  let user = getUser();
  let token = user ? user?.token : '';
  let taskId = $('#task-edit-table input[name=id]').val();
  let data = {
    token: token,
    id: taskId,
    category: $('#task-edit-table input[name=category]').val(),
    request: $('#task-edit-table textarea[name=request]').val(),
    status: getSelectWidgetValue('status'),
    priority: getSelectWidgetValue('priority'),
    due_date: $('#task-edit-table input[name=due_date]').val(),
    company_id: getSelectWidgetValue('company'),
    accountant_id: getSelectWidgetValue('accountant'),
  };
  let errors = 0;
  if (!data.category) {
    showError(dictionaryLookup('categoryIsRequired', user.lang), '');
    $('#task-edit-table input[name=category]').closest('td').addClass('error-input');
    errors++;
  }
  if (!data.due_date) {
    showError(dictionaryLookup('dueDateIsRequired', user.lang), '');
    $('#task-edit-table input[name=due_date]').closest('td').addClass('error-input');
    errors++;
  }
  if (!data.company_id) {
    showError(dictionaryLookup('companyIsRequired', user.lang), '');
    $('#company').closest('td').addClass('error-input');
    errors++;
  }
  if (!data.accountant_id) {
    showError(dictionaryLookup('accountantIsRequired', user.lang), '');
    $('#accountant').closest('td').addClass('error-input');
    errors++;
  }
  if (errors > 0) {
    return false;
  }
  if (!data.status) {
    data.status = 'new';
  }
  if (!data.priority) {
    data.priority = 'low';
  }
  $.ajax({
    url: '/task/save',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        let data = {
          id: response.id
        }
        if (taskId) {
          loadPage('/task/view', data, 1);
        } else {
          loadPage('/task/view', data, 0);
        }
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

$(document).on('click', 'tr.task-row', function (e) {
  setSearchHistory();
  let taskId = $(this).data('task-id');
  data = {
    id: taskId
  }
  loadPage('/task/view', data, true);
});

$(document).on('click', 'div.task-row', function (e) {
  let taskId = $(this).data('task-id');
  let data = {
    id: taskId
  }
  loadPage('/task/view', data, true);
});

$(document).on('click', '#archive-task', function (e) {
  const taskId = $(this).data('task-id');
  const user = getUser();
  const data = {
    token: user.token,
    id: taskId
  };
  $.ajax({
    url: '/task/archive',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        loadPage('/task/view', { id: taskId }, 0);
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

