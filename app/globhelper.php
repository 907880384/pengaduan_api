<?php

if(!function_exists('routerCollections')) {
  function routerCollections() {
    return [
      [
        "slug" => "all",
        "label" => "Dashboard", 
        "components" => [
          [
            "to" => "all",
            "title" => "Dashboard",
            "route" => '/dashboard',
            "icon" => "fas fa-fire",
          ]
        ]
      ],
      [
        "slug" => "admin",
        "label" => "Master Data",
        "components" => [
          [
            "to" => "admin",
            "title" => "Pengguna",
            "route" => '/users',
            "icon" => "fas fa-user",
          ],
          [
            "to" => "admin",
            "title" => "Role",
            "route" => '/roles',
            "icon" => "fas fa-user-tag",
          ],
        ]
      ],
      [
        "slug" => "all",
        "label" => "Information",
        "components" => [
          [
            "to" => "all",
            "title" => "Pengaduan",
            "route" => '/complaints',
            "icon" => "fas fa-comments",
          ],
        ], 
      ],
      [
        "slug" => "all",
        "label" => "Pengaturan", 
        "components" => [
          [
            "to" => "all",
            "title" => "Profile",
            "route" => '/profile',
            "icon" => "fas fa-id-card",
          ],
          [
            "to" => "admin",
            "title" => "Logs",
            "route" => '/roles',
            "icon" => "fas fa-cogs",
          ],
        ],
      ],
      
    ];

  }
}