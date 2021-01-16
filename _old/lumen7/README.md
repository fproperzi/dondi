# lumen7-auth-jwt
Authentication JWT Lumen 7

> https://blog.cacan.id/authentication-jwt-lumen-7

![000](https://user-images.githubusercontent.com/51890752/98467041-c928b080-2205-11eb-88bb-e0aa0546d2a6.jpg)


# Cara Penggunaan:

## Clone dari GitHub:
    git clone https://github.com/blogcacanid/lumen7-auth-jwt.git

## Lalu masuk ke direktori project:
    cd lumen7-auth-jwt

## Selanjutnya jalankan perintah berikut secara berurutan:
    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan jwt:secret
    php artisan migrate


## Database
Buat database baru dengan nama lumen7_auth_jwt
Dari command prompt ketikkan perintah berikut:

    mysql -uroot -p
    CREATE DATABASE lumen7_auth_jwt;

### Configure Database
Selanjutnya buka file .env folder root project kemudian edit bagian DB menjadi seperti berikut:

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=lumen7_auth_jwt
    DB_USERNAME=root
    DB_PASSWORD=j.fUjHyL


## Testing
Jalankan Lumen 7 dengan menggunakan perintah berikut:

    php -S localhost:8000 -t public

Lalu buka browser dan ketikkan URL http://localhost:8000


Untuk menjalankan Lumen 7 pada port tertentu, misalnya port 9090 anda bisa menjalankannya dengan mengetikkan perintah berikut:

    php -S localhost:9090 -t public


### Testing via Postman
Selanjutnya kita akan testing menggunakan Postman.

#### Register
Pertama-tama kita daftarkan user baru terlebih dahulu agar kita bisa melakukan login.
Buka postman lalu pilih method POST kemudian ketikkkan URL 

    http://localhost:8000/api/auth/register

Kemudian pilih tab Body. Lalu pada radiobox pilih raw dan typenya pilih JSON. 
Selanjutnya pada bagian textbox inputkan data registrasinya seperti berikut:

    {
	"username": "cacan",
	"email": "cacan@email.com",
	"password": "rahasia"
    }
    
Selanjutnya klik tombol Send

![001](https://user-images.githubusercontent.com/51890752/98467062-ecebf680-2205-11eb-8467-b6658baa8459.jpg)



#### Login
Setelah registrasi berhasil selanjutnya kita coba untuk login dengan user yang sudah kita registrasikan tersebut.
Buka postman lalu pilih method POST kemudian ketikkkan URL http://localhost:8000/api/auth/login
Kemudian pilih tab Body. Lalu pada radiobox pilih raw raw dan typenya pilih JSON. Selanjutnya pada bagian textbox inputkan data email dan password untuk login:

    {
	"username": "cacan",
	"password": "rahasia"
    }

Selanjutnya klik tombol Send

![002](https://user-images.githubusercontent.com/51890752/98467073-fd03d600-2205-11eb-8193-09a1cd79bef6.jpg)

Jika login berhasil, maka kita akan mendapatkan access token. 



# Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://poser.pugx.org/laravel/lumen-framework/d/total.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/lumen-framework/v/stable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://poser.pugx.org/laravel/lumen-framework/license.svg)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

## Official Documentation

Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs).

## Contributing

Thank you for considering contributing to Lumen! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Lumen, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
