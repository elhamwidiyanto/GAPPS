<?php

return [
    'models' => [

        'menu' => BalajiDharma\LaravelMenu\Models\Menu::class,

        'menu_item' => BalajiDharma\LaravelMenu\Models\MenuItem::class,
    ],

    'table_names' => [

        'menus' => 'lara_menus',

        'menu_items' => 'lara_menu_items',
    ]
];
