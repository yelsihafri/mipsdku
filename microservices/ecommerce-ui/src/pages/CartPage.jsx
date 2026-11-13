import { useState } from 'react'
import { Link } from 'react-router-dom'
import { getCartDetail } from '../api/aggregate'
import { clearCart, removeCartItem, updateCartItem } from '../api/cartApi'
import AsyncView from '../components/AsyncView'
import ProductImage from '../components/ProductImage'
import { useAsync } from '../hooks/useAsync'
import { describeError, formatRupiah } from '../utils/format'

export default function CartPage({ userId }) {
    const { loading, data, error, reload } = useAsync(
        () => getCartDetail(userId),
        [userId],
    )
    const [actionError, setActionError] = useState(null)

    async function run(action) {
        setActionError(null)
        try {
            await action()
            reload()
        } catch (err) {
            setActionError(err)
        }
    }

    return (
        <section className="cart-page">
            <div className="cart-heading">
                <div>
                    <span className="eyebrow">HAMPIR JADI MILIKMU</span>
                    <h1>Keranjang<span>.</span></h1>
                    <p className="muted">Hai User {userId}, cek lagi pilihan favoritmu.</p>
                </div>
                <span className="cart-heading-mark">✳</span>
            </div>
            {actionError && (
                <p className="alert alert-error">{describeError(actionError)}</p>
            )}
            <AsyncView loading={loading} error={error} onRetry={reload}>
                {data && data.items.length === 0 && (
                    <div className="empty-cart">
                        <span className="empty-cart-icon">♡</span>
                        <h2>Keranjangmu masih kosong</h2>
                        <p className="muted">Yuk, temukan sesuatu yang bikin harimu lebih seru.</p>
                        <Link className="button button-dark" to="/">
                            Mulai belanja <span>↗</span>
                        </Link>
                    </div>
                )}
                {data && data.items.length > 0 && (
                    <div className="cart-layout">
                        <div className="cart-items">
                            <div className="cart-list-heading">
                                <strong>Produk pilihanmu</strong>
                                <span>{data.items.length} item</span>
                            </div>
                            <div className="cart-list">
                                {data.items.map((item) => (
                                    <article className="cart-row" key={item.id}>
                                        <ProductImage
                                            product={item.product ?? { name: 'Produk' }}
                                            className="cart-product-image"
                                        />
                                        <div className="cart-product-info">
                                            <strong>
                                                {item.product ? item.product.name : (
                                                    <em>Produk tidak tersedia</em>
                                                )}
                                            </strong>
                                            <span>
                                                {item.product ? formatRupiah(item.product.price) : '-'}
                                            </span>
                                            <button
                                                className="remove-button"
                                                type="button"
                                                onClick={() => run(() => removeCartItem(item.id))}
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                        <div className="quantity-control">
                                            <button
                                                className="quantity-button"
                                                type="button"
                                                aria-label="Kurangi"
                                                disabled={item.quantity <= 1}
                                                onClick={() => run(() =>
                                                    updateCartItem(item.id, item.quantity - 1))}
                                            >
                                                −
                                            </button>
                                            <span className="qty">{item.quantity}</span>
                                            <button
                                                className="quantity-button"
                                                type="button"
                                                aria-label="Tambah"
                                                onClick={() => run(() =>
                                                    updateCartItem(item.id, item.quantity + 1))}
                                            >
                                                +
                                            </button>
                                        </div>
                                        <strong className="cart-subtotal">
                                            {item.product
                                                ? formatRupiah(Number(item.product.price) * item.quantity)
                                                : '-'}
                                        </strong>
                                    </article>
                                ))}
                            </div>
                            <Link className="continue-shopping" to="/">← Lanjut belanja</Link>
                        </div>
                        <aside className="cart-summary">
                            <span className="eyebrow">RINGKASAN</span>
                            <h2>Total belanja</h2>
                            <div className="summary-line">
                                <span>Jumlah item</span>
                                <span>{data.items.reduce((sum, item) => sum + item.quantity, 0)}</span>
                            </div>
                            <div className="summary-total">
                                <span>Total</span>
                                <strong>{formatRupiah(data.totalPrice)}</strong>
                            </div>
                            <button
                                className="button button-dark summary-clear"
                                type="button"
                                onClick={() => run(() => clearCart(userId))}
                            >
                                Kosongkan keranjang
                            </button>
                            <p className="summary-note">✦ Terima kasih sudah belanja di TokoKita</p>
                        </aside>
                    </div>
                )}
            </AsyncView>
        </section>
    )
}
