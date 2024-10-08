## Запуск проекта
<br>
<br>
<strong>Требования для запуска проекта:</strong><br>
- регистрация на github.com (важно наличие ssh ключей связанных с аккаунтом);<br>
- установленный в системе git;<br>
- Docker с версией 18.06.0+ (совместимый с docker compose 3.7);<br>
<br>
<strong>Шаги по установке проекта:</strong><br>
1. Клонируем репозиторий командой:<br>
<i>git clone https://github.com/sneef/ftt_task.git</i><br>
2. Копируем файлы ключей (id_rsa, id_rsa.pub) в папку .docker-conf/.ssh/<br>
3. Запускаем команды в корне проекта:<br>
<i>cp .env.example .env</i><br>
<i>docker-compose build app --no-cache</i><br>
<i>docker-compose up -d</i><br>
4. После автоматической сборки образа и всего проекта в целом, должны запуститься все контейнеры (их всего 4)<br>
5. Проверить открылся ли сайт проекта по ссылке:<br>
<i>http://localhost:8020/</i><br>
6. Подключившись внутрь контейнера ftt-app в корне проекта (/var/www/html/) выполнить следующую команду для запуска воркера:<br>
<i>php artisan queue:work</i><br>
7. Открыть в браузере ссылку для создания воркеру задачи:<br>
<i>http://localhost:8020/currency/index</i><br>
8. Ожидать окончания работы воркера (в терминале перестанут подгружаться изменения)<br>
9. Остановить работу очереди командой:<br>
<i>php artisan queue:clear</i><br>
10. Проверить результаты в файле /storage/logs/laravel.log<br>
11. Запустить повторно команды из пунктов 6-9 чтобы проверить работает ли кеш<br>
<br>
<br>
<br>
<br>
<stong>Характеристики проекта:</strong><br>
- nginx:1.17-alpine<br>
- postgres:16.2-alpine3.19<br>
- redis:7.2.4-alpine<br>
- php:8.3-fpm<br>