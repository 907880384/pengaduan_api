<?php

if(!function_exists('routerCollections')) {
  function routerCollections() {
    return[
      //Dashboard
      [
        "slug" => [],
        "label" => "Dashboard",
        "components" => [
          [
            "title" => "Dashboard",
            "route" => '/dashboard',
            "icon" => "fas fa-fire",
          ]
        ]
      ],

      //Master Pengguna
      [
        "slug" => ["admin"],
        "label" => "Master Pengguna",
        "components" => [
          [
            "to" => ["admin"],
            "title" => "Pengguna",
            "route" => '/users',
            "icon" => "fas fa-user",
          ],
          [
            "to" => ["admin"],
            "title" => "Role",
            "route" => '/roles',
            "icon" => "fas fa-user-tag",
          ],
        ]
      ],

      //Master Barang
      [
        "slug" => ["admin", "cleaning-service", "teknisi", "security", "pest-control", "gardener"],
        "label" => "Master Barang",
        "components" => [
          [
            "to" => ["admin", "cleaning-service", "teknisi", "security", "pest-control", "gardener"],
            "title" => "Barang Masuk",
            "route" => '/products',
            "icon" => "fab fa-product-hunt",
          ],
          [
            "to" => ["admin"],
            "title" => "Barang Keluar",
            "route" => '/orders',
            "icon" => "fas fa-external-link-alt",
          ],
        ]
      ],

      //Info Pengaduan
      [
        "slug" => ["admin", "customer", "cleaning-service", "teknisi", "security", "pest-control", "gardener"],
        "label" => "Info Pengaduan",
        "except" => "receptionis",
        "components" => [
          [
            "to" => ["admin", "customer", "cleaning-service", "teknisi", "security", "pest-control", "gardener"],
            "title" => "Pengaduan",
            "route" => '/complaints',
            "icon" => "fas fa-comments",
          ]
        ], 
      ],

      //Info Tamu
      [
        "slug" => ["admin", "receptionis"],
        "label" => "Info Tamu", 
        "components" => [
          [
            "to" =>  ["admin", "receptionis"],
            "title" => "Buku Tamu",
            "route" => '/visitors',
            "icon" => "fas fa-users",
          ],
        ],
      ],

      //Pengaturan
      [
        "slug" => ["admin"],
        "label" => "Pengaturan", 
        "components" => [
          [
            "to" => ["admin"],
            "title" => "Logs",
            "route" => '/logs',
            "icon" => "fas fa-clipboard-list",
          ],
        ],
      ],

    ];
  }
}