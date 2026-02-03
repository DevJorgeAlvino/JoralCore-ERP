#!/bin/bash

echo "🚀 Iniciando Instalación Maestra de JoralCore..."

# 1. Instalar dependencias PHP (Esto crea la carpeta vendor)
echo "📦 Ejecutando Dependencias Composer y Npm"
composer install
npm install
npm run build

# 2. Copiar .env si no existe
if [ ! -f .env ]; then
    echo "📄 Creando archivo .env..."
    cp .env.example .env
fi

echo "✅ ¡TODO LISTO! Configura tu archivo .env y utiliza php artisan joral:install --force para instalar JoralCore..."
# Opcional: Abrir servidores
# php artisan serve & npm run dev