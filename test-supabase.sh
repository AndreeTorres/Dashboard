#!/bin/bash

echo "🧪 Probando conexión a Supabase..."

# Probar conexión a la base de datos
php artisan migrate:status

if [ $? -eq 0 ]; then
    echo "✅ Conexión exitosa a Supabase!"
    echo "🚀 Ejecutando migraciones..."
    php artisan migrate --force
    
    echo "🛡️ Configurando permisos..."
    php artisan shield:install --fresh
    
    echo "📊 Base de datos lista para producción!"
else
    echo "❌ Error de conexión. Verifica:"
    echo "   - Credenciales en .env"
    echo "   - Conexión a internet"
    echo "   - Configuración de Supabase"
fi
