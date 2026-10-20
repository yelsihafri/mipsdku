# Review Service

Flask service untuk mengelola review produk. Data review disimpan di MongoDB,
dan produk diverifikasi lewat `product-service` sebelum review baru disimpan.

## Menjalankan dengan Docker Compose mandiri

Jalankan dari folder `microservices/review-service`:

```bash
docker compose up --build
```

Compose ini juga menjalankan `product-service` dan databasenya, karena review
perlu memeriksa bahwa produk tersedia. Product service dibangun dari folder
`../product-service`. MongoDB dan MySQL menyimpan data pada named volume.

## Menjalankan Flask langsung untuk development

Siapkan dependency dan file environment dari folder `microservices/review-service`:

```powershell
py -m venv venv
.\venv\Scripts\Activate.ps1
pip install -r requirements.txt
python run.py
```

Pastikan MongoDB dan product service aktif terlebih dahulu. Anda bisa
menjalankannya lewat Compose mandiri dengan `docker compose up -d review-db
product-service product-db`, atau lewat Compose gabungan dari folder
`microservices` dengan `docker compose up -d review-db product-service product-db`.
File `.env` lokal mengatur koneksi Flask ke port host `27017` dan `3000`.

Review service tersedia di `http://localhost:3002`. Jika PowerShell memblokir
aktivasi environment, jalankan `venv\Scripts\python.exe run.py` dan gunakan
`venv\Scripts\pip.exe install -r requirements.txt`.

## Menjalankan seluruh service dengan Docker

Alternatifnya, Compose gabungan dari folder `microservices` menjalankan semua
service proyek:

```powershell
docker compose up --build
```

Compose menghubungkan review service ke MongoDB dan product service melalui
jaringan internal. Volume database persisten; skrip inisialisasi product
dijalankan otomatis hanya ketika volume MySQL dibuat pertama kali.

## Endpoint

| Method | Path | Keterangan |
| --- | --- | --- |
| GET | `/health` | Status service |
| GET | `/reviews/product/{product_id}` | Review dan ringkasan rating produk |
| GET | `/reviews/{id}` | Ambil satu review |
| POST | `/reviews` | Buat review setelah produk diverifikasi |
| PUT | `/reviews/{id}` | Ubah rating dan/atau komentar |
| DELETE | `/reviews/{id}` | Hapus review |

`product_id` dan `user_id` harus bilangan bulat minimal 1. `rating` harus
bilangan bulat 1–5. `comment` opsional dan dibatasi maksimal 1000 karakter.
Satu user hanya dapat mengulas produk yang sama sekali, dijaga oleh indeks
unik MongoDB.
