<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Product Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration untuk product management
    |
    */

    // Upload path
    'upload_path' => 'assets/photoProduct',
    
    // Default product image
    'default_image' => 'assets/photoProduct/default_batik.svg',
    
    // Allowed image types
    'allowed_image_types' => ['png', 'jpg', 'jpeg', 'webp'],
    
    // Max image size (KB)
    'max_image_size' => 5120, // 5MB
    
    // Product sizes
    'sizes' => [
        'S'  => 'Small',
        'M'  => 'Medium',
        'L'  => 'Large',
        'XL' => 'Extra Large',
    ],
    
    // Product categories
    'categories' => [
        'pria'      => 'Pria',
        'wanita'    => 'Wanita',
        'anak'      => 'Anak-anak',
        'unisex'    => 'Unisex',
    ],
    
    // Materials
    'materials' => [
        'katun'     => 'Katun',
        'sutra'     => 'Sutra',
        'rayon'     => 'Rayon',
        'polyester' => 'Polyester',
    ],
    
    // Process types
    'processes' => [
        'press'     => 'Press',
        'tulis'     => 'Tulis',
        'cap'       => 'Cap',
        'kombinasi' => 'Kombinasi',
    ],
    
    // Sleeve types
    'sleeve_types' => [
        'pendek'    => 'Pendek',
        'panjang'   => 'Panjang',
        'tiga_perempat' => '3/4',
    ],
    
    // Discount limits
    'discount' => [
        'min' => 0,
        'max' => 100,
    ],
    
    // Stock alert threshold
    'low_stock_threshold' => 10,
    
];
