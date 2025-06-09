<?php

class Config
{
    private static function is_production() {
        return isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== 'localhost';
    }

    public static function DB_HOST()
    {
        return self::is_production() 
            ? 'database-web-do-user-23068801-0.e.db.ondigitalocean.com'
            : 'localhost';
    }

    public static function DB_SCHEME()
    {
        return Config::get_env('DB_SCHEME', 'hercafe');
    }

    public static function DB_USERNAME()
    {
        return self::is_production() 
            ? 'doadmin'
            : 'root';
    }

    public static function DB_PASSWORD()
    {
        return self::is_production() 
            ? 'AVNS_d09-jvxLlZx5NociUec'
            : '';
    }

    public static function DB_PORT()
    {
        return self::is_production() 
            ? '25060'
            : '3306';
    }

    public static function JWT_SECRET()
    {
        return Config::get_env('JWT_SECRET', 'aydin');
    }

    public static function IMGUR_CLIENT_ID()
    {
        return Config::get_env('IMGUR_CLIENT_ID', 'your_imgur_id');
    }

    public static function GOOGLE_CLIENT_ID()
    {
        return Config::get_env('GOOGLE_CLIENT_ID', 'your_google_console_id');
    }

    public static function GOOGLE_CLIENT_SECRET()
    {
        return Config::get_env('GOOGLE_CLIENT_SECRET', 'your_google_console_secret');
    }

    public static function GOOGLE_REDIRECT_URI()
    {
        return Config::get_env('GOOGLE_REDIRECT_URI', 'http://localhost/my-uni-blog/rest/google-callback');
    }

    public static function APP_BASE_URL()
    {
        return Config::get_env('APP_BASE_URL', 'http://localhost/my-uni-blog');
    }

    public static function get_env($name, $default)
    {
        return isset($_ENV[$name]) && trim($_ENV[$name]) != '' ? $_ENV[$name] : $default;
    }
}