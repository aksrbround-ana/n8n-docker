$(document).on('click', 'tr.doc-row', function (e) {
  setSearchHistory();
  let docId = $(this).data('doc-id');
  data = {
    id: docId
  }
  loadPage('/document/view', data, true);
});

$(document).on('click', 'div.document-view', function (e) {
  const id = $(this).data('doc-id');
  const data = { id: id };
  loadPage('/document/view', data, true);
  return false;
});

$(document).on('click', '#finish-task', function (e) {
  const taskId = $(this).data('task-id');
  const user = getUser();
  const data = {
    token: user.token,
    id: taskId
  };
  $.ajax({
    url: '/task/finish',
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
    error: function () {
      showError(dictionaryLookup('error', user.lang), 'Unknown error');
    }
  });
  return false;
});

$(document).on('click', '#doc-change-status', function (e) {
  $('#doc-change-status').hide();
  $('#doc-status-select').removeClass('hidden');
});

$(document).on('change', '#doc-status-select', function (e) {
  $('#doc-change-status').show();
  $('#doc-status-select').addClass('hidden');
  let user = getUser();
  let token = user ? user?.token : '';
  let docId = $(this).data('doc-id');
  let newStatus = $(this).val();
  $.ajax({
    url: '/document/change-status',
    type: 'POST',
    data: {
      token: token,
      id: docId,
      status: newStatus
    },
    success: function (response) {
      if (response.status === 'success') {
        $('#doc-status-block').html(response.data);
        $('#doc-activity-block').replaceWith(response.activity);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка при изменении статуса документа.');
    }
  });
});

$(document).on('click', '#doc-send-comment', function (e) {
  const docId = $(this).data('document-id');
  const commentText = $('#doc-comment-input').val();
  const user = getUser();
  const data = {
    token: user.token,
    id: docId,
    comment_text: commentText
  };
  $.ajax({
    url: '/document/comment',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $('#commentInput').val('');
        $('#doc-comment-block').replaceWith(response.data);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка при добавлении комментария.');
    }
  });
  return false;
});

async function uploadFile(file, taskId) {
  let formData = new FormData();
  formData.append('document', file);
  formData.append('task_id', taskId);
  const user = getUser();
  formData.append('token', user.token);

  try {
    const response = await fetch('/document/upload/', {
      method: 'POST',
      body: formData,
    });

    const result = await response.json();

    if (result.status === 'success') {
      formData = new FormData();
      formData.append('token', user.token);
      formData.append('id', taskId);
      const responseList = await fetch('/task/documents', {
        method: 'POST',
        body: formData,
      });
      return await responseList.json();
    } else if (result.status === 'logout') {
      clearUser();
      loadContent();
      return { status: 'logout' };
    } else {
      showError(dictionaryLookup('error', user.lang), result.message);
      return { status: 'error', message: result.message };
    }
  } catch (error) {
    console.error('Сетевая ошибка:', error);
    return { status: 'error', error: error.message };
  }
}

$(document).on('change', '#document-to-upload', async (e) => {
  const file = e.target.files[0];
  if (file) {
    const taskId = $(e.target).data('task-id');
    const result = await uploadFile(file, taskId);
    e.target.value = '';
    if (result.status === 'success') {
      $('#task-documents-list').html(result.data);
    } else {
      const user = getUser();
      showError(dictionaryLookup('error', user.lang), result.message);
    }
  }
});

let isDialogOpen = false;
$(document).on('click', '#dropArea', () => {
  if (isDialogOpen) return;
  isDialogOpen = true;
  setTimeout(() => isDialogOpen = false, 300);
  $('#document-to-upload').click()
});
$(document).on('dragover', '#dropArea', (e) => {
  e.preventDefault();
  $('#dropArea').css('border-color', '#ff7700');
});
$(document).on('dragleave', '#dropArea', (e) => {
  e.preventDefault();
  $('#dropArea').css('border-color', '#ccc');
});
$(document).on('drop', '#dropArea', async (e) => {
  e.preventDefault();
  $('#dropArea').css('border-color', '#ccc');
  const originalEvent = e.originalEvent;
  const files = originalEvent.dataTransfer.files;
  if (files.length > 0) {
    const taskId = $('#dropArea').data('task-id');
    for (let i = 0; i < files.length; i++) {
      const result = await uploadFile(files[i], taskId);
      if (result.status === 'success') {
        $('#task-documents-list').html(result.data);
      } else {
        showError(dictionaryLookup('error', user.lang), result.message);
      }
    }
  }
});

