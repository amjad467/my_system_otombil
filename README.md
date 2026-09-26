# Vehicle Movement System - Laravel 11

سیستەمی بەڕێوەبردنی شۆفێر و ئۆتۆمبێل بۆ تۆمارکردنی دەرچوون و گەڕانەوە.

## تایبەتمەندییەکان
- Login و Logout
- دوو Role: Admin و Driver
- بەڕێوەبردنی شۆفێرەکان تەنها بۆ Admin
- بەڕێوەبردنی ئۆتۆمبێلەکان تەنها بۆ Admin
- ژمارەی ئۆتۆمبێل Unique
- تۆمارکردنی دەرچوون بە کاتی خۆکار
- تۆمارکردنی مەبەست، هۆکار و تێبینی
- تۆمارکردنی گەڕانەوە
- ڕێگری لە بەکارهێنانی دووبارەی ئۆتۆمبێلی لە دەرەوە
- Dashboard
- مێژووی گەشتەکانی شۆفێر
- راپۆرت بە فلتەری بەروار/شۆفێر/ئۆتۆمبێل/دۆخ/مەبەست
- چاپ و CSV
- Responsive UI بۆ مۆبایل و کۆمپیوتەر
- RTL/Kurdish UI

## دامەزراندن
1. PHP 8.2+ و MySQL یان SQLite و Composer دابین بکە.
2. `.env` ڕێکبخە بەپێی Database ـەکەت.
3. `composer install`
4. `php artisan key:generate` (ئەگەر APP_KEY نەبوو)
5. `php artisan migrate --seed`
6. `php artisan serve`

## هەژماری تاقیکردنەوە
Admin:
- Email: `admin@example.com`
- Password: `Admin@12345`

Driver:
- Email: `driver@example.com`
- Password: `Driver@12345`

لە ژینگەی ڕاستەقینەدا وشەی نهێنیی ئەم هەژمارانە بگۆڕە.

## تێبینی
PDF package ـێکی تایبەت زیاد نەکراوە؛ راپۆرتەکان بە Print ـی browser چاپ دەکرێن و CSV هەیە. دەتوانرێت DomPDF/Excel لە deployment ـی کۆتایی زیاد بکرێت.
