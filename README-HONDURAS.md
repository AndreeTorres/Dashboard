# 🍽️ Karina's Dashboard - Honduras

Sistema de gestión para restaurantes desarrollado con Laravel y Filament, **configurado específicamente para Honduras** con moneda Lempiras (HNL) e ISV del 15%.

## 🇭🇳 Configuraciones para Honduras

- **Moneda**: Lempiras Hondureñas (L)
- **Impuesto**: ISV del 15%
- **Zona Horaria**: América/Tegucigalpa
- **Idioma**: Español (es)
- **Usuario Admin**: admin@restaurante.com

## 🚀 Instalación Rápida

### 1. Configurar Entorno de Desarrollo

```bash
# Copiar configuración
cp .env.example .env

# Instalar dependencias
composer install
npm install

# Configurar aplicación
php artisan key:generate
```

### 2. Configurar Base de Datos

Edita `.env`:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=restaurante_dashboard
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

### 3. Ejecutar Migraciones

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### 4. Iniciar Servidor

```bash
php artisan serve
npm run dev
```

**Acceso**: http://localhost:8000/admin
- Usuario: `admin@restaurante.com`
- Contraseña: `password`

## 🏗️ Deploy a Producción

### Opción 1: Script Automático
```bash
chmod +x deploy.sh
./deploy.sh
```

### Opción 2: Manual
```bash
cp .env.production.example .env
# Editar .env con tus configuraciones

composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan optimize
```

## ⚙️ Variables de Entorno para Producción

Ver archivo: `.env.production.example`

Variables críticas:
```bash
APP_NAME="Karina's Dashboard"
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE="America/Tegucigalpa"
VAT_RATE=0.15  # ISV Honduras 15%

# Base de datos
DB_CONNECTION=mysql
DB_DATABASE=restaurante_dashboard
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password_seguro

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=tu-email@gmail.com
```

## 📱 Funcionalidades

- ✅ Gestión de Pedidos
- ✅ Inventario de Productos  
- ✅ Control de Gastos
- ✅ Gestión de Mesas
- ✅ Reportes y Estadísticas
- ✅ Sistema de Usuarios con Roles

## 🔧 Comandos Útiles

```bash
# Limpiar cache
php artisan optimize:clear

# Reset de base de datos
php artisan migrate:fresh --seed

# Generar nueva clave
php artisan key:generate
```

## 🛡️ Seguridad

**IMPORTANTE**: Después de la instalación:

1. Cambiar contraseña del admin
2. Configurar HTTPS
3. Revisar permisos de archivos
4. Configurar backups

---

Para más detalles, consulta el README.md principal.
