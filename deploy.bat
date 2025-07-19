@echo off
echo 🚀 Préparation du déploiement Monopti sur Railway...

REM Vérifier si nous sommes dans un repository git
if not exist ".git" (
    echo ❌ Erreur: Ce n'est pas un repository Git
    echo Initialisez d'abord un repository avec: git init
    pause
    exit /b 1
)

REM Vérifier si les fichiers Docker existent
if not exist "Dockerfile" (
    echo ❌ Erreur: Dockerfile manquant
    pause
    exit /b 1
)

if not exist "railway.json" (
    echo ❌ Erreur: railway.json manquant
    pause
    exit /b 1
)

echo ✅ Vérification des fichiers de configuration...

REM Générer une clé d'application si elle n'existe pas
if not exist ".env" (
    echo 📝 Création du fichier .env...
    copy .env.example .env
    php artisan key:generate
)

echo 🔑 Clé d'application générée:
php artisan key:generate --show

echo.
echo 📋 Variables d'environnement à configurer sur Railway:
echo ==================================================
echo APP_NAME=Monoptic
echo APP_ENV=production
echo APP_DEBUG=false
for /f "tokens=*" %%i in ('php artisan key:generate --show') do echo APP_KEY=%%i
echo APP_URL=https://votre-domaine.railway.app
echo.
echo DB_CONNECTION=pgsql
echo DB_HOST=${{Postgres.PGHOST}}
echo DB_PORT=${{Postgres.PGPORT}}
echo DB_DATABASE=${{Postgres.PGDATABASE}}
echo DB_USERNAME=${{Postgres.PGUSER}}
echo DB_PASSWORD=${{Postgres.PGPASSWORD}}
echo.
echo SESSION_DRIVER=database
echo CACHE_STORE=database
echo QUEUE_CONNECTION=database
echo LOG_CHANNEL=stack
echo LOG_LEVEL=error
echo ==================================================
echo.

REM Vérifier si des changements doivent être committés
git status --porcelain > temp_status.txt
for %%A in (temp_status.txt) do if %%~zA gtr 0 (
    echo 📦 Ajout des fichiers au repository...
    git add .
    
    set /p commit_message="💬 Entrez un message de commit (ou appuyez sur Entrée pour le message par défaut): "
    if "%commit_message%"=="" set commit_message=feat: Add Railway deployment configuration
    
    git commit -m "%commit_message%"
    
    echo 🔄 Push vers le repository distant...
    git push origin main
    
    if %errorlevel% equ 0 (
        echo ✅ Code poussé avec succès!
    ) else (
        echo ❌ Erreur lors du push. Vérifiez votre repository distant.
        del temp_status.txt
        pause
        exit /b 1
    )
) else (
    echo ✅ Repository à jour, aucun changement à committer.
)

del temp_status.txt

echo.
echo 🎉 Préparation terminée!
echo.
echo 📋 Prochaines étapes:
echo 1. Allez sur https://railway.app
echo 2. Créez un nouveau projet
echo 3. Connectez votre repository GitHub/GitLab
echo 4. Ajoutez un service PostgreSQL
echo 5. Configurez les variables d'environnement (voir ci-dessus)
echo 6. Déployez!
echo.
echo 📖 Consultez DEPLOYMENT_GUIDE.md pour plus de détails.
pause
