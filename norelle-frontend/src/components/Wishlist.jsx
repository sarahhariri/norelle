import { useContext, useEffect, useState } from "react";
import { Link } from "react-router";
import Navbar from "./Navbar";
import ProductCard from "./ProductCard";
import { WishlistContext } from "../context/WishlistContext";
import { API_URL } from "../config/api";

function Wishlist() {
  const { wishlistIds, toggleWishlist } = useContext(WishlistContext);

  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);

  useEffect(() => {
    const controller = new AbortController();

    async function loadProducts() {
      try {
        const response = await fetch(
          `${API_URL}/api/products`,
          { signal: controller.signal }
        );

        if (!response.ok) {
          throw new Error("Could not load products");
        }

        const result = await response.json();

        if (!Array.isArray(result.data)) {
          throw new Error("Invalid products response");
        }

        if (controller.signal.aborted) return;

        setProducts(result.data);
        setError(false);
      } catch (err) {
        if (!controller.signal.aborted) {
          setError(true);
        }
      } finally {
        if (!controller.signal.aborted) {
          setLoading(false);
        }
      }
    }

    loadProducts();

    return () => controller.abort();
  }, []);

  const savedProducts = products.filter((product) =>
    wishlistIds.some((id) => String(id) === String(product.id))
  );

  const unavailableIds = wishlistIds.filter(
    (id) =>
      !products.some((product) => String(product.id) === String(id))
  );

  return (
    <>
      <Navbar />

      <main className="container py-5">
        <div className="row justify-content-center">
          <div className="col-12 col-lg-9">
            <div className="d-flex align-items-baseline gap-3 mb-4">
              <h1 className="section-title mb-0">Your Wishlist</h1>

              <span className="cart-item-count">
                {wishlistIds.length}{" "}
                {wishlistIds.length === 1 ? "item" : "items"}
              </span>
            </div>

            {wishlistIds.length === 0 ? (
              <div className="text-center py-5">
                <p className="text-secondary">
                  Your wishlist is empty.
                </p>

                <Link
                  to="/#shop"
                  className="btn btn-norelle px-4 py-2"
                >
                  Explore products
                </Link>
              </div>
            ) : loading ? (
              <p className="text-secondary" role="status">
                Loading your favourites...
              </p>
            ) : error ? (
              <p className="alert alert-danger" role="alert">
                Could not load your favourites. Please refresh the page.
              </p>
            ) : (
              <>
                {unavailableIds.length > 0 && (
                  <div
                    className="alert alert-light border d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4"
                    role="status"
                  >
                    <p className="mb-0">
                      {unavailableIds.length} saved{" "}
                      {unavailableIds.length === 1
                        ? "item is"
                        : "items are"}{" "}
                      no longer available.
                    </p>

                    <button
                      type="button"
                      className="btn btn-outline-secondary btn-sm flex-shrink-0"
                      onClick={() =>
                        unavailableIds.forEach((id) => toggleWishlist(id))
                      }
                    >
                      Remove unavailable items
                    </button>
                  </div>
                )}

                {savedProducts.length > 0 ? (
                  <div className="row g-4">
                    {savedProducts.map((product) => {
                      const outOfStock =
                        !(product.variants ?? []).some(
                          (variant) => Number(variant.stock) > 0
                        );

                      return (
                        <div
                          className="col-12 col-sm-6"
                          key={product.id}
                        >
                          <div className="d-flex flex-column h-100">
                            {outOfStock && (
                              <p className="small text-secondary mb-2">
                                Currently out of stock
                              </p>
                            )}

                            <ProductCard product={product} />
                          </div>
                        </div>
                      );
                    })}
                  </div>
                ) : (
                  <div className="text-center py-4">
                    <p className="text-secondary">
                      None of your saved products are currently available.
                    </p>

                    <Link
                      to="/#shop"
                      className="btn btn-norelle px-4 py-2"
                    >
                      Explore products
                    </Link>
                  </div>
                )}
              </>
            )}
          </div>
        </div>
      </main>
    </>
  );
}

export default Wishlist;