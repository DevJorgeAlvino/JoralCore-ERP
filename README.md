# 🚀 JoralCore - SaaS Multi-Tenancy System

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-3.x-F2C14E?style=for-the-badge&logo=livewire&logoColor=black)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind-CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

**JoralCore** es una plataforma SaaS robusta diseñada para la gestión empresarial multi-empresa (Multi-Tenancy). Construida con la potencia de Laravel y la elegancia de FilamentPHP, incluye gestión avanzada de roles (Shield), separación de datos por inquilino y una arquitectura escalable.

---

## ✨ Características Principales

* 🏢 **Arquitectura Multi-Tenant:** Aislamiento total de datos por empresa.
* 🛡️ **Seguridad Avanzada:** Gestión de roles y permisos granulares con Filament Shield.
* 👤 **Paneles Separados:**
    * **Admin Panel:** Para la gestión global del SaaS (Super Admin).
    * **Company Panel:** Entorno exclusivo para cada empresa y sus empleados.
* ⚡ **Stack Moderno:** Laravel 11 + Livewire + Tailwind CSS + Vite.

---

## 🛠️ Requisitos del Sistema

Antes de comenzar, asegúrate de tener instalado:
* PHP 8.2 o superior
* Composer
* Node.js & NPM
* Git
* Base de datos (MySQL/MariaDB/PostgreSQL)

---

## 📥 Instalación (Despliegue Rápido)

Sigue estos 3 pasos sencillos para levantar el proyecto desde cero en cualquier entorno.

### 1. Clonar el Repositorio
```bash
git clone [https://github.com/DevJorgeAlvino/JoralCore.git](https://github.com/DevJorgeAlvino/JoralCore.git)
cd JoralCore
```

### 2. Iniciar procesos internos
```bash
cp .env.example .env
```

### 3. Configurar las credenciales de la bd en el archivo .env
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1 <- direccion de la base de datos
DB_PORT=3306 <- puerto de la base de datos
DB_DATABASE=laravel <- nombre de la base de datos
DB_USERNAME=root <- usuario de la base de datos
DB_PASSWORD=root <- contraseña de la base de datos
```

### 4. Ejecutar el comando joral:install
```bash
php artisan joral:install --force
```

### 5. Y listo inicializa el proyecto.