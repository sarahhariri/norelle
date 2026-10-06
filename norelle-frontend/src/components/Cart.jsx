import { useContext, useEffect, useState } from "react";
import { Link } from "react-router";
import Navbar from "./Navbar";
import { CartContext } from "../context/CartContext";
import { API_URL } from "../config/api";
function Cart() {
  const { items, cartCount, removeFromCart, updateQuantity } =
    useContext(CartContext);

  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [loadError, setLoadError] = useState(false);

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
        setLoadError(false);
        setLoading(false);
      } catch (error) {
        if (controller.signal.aborted) return;

        setLoadError(true);
        setLoading(false);
      }
    }

    loadProducts();

    return () => controller.abort();
  }, []);

  const cartProducts = items.map((item) => {
    const product = products.find(
      (product) => Number(product.id) === Number(item.productId)
    );

    const variant = product?.variants?.find(
      (variant) => Number(variant.id) === Number(item.variantId)
    );

    const stock = Number(variant?.stock ?? 0);
    const maxStock = Number.isFinite(stock)
      ? Math.max(0, Math.min(Math.floor(stock), 20))
      : 0;

    const quantity = Number(item.quantity);
    const validQuantity = Number.isInteger(quantity) && quantity >= 1;

    const currentPrice = product
      ? Number(product.sale_price ?? product.price)
      : NaN;

    const price =
      Number.isFinite(currentPrice) && currentPrice >= 0
        ? currentPrice
        : null;

    const unavailable =
      !loading &&
      !loadError &&
      (!product || !variant || maxStock === 0);

    const exceedsStock =
      !loading &&
      !loadError &&
      !unavailable &&
      validQuantity &&
      quantity > maxStock;

    const imageUrl = product?.main_image
      ? product.main_image.startsWith("http")
        ? product.main_image
        : `${API_URL}/${product.main_image}`
      : null;

    return {
      item,
      product,
      variant,
      maxStock,
      quantity,
      validQuantity,
      price,
      unavailable,
      exceedsStock,
      imageUrl,
    };
  });

  const canShowSubtotal =
    !loading &&
    !loadError &&
    cartProducts.every(
      ({ product, price, validQuantity }) =>
        Boolean(product) && price !== null && validQuantity
    );

  const canCheckout =
    items.length > 0 &&
    canShowSubtotal &&
    cartProducts.every(
      ({ variant, quantity, maxStock }) =>
        Boolean(variant) && quantity <= maxStock
    );

  const subtotal = cartProducts.reduce(
    (total, { price, quantity, validQuantity }) => {
      if (price === null || !validQuantity) return total;

      return total + price * quantity;
    },
    0
  );

  return (
    <>
      <Navbar />

      <main className="container py-5">
        <div className="row justify-content-center">
          <div className="col-12 col-lg-9">
            <div className="d-flex align-items-baseline gap-3 mb-4">
              <h1 className="section-title mb-0">Your Bag</h1>

              <span className="cart-item-count">
                {cartCount} {cartCount === 1 ? "item" : "items"}
              </span>
            </div>

            {items.length === 0 ? (
              <div className="text-center py-5">
                <p className="text-secondary">Your bag is empty.</p>

                <Link
                  to="/#shop"
                  className="btn btn-norelle px-4 py-2"
                >
                  Explore products
                </Link>
              </div>
            ) : (
              <>
                {loading && (
                  <p className="text-secondary mb-4" role="status">
                    Checking current prices and availability...
                  </p>
                )}

                {loadError && (
                  <p className="alert alert-danger" role="alert">
                    Could not check current prices and availability.
                    Please refresh the page.
                  </p>
                )}

                {cartProducts.map(
                  ({
                    item,
                    product,
                    variant,
                    maxStock,
                    quantity,
                    validQuantity,
                    price,
                    unavailable,
                    exceedsStock,
                    imageUrl,
                  }) => {
                    const name = product?.name ?? item.name;
                    const size = variant?.size ?? item.size;

                    return (
                      <article
                        className="cart-item position-relative p-3 p-sm-4 mb-3"
                        key={item.variantId}
                      >
                        <div className="row g-3 align-items-center pe-4">
                          {imageUrl && (
                            <div className="col-auto">
                              <img
                                src={imageUrl}
                                alt={name}
                                className="cart-image"
                              />
                            </div>
                          )}

                          <div className="col cart-item-info">
                            <h2 className="product-title fs-5 mb-1">
                              {name}
                            </h2>

                            <p className="cart-item-size mb-3">
                              Size: <strong>{size}</strong>
                            </p>

                            {unavailable && (
                              <p
                                className="small text-danger mb-3"
                                role="status"
                              >
                                This item is no longer available.
                                Please remove it from your bag.
                              </p>
                            )}

                            {exceedsStock && (
                              <p
                                className="small text-danger mb-3"
                                role="status"
                              >
                                Only {maxStock} available. Please reduce
                                the quantity.
                              </p>
                            )}

                            {!loading &&
                              !loadError &&
                              !validQuantity && (
                                <p className="small text-danger mb-3">
                                  This item has an invalid quantity.
                                  Please remove it and add it again.
                                </p>
                              )}

                            {!loading &&
                              !loadError &&
                              !unavailable &&
                              price === null && (
                                <p className="small text-danger mb-3">
                                  The current price is unavailable.
                                  Please try again later.
                                </p>
                              )}

                            <div className="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2">
                              <span className="cart-quantity-label">
                                Quantity
                              </span>

                              <div
                                className="cart-quantity d-inline-flex align-items-center"
                                role="group"
                                aria-label={`Quantity for ${name}`}
                              >
                                <button
                                  type="button"
                                  className="d-inline-flex align-items-center justify-content-center"
                                  disabled={
                                    loading ||
                                    loadError ||
                                    !variant ||
                                    maxStock <= 0 ||
                                    !validQuantity ||
                                    quantity <= 1
                                  }
                                  onClick={() =>
                                    updateQuantity(
                                      item.variantId,
                                      Math.min(quantity - 1, maxStock),
                                      maxStock
                                    )
                                  }
                                  aria-label={`Decrease quantity for ${name}`}
                                >
                                  −
                                </button>

                                <span
                                  className="d-inline-flex align-items-center justify-content-center"
                                  aria-live="polite"
                                >
                                  {item.quantity}
                                </span>

                                <button
                                  type="button"
                                  className="d-inline-flex align-items-center justify-content-center"
                                  disabled={
                                    loading ||
                                    loadError ||
                                    !variant ||
                                    maxStock <= 0 ||
                                    !validQuantity ||
                                    quantity >= maxStock
                                  }
                                  onClick={() =>
                                    updateQuantity(
                                      item.variantId,
                                      quantity + 1,
                                      maxStock
                                    )
                                  }
                                  aria-label={`Increase quantity for ${name}`}
                                >
                                  +
                                </button>
                              </div>
                            </div>
                          </div>

                          {price !== null && validQuantity && (
                            <div className="col-12 col-sm-auto text-sm-end cart-item-price">
                              {quantity > 1 && (
                                <small className="d-block">
                                  ${price.toFixed(2)} each
                                </small>
                              )}

                              <strong className="d-block">
                                ${(price * quantity).toFixed(2)}
                              </strong>
                            </div>
                          )}
                        </div>

                        <button
                          type="button"
                          className="cart-remove-icon position-absolute top-0 end-0 m-3 d-inline-flex align-items-center justify-content-center"
                          onClick={() => removeFromCart(item.variantId)}
                          aria-label={`Remove ${name}, size ${size}`}
                          title="Remove item"
                        >
                          <i
                            className="bi bi-x-lg"
                            aria-hidden="true"
                          ></i>
                        </button>
                      </article>
                    );
                  }
                )}

                {canShowSubtotal && (
                  <div className="cart-summary d-flex justify-content-between gap-3 mt-4 p-3">
                    <span>Subtotal</span>
                    <strong>${subtotal.toFixed(2)}</strong>
                  </div>
                )}

                {canCheckout && (
                  <div className="text-end mt-4">
                    <Link
                      to="/checkout"
                      className="btn btn-norelle px-5 py-3"
                    >
                      Continue to checkout
                      <i
                        className="bi bi-arrow-right ms-2"
                        aria-hidden="true"
                      ></i>
                    </Link>
                  </div>
                )}

                {!canCheckout && !loading && !loadError && (
                  <p className="text-danger mt-3" role="status">
                    Please resolve the items marked above before checkout.
                  </p>
                )}
              </>
            )}
          </div>
        </div>
      </main>
    </>
  );
}

export default Cart;