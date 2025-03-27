#!/bin/bash

echo "🔧 Configurando hooks personalizados..."

# Verificar si la carpeta hooks existe
if [ -d "./hooks" ]; then
    # Copiar todos los hooks personalizados a .git/hooks
    cp -r ./hooks/* .git/hooks/
    
    # Dar permisos de ejecución a todos los hooks
    chmod +x .git/hooks/*

    echo "✔️ Hooks personalizados configurados correctamente."
else
    echo "❌ Carpeta 'hooks' no encontrada. Asegúrate de que exista en la raíz del proyecto."
fi
