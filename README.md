# 🚀 JoralCore ERP - SaaS Multi-Tenancy System

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-3.x-F2C14E?style=for-the-badge&logo=livewire&logoColor=black)
![React](https://img.shields.io/badge/React-18-61DAFB?style=for-the-badge&logo=react&logoColor=black)
![TailwindCSS](https://img.shields.io/badge/Tailwind-CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

**JoralCore ERP** es una plataforma robusta diseñada para la gestión empresarial multi-empresa (Multi-Tenancy). Construida con la potencia de Laravel 11, FilamentPHP v3 y React/Inertia, incluye una arquitectura escalable con separación de datos por inquilino y personalización dinámica.

---

## ✨ Características y Módulos Principales (Estado Actual)

### 🏢 1. Arquitectura Multi-Tenant y Paneles Separados
* **Aislamiento Total de Datos:** Estructura de base de datos preparada para separar la información por empresa.
* **Admin Panel (Super Admin):** Panel de control global para la gestión general del SaaS, clientes y configuraciones del sistema.
* **Company Panel (Inquilino):** Entorno exclusivo para cada empresa y sus empleados.

### 🎨 2. Branding Dinámico Multi-Tenant
* **Personalización en Tiempo Real:** Cada empresa puede configurar su propia identidad visual desde su panel.
* **Logotipos y Favicon:** Subida y renderizado de logos específicos por inquilino.
* **Colores Dinámicos:** Personalización de colores primarios y secundarios que sobreescriben la configuración global de Filament mediante middlewares.

### 📦 3. Maestro de Ítems (Inventario Clínico)
* **Gestión de Ítems y Unidades de Medida:** Recursos completos para catalogar productos o servicios.
* **Galería Multi-Imagen:** Sistema avanzado de subida de imágenes para cada ítem, organizadas en galerías clínicas inferiores.
* **Almacenamiento Estructurado:** Movimiento perfecto de archivos desde almacenamiento temporal hacia rutas definitivas (`companies/{company_id}/items/{item_id}/images`) compatible con discos locales o Cloudflare R2.

### 🛡️ 4. Seguridad y Roles
* **Filament Shield:** Gestión granular de roles y permisos para usuarios, permitiendo definir accesos detallados dentro de cada panel.

### 🖥️ 5. Landing Page e Interfaz de Usuario
* **Welcome Page Renovado:** Página de inicio (Landing) desarrollada en React (Inertia.js) con un diseño minimalista, efectos "glassmorphism", responsividad (incluyendo vista apaisada para móviles) y flujos de navegación claros hacia los portales de Admin y Compañía.
* **Localización (i18n):** Archivos de idioma configurados para soportar traducciones en toda la aplicación.

---

## 🛠️ Requisitos del Sistema

* PHP 8.2 o superior
* Composer
* Node.js & NPM
* Git
* Base de datos (MySQL/MariaDB/PostgreSQL)

---

## 📥 Instalación

### 1. Clonar el Repositorio
```bash
git clone https://github.com/DevJorgeAlvino/JoralCore-ERP.git JoralCore-ERP
cd JoralCore-ERP
```

### 2. Configuración y Dependencias
Ejecuta el script de preparación rápida:
```bash
sh joralcore.sh
```

### 3. Configurar Base de Datos (`.env`)
Configura tus credenciales en el archivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=joralcore_erp
DB_USERNAME=root
DB_PASSWORD=root
```

### 4. Instalación de JoralCore
```bash
php artisan joral:install --force
```

---
*Documentación actualizada automáticamente reflejando las últimas implementaciones.*
