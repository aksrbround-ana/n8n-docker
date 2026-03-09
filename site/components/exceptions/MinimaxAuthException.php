<?php

namespace app\components\exceptions;

/**
 * Выбрасывается при ошибках OAuth2-авторизации:
 * неверные credentials, недоступен endpoint, неожиданный ответ.
 */
class MinimaxAuthException extends \RuntimeException {}
