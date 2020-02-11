## Instalation

```bash
git clone git@github.com:musikita/xyz-track.git
cd xyz-track
cp .env.example .env
composer install
php artisan key:generate
```

## Database Setup

Create your mysql database :
```sql
create database tracking;
```

Open file `.env` and edit line that seems like this :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tracking
DB_USERNAME=root
DB_PASSWORD=root
```

After that, do migration :
```bash
php artisan migrate
```

## Access via Broser
After database setup, you can access via browser

