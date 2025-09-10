<?php

return [
    "menu" => [
        [
            "title" => "Data Master",
            "icon" => "<i class='fas fa-database'></i>",
            "sub_menu" => [
                [
                    "title" => "Kode Akun",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Barang Dagang",
                    "method" => ["Index", "Form"],
                ],
                // [
                //     "title" => "Barang Konsinyasi",
                //     "method" => ["Index", "Form"],
                // ],
                [
                    "title" => "Pegawai",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Supplier",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Pasien",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Tarif Tindakan",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Aset/Inventaris",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Unsur Gaji",
                    "method" => ["Index"],
                ],
                [
                    "title" => "Metode Bayar",
                    "method" => ["Index", "Form"],
                ],
            ]
        ],
        [
            "title" => "Pengaturan",
            "icon" => "<i class='fas fa-cog'></i>",
            "sub_menu" => [
                [
                    "title" => "Harga Jual",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Jam Kerja",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Nakes",
                    "method" => ["Index", "Form"],
                ],
            ]
        ],
        [
            "title" => "Rekapitulasi Stok",
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
            "title" => "Pengadaan",
            "icon" => "<i class='fas fa-cubes'></i>",
            "sub_menu" => [
                [
                    "title" => "Permintaan",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Pembelian",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Verifikasi",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Barang Masuk",
                    "method" => ["Index", "Form"],
                ]
            ]
        ],
        [
            "title" => "Klinik",
            "icon" => "<i class='fas fa-stethoscope'></i>",
            "sub_menu" => [
                [
                    "title" => "Registrasi",
                    "method" => ["Index", "Data"],
                ],
                [
                    "title" => "Pemeriksaan Awal",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Diagnosis",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Tindakan",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Kasir",
                    "method" => ["Index", "Form"],
                ]
            ]
        ],
        [
            "title" => "Jurnal Keuangan",
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
            "icon" => "<i class='fa fa-shopping-cart'></i>",
            "method" => ["Index", "Data"],
        ],
        [
            "title" => "Hak Akses",
            "icon" => "<i class='fa fa-cog'></i>",
            "method" => ["Index", "Form"],
        ],
    ]
];
