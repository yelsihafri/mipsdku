const PRODUCT_IMAGES = [
  {
    match: /keyboard|mekanikal/i,
    src: 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=1000&q=85',
  },
  {
    match: /mouse/i,
    src: 'https://images.unsplash.com/photo-1527814050087-3793815479db?auto=format&fit=crop&w=1000&q=85',
  },
  {
    match: /headset|headphone/i,
    src: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1000&q=85',
  },
]

export default function ProductImage({ product, className = '' }) {
  const productImage = PRODUCT_IMAGES.find(({ match }) =>
    match.test(product.name ?? ''),
  )
  const imageType = product.image?.startsWith('/9j/')
    ? 'image/jpeg'
    : product.image?.startsWith('UklGR')
      ? 'image/webp'
      : 'image/png'
  const image =
    product.image && product.image.length > 1000
      ? `data:${imageType};base64,${product.image}`
      : productImage?.src ??
        'https://images.unsplash.com/photo-1498049794561-7780e7231661?auto=format&fit=crop&w=1000&q=85'

  return (
    <div className={`product-image ${className}`}>
      <img
        src={image}
        alt={product.name}
        loading="lazy"
        onError={(event) => {
          event.currentTarget.style.display = 'none'
        }}
      />
      <span className="image-fallback" aria-hidden="true">✦</span>
    </div>
  )
}
