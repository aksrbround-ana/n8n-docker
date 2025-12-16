
function setUser(user) {
  localStorage.setItem('user', JSON.stringify(user));
}

function getUser() {
  const user = localStorage.getItem('user');
  return (user && (user !== 'undefined')) ? JSON.parse(user) : null;
}

function clearUser() {
  localStorage.removeItem('user');
}

function showError(title, message) {
  $('#error-tab ol').append(
    `<li class="mb-2 animate-in slide-in-from-top-2 duration-300">
      <div class="pointer-events-auto w-full max-w-sm rounded-lg bg-white shadow-lg ring-1 ring-black/5">
        <div class="p-4">
          <div class="flex items-start">
            <div class="flex-1">
              <p class="font-medium text-gray-900">${title}</p>
              <p class="mt-1 text-sm text-gray-700">${message}</p>
            </div>
            <button class="ml-4 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-white text-gray-400 hover:bg-gray-50 hover:text-gray-500" onclick="this.closest('li').remove();">
              <span class="sr-only">Close</span>
              &times;
            </button>
          </div>
        </div>
      </div>
    </li>`
  );
}

function menu(el) {
  if ($(el).hasClass('active')) {
    alert('active')
  } else {
    alert('not active')
  }
  return false;
}

function putPtm() {
  user = getUser();
  $('.ptm-name').text(user.firstname + ' ' + user.lastname);
  $('.ptm-email').text(user.email);
  $('.ptm-position').text(user.rule);
}

function loadContent() {
  let user = getUser();
  let token = user ? user?.token : '';
  $.ajax({
    url: '/site/load',
    type: 'POST',
    data: {
      token: token
    },
    success: function (response) {
      $('#root').html(response.data);
      userMenuResize();
    },
    error: function (e) {
      showError('Load error', e);
    },
    type: 'json'
  })
}

function showTiff() {
  if ($('#tiffCanvas').length) {
    let docId = $('#tiffCanvas').data('doc-id');
    var xhr = new XMLHttpRequest();
    xhr.responseType = 'arraybuffer';
    xhr.open('GET', "/document/file/" + docId, true);
    xhr.onload = function (e) {
      var tiff = new Tiff({
        buffer: xhr.response
      });
      var canvas = tiff.toCanvas();
      document.getElementById('tiffCanvas').appendChild(canvas);
    };
    xhr.send();
  }

}

function loadPage(url, data = {}, saveHistory = false) {
  let user = getUser();
  let token = user ? user?.token : '';
  if (!data) {
    data = {};
  }
  data.token = token;
  let itemHistory = { url: url, data: data };
  if (saveHistory) {
    let pageHistory = localStorage.getItem('pageHistory');
    pageHistory = pageHistory ? JSON.parse(pageHistory) : [];
    pageHistory.push(itemHistory);
    localStorage.setItem('pageHistory', JSON.stringify(pageHistory));
  } else {
    let pageHistory = [itemHistory];
    localStorage.setItem('pageHistory', JSON.stringify(pageHistory));
  }
  $.ajax({
    url: url,
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        $('main').html(response.data);
        userMenuResize();
        showTiff();
      }
    },
    error: function (e) {
      if (e.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError('Load error', e.message);
      }
    },
    type: 'json'
  })
}

function goBack() {
  let pageHistory = localStorage.getItem('pageHistory');
  pageHistory = pageHistory ? JSON.parse(pageHistory) : [];
  if (pageHistory.length > 1) {
    pageHistory.pop();
    let lastPage = pageHistory[pageHistory.length - 1];
    localStorage.setItem('pageHistory', JSON.stringify(pageHistory));
    $.ajax({
      url: lastPage.url,
      type: 'POST',
      data: lastPage.data,
      success: function (response) {
        if (response.status === 'logout') {
          clearUser();
          loadContent();
        } else {
          $('main').html(response.data);
          userMenuResize();
          showTiff();
        }
      },
      error: function (e) {
        if (e.status === 'logout') {
          clearUser();
          loadContent();
        } else {
          showError('Load error', e.message);
        }
      },
      type: 'json'
    })
  }
}

//------------------------------------------------------------------
//                Handlers
//------------------------------------------------------------------

document.addEventListener('DOMContentLoaded', function () {
  loadContent();
});

