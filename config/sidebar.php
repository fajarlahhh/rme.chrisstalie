<?php

return [
    "menu" => [
        [
            "title" => "Data Master",
            "icon" => "<i class='fas fa-database'></i>",
            "urutkan" => true,
            "sub_menu" => [
                [
                    "title" => "Kode Akun",
                    "urutkan" => true,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Barang Dagang",
                    "urutkan" => true,
                    "method" => ["Index", "Form"],
                ],
                // [
                //     "title" => "Barang Konsinyasi",
                //     "method" => ["Index", "Form"],
                // ],
                [
                    "title" => "Pegawai",
                    "urutkan" => true,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Supplier",
                    "urutkan" => true,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Pasien",
                    "urutkan" => true,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Tarif Tindakan",
                    "urutkan" => true,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Aset/Inventaris",
                    "urutkan" => true,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Unsur Gaji",
                    "urutkan" => true,
                    "method" => ["Index"],
                ],
                [
                    "title" => "Metode Bayar",
                    "urutkan" => true,
                    "method" => ["Index", "Form"],
                ],
            ]
        ],
        [
            "title" => "Pengaturan",
            "icon" => "<i class='fas fa-cog'></i>",
            "urutkan" => true,
            "sub_menu" => [
                [
                    "title" => "Harga Jual",
                    "urutkan" => true,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Jam Kerja",
                    "urutkan" => true,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Nakes",
                    "urutkan" => true,
                    "method" => ["Index", "Form"],
                ],
            ]
        ],
        [
            "title" => "Rekapitulasi Stok",
            "urutkan" => true,
            "method" => ["Index"],
            "icon" => "<i class='fa fa-legal'></i>",
        ],
        // [
        //     "title" => "Laporan",
        //     "icon" => "<i class='fa fa-file-text'></i>",
        //     "sub_menu" => [
        //         [
        //             "title" => "Laba Rugi",
        //             "method" => ["Index"],
        //         ],
        //         [
        //             "title" => "Konsinyasi",
        //             "method" => ["Index"],
        //         ],
        //         [
        //             "title" => "Jasa Pelayanan",
        //             "method" => ["Index"],
        //         ],
        //         [
        //             "title" => "Pengadaan",
        //             "method" => ["Index"],
        //         ],
        //         [
        //             "title" => "Pengeluaran",
        //             "method" => ["Index"],
        //         ],
        //         [
        //             "title" => "LHK",
        //             "method" => ["Index"],
        //         ],
        //         [
        //             "title" => "Pengeluaran Gaji",
        //             "method" => ["Index"],
        //         ],
        //         [
        //             "title" => "Penerimaan",
        //             "sub_menu" => [
        //                 [
        //                     "title" => "Klinik",
        //                     "method" => ["Index"],
        //                 ],
        //                 [
        //                     "title" => "Klinik",
        //                     "method" => ["Index"],
        //                 ]
        //             ]
        //         ],
        //         [
        //             "title" => "Stok Barang",
        //             "method" => ["Index"],
        //         ]
        //     ]
        // ],
        [
            "title" => "Pengadaan Brg. Dagang",
            "icon" => "<i class='fas fa-cubes'></i>",
            "urutkan" => false,
            "sub_menu" => [
                [
                    "title" => "Permintaan",
                    "urutkan" => false,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Verifikasi",
                    "urutkan" => false,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Pembelian",
                    "urutkan" => false,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Stok Masuk",
                    "urutkan" => false,
                    "method" => ["Index", "Form"],
                ]
            ]
        ],
        [
            "title" => "Klinik",
            "icon" => "<i class='fas fa-stethoscope'></i>",
            "urutkan" => false,
            "sub_menu" => [
                [
                    "title" => "Registrasi",
                    "urutkan" => false,
                    "method" => ["Index", "Data"],
                ],
                [
                    "title" => "Pemeriksaan Awal",
                    "urutkan" => false,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Diagnosis",
                    "urutkan" => false,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Site Marking",
                    "urutkan" => false,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Tindakan",
                    "urutkan" => false,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Kasir",
                    "urutkan" => false,
                    "method" => ["Index", "Form"],
                ]
            ]
        ],
        [
            "title" => "Jurnal Keuangan",
            "urutkan" => true,
            "method" => ["Index", "Form"],
            "icon" => "<i class='fas fa-book'></i>"
        ],
        // [
        //     "title" => "Penjualan",
        //     "icon" => "<i class='fas fa-cash-register'></i>",
        //     "sub_menu" => [
        //         [
        //             "title" => "Data",
        //             "method" => ["Index"],
        //         ],
        //         [
        //             "title" => "Bebas",
        //             "method" => ["Index"],
        //         ],
        //         [
        //             "title" => "Resep",
        //             "method" => ["Index"],
        //         ]
        //     ]
        // ],
        [
            "icon" => "<i class='fas fa-info-circle'></i>",
            "title" => "Informasi Harga",
            "urutkan" => true,
            "method" => ["Index"],
        ],
        // [
        //     "icon" => "<i class='fas fa-users'></i>",
        //     "title" => "Informasi Pasien",
        //     "method" => ["Index"],
        // ],
        // [
        //     "icon" => "<i class='fas fa-dollar'></i>",
        //     "title" => "Gaji",
        //     "method" => ["Index", "Form"],
        // ],
        // [
        //     "icon" => "<i class='fas fa-dollar'></i>",
        //     "title" => "Pengeluaran",
        //     "method" => ["Index", "Form"],
        // ],
        // [
        //     "icon" => "<i class='fas fa-dollar'></i>",
        //     "title" => "Pelunasan Pengadaan",
        //     "method" => ["Index", "Form"],
        // ],       
        [
            "title" => "Penjualan",
            "urutkan" => true,
            "icon" => "<i class='fa fa-shopping-cart'></i>",
            "method" => ["Index", "Data"],
        ],
        [
            "title" => "Hak Akses",
            "urutkan" => true,
            "icon" => "<i class='fa fa-cog'></i>",
            "method" => ["Index", "Form"],
        ],
    ]
];
