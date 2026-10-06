import { useContext } from "react";
import { Link } from "react-router";
import { WishlistContext } from "../context/WishlistContext";
import { API_URL } from "../config/api";

function ProductCard({ product }) {
  const { isWishlisted, toggleWishlist } = useContext(WishlistContext);
  const saved = isWishlisted(product.id);

  const imageUrl = product.main_image
    ? product.main_image.startsWith("http")
      ? product.main_image
      : `${API_URL}/${product.main_image}`
    : null;

  return (
    <article className="card product-card h-100 border-0 position-relative">
      <button
        type="button"
        className={`btn wishlist-toggle position-absolute top-0 end-0 m-3 rounded-circle ${
          saved ? "is-saved" : ""
        }`}
        onClick={() => toggleWishlist(product.id)}
        aria-label={
          saved
            ? `Remove ${product.name} from wishlist`
            : `Add ${product.name} to wishlist`
        }
        aria-pressed={saved}
      >
        <i className={`bi ${saved ? "bi-heart-fill" : "bi-heart"}`} />
      </button>

      {imageUrl ? (
        <Link
  to={`/products/${product.slug}`}
  className="d-block"
  aria-label={`View ${product.name}`}
>
  <img
    src={imageUrl}
    className="card-img-top product-image"
    alt={product.name}
  />
</Link>
      ) : (
        <div className="product-image-placeholder">
          <i className="bi bi-image"></i>
          <span>Image coming soon</span>
        </div>
      )}

      <div className="card-body d-flex flex-column p-4">
        <span className="product-category badge rounded-pill align-self-start mb-3">
          {product.category?.name || "Collection"}
        </span>

        <h3 className="card-title product-title">{product.name}</h3>

        <p className="card-text product-description">
          {product.description}
        </p>

        <div className="mt-auto">
          {product.sale_price ? (
            <div className="d-flex align-items-center gap-2 mb-3">
              <del className="original-price">${product.price}</del>
              <strong className="sale-price">
                ${product.sale_price}
              </strong>
            </div>
          ) : (
            <strong className="regular-price d-block mb-3">
              ${product.price}
            </strong>
          )}

          <Link to={`/products/${product.slug}`} className="product-link">
            <span>View details</span>
            <i className="bi bi-arrow-right"></i>
          </Link>
        </div>
      </div>
    </article>
  );
}

export default ProductCard;