$(document).on('click', '#login-button', function (e) {
  const email = $('#login-email').val();
  const password = $('#login-password').val();
  $.ajax({
    url: '/api/auth/login',
    type: 'POST',
    data: {
      username: email,
      password: password
    },
    success: function (response) {
      if (response.status === 'success') {
        setUser(response.user);
        loadContent();
      } else {
        showError('Ошибка входа', response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка при попытке входа.');
    }
  });
});

$(document).on('click', 'a.menuitem', function (e) {
  e.preventDefault();
  $('a.menuitem').removeClass('active');
  $('a.menuitem').removeClass('bg-sidebar-primary');
  $('a.menuitem').removeClass('text-sidebar-primary-foreground');
  $('a.menuitem').addClass('text-sidebar-foreground');
  $(this).addClass('active');
  $(this).addClass('bg-sidebar-primary');
  $(this).addClass('text-sidebar-primary-foreground');
  let url = $(this).attr('href');
  loadPage(url);
  return false;
});

$(document).on('click', 'div span.ptm-logout', function (e) {
  let user = getUser();
  $.ajax({
    url: '/api/auth/logout',
    type: 'POST',
    data: {
      token: user.token
    },
    success: function (response) {
      if (response.status === 'success') {
        clearUser();
        loadContent();
      } else {
        showError('Ошибка', response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка');
    }
  });
});

$(document).on('click', '#user-card-mini', function () {
  $('#user-menu').toggle();
  userMenuResize();
})

$(document).on('click', '#lang-card-mini', function () {
  $('#lang-menu').toggle();
  userMenuResize();
})

$(document).on('click', '.company_open_profile', function (e) {
  e.preventDefault();
  let id = $(this).data('id');
  let data = {
    id: id
  }
  loadPage('/company/profile', data, true);
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

$(document).on('click', 'div[role=tablist] button[role=tab]', function (e) {
  let tabId = $(this).attr('id');
  let tablist = $(this).closest('div[role=tablist]');
  tablist.find('button[role=tab]').attr('data-state', 'inactive');
  $(this).attr('data-state', 'active');
  let parentTablist = tablist.parent();
  let selector = "div[role='tabpanel'][aria-labelledby='" + tabId + "']";
  let targetTab = parentTablist.find(selector);
  if (targetTab.length) {
    parentTablist.find('div[role=tabpanel]').attr('data-state', 'inactive').attr('hidden', '');
    targetTab.attr('data-state', 'active').removeAttr('hidden');
  }
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
      } else {
        showError('Ошибка', response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка при добавлении заметки.');
    }
  });
});

$(document).on('click', 'tr.task-row', function (e) {
  let taskId = $(this).find('input.task-id').val();
  let data = {
    id: taskId
  }
  loadPage('/task/view', data, true);
});

$(document).on('click', 'button.back', function (e) {
  goBack();
});

$(document).on('click', 'tr.doc-row', function (e) {
  let docId = $(this).data('doc-id');
  let data = {
    id: docId
  }
  loadPage('/document/view', data, true);
});

$(document).on('click', 'a.document-view', function (e) {
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
        loadPage('/task/view', { id: taskId });
      } else {
        showError('Ошибка', response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка при завершении задачи.');
    }
  });
  return false;
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
      } else {
        showError('Ошибка', response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка при добавлении комментария.');
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
      } else {
        showError('Ошибка', response.message);
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
      } else {
        showError('Ошибка', response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка при добавлении комментария.');
    }
  });
  return false;
});

$(document).on('click', '#upload-docs', function (e) {
  loadPage('/document/upload', {}, true);
  return false;
});

// ----------------------------------------------------
//                Resize Debounce
//----------------------------------------------------

function debounce(func, delay = 250) {
  let timeoutId;
  return function () {
    const context = this;
    const args = arguments;

    // Сбросить предыдущий таймер
    clearTimeout(timeoutId);

    // Установить новый таймер
    timeoutId = setTimeout(() => {
      func.apply(context, args);
    }, delay);
  };
}

function userMenuResize() {
  if ($('#user-menu').length) {
    let docWidth = document.documentElement.clientWidth;
    let menuWidth = $('#user-menu').get(0).offsetWidth;
    let transformUser = docWidth - menuWidth - 24;
    let transformLang = transformUser - 314;
    $('#user-menu').css('transform', 'translate(' + transformUser + 'px, 62px)');
    $('#lang-menu').css('transform', 'translate(' + transformLang + 'px, 54px)');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  window.addEventListener('resize', debounce(userMenuResize, 50));
  userMenuResize();
});

// ----------------------------------------------------
//                Document Ready
//----------------------------------------------------

$(document).ready(function () {
});


