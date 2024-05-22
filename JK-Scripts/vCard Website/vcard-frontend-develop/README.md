# VCARD frontend

## Запуск локального сервера для разработки сайта ## Starting a local server for website development

```bash
# Установить зависимости # Install dependencies
$ npm ci

# Запустить локальный сервер # Start local server
$ npm run dev
```

## Сборка приложения для продакшн сервера ## Building an application for a production server

### Приложение собирается из master ветки ### The application is built from the master branch

```bash
# Перейти в папку деплоера # Go to deployment folder
$ cd deploy

# Установить зависимости # Install dependencies
$ npm ci

# Запустить сборку # Run build
$ npx shipit prod deploy
```

## Откат приложения к предыдущей версии на продакшн сервере ## Rolling back an application to a previous version on the production server

```bash
# Перейти в папку деплоера # Go to the deployer folder
$ cd deploy

# Установить зависимости # Install dependencies
$ npm ci

# Запустить сборку # Run build
$ npx shipit prod rollback
```
