# 🗺️ Travel Record

A modern web application for recording and sharing your travels with photos, places, and custom hashtags.

## ✨ Features

- **📸 Trip Management** - Create and organize your trips with photos and details
- **🗺️ Interactive Map** - View all visited places on a global map
- **👥 Social Network** - Follow other travelers and share your trips
- **🏷️ Custom Hashtags** - Organize content with colored tags
- **📱 Responsive Design** - Works perfectly on desktop and mobile
- **🎨 Customizable Themes** - Choose your preferred color

## 🚀 Tech Stack

- **Backend**: Laravel 10 (PHP)
- **Frontend**: Vue.js 3 + Inertia.js
- **Styling**: Tailwind CSS
- **Maps**: Google Maps API
- **Database**: MySQL/PostgreSQL

## 📦 Installation

1. **Clone the repository**
   ```bash
   git clone [repository-url]
   cd travel-record
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start the application**
   ```bash
   npm run dev
   php artisan serve
   ```

## 🏗️ Frontend Structure

```
resources/js/
├── Components/          # Reusable components
│   ├── UI/             # Base UI components
│   ├── Trip/           # Trip-related components
│   ├── Friends/        # Friends-related components
│   └── Profile/        # Profile components
├── Pages/              # Application pages
│   ├── Home.vue        # Main dashboard
│   ├── NewTrip.vue     # Trip creation
│   ├── Map.vue         # Global map
│   └── [others]        # Other pages
└── Layouts/            # Application layouts
```

## 🎯 Main Features

### Trips
- ✅ Create trips with dates and destinations
- ✅ Add places via Google Maps
- ✅ Upload and manage photos
- ✅ Custom hashtags

### Social
- ✅ Follow/unfollow system
- ✅ Public and private profiles
- ✅ User search
- ✅ Friendship requests

### Map
- ✅ Global place visualization
- ✅ Custom markers
- ✅ Info windows with details
- ✅ Trip navigation

## 🔧 Configuration

### Required Environment Variables

```env
GOOGLE_MAPS_API_KEY=your_google_maps_api_key
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=travel_record
DB_USERNAME=root
DB_PASSWORD=
```

## 📱 Screenshots

*[Application screenshots]*

## 🤝 Contributing

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License. See the `LICENSE` file for details.

## 👨‍💻 Author

**Travel Record Team**

---

⭐ If you like this project, give it a star!
