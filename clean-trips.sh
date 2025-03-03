echo "Riscrivo db"
php artisan migrate:fresh
php artisan db:seed
sleep 2
echo "Cancello immagini sul fs"
rm -rf storage/app/public/uploads/trips