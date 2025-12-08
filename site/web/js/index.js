
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

function loadPage(url) {
  let user = getUser();
  let token = user ? user?.token : '';
  $.ajax({
    url: url,
    type: 'POST',
    data: {
      token: token
    },
    success: function (response) {
      $('main').html(response.data);
      userMenuResize();
    },
    error: function (e) {
      showError('Load error', e);
    },
    type: 'json'
  })
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
  // if (!$(this).hasClass('active')) {
    $('a.menuitem').removeClass('active');
    $('a.menuitem').removeClass('bg-sidebar-primary');
    $('a.menuitem').removeClass('text-sidebar-primary-foreground');
    $('a.menuitem').addClass('text-sidebar-foreground');
    $(this).addClass('active');
    $(this).addClass('bg-sidebar-primary');
    $(this).addClass('text-sidebar-primary-foreground');
    let url = $(this).attr('href');
    loadPage(url);
    // $('main').load(url);
  // }
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

// Функция Debounce
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

// ----------------------------------------------------
// Ваша логика, которую нужно выполнить после изменения размера
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

// Прикрепляем оптимизированный обработчик к событию resize
// window.addEventListener('resize', debounce(userMenuResize, 100)); // Задержка 300 мс

document.addEventListener('DOMContentLoaded', () => {
  // Привязываем Debounce-обрабочик события resize
  window.addEventListener('resize', debounce(userMenuResize, 50));

  // Также можно вызвать функцию один раз при загрузке страницы
  userMenuResize();
});

$(document).ready(function () {
});


