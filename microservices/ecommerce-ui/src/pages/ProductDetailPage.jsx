// src/pages/ProductDetailPage.jsx
// Halaman detail: informasi produk, tombol masuk keranjang, dan daftar review.
import { useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { getProductDetail } from '../api/aggregate'
import { addToCart } from '../api/cartApi'
import AsyncView from '../components/AsyncView'
import ProductImage from '../components/ProductImage'
import ReviewForm from '../components/ReviewForm'
import StarRating from '../components/StarRating'
import { useAsync } from '../hooks/useAsync'
import { describeError, formatRupiah } from '../utils/format'
export default function ProductDetailPage({ userId }) {
    const { id } = useParams() // nilai :id dari alamat /products/:id
    const { loading, data, error, reload } = useAsync(() =>
        getProductDetail(id), [id])
// Jumlah disimpan sebagai teks, supaya kolomnya bisa dikosongkan saat mengetik.
        const[quantity, setQuantity] = useState('1')
    const [cartMessage, setCartMessage] = useState(null)
    async function handleAddToCart() {
        const amount = Math.max(1, Number(quantity) || 1)
        try {
            await addToCart(userId, Number(id), amount)
            setCartMessage({
                type: 'success', text: 'Produk masuk kekeranjang.' })

} catch (err) {
                setCartMessage({ type: 'error', text: describeError(err) })
            }
        }
return (
            <section className="detail-page">
                <Link className="back-link" to="/">← Kembali ke koleksi</Link>
                <AsyncView loading={loading} error={error} onRetry={reload}>
                    {data && (
                        <>
                            <div className="detail-card">
                                <ProductImage product={data.product} className="detail-image" />
                                <div className="detail-info">
                                    <span className="eyebrow">KOLEKSI TOKOKITA</span>
                                    <h1>{data.product.name}</h1>
                                    {data.reviews && (
                                        <StarRating
                                            average={data.reviews.summary.average_rating}
                                            total={data.reviews.summary.total_reviews}
                                        />
                                    )}
                                    <p className="detail-description">{data.product.description}</p>
                                    <p className="price detail-price">{formatRupiah(data.product.price)}</p>
                                    <p className="stock-label">{data.product.stock > 0 ? `${data.product.stock} tersedia` : 'Stok habis'}</p>
                                    <div className="actions">
                                        <input
                                            type="number"
                                            min="1"
                                            value={quantity}
                                            aria-label="Jumlah"
                                            onChange={(e) => setQuantity(e.target.value)}
                                        />
                                        <button className="button button-dark" type="button" onClick={handleAddToCart}>
                                            Masukkan ke keranjang <span>↗</span>
                                        </button>
                                    </div>
                                    {cartMessage && (
                                        <p className={`alert alert-${cartMessage.type}`}>{cartMessage.text}</p>
                                    )}
                                    <p className="detail-promise">✦ Pilihan berkualitas, siap menemani harimu.</p>
                                </div>
                            </div>
                            <div className="review-section">
                                <div className="section-heading">
                                    <div><span className="eyebrow">CERITA PELANGGAN</span><h2>Review<span>.</span></h2></div>
                                </div>
                            {data.reviews ? (
                                <ul className="reviews">
                                    {data.reviews.reviews.map((review) => (
                                        <li key={review.id}>
                                            <StarRating average={review.rating} total={1}

                                                compact />

                                            <p>{review.comment || <em

                                                className="muted">Tanpa komentar</em>}</p>

                                            <small className="muted">
                                                User {review.user_id},{' '}
                                                {new

                                                    Date(review.created_at).toLocaleDateString('id-ID')}

                                            </small>
                                        </li>
                                    ))}
                                    {data.reviews.reviews.length === 0 && (
                                        <li className="muted">Belum ada review.</li>
                                    )}
                                </ul>
                            ) : (
                                <p className="alert alert-error">Review tidak dapat

                                    dimuat saat ini.</p>
                            )}
                            <ReviewForm productId={Number(id)} userId={userId}

                                onCreated={reload} />
                            </div>
                        </>
                    )}
                </AsyncView>
            </section>
        )
    }