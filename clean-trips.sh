echo "Riscrivo db"
php artisan migrate:fresh
sleep 2
echo "Cancello immagini sul fs"
rm -rf storage/app/public/uploads/trips