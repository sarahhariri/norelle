import { useContext ,useEffect, useState } from "react";
import { CartContext } from "../context/CartContext";
import { Link, useParams } from "react-router";
import { WishlistContext } from "../context/WishlistContext";
import Navbar from "./Navbar";
import ProductCard from "./ProductCard";
import { API_URL } from "../config/api";

function ProductDetailsContent({slug}) {
  
  const [product, setProduct] = useState(null);
  const [error, setError] = useState("");
  const [selectedVariant, setSelectedVariant] = useState(null);
  const { addToCart, items } = useContext(CartContext);
  const { isWishlisted, toggleWishlist } =useContext(WishlistContext);
  const saved = product ? isWishlisted(product.id) : false;
  const [relatedProducts, setRelatedProducts] = useState([]);

const quantityInCart =
  items.find((item) => item.variantId === selectedVariant?.id)
    ?.quantity ?? 0;

const reachedStockLimit =
  selectedVariant && quantityInCart >= Math.min(Number(selectedVariant.stock), 20);

  useEffect(() => {
    window.scrollTo({top: 0, behavior: "instant"});
    
    fetch(`${API_URL}/api/products/${slug}`)
      .then((response) => {
        if (!response.ok) throw new Error("Product not found");
        return response.json();
      })
      .then((result) => setProduct(result.data))
      .catch((err) => setError(err.message));
  }, [slug]);

  useEffect(() => {
  if (!product) return;

  const controller = new AbortController();

  async function loadRelatedProducts() {
    try {
      const response = await fetch(
        `${API_URL}/api/products`,
        { signal: controller.signal }
      );

      if (!response.ok) {
        throw new Error("Could not load related products");
      }

      const result = await response.json();

      const otherProducts = (result.data ?? []).filter(
        (item) => String(item.id) !== String(product.id)
      );

      const categoryId =
        product.category?.id ?? product.category_id;

      const sameCategory = [];
      const otherCategories = [];

      otherProducts.forEach((item) => {
        const itemCategoryId =
          item.category?.id ?? item.category_id;

        if (
          categoryId != null &&
          itemCategoryId != null &&
          String(itemCategoryId) === String(categoryId)
        ) {
          sameCategory.push(item);
        } else {
          otherCategories.push(item);
        }
      });

      setRelatedProducts(
        [...sameCategory, ...otherCategories].slice(0, 4)
      );
    } catch (err) {
      if (err.name !== "AbortError") {
        setRelatedProducts([]);
      }
    }
  }

  loadRelatedProducts();

  return () => controller.abort();
}, [product]);

  return (
    <>
      <Navbar />

      <main className="container py-5">
        <Link to="/#shop" className="product-link mb-4">
          <i className="bi bi-arrow-left"></i> Back to products
        </Link>

        {error && <p className="alert alert-danger">{error}</p>}

        {!product && !error && <p>Loading product...</p>}

        {product && (
          <div className="row g-4  g-lg-5 align-items-start">
           <div className="col-12 col-lg-5">
  <div className="position-relative">
    <img
      src={
        product.main_image.startsWith("http")
          ? product.main_image
          : `${API_URL}/${product.main_image}`
      }
      alt={product.name}
      className="product-detail-image"
    />

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
      <i
        className={`bi ${saved ? "bi-heart-fill" : "bi-heart"}`}
        aria-hidden="true"
      ></i>
    </button>
  </div>
</div>

           <div className="col-12 col-lg-7">
  <span className="product-category">
    {product.category?.name}
  </span>

  <h1 className="product-detail-title mt-2">
    {product.name}
  </h1>

  <div className="mt-3 mb-4">
    {product.sale_price ? (
      <div className="d-flex gap-3 align-items-center">
        <del className="original-price">${product.price}</del>
        <strong className="sale-price">
          ${product.sale_price}
        </strong>
      </div>
    ) : (
      <strong className="regular-price">
        ${product.price}
      </strong>
    )}
  </div>

  <fieldset className="mb-0">
    <legend className="fs-6 fw-semibold mb-3">
      Select size
    </legend>

    <div className="d-flex flex-wrap gap-2">
      {[...(product.variants ?? [])]
        .sort((a, b) => {
          const sizes = ["XS", "S", "M", "L", "XL", "XXL"];
          const rank = (size) => {
            const index = sizes.indexOf(size);
            return index === -1 ? sizes.length : index;
          };

          return rank(a.size) - rank(b.size);
        })
        .map((variant) => (
          <button
            key={variant.id}
            type="button"
            className={`btn size-option ${
              selectedVariant?.id === variant.id
                ? "is-selected"
                : ""
            }`}
            disabled={Number(variant.stock) <= 0}
            aria-pressed={selectedVariant?.id === variant.id}
            onClick={() => setSelectedVariant(variant)}
          >
            {variant.size}
          </button>
        ))}
    </div>
  </fieldset>

  <p className="small text-muted mt-3 mb-0" aria-live="polite">
    {selectedVariant
      ? `Selected size: ${selectedVariant.size}`
      : "Choose a size to add this item to your bag."}
  </p>

  <button
    type="button"
    className="btn btn-norelle product-add-button w-100 py-3 mt-4"
    disabled={
      !selectedVariant ||
      Number(selectedVariant.stock) <= 0 ||
      reachedStockLimit
    }
    onClick={() => addToCart(product, selectedVariant)}
  >
    <i className="bi bi-bag me-2" aria-hidden="true"></i>
    {reachedStockLimit ? "Maximum available added" : "Add to Bag"}
  </button>

  {product.description && (
    <div className="product-details-info border-top mt-4 pt-4">
      <h2 className="h6 mb-3">Product Details</h2>
      <p className="product-description mb-0">
        {product.description}
      </p>
    </div>
  )}
</div>
            
          </div>
        )}


        {product && relatedProducts.length > 0 && (
  <section
    className="border-top mt-5 pt-5"
    aria-labelledby="related-products-title"
  >
    <div className="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
      <h2 id="related-products-title" className="h3 mb-0">
        You May Also Like
      </h2>

      <Link to="/#shop" className="product-link">
        <span>Explore all products</span>
        <i className="bi bi-arrow-right" aria-hidden="true"></i>
      </Link>
    </div>

    <div className="row g-4">
      {relatedProducts.map((item) => (
        <div
          key={item.id}
          className="col-12 col-sm-6 col-lg-3"
        >
          <ProductCard product={item} />
        </div>
      ))}
    </div>
  </section>
)}
      </main>
    </>
  );
}

function ProductDetails() {
    const {slug} = useParams();
    return <ProductDetailsContent key={slug} slug={slug} />;
}
export default ProductDetails;