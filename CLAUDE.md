# Brite Sprint Planner

Интерактивная доска спринт-задач с PHP API и JSON хранилищем.

## Архитектура

- **`api.php`** — REST API, все данные хранит в JSON файлах
- **`index.php`** — SPA фронтенд (vanilla JS, никаких зависимостей)
- **`tasks.json`** — задачи
- **`activity.json`** — лог действий
- **`uploads/`** — загруженные файлы
- **`telegram.json`** — конфиг Telegram бота (создаётся через UI)
- **`manifest.json`** + **`sw.js`** — PWA

## API

Базовый URL: `https://185.251.91.174/plan/api.php`

### GET — список задач
```bash
curl -sk 'https://185.251.91.174/plan/api.php'
```

### POST — создать задачу
```bash
curl -sk -X POST 'https://185.251.91.174/plan/api.php' \
  -H 'Content-Type: application/json' \
  -d '{"title":"Название","app":"Brite","priority":"med","owner":"Марат","deadline":"2026-04-10","flag":"Описание","tags":["dev","ASO"],"subtasks":[{"label":"Подзадача 1","done":false}],"done":false,"blocker":false,"status":"todo"}'
```

### PUT — обновить задачу (частичное обновление по id)
```bash
curl -sk -X PUT 'https://185.251.91.174/plan/api.php' \
  -H 'Content-Type: application/json' \
  -d '{"id":1,"done":true,"status":"done"}'
```

Поля: title, app, priority (high/med/low), owner (Борис/Марат), deadline (YYYY-MM-DD), flag, blocker, done, status (todo/progress/done), tags (массив строк), subtasks (массив {label, done}), comments, attachments.

### DELETE — удалить задачу
```bash
curl -sk -X DELETE 'https://185.251.91.174/plan/api.php' \
  -H 'Content-Type: application/json' \
  -d '{"id":1}'
```

### POST ?action=reorder — изменить порядок
```bash
curl -sk -X POST 'https://185.251.91.174/plan/api.php?action=reorder' \
  -H 'Content-Type: application/json' \
  -d '{"ids":[3,1,2,5,4]}'
```

### POST ?action=comment — добавить комментарий
```bash
curl -sk -X POST 'https://185.251.91.174/plan/api.php?action=comment' \
  -H 'Content-Type: application/json' \
  -d '{"task_id":1,"text":"Текст комментария","author":"Борис"}'
```

### POST ?action=delete_comment — удалить комментарий
```bash
curl -sk -X POST 'https://185.251.91.174/plan/api.php?action=delete_comment' \
  -H 'Content-Type: application/json' \
  -d '{"task_id":1,"index":0}'
```

### POST ?action=upload — загрузить файл (multipart/form-data)
```bash
curl -sk -X POST 'https://185.251.91.174/plan/api.php?action=upload' \
  -F 'task_id=1' -F 'file=@screenshot.png'
```

### POST ?action=delete_attachment — удалить вложение
```bash
curl -sk -X POST 'https://185.251.91.174/plan/api.php?action=delete_attachment' \
  -H 'Content-Type: application/json' \
  -d '{"task_id":1,"file":"1234_screenshot.png"}'
```

### GET ?action=activity — лог действий
```bash
curl -sk 'https://185.251.91.174/plan/api.php?action=activity'
```

### Telegram конфиг
```bash
# Сохранить
curl -sk -X POST 'https://185.251.91.174/plan/api.php?action=telegram_config' \
  -H 'Content-Type: application/json' \
  -d '{"bot_token":"123:ABC","chat_id":"-100123"}'

# Тест
curl -sk -X POST 'https://185.251.91.174/plan/api.php?action=telegram_test'
```

## Структура задачи

```json
{
  "id": 1,
  "title": "Название задачи",
  "app": "Brite",
  "priority": "high|med|low",
  "owner": "Борис|Марат",
  "status": "todo|progress|done",
  "deadline": "2026-04-10",
  "flag": "Описание/комментарий",
  "blocker": false,
  "done": false,
  "tags": ["dev", "ASO"],
  "subtasks": [{"label": "Подзадача", "done": false}],
  "comments": [{"author": "Борис", "text": "Текст", "time": "2026-04-03T12:00:00+00:00"}],
  "attachments": [{"name": "file.png", "file": "1234_file.png", "size": 12345, "time": "2026-04-03T12:00:00+00:00"}],
  "created": "2026-04-03T12:00:00+00:00"
}
```

## Фичи фронтенда

- **Два вида**: список (List) и канбан-доска (Board) — переключатель в хедере
- **Inline добавление**: нажми N или кнопку "+", поле прямо в списке
- **Поиск**: "/" для фокуса, фильтрация по названию/приложению/тегам
- **Фильтры**: по владельцу, приоритету, статусу
- **Drag & Drop**: перетаскивание карточек, в kanban — между колонками
- **Быстрая смена**: клик по бейджу приоритета/статуса/аватару — попап с выбором
- **Inline редактирование**: двойной клик по заголовку/подзадаче
- **Теги**: цветные метки, "+" для добавления, клик для удаления
- **Комментарии**: лента под задачей с автором и временем
- **Вложения**: загрузка файлов к задачам
- **Дедлайны**: подсветка просроченных и скоро горящих
- **Конфетти**: при завершении задачи
- **История**: лог всех действий внизу страницы
- **PWA**: устанавливается как приложение на телефоне
- **Горячие клавиши**: N — новая задача, / — поиск, Esc — закрыть

## Установка на свой сервер

1. Склонировать репо
2. Положить файлы в любую папку на PHP-сервере (Apache/Nginx с PHP)
3. Убедиться что PHP может писать в папку (tasks.json, activity.json, uploads/)
4. Открыть index.php в браузере

## Slash-команда /pl для Claude Code

Файл `pl.md` — это slash-команда для Claude Code CLI. Чтобы подключить:

```bash
cp pl.md ~/.claude/commands/pl.md
```

После этого в Claude Code можно писать:
- `/pl` — показать все задачи
- `/pl добавь "Название" на Бориса, срочно` — создать задачу
- `/pl задача 3 выполнена` — обновить статус
- `/pl удали 7` — удалить
- `/pl что горит?` — просроченные и срочные
