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

## Execution

il faut toujours spécifier le chemin vers le fichier contenant les variables d'environements.  
Pour cela on a le fichier **Makefile**, pour facilité le lancement de différentes commandes **docker compose**