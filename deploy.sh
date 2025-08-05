#!/bin/bash
set -e

echo "🚀 Deploying Standplaner to Production"

# Prüfe ob .env.prod vorhanden ist
if [ ! -f .env.prod ]; then
    echo "❌ .env.prod file not found!"
    echo "Please copy .env.prod.example to .env.prod and configure it."
    exit 1
fi

# Lade Environment-Variablen
export $(cat .env.prod | xargs)

echo "📦 Pulling latest Docker image..."
docker compose -f docker-compose.prod.yml --env-file .env.prod pull

echo "🔄 Updating services..."
docker compose -f docker-compose.prod.yml --env-file .env.prod up -d

echo "🧹 Cleaning up old images..."
docker image prune -f

echo "📊 Checking service status..."
docker compose -f docker-compose.prod.yml --env-file .env.prod ps

echo "✅ Deployment completed!"
echo "🌐 Application should be available at: https://${DOMAIN}"
echo "📊 Traefik Dashboard: https://traefik.${DOMAIN}"

# Optional: Warte auf Health Check
echo "⏳ Waiting for application to be healthy..."
timeout 60 bash -c 'until curl -f http://localhost/health; do sleep 2; done' || echo "⚠️  Health check timed out"

echo "🎉 Deployment finished successfully!"
