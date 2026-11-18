# AntriSehat — Sistem Manajemen Antrean & Layanan Kesehatan

**AntriSehat** adalah aplikasi berbasis web yang dibangun menggunakan framework **Laravel** untuk mempermudah pengelolaan antrean pasien, layanan portal pasien, serta operasional dokter dan petugas klinik secara terintegrasi.

Repositori ini dirancang dengan praktik **DevOps Modern**, menggabungkan kontainerisasi berbasis **Docker** serta alur **CI/CD (Continuous Integration & Continuous Deployment)** otomatis menggunakan **Jenkins**, **Docker Hub**, dan **GitHub Webhook**.

---

## Fitur Utama Aplikasi

* **Portal Pasien:** Akses pendaftaran dan pemantauan nomor antrean secara mandiri tanpa hambatan.
* **Manajemen Operasional & Role-Based Access:** Pemisahan hak akses terstruktur untuk pasien, dokter, dan operator klinik.
* **Health Check Endpoint:** Endpoint khusus pemantauan status kesehatan layanan untuk kebutuhan verifikasi deployment otomatis.

---

## Arsitektur DevOps & CI/CD Pipeline

Setiap perubahan kode yang di-merge ke branch `main` akan secara otomatis memicu eksekusi pipeline di Jenkins melalui **GitHub Webhook** dengan 6 tahap terotomatisasi:

| Tahap (Stage) | Nama Stage | Deskripsi Proses |
| :--- | :--- | :--- |
| **Stage 1** | `1. Checkout` | Mengambil kode sumber terbaru dari branch `main` di repositori GitHub. |
| **Stage 2** | `2. Build` | Membangun multi-stage Docker image (`--target production`) dan memberikan tag versi (`:<BUILD_NUMBER>` serta `:latest`). |
| **Stage 3** | `3. Test` | Membangun image pengujian (`--target test`) dan menjalankan *automated test suite* (Pest / PHPUnit) di dalam container terisolasi. |
| **Stage 4** | `4. Push Image` | Melakukan autentikasi ke Docker Hub dan mengunggah image produksi terverifikasi ke `rafliriandraa/antrisehat`. |
| **Stage 5** | `5. Deploy` | Menghentikan container lama dan menjalankan ulang stack aplikasi produksi menggunakan `docker compose`. |
| **Stage 6** | `6. Verify` | Melakukan pengujian HTTP request terhadap aplikasi yang berjalan untuk memastikan layanan merespons status `200 OK`. |

---

## Struktur Repositori

```text
antrisehat-devops/
├── app/                    # Logika utama aplikasi Laravel (Controllers, Models, Services)
├── database/               # Migrasi skema database dan seeder
├── docker/                 # Konfigurasi pendukung Docker (entrypoint.sh, konfigurasi server)
├── resources/              # Tampilan antarmuka (Blade views) dan aset frontend
├── routes/                 # Definisi routing web dan endpoint health check
├── tests/                  # Automated Unit & Feature tests
├── compose.yaml            # Orkestrasi service container produksi menggunakan Docker Compose
├── Dockerfile              # Multi-stage Dockerfile (base, test, dan production)
├── Jenkinsfile             # Definisi pipeline CI/CD 6-stage deklaratif
└── README.md               # Dokumentasi proyek dan arsitektur DevOps
```

---

## Menjalankan Aplikasi Secara Lokal

### 1. Menggunakan Docker Compose (Direkomendasikan)

Pastikan **Docker** dan **Docker Compose** sudah terpasang dan berjalan di sistem Anda:

```bash
# Clone repositori
git clone https://github.com/Ghazali20/antrisehat-devops.git
cd antrisehat-devops

# Jalankan container aplikasi
docker compose up -d
```

Setelah container berjalan, aplikasi dapat diakses melalui browser di `http://localhost:8000` *(sesuaikan dengan mapping port pada `compose.yaml`)*.

### 2. Menjalankan Automated Test via Docker

```bash
docker build --target test -t antrisehat-test .
docker run --rm antrisehat-test
```

---

## Alur Kolaborasi Git (Branching Strategy)

1. Setiap pengembangan fitur, perbaikan bug, atau pembaruan konfigurasi dilakukan pada branch terpisah (`feat/*`, `fix/*`, `docs/*`).
2. Perubahan diajukan melalui **Pull Request (PR)** menuju branch `main`.
3. Setelah PR di-review dan di-**Merge** ke `main`, GitHub Webhook akan mengirim event ke server Jenkins untuk menjalankan seluruh rangkaian build, pengujian, push image, hingga deployment otomatis.
