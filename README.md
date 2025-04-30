# ENV vars
 - copy .env.example into .env
 - put values
   - NYT_API_KEY=TNhNAIEsYc4xlp3yJo5ks8boA8RJWGl1
   - NYT_BASE_URL=https://api.nytimes.com
# SETUP
 - composer install
 - php artisan serve
# Tests
 - php artisan test
# API
 - http://localhost:8000/api/v1/bestsellers/history

 Body Example:
 {
    "author": "Rowling",
    "isbn": {
        "isbn10": "1546129952"
    }
}
