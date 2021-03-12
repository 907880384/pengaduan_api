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
        "label" => "Master Pengguna",
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
        "label" => "Master Barang",
        "components" => [
          [
            "to" => "admin",
            "title" => "Barang Masuk",
            "route" => '/products',
            "icon" => "fab fa-product-hunt",
          ],
          [
            "to" => "admin",
            "title" => "Barang Keluar",
            "route" => '/orders',
            "icon" => "fa fa-external-link",
          ],
          
        ]
      ],
      [
        "slug" => "all",
        "label" => "Info Pengaduan",
        "components" => [
          [
            "to" => "all",
            "title" => "Pengaduan",
            "route" => '/complaints',
            "icon" => "fas fa-comments",
          ]
        ], 
      ],
      [
        "slug" => "all",
        "label" => "Info Tamu", 
        "components" => [
          [
            "to" => "all",
            "title" => "Buku Tamu",
            "route" => '/visitors',
            "icon" => "fas fa-users",
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
            "route" => '/logs',
            "icon" => "fas fa-clipboard-list",
          ],
        ],
      ],
      
    ];

  }
}