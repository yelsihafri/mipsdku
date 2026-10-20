# Review Service

Flask service untuk mengelola review produk. Data review disimpan di MongoDB,
dan produk diverifikasi lewat `product-service` sebelum review baru disimpan.

## Menjalankan untuk development

Dari folder `microservices`, jalankan database MongoDB dan product service:

```powershell
docker compose up -d product-service product-db review-db
```

Pastikan product service tersedia di `http://localhost:3000`, lalu dari folder
`microservices/review-service` siapkan environment Python:

```powershell
py -m venv venv
.\venv\Scripts\Activate.ps1
pip install -r requirements.txt
Copy-Item .env.example .env
python run.py
```

Review service tersedia di `http://localhost:3002`. Jika PowerShell memblokir
aktivasi environment, jalankan `venv\Scripts\python.exe run.py` dan gunakan
`venv\Scripts\pip.exe install -r requirements.txt`.

## Menjalankan seluruh service dengan Docker

Dari folder `microservices`, jalankan:

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
