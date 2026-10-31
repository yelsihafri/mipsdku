\# Microservices Project – Order Service



\## 1. Deskripsi Proyek



Proyek ini merupakan implementasi sistem berbasis arsitektur microservices menggunakan Laravel, Node.js, MySQL, PostgreSQL, dan MongoDB. Setiap layanan memiliki tanggung jawab masing-masing dan berkomunikasi melalui REST API.



Fokus pengembangan pada proyek ini adalah \*\*Order Service\*\*, yaitu layanan untuk mengelola pesanan dan proses checkout dengan mengambil data dari layanan lain.



\## 2. Arsitektur Sistem



Sistem terdiri dari beberapa layanan:



| Service | Teknologi | Port |

|---|---|---:|

| Product Service | Node.js dan MySQL | 3000 |

| Cart Service | Laravel dan PostgreSQL | 3001 |

| Review Service | Flask dan MongoDB | 3002 |

| Order Service | Laravel dan MySQL | 3003 |



Komunikasi antarlayanan dilakukan melalui REST API menggunakan alamat service pada jaringan Docker.



Gambaran komunikasi:



```text

&#x20;                Client

&#x20;                  |

&#x20;                  v

&#x20;            Order Service

&#x20;              (Laravel)

&#x20;             /        \\

&#x20;            v          v

&#x20;     Product Service  Cart Service

&#x20;       (Node.js)       (Laravel)



&#x20;     Review Service

&#x20;        (Flask)

```



\## 3. Teknologi yang Digunakan



\- Laravel

\- PHP

\- Node.js

\- MySQL

\- PostgreSQL

\- MongoDB

\- Docker dan Docker Compose

\- REST API

\- Insomnia untuk pengujian API



\## 4. Struktur Direktori



```text

microservices/

├── cart-service/

├── ecommerce-ui/

├── order-service/

├── product-service/

├── review-service/

├── docker-compose.yml

├── insomnia-collection.json

└── README.md

```



\## 5. Cara Menjalankan Proyek



\### Persyaratan



Pastikan perangkat sudah memiliki:



\- Docker Desktop

\- Git

\- Insomnia (untuk pengujian API)



\### Menjalankan seluruh service



1\. Clone repository:



```bash

git clone <URL\_REPOSITORY>

```



2\. Masuk ke direktori proyek:



```bash

cd microservices

```



3\. Jalankan seluruh container:



```bash

docker compose up --build

```



4\. Periksa container:



```bash

docker compose ps

```



Pastikan container service berjalan dengan baik.



\### Menghentikan service



```bash

docker compose down

```



\## 6. Endpoint API



Order Service berjalan pada:



```text

http://localhost:3003

```



Endpoint yang tersedia pada aplikasi:



| Method | Endpoint | Keterangan |

|---|---|---|

| GET | `/up` | Memeriksa status aplikasi Laravel |

| POST | `/api/login` | Login pengguna |

| POST | `/api/logout` | Logout pengguna |

| GET | `/api/orders` | Menampilkan daftar pesanan |

| POST | `/api/orders` | Membuat pesanan |

| GET | `/api/orders/{id}` | Melihat detail pesanan |

| PUT | `/api/orders/{id}` | Memperbarui pesanan |

| DELETE | `/api/orders/{id}` | Menghapus pesanan |

| POST | `/api/checkout` | Menjalankan proses checkout |

| POST | `/api/orders/{id}/pay` | Simulasi pembayaran |



Beberapa endpoint memerlukan autentikasi menggunakan Bearer Token.



\## 7. Pengujian API



Pengujian dilakukan menggunakan Insomnia.



Pengujian mencakup:



\- Login dan autentikasi pengguna.

\- Menampilkan daftar pesanan.

\- Melihat detail pesanan.

\- Membuat pesanan melalui proses checkout.

\- Simulasi pembayaran.

\- Menguji pesanan yang tidak ditemukan.

\- Menguji keranjang belanja kosong.

\- Menguji validasi data yang tidak lengkap.

\- Menguji akses menggunakan token yang tidak valid.



File koleksi pengujian tersedia pada:



```text

insomnia-collection.json

```



\## 8. Konfigurasi Environment



Setiap service memiliki konfigurasi environment masing-masing.



Beberapa konfigurasi yang diperlukan antara lain:



\- `APP\_KEY`

\- `APP\_URL`

\- `DB\_CONNECTION`

\- `DB\_HOST`

\- `DB\_DATABASE`

\- `DB\_USERNAME`

\- `DB\_PASSWORD`

\- `PRODUCT\_SERVICE\_URL`

\- `CART\_SERVICE\_URL`

\- `REVIEW\_SERVICE\_URL`



Sesuaikan konfigurasi berdasarkan environment masing-masing. Jangan mengunggah file `.env` yang berisi password atau informasi rahasia ke repository publik.



\## 9. Keputusan Implementasi



Proyek menggunakan Docker Compose agar beberapa service dapat dijalankan dalam satu lingkungan. Setiap service memiliki database dan tanggung jawab tersendiri.



Komunikasi antarlayanan menggunakan REST API sehingga service dapat berinteraksi tanpa mengakses database service lain secara langsung.



Pengujian endpoint dilakukan menggunakan Insomnia untuk memeriksa respons API dan alur proses aplikasi.



\## 10. Video Demonstrasi



Link video demonstrasi:



\*\*\[Masukkan link video demonstrasi di sini]\*\*



Video demonstrasi memperlihatkan arsitektur sistem, proses menjalankan aplikasi, pengujian endpoint, serta penanganan beberapa kondisi berhasil dan gagal.



\## 11. Repository



Source code proyek tersedia pada repository GitHub:



\*\*\[Masukkan link repository GitHub di sini]\*\*



