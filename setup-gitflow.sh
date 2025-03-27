#!/bin/bash

echo "Iniciando configuración automática de Git Flow..."

# Verificar si Git Flow está instalado
if ! command -v git-flow &> /dev/null
then
    echo "❌ Git Flow no está instalado. Por favor, instálalo e intenta nuevamente."
    exit
fi

# Verificar si el proyecto está inicializado como un repositorio Git
if [ ! -d .git ]; then
    echo "❌ Este directorio no es un repositorio Git. Por favor, inicia un repositorio primero con 'git init'."
    exit
fi

# Verificar si Git Flow ya fue configurado anteriormente
if [ -f ".git/.gitflow_configured" ]; then
    echo "✔️ Git Flow ya está configurado en este proyecto. Omitiendo configuración..."
    exit
fi

# Configurar Git Flow automáticamente
echo "Configurando Git Flow..."
git flow init -d

# Crear archivo de estado para indicar que Git Flow ya fue configurado
touch .git/.gitflow_configured
echo "✔️ Git Flow configurado exitosamente con la estructura por defecto."

# Obtener la rama actual
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)

# Si la rama actual es 'main', cambiar a 'develop'
if [ "$CURRENT_BRANCH" = "main" ]; then
    echo "Cambiando a la rama 'develop'..."
    git checkout develop || { echo "❌ La rama 'develop' no existe localmente. Clonando desde remoto..."; git fetch origin develop && git checkout -b develop origin/develop; }
fi

echo "📌 Actualizando ramas remotas..."
git fetch origin

echo "✔️ Proyecto configurado correctamente para usar Git Flow."

echo "------------------------------------"
echo "📌 Instrucciones rápidas de uso:"
echo "Para crear una feature: git flow feature start nombre-feature"
echo "Para terminar una feature: git flow feature finish nombre-feature"
echo "Para crear una release: git flow release start nombre-release"
echo "Para terminar una release: git flow release finish nombre-release"
echo "Para crear un hotfix: git flow hotfix start nombre-hotfix"
echo "Para terminar un hotfix: git flow hotfix finish nombre-hotfix"
echo "------------------------------------"
