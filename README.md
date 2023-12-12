
<div align="center">
    <img width="700" src="https://github.com/Achernov7/images/blob/master/example.svg">
</div>


<p align="center">
  <font size="5px" color="white"> Проект использовался в учебных целях.</font>
</p>

### Содержание

* [Пролог](#Prolog)
  * [Общее](#general)
  * [Основные недочеты](#mainShortcomings)
* [Запуск приложения из-под системы](#InstallationFromTheSystem)
* [Запуск приложения из-под докера](#InstallationFromTheDocer)
* [Ссылка на видео с демонстрацией основного функционала приложения](#VideoLink)


&nbsp;
<a name="Prolog">
  <h1>Пролог🔥</h1>
</a>
&nbsp;
<a name="general">
  <h2>Общее</h2>
</a>

От начала данного проекта к его нынешнему варианту изменилось представление о более правильном осуществлении его компонентов.

Часть однотипного кода специально реализовывалась по-разному.
Ради интереса использовал лонгпол для пуша новых сообщений на странице "Сообщения". Это не имеет особого смысла, т.к. к сокету, прослушивающему все входящие сообщения, подключаемся при авторизации.


<a name="mainShortcomings">
  <h2>Основные недочеты</h2>
</a>

#### Бэкенд:

* Обязательно стоит отказаться от sqlite. Использовал из-за удобства.
* Обязательно нужно выходить из цикла лонгпола с помощью редиса.
* Отправляются лишние http запросы при поиске людей(FriendController.php). Это был 1 контроллер -> в последующем ушел от данной практики. Лишние запросы в бд.
* Между запросами для пагинации часто использовались id уже отображенных объектов. Более правильной практикой было бы отправлять id последнего объекта или время его добавления. При добавлении новых объектов пагинация часто идет от самых новых к самым старым -> новый объект при добавлении уходит в начало списка и не ломает пагинацию.
* Неправильно храниться в бд даты дней рождений пользователей. Ушел от этой практики позже.
* Раздутые контроллеры. Возможно стоило часть логики перенести в сервисы или вместо методов использовать классы с инвоком.
* Хранить состояние отношений(семейное положение) стрингом вместо инта в бд - плохая идея.

#### Фронтенд:
* Не использовал дебаунс.
* Верстка не совсем аккуратная:
  * Не особо адаптивная. Дополнительный набор стилей нужно создать для экранов уже 1100 пикселей и меньше 1000 пикселей по высоте.
  * Некоторый больший набор стилей нужно было объединить в классы.
  * Не использовал препроцессоры.
  * Добавить больше тегов заголовков.
  * Злоупотреблял инлайн стилями.
* Возможно стоило:
  * Прокидывать объекты, а не множить пропсы.
  * Крутить цикл с лонгполом в app.vue, чтобы не обрывать запрос с помощью флага.
  * Убирать \<br> тэги на фронте, а не на бэке(у файрфокса div contenteditable).
  * Использовать не mounted хуки для загрузки данных.


#### Что можно в первую очередь добавить или исправить со временем:
* Добавить разного рода обработчики ошибок.
* Более правильно было бы отправлять время последнего визита на фронт. И на js рассчитывать свой diffForHumans. Придало бы интерактивности. Стоило сделать правила плюрализации для js'овского diffForhumans вместе с переводом.
* Частоту перекидывания данных о последнем онлайне юзеров из редиса в бд стоит уменьшить на основании данных о средней частоте покидания сайта пользователями.
* Часть данных можно закешировать:
  * Популярные пользователи.
  * Вместо вывода популярных постов для каждого пользователя, создать псевдоуникальный вывод - брать по 10 популярных постов у популярных групп и смешивать их.


&nbsp;
<a name="InstallationFromTheSystem">
  <h1>Запуск из-под системы🔥</h1>
</a>

1. Настраиваем nginx/apache. Смотрим в папку public(стандартно). Стандартный сервер не подойдет, т.к. использовался лонгпол.

2. Устанавливаем redis, cron, ffmpeg, драйвер к sqlite3:

  * &emsp; Устанавливаем redis и запускаем его. Пример установки снэп пакетом и его запуск:
  ```console
  sudo apt install redis-server
  ```
  
  ```console
  /etc/init.d/redis-server start
  ```
  
  * &emsp; Устанавливаем планировщик и запускаем в нем каждоминутное выполнение задачи(на примере крон):
    * Пример установки снэп пакетом крона
  ```console
  sudo apt install cron
  ```
  * &emsp; Открываем крон юзера.
    
  ```console
  crontab -e
  ```

  * &emsp; Прописываем в него.
  ```console
  SHELL=/bin/bash
  HOME=/path/to/project  - изменяем на директорию проекта.

  * * * * * php artisan schedule:run > /dev/null 2>&1
  ```

  * &emsp; Устанавиливаем ffmpeg.
  ```console
  sudo apt install ffmpeg
  ```

3. Копируем с github:
  * &emsp; 1 пример:

```console
git clone https://github.com/Achernov7/social.git
```

  * &emsp; 2 пример(с иницилизированным репризиторием):
```console
git remote add origin https://github.com/Achernov7/social.git
```
```console
git pull origin master
```

4. Устанавливаем пакеты:
  * из composer:
  ```console
  composer install
  ```
  * из package:
```console
npm install
```

5. Создаем .env из .env.example:
```console
cp .env.example .env
```

6. Генерируем ключ:
```console
php artisan key:generate
```

7. Создаем таблицы в бд:
```console
php artisan migrate
```

8. Заполняем их рандомными данными:
```console
php artisan db:seed
```

9. Добавляем в env(при работе с апачем):

```
APP_URL=domainOfApache:portOfApache (пример: example.org:8001)
SANCTUM_STATEFUL_DOMAINS=domainOfApache:portOfApache (пример: example.org:8001)
SESSION_DOMAIN=domainOfApache (пример: example.org)
```

10. Даем допуск на запись группе www-data ко всем папкам в /storage и к папке с базой данных /database/database и самой базе данных.

11. Запускаем команду(опционально):
```console
php artisan optimize:clear
```

12. Создаем ссылку на сторадж:
```console
php artisan storage:link
```

13. Запускаем vite сервер:
```console
npm run dev
```

14. Запускаем сервер laravel websocket:
```console
php artisan websockets:serve
```

Юзера нужно посмотреть в бд. Желательно выбрать у которого id=creator_id в таблице groups. Пароль для входа - 2 имя пользователя. (Например: при имени Orie -> пароль: OrieOrie)

<a name="InstallationFromTheDocer">
  <h1>Запуск из-под докера🔥</h1>
</a>

1. Устанавливаем крон и драйвер sqlite3.

2. Отключить редис(если на хосте работает):
  ```console
  /etc/init.d/redis-server stop
  ```

3. Копируем с github:
  * &emsp; 1 пример:

```console
git clone https://github.com/Achernov7/social.git
```

  * &emsp; 2 пример(с иницилизированным репризиторием):
```console
git remote add origin https://github.com/Achernov7/social.git
```
```console
git pull origin master
```

4. Создаем .env из .env.example(docker):
```console
cp .env.exampledocker .env
```

5. Прописываем в кронтаб на внешний запуск:

```console
SHELL=/bin/bash
HOME=/path/to/project  - изменяем на директорию проекта.
* * * * * docker exec -i app_laravel php /var/www/artisan schedule:run >> /dev/null 2>&1
```

6. Строим контейнеры:

```console
docker compose build
```

7. Поднимаем контейнеры:

```console
docker compose up
```

8. Генерируем ключ:
```console
docker exec -it app_laravel php artisan key:generate
```

9. Создаем таблицы в бд:
```console
docker exec -it app_laravel php artisan migrate --force
```

10. Заполняем их рандомными данными:
```console
docker exec -it app_laravel php artisan db:seed
```

11. Создаем ссылку на сторадж:
```console
docker exec -it app_laravel php artisan storage:link
```

12. Раскомментируем в vite.config.js:

![Расскоментируем vite](https://github.com/Achernov7/images/blob/master/ucommentConfig.png)

13. Раскомментируем в resources/js/bootstrap.js строчку с docker и верхнюю закомментируем(как на скрине):

![Раскомментируем Bootstrap](https://github.com/Achernov7/images/blob/master/uncommentBootstrap.png)

14. Переходим по ссылке: localhost:8871

Юзера нужно посмотреть в бд. Желательно выбрать того, у которого id=creator_id в таблице groups. Пароль для входа - 2 имя пользователя. (Например: при имени Orie -> пароль: OrieOrie)


<a name="VideoLink">
  <h1>Ссылка на видео с демонстрацией основного функционала приложения🔥</h1>
</a>

[<img src="https://github.com/Achernov7/images/blob/master/output.gif">](https://drive.google.com/open?id=1POOiBfZ0MGM1jz0NP4ZxLnzoXmbQw-as&usp=drive_copy)