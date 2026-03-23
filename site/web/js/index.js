let companyList = [];

function setUser(user) {
  localStorage.setItem('user', JSON.stringify(user));
}

function getUser() {
  const user = localStorage.getItem('user');
  return (user && (user !== 'undefined')) ? JSON.parse(user) : null;
}

function clearUser() {
  localStorage.removeItem('user');
  localStorage.removeItem('pageHistory');
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
  let lastError = $('#error-tab ol li').last();
  setTimeout(() => {
    $(lastError).remove();
  }, 5000);
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
      addBackButton();
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

function startPageHistory(itemHistory) {
  const pageHistory = [itemHistory];
  localStorage.setItem('pageHistory', JSON.stringify(pageHistory));
}

function addPageHistory(itemHistory) {
  let pageHistory = localStorage.getItem('pageHistory');
  pageHistory = pageHistory ? JSON.parse(pageHistory) : [];
  if (pageHistory.length > 0) {
    let lastPage = pageHistory[pageHistory.length - 1];
    if (lastPage.url !== itemHistory.url || JSON.stringify(lastPage.data) !== JSON.stringify(itemHistory.data)) {
      pageHistory.push(itemHistory);
    }
  } else {
    pageHistory.push(itemHistory);
  }
  localStorage.setItem('pageHistory', JSON.stringify(pageHistory));
}

function clearPageHistory(count) {
  let pageHistory = localStorage.getItem('pageHistory');
  pageHistory = pageHistory ? JSON.parse(pageHistory) : [];
  while ((count > 0) && (pageHistory.length > 0)) {
    count--;
    pageHistory.pop();
  }
  localStorage.setItem('pageHistory', JSON.stringify(pageHistory));
}

function loadPage(url, data = {}, saveHistory = false, success) {
  let user = getUser();
  let token = user ? user?.token : '';
  if (!data) {
    data = {};
  }
  data.token = token;

  let itemHistory = { url: url, data: data };
  if (saveHistory === true) {
    addPageHistory(itemHistory);
  } else if (saveHistory === false) {
    startPageHistory(itemHistory);
  } else if (typeof saveHistory === 'number') {
    clearPageHistory(saveHistory);
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
        addBackButton();
        if (success && (typeof (success) === 'function')) {
          success();
        }
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
    dataType: 'json'
  })
}

async function getBackButton() {
  const user = getUser();
  let formData = new FormData();
  formData.append('token', user.token);
  let response = await fetch('/util/back-button', {
    method: 'POST',
    body: formData
  });
  return response.button;
  // return '<button class="back inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 gap-2">' +
  //   '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-4 w-4">' +
  //   '<path d="m12 19-7-7 7-7"></path>' +
  //   '<path d="M19 12H5"></path>' +
  //   '</svg>' +
  //   dictionaryLookup('back', user.lang) +
  //   '</button>';
}

async function addBackButton() {
  let pageHistory = localStorage.getItem('pageHistory');
  pageHistory = pageHistory ? JSON.parse(pageHistory) : [];
  if (pageHistory.length > 1) {
    const user = getUser();
    $.ajax({
      url: '/util/back-button',
      type: 'POST',
      data: {
        token: user.token
      },
      success: function (response) {
        let button = response.button
        $('#page-header').prepend(button);
      }
    })
  }
}

function goBack() {
  let pageHistory = localStorage.getItem('pageHistory');
  pageHistory = pageHistory ? JSON.parse(pageHistory) : [];
  if (pageHistory.length > 1) {
    pageHistory.pop();
    let lastPage = pageHistory[pageHistory.length - 1];
    localStorage.setItem('pageHistory', JSON.stringify(pageHistory));
    let data = lastPage.data;
    data.token = getUser().token;
    $.ajax({
      url: lastPage.url,
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
          addBackButton();
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
      dataType: 'json'
    })
  }
}

function putLangDependentWords() {
  let user = getUser();
  let lang = user ? user?.lang : 'rs';
  $('.edit-calendar-btn').attr('title', dictionaryLookup('edit', lang));
  $('.delete-calendar-btn').attr('title', dictionaryLookup('delete', lang));
  $('.btn-cancel').text(dictionaryLookup('cancel', lang));
  $('.btn-save').text(dictionaryLookup('save', lang));
}

function makeCompanyListTr(listItem) {
  return '<tr><td><label for="company_ps_reminder_' + listItem.id + '">' + listItem.name + '</label></td>' +
    '<td><input id="company_ps_reminder_' + listItem.id + '" type="checkbox" value="' + listItem.id + '"' + (listItem.count > 0 ? ' checked' : '') + ' /></td></tr>';
}

function makeCompanyReminderList(listItem) {
  return '<tr><td><label for="company_reg_reminder_' + listItem.id + '">' + listItem.name + '</label></td>' +
    '<td><input id="company_reg_reminder_' + listItem.id + '" type="checkbox" value="' + listItem.id + '"' + (listItem.count > 0 ? ' checked' : '') + ' /></td></tr>';
}

//------------------------------------------------------------------
//                Handlers
//------------------------------------------------------------------

$(document).on('load', function () {
  putLangDependentWords();
});

document.addEventListener('DOMContentLoaded', function () {
  loadContent();
});

$(document).on('click', '#login-button', function (e) {
  const email = $('#login-email').val();
  const password = $('#login-password').val();
  $.ajax({
    url: '/auth/login',
    type: 'POST',
    data: {
      username: email,
      password: password
    },
    success: function (response) {
      if (response.status === 'success') {
        setUser(response.user);
        loadContent();
        putLangDependentWords();
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError('Login error', response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка при попытке входа.');
    }
  });
});

$(document).on('click', '#close-menu-button', function (e) {
  $('#sidebar').toggleClass('w-16 w-64');
  $('main').toggleClass('ml-4 ml-64');
  if ($('#sidebar').hasClass('w-16')) {
    $(this).html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right h-4 w-4"><path d="m9 18 6-6-6-6"></path></svg>');
    $('#sidebar span.blink').hide();
  } else {
    $(this).html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-4 w-4"><path d="m15 18-6-6 6-6"></path></svg>');
    $('#sidebar span.blink').show();
  }
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
    url: '/auth/logout',
    type: 'POST',
    data: {
      token: user.token
    },
    success: function (response) {
      if (response.status === 'success') {
        clearUser();
        loadContent();
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка');
    }
  });
});

$(document).on('click', '#user-card-mini, #user-menu .user-menu-item', function () {
  $('#user-menu').toggle();
  userMenuResize();
})

$(document).on('click', '#lang-card-mini', function () {
  $('#lang-menu').toggle();
  userMenuResize();
})

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

$(document).on('click', '.go-to-link', function (e) {
  const link = $(this).data('link');
  const count = $(this).data('count');
  if ((typeof count === 'number' && count > 0) || (typeof count === 'undefined')) {
    loadPage(link, {}, true);
  } else {
    const user = getUser();
    showError(dictionaryLookup('information', user.lang), dictionaryLookup('noItemsToShow', user.lang));
  }
  return false;
});

$(document).on('click', '.load-by-link', function (e) {
  const link = $(this).data('link');
  const target = $(this).data('target');
  const user = getUser();
  const data = {
    token: user.token
  };
  $.ajax({
    url: link,
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $('#' + target).html(response.data);
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

$(document).on('click', 'button.back', function (e) {
  goBack();
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


