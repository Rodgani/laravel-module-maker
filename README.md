# 📦 Laravel Module Maker

**Laravel Module Maker** is a Laravel package that enhances the default `make:*` Artisan commands by generating files directly inside your custom `Modules/` structure — perfect for developers who prefer a modular architecture in Laravel.

---

## 🚀 Features

- 🏗️ Automatically generates Laravel files like controllers, models, requests, migrations, seeders, factories, and more.
- 📁 Moves generated files into `Modules/{Module}/` structure.
- 🧠 Updates namespaces to reflect the module directory.
- 🔁 Works seamlessly with existing `make:*` commands.
- 🧼 Cleans up empty directories after moving files.

---

## 📦 Installation

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

You can install it via Composer using composer require rodgani/laravel-module-maker --dev

🧪 Usage
Use the following Artisan command to create Laravel classes inside your module:
```
php artisan make:module {type} {name} {module}

php artisan make:module controller UserController Blog
```

