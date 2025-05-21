#!/bin/bash
# Ce script est destiné à un usage local pour basculer entre dev et prod.
# Nom des containers
PROD_CONTAINER_NAME="ecoride-prod"
DEV_CONTAINER_NAME="ecoride_apache"

echo "----------------------------"
echo "  🔄 Basculer entre modes  "
echo "----------------------------"
echo "1) Lancer en développement"
echo "2) Lancer en production"
echo "3) Stopper tous les containers"
echo "4) Quitter"
echo "----------------------------"
read -p "Ton choix : " choix

case $choix in
1)
echo "🧹 Suppression éventuelle de prod..."
docker stop $PROD_CONTAINER_NAME 2>/dev/null && docker rm $PROD_CONTAINER_NAME 2>/dev/null

echo "🔁 Lancement du mode développement..."
docker-compose up --build
;;
2)
echo "🧹 Suppression éventuelle des containers dev..."
docker-compose down

echo "🏗️  Construction et lancement du conteneur de prod..."
docker build -t $PROD_CONTAINER_NAME -f apache/Dockerfile .
docker run -d -p 8080:80 --name $PROD_CONTAINER_NAME $PROD_CONTAINER_NAME
;;
3)
echo "⛔️ Arrêt et suppression de tous les containers liés à EcoRide..."
docker stop $PROD_CONTAINER_NAME $DEV_CONTAINER_NAME ecoride_mysql ecoride_mongo ecoride_phpmyadmin 2>/dev/null
docker rm $PROD_CONTAINER_NAME $DEV_CONTAINER_NAME ecoride_mysql ecoride_mongo ecoride_phpmyadmin 2>/dev/null
;;
4)
echo "👋 Bye !"
exit 0
;;
*)
echo "❌ Choix invalide."
;;
esac