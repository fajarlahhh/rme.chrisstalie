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
                    "title" => "ICD 10",
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
                // [
                //     "title" => "Unsur Gaji",
                //     "urutkan" => true,
                //     "method" => ["Index"],
                // ],
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
                    "title" => "Shift",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Harga Jual",
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
            "title" => "Penjualan",
            "urutkan" => true,
            "method" => ["Index", "Data"],
            "icon" => "<i class='fas fa-shopping-cart'></i>",
        ],
        [
            "title" => "Rekapitulasi Bulanan",
            "urutkan" => true,
            "method" => ["Index"],
            "icon" => "<i class='fa fa-legal'></i>",
        ],
        [
            "title" => "Informasi",
            "urutkan" => true,
            "icon" => "<i class='fa fa-info-circle'></i>",
            "sub_menu" => [
                [
                    "title" => "Pasien",
                    "urutkan" => true,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Barang Dagang",
                    "urutkan" => true,
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Tarif Tindakan",
                    "urutkan" => true,
                    "method" => ["Index", "Form"],
                ],
            ]
        ],
        [
            "title" => "Manajemen Stok",
            "urutkan" => true,
            "icon" => "<i class='fas fa-boxes'></i>",
            "sub_menu" => [
                [
                    "title" => "Opname",
                    "urutkan" => true,
                    "sub_menu" => [
                        [
                            "title" => "Penambahan",
                            "urutkan" => true,
                            "method" => ["Index", "Form"],
                        ],
                        [
                            "title" => "Pengurangan",
                            "urutkan" => true,
                            "method" => ["Index", "Form"],
                        ],
                    ]
                ],
                [
                    "title" => "Pengadaan Brg. Dagang",
                    "icon" => "<i class='fas fa-cubes'></i>",
                    "urutkan" => false,
                    "sub_menu" => [
                        [
                            "title" => "Permintaan",
                            "urutkan" => true,
                            "method" => ["Index", "Form"],
                        ],
                        [
                            "title" => "Verifikasi",
                            "urutkan" => true,
                            "method" => ["Index", "Form"],
                        ],
                        [
                            "title" => "Pembelian",
                            "urutkan" => true,
                            "method" => ["Index", "Form"],
                        ],
                        [
                            "title" => "Pemesanan",
                            "urutkan" => true,
                            "method" => ["Index", "Form"],
                        ],
                        [
                            "title" => "Persetujuan Pemesanan",
                            "urutkan" => true,
                            "method" => ["Index", "Form"],
                        ],
                        [
                            "title" => "Stok Masuk",
                            "urutkan" => true,
                            "method" => ["Index", "Form"],
                        ],
                        [
                            "title" => "Tagihan",
                            "urutkan" => true,
                            "method" => ["Index", "Form"],
                        ],
                        [
                            "title" => "Pelunasan",
                            "urutkan" => true,
                            "method" => ["Index", "Form"],
                        ],
                        [
                            "title" => "Lainnya",
                            "urutkan" => true,
                            "sub_menu" => [
                                [
                                    "title" => "Alat Dan Bahan",
                                    "urutkan" => true,
                                    "method" => ["Index", "Form"],
                                ],
                                [
                                    "title" => "Barang Khusus",
                                    "urutkan" => true,
                                    "method" => ["Index", "Form"],
                                ]
                            ]
                        ],
                    ]
                ],
            ],
        ],
        [
            "title" => "Laporan",
            "icon" => "<i class='fa fa-file-text'></i>",
            "urutkan" => true,
            "sub_menu" => [
                [
                    "title" => "Keuangan Bulanan",
                    "urutkan" => true,
                    "sub_menu" => [
                        [
                            "title" => "Laba Rugi",
                            "method" => ["Index"],
                        ],
                        [
                            "title" => "Neraca",
                            "method" => ["Index"],
                        ],
                        [
                            "title" => "Arus Kas",
                            "method" => ["Index"],
                        ],
                        [
                            "title" => "Neraca Lajur",
                            "method" => ["Index"],
                        ],
                        [
                            "title" => "Buku Besar",
                            "method" => ["Index"],
                        ]
                    ]
                ],
                [
                    "title" => "Kepegawaian",
                    "urutkan" => true,
                    "sub_menu" => [
                        [
                            "title" => "Jadwal Shift",
                            "method" => ["Index"],
                        ],
                        [
                            "title" => "Absensi",
                            "method" => ["Index"],
                        ],
                        [
                            "title" => "Gaji",
                            "method" => ["Index"],
                        ]
                    ]
                ],
                [
                    "title" => "Barang Dagang",
                    "urutkan" => true,
                    "sub_menu" => [
                        [
                            "title" => "Barang Masuk",
                            "method" => ["Index"],
                        ],
                        [
                            "title" => "Barang Keluar",
                            "method" => ["Index"],
                        ],
                        [
                            "title" => "Rekap Transaksi",
                            "method" => ["Index"],
                        ],
                        [
                            "title" => "Persediaan",
                            "method" => ["Index"],
                        ],
                    ]
                ],
                [
                    "title" => "Laporan Harian Kas",
                    "method" => ["Index"],
                ],
                [
                    "title" => "Penerimaan",
                    "method" => ["Index"],
                ],
                [
                    "title" => "Pengeluaran",
                    "method" => ["Index"],
                ],
                [
                    "title" => "Jasa Perawat",
                    "method" => ["Index"],
                ],
                [
                    "title" => "Jasa Dokter",
                    "method" => ["Index"],
                ],
            ]
        ],
        [
            "title" => "Klinik",
            "icon" => "<i class='fas fa-stethoscope'></i>",
            "urutkan" => false,
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
                    "title" => "Site Marking",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Resep Obat",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Peracikan Resep Obat",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Kasir",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Upload",
                    "method" => ["Index", "Form"],
                ],
            ]
        ],
        [
            "title" => "Jurnal Keuangan",
            "urutkan" => true,
            "method" => ["Index", "Form"],
            "icon" => "<i class='fas fa-book'></i>"
        ],
        [
            "title" => "Kepegawaian",
            "icon" => "<i class='fas fa-users'></i>",
            "urutkan" => true,
            "sub_menu" => [
                [
                    "title" => "Jadwal Shift",
                    "method" => ["Index"],
                ],
                [
                    "title" => "Absensi",
                    "method" => ["Index"],
                ],
                [
                    "title" => "Izin",
                    "method" => ["Index", "Form"],
                ],
                [
                    "title" => "Penggajian",
                    "method" => ["Index", "Form"],
                ]
            ]
        ],
        [
            "title" => "Hak Akses",
            "urutkan" => true,
            "icon" => "<i class='fa fa-cog'></i>",
            "method" => ["Index", "Form"],
        ],
    ]
];
