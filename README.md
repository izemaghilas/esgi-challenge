## Configuration

ajouter le fichier **.env.local** à la racine du dossier **api**

```
APP_ENV=dev
APP_SECRET=

CORS_ALLOW_ORIGIN=

MESSENGER_TRANSPORT_DSN=

DB_HOST=
DB_USER=
DB_PASSWORD=
DB_NAME=
DATABASE_URL=
```

ajouter le fichier **.env.local** à la racine du dossier **front**
```
APP_API_URL=
```

## Execution

il faut toujours spécifier le chemin vers le fichier contenant les variables d'environements.  
Pour cela on a le fichier **Makefile**, pour facilité le lancement de différentes commandes **docker compose**

## Configuration de xdebug sur vscode

installer l'extension suivante sur vscode:
https://marketplace.visualstudio.com/items?itemName=xdebug.php-debug

ajouter le dossier **.vscode** à la racine du projet.
ajouter le fichier **launch.json** dans le dossier **.vscode**, puis entrer la configuration suivante:
```
{
    "configurations": [
        {
            "name": "Listen for XDebug on Docker", 
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/api/": "${workspaceFolder}/api"
            },
            "hostname": "localhost"
        }
    ]
}
```
