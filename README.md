# 🌍 Travel Record

**Travel Record** is a travel tracking and sharing app built with Laravel and Vue.  
It allows users to record trips, add visited places with hashtags, and visualize their experiences on an interactive map.

---

## ✨ Features

- 🧳 **Trip management**: Create and view trips with start and end dates
- 📍 **Visited places**: Add locations with descriptions, coordinates, and images
- 🔖 **Hashtags**: Tag places to organize and filter experiences
- 🗺️ **Interactive map**: View all visited places using Google Maps with custom markers
- 🧠 **Smart hashtag summary**: See a full overview of hashtags used throughout a trip
- 🔒 **Privacy-aware profiles**: Users can set their profile and trips as public or private
- 🧭 **Follow system**: Follow other users and manage follow requests
- 🔎 **Full-text search**: Search places and hashtags with Meilisearch
- 🧩 **Ziggy integration**: Access Laravel routes directly from Vue

---

## ⚙️ Installation (with Laravel Sail)

```bash
git clone https://github.com/your-username/travelrecord.git
cd travelrecord

cp .env.example .env

composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan storage:link
./vendor/bin/sail artisan ziggy:generate resources/js/ziggy.js

./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
