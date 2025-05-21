<?php

return [
  /*
    |----------------------------------------------------------------------
    /*
    |----------------------------------------------------------------------
     /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |----------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

  'paths' => ['api/*'],  // Specify the paths to which CORS should apply  
  'allowed_methods' => ['*'],  // Allow any HTTP methods (GET, POST, etc.)  
  'allowed_origins' => ['http://localhost:3000', 'https://precioushairmpire-git-main-salawu-babatundes-projects.vercel.app'], // Allow requests from this origin  
  'allowed_headers' => ['Origin,Content-Type, X-Auth-Token,Authorization,X-Requested-With,Content-Range,Content-Disposition, Content-Description, x-csrf-token'],  // Allow any headers  
  'exposed_headers' => [],
  'max_age' => 17280000,
  'supports_credentials' => true,
];
