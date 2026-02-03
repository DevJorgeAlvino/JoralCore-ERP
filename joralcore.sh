#!/bin/bash

# Colores para mensajes
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${CYAN}🚀 Iniciando preparación del entorno JoralCore...${NC}"

# 1. Instalar dependencias PHP
echo -e "\n${YELLOW}📦 Instalando dependencias de Backend (Composer)...${NC}"
composer install

# 2. Instalar dependencias JS
echo -e "\n${YELLOW}🎨 Instalando dependencias de Frontend (NPM)...${NC}"
npm install
npm run build

# 3. Preparar .env
if [ ! -f .env ]; then
    echo -e "\n${YELLOW}📄 Creando archivo de configuración .env...${NC}"
    cp .env.example .env
else
    echo -e "\n${GREEN}✅ El archivo .env ya existe.${NC}"
fi

# 4. Mensaje Final
echo -e "\n${GREEN}-------------------------------------------------------------${NC}"
echo -e "${GREEN}✅ Dependencias instaladas correctamente.${NC}"
echo -e "${GREEN}-------------------------------------------------------------${NC}"
echo -e "${YELLOW}⚠️  PASO OBLIGATORIO:${NC}"
echo -e "1. Abre el archivo ${CYAN}.env${NC} ahora mismo."
echo -e "2. Configura las credenciales de tu Base de Datos (DB_DATABASE, DB_USERNAME, etc)."
echo -e "3. Guarda los cambios."
echo -e "\nCuando hayas terminado, ejecuta el siguiente comando mágico para finalizar:"
echo -e "\n    ${CYAN}php artisan joral:install${NC}\n"