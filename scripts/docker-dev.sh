#!/bin/bash
COMPOSE_FILE="docker/docker-compose.yml"

case $1 in
    "start")
        echo "🚀 Démarrage environnement..."
        docker-compose -f $COMPOSE_FILE up -d
        ;;
    "stop")
        echo "⏹️ Arrêt environnement..."
        docker-compose -f $COMPOSE_FILE down
        ;;
    "build")
        echo "🔨 Build React..."
        docker-compose -f $COMPOSE_FILE exec app npm run dev
        ;;
    "migrate")
        echo "📊 Migration BDD..."
        docker-compose -f $COMPOSE_FILE exec app php artisan migrate
        ;;
    "test")
        echo "🧪 Tests..."
        docker-compose -f $COMPOSE_FILE exec app php artisan test
        ;;
    *)
        echo "Usage: ./scripts/docker-dev.sh [start|stop|build|migrate|test]"
        ;;
esac
