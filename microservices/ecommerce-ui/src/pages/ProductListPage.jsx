// src/pages/ProductListPage.jsx
// Halaman daftar produk: data produk dari product-service, rating dari review-service.
import { getProductsWithRating } from '../api/aggregate'
import AsyncView from '../components/AsyncView'
import ProductCard from '../components/ProductCard'
import { useAsync } from '../hooks/useAsync'
export default function ProductListPage() {
    const { loading, data, error, reload } =
        useAsync(getProductsWithRating)

    return (
        <section className="catalog-page">
            <div className="hero">
                <div className="hero-copy">
                    <span className="eyebrow"><span className="eyebrow-dot" /> TEMUKAN FAVORIT BARUMU</span>
                    <h1>Upgrade kecil,<br /><span>bahagia besar.</span></h1>
                    <p>Perlengkapan pilihan untuk bikin setiap momen dan ruang kerjamu terasa lebih istimewa.</p>
                    <a className="button button-dark" href="#koleksi">Jelajahi koleksi <span>↓</span></a>
                    <div className="hero-note"><span className="note-stars">★★★★★</span> Produk pilihan, belanja lebih nyaman</div>
                </div>
                <div className="hero-visual">
                    <img
                        src="https://images.unsplash.com/photo-1498049794561-7780e7231661?auto=format&fit=crop&w=1400&q=90"
                        alt="Koleksi perangkat elektronik pilihan"
                    />
                    <div className="hero-sticker"><span>GOOD</span><strong>things<br />inside</strong><i>✳</i></div>
                    <div className="hero-caption">DESAIN PILIHAN <span>·</span> KUALITAS TERJAGA</div>
                </div>
            </div>
            <div className="section-heading" id="koleksi">
                <div>
                    <span className="eyebrow">DIPILIH UNTUKMU</span>
                    <h2>Temukan yang kamu suka<span>.</span></h2>
                </div>
                <span className="collection-note">{data?.length ?? 0} produk pilihan</span>
            </div>
            <AsyncView loading={loading} error={error} onRetry={reload}>
                {data && (
                    <div className="grid">
                        {data.map((product) => (
                            <ProductCard key={product.id} product={product} />
                        ))}
                    </div>
                )}
            </AsyncView>
        </section>
    )
}