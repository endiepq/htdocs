1. Склонировать репозиторий в htdocs XAMPP
2. Запустить Apache и MySQL
3. Импортировать database в MySQL с названием database
4. Запустить localhost в браузере и посмотреть результат

ОШИБКИ:
1. В файле env.php $dbname - указываем название database в mysql
2. $host - localhost, если в XAMPP иначе название хоста сервиса
3. $user = 'root' $passwd = '' указываем ваше имя и пароль от хоста (это в XAMPP)
