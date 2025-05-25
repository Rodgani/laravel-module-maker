# 📦 Module Maker

**Module Maker** is a Laravel package that enhances the default `make:*` Artisan commands by generating files directly inside your custom `Modules/` structure — perfect for developers who prefer a modular architecture in Laravel.

---

## 🚀 Features

- 🏗️ Automatically generates Laravel files like controllers, models, requests, migrations, seeders, factories, and more.
- 📁 Moves generated files into `Modules/{Module}/` structure.
- 🧠 Updates namespaces to reflect the module directory.
- 🔁 Works seamlessly with existing `make:*` commands.
- 🧼 Cleans up empty directories after moving files.

---

## 📦 Installation

> ⚠️ This package is in development and **not yet published on Packagist**.

First step make sure register module at composer.json autoload

```
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/",
        "Modules\\": "modules/"
    }
},
```

You can install it via Composer using either a **local path** or directly from **GitHub**.

### 🔹 Option 1: Install from Local Path

1. Clone or move the package into your Laravel app: packages/Rodgani/module-maker
2. Add this to your Laravel project’s `composer.json`:
```
"repositories": [
  {
    "type": "path",
    "url": "packages/Rodgani/module-maker"
  }
],
```
3. Require the package: composer require rodgani/module-maker:dev-master

### 🔹 Option 2: Install from GitHub

1. Add this to your Laravel project’s composer.json:
```
"repositories": [
  {
    "type": "vcs",
    "url": "https://github.com/Rodgani/module-maker"
  }
],
```
2. Require the package: composer require rodgani/module-maker:dev-master

🧪 Usage
Use the following Artisan command to create Laravel classes inside your module:
```
php artisan make:module {type} {name} {module}

php artisan make:module controller UserController Blog
```

