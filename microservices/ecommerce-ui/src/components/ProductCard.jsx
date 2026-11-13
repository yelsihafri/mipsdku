import { Link } from 'react-router-dom'
import { formatRupiah } from '../utils/format'
import ProductImage from './ProductImage'
import StarRating from './StarRating'

export default function ProductCard({ product }) {
    const { id, name, price, stock, rating } = product
    return (
        <article className="card">
            <Link className="card-image-link" to={`/products/${id}`} aria-label={`Lihat ${name}`}>
                <ProductImage product={product} />
                <span className="image-label">Pilihan hari ini</span>
            </Link>
            <div className="card-content">
                <div className="card-meta">
                    <span className="stock-label">{stock > 0 ? 'Tersedia' : 'Stok habis'}</span>
                    {rating ? (
                        <StarRating average={rating.average_rating} total={rating.total_reviews} compact />
                    ) : (
                        <span className="muted">Rating belum tersedia</span>
                    )}
                </div>
                <h3><Link to={`/products/${id}`}>{name}</Link></h3>
                <div className="card-bottom">
                    <p className="price">{formatRupiah(price)}</p>
                    <Link className="card-cta" to={`/products/${id}`} aria-label={`Lihat ${name}`}>↗</Link>
                </div>
            </div>
        </article>
    )
}