#!/bin/bash
set -e

# Release Script für Standplaner
# Erstellt ein neues Git-Tag und löst damit den Docker-Build aus

# Farben für Output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Standplaner Release Script${NC}"
echo "==============================="

# Prüfe ob wir auf main branch sind
CURRENT_BRANCH=$(git branch --show-current)
if [ "$CURRENT_BRANCH" != "main" ]; then
    echo -e "${RED}❌ Error: You must be on 'main' branch to create a release${NC}"
    echo "Current branch: $CURRENT_BRANCH"
    exit 1
fi

# Prüfe ob working directory clean ist
if [ -n "$(git status --porcelain)" ]; then
    echo -e "${RED}❌ Error: Working directory is not clean${NC}"
    echo "Please commit or stash your changes first:"
    git status --short
    exit 1
fi

# Pull latest changes
echo -e "${BLUE}📥 Pulling latest changes...${NC}"
git pull origin main

# Zeige aktuelle Tags
echo -e "\n${BLUE}📋 Recent tags:${NC}"
git tag --sort=-version:refname | head -5

# Frage nach Version
echo -e "\n${YELLOW}Please enter the new version (e.g., v1.0.0, v1.2.3):${NC}"
read -r VERSION

# Validiere Version Format
if [[ ! $VERSION =~ ^v[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
    echo -e "${RED}❌ Error: Invalid version format. Use semantic versioning like v1.0.0${NC}"
    exit 1
fi

# Prüfe ob Tag bereits existiert
if git tag | grep -q "^$VERSION$"; then
    echo -e "${RED}❌ Error: Tag $VERSION already exists${NC}"
    exit 1
fi

# Frage nach Release Notes
echo -e "\n${YELLOW}Enter release notes (press Ctrl+D when finished):${NC}"
RELEASE_NOTES=$(cat)

# Bestätigung
echo -e "\n${BLUE}📦 Release Summary:${NC}"
echo "Version: $VERSION"
echo "Branch: $CURRENT_BRANCH"
echo "Release Notes:"
echo "$RELEASE_NOTES"
echo ""

echo -e "${YELLOW}Do you want to create this release? (y/N):${NC}"
read -r CONFIRM

if [[ $CONFIRM != [yY] ]]; then
    echo -e "${YELLOW}Release cancelled.${NC}"
    exit 0
fi

# Erstelle annotated tag
echo -e "\n${BLUE}🏷️  Creating tag $VERSION...${NC}"
git tag -a "$VERSION" -m "Release $VERSION

$RELEASE_NOTES"

# Push tag
echo -e "${BLUE}📤 Pushing tag to origin...${NC}"
git push origin "$VERSION"

echo -e "\n${GREEN}✅ Release $VERSION created successfully!${NC}"
echo -e "${BLUE}📊 You can monitor the Docker build at:${NC}"
echo "https://github.com/dnl-jst/standplaner/actions"
echo ""
echo -e "${BLUE}🐳 Once built, the image will be available at:${NC}"
echo "ghcr.io/dnl-jst/standplaner:$VERSION"
echo "ghcr.io/dnl-jst/standplaner:latest"
echo ""
echo -e "${BLUE}🚀 To deploy the new version, update your docker-compose.prod.yml:${NC}"
echo "image: ghcr.io/dnl-jst/standplaner:$VERSION"
