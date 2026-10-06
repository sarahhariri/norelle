import { useContext, useEffect, useState } from "react";
import { Link } from "react-router";
import Navbar from "./Navbar";
import { CartContext } from "../context/CartContext";
import { API_URL } from "../config/api";

export default function Checkout() {
  const { items, clearCart } = useContext(CartContext);

  const [products, setProducts] = useState(null);
  const [loadError, setLoadError] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState("");
  const [receipt, setReceipt] = useState(null);

  const [form, setForm] = useState({
    customer_name: "",
    phone: "",
    city: "",
    address: "",
    notes: "",
  });

  useEffect(() => {
    const controller = new AbortController();

    async function loadProducts() {
      try {
        const response = await fetch(
          `${API_URL}/api/products`,
          { signal: controller.signal }
        );

        if (!response.ok) {
          throw new Error("Could not load products.");
        }

        const result = await response.json();

        if (!Array.isArray(result.data)) {
          throw new Error("Invalid products response.");
        }

        if (controller.signal.aborted) return;

        setProducts(result.data);
        setLoadError(false);
      } catch (err) {
        if (!controller.signal.aborted) {
          setLoadError(true);
        }
      }
    }

    loadProducts();

    return () => controller.abort();
  }, []);

  const lines = items.map((item) => {
    const product = products?.find(
      (product) => Number(product.id) === Number(item.productId)
    );

    const variant = product?.variants?.find(
      (variant) => Number(variant.id) === Number(item.variantId)
    );

    const currentPrice = product
      ? Number(product.sale_price ?? product.price)
      : NaN;

    const price =
      Number.isFinite(currentPrice) && currentPrice >= 0
        ? currentPrice
        : null;

    const quantity = Number(item.quantity);

    const validQuantity =
      Number.isInteger(quantity) &&
      quantity >= 1 &&
      quantity <= 20;

    const stock = Number(variant?.stock ?? 0);

    const available =
      Boolean(product) &&
      Boolean(variant) &&
      validQuantity &&
      Number.isFinite(stock) &&
      stock >= quantity;

    return {
      item,
      product,
      variant,
      price,
      quantity,
      validQuantity,
      available,
    };
  });

  const canShowSubtotal =
    products !== null &&
    !loadError &&
    lines.every(
      ({ product, price, validQuantity }) =>
        Boolean(product) &&
        price !== null &&
        validQuantity
    );

  const canOrder =
    canShowSubtotal &&
    items.length > 0 &&
    items.length <= 30 &&
    lines.every(({ available }) => available);

  const subtotal = lines.reduce(
    (total, { price, quantity, validQuantity }) => {
      if (price === null || !validQuantity) return total;

      return total + price * quantity;
    },
    0
  );

  function handleChange(event) {
    const { name, value } = event.target;

    setForm((current) => ({
      ...current,
      [name]: value,
    }));
  }

  async function handleSubmit(event) {
    event.preventDefault();

    if (!canOrder || submitting) return;

    setSubmitting(true);
    setError("");

    try {
      const response = await fetch(
        `${API_URL}/api/orders`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          body: JSON.stringify({
            ...form,
            items: items.map((item) => ({
              variant_id: Number(item.variantId),
              quantity: Number(item.quantity),
            })),
          }),
        }
      );

      const result = await response.json();

      if (!response.ok) {
        const firstError = Object.values(result.errors ?? {})
          .flat()
          .find((message) => typeof message === "string");

        throw new Error(
          firstError ||
            result.message ||
            "Could not place order."
        );
      }

      if (!result.success || !result.data?.id) {
        throw new Error(
          "We could not confirm your order. Please contact the store before trying again."
        );
      }

      setReceipt(result.data);
      clearCart();

      window.scrollTo({ top: 0, behavior: "instant" });
    } catch (err) {
      setError(
        err.message || "Could not place order. Please try again."
      );
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <>
      <Navbar />

      <main className="container py-5">
        {receipt ? (
          <div className="row justify-content-center">
            <div className="col-12 col-md-8 col-lg-6 text-center py-5">
              <i
                className="bi bi-check-circle fs-1 text-success"
                aria-hidden="true"
              ></i>

              <h1 className="section-title mt-3">
                Thank you for your order
              </h1>

              <p>Your order number is #{receipt.id}.</p>

              <p>
                Subtotal:{" "}
                <strong>
                  ${Number(receipt.subtotal).toFixed(2)}
                </strong>
              </p>

              <p className="text-secondary">
                We’ll contact you to confirm the delivery details.
              </p>

              <Link
                to="/"
                className="btn btn-norelle px-4 py-2 mt-3"
              >
                Continue shopping
              </Link>
            </div>
          </div>
        ) : items.length === 0 ? (
          <div className="text-center py-5">
            <h1 className="section-title">Your bag is empty</h1>

            <Link
              to="/#shop"
              className="btn btn-norelle mt-3"
            >
              Explore products
            </Link>
          </div>
        ) : (
          <>
            <Link to="/bag" className="product-link">
              <i
                className="bi bi-arrow-left me-2"
                aria-hidden="true"
              ></i>
              Back to bag
            </Link>

            <h1 className="section-title my-4">Checkout</h1>

            <div className="row g-5">
              <div className="col-12 col-lg-7">
                <h2 className="h5 mb-4">Delivery details</h2>

                <form onSubmit={handleSubmit}>
                  <div className="row g-3">
                    {[
                      ["customer_name", "Full name", "text"],
                      ["phone", "Phone number", "tel"],
                      ["city", "City / area", "text"],
                    ].map(([name, label, type]) => (
                      <div
                        className="col-12 col-md-6"
                        key={name}
                      >
                        <label
                          className="form-label"
                          htmlFor={name}
                        >
                          {label}
                        </label>

                        <input
                          id={name}
                          name={name}
                          type={type}
                          className="form-control"
                          value={form[name]}
                          onChange={handleChange}
                          maxLength={
                            name === "customer_name"
                              ? 120
                              : name === "phone"
                                ? 30
                                : 100
                          }
                          autoComplete={
                            name === "customer_name"
                              ? "name"
                              : name === "phone"
                                ? "tel"
                                : "address-level2"
                          }
                          disabled={submitting}
                          required
                        />
                      </div>
                    ))}

                    <div className="col-12">
                      <label
                        className="form-label"
                        htmlFor="address"
                      >
                        Full address
                      </label>

                      <textarea
                        id="address"
                        name="address"
                        className="form-control"
                        rows="3"
                        value={form.address}
                        onChange={handleChange}
                        maxLength={500}
                        autoComplete="street-address"
                        disabled={submitting}
                        required
                      />
                    </div>

                    <div className="col-12">
                      <label
                        className="form-label"
                        htmlFor="notes"
                      >
                        Notes (optional)
                      </label>

                      <textarea
                        id="notes"
                        name="notes"
                        className="form-control"
                        rows="2"
                        value={form.notes}
                        onChange={handleChange}
                        maxLength={1000}
                        disabled={submitting}
                      />
                    </div>
                  </div>

                  <p className="text-secondary small mt-4 mb-2">
                    Payment: Cash on delivery
                  </p>

                  {error && (
                    <div
                      className="alert alert-danger"
                      role="alert"
                    >
                      {error}
                    </div>
                  )}

                  <button
                    type="submit"
                    className="btn btn-norelle px-5 py-3 mt-3"
                    disabled={!canOrder || submitting}
                  >
                    {submitting
                      ? "Placing order..."
                      : "Place order"}
                  </button>
                </form>
              </div>

              <div className="col-12 col-lg-5">
                <div className="border p-4">
                  <h2 className="h5 mb-4">Order summary</h2>

                  {lines.map(
                    ({
                      item,
                      product,
                      variant,
                      price,
                      quantity,
                      validQuantity,
                      available,
                    }) => (
                      <div className="mb-3" key={item.variantId}>
                        <div className="d-flex justify-content-between gap-3">
                          <span>
                            {product?.name ?? item.name} ·{" "}
                            {variant?.size ?? item.size} ×{" "}
                            {item.quantity}
                          </span>

                          <strong className="text-nowrap">
                            {price === null || !validQuantity
                              ? "—"
                              : `$${(price * quantity).toFixed(2)}`}
                          </strong>
                        </div>

                        {!loadError &&
                          products !== null &&
                          !available && (
                            <p className="small text-danger mt-1 mb-0">
                              Unavailable or insufficient stock.
                              Please review this item in your bag.
                            </p>
                          )}
                      </div>
                    )
                  )}

                  <hr />

                  <div className="d-flex justify-content-between">
                    <span>Subtotal</span>

                    <strong>
                      {loadError
                        ? "—"
                        : products === null
                          ? "Loading..."
                          : canShowSubtotal
                            ? `$${subtotal.toFixed(2)}`
                            : "—"}
                    </strong>
                  </div>
                </div>

                {loadError && (
                  <p className="text-danger mt-3" role="alert">
                    Could not load current prices and stock.
                    Please refresh the page.
                  </p>
                )}

                {!loadError &&
                  products !== null &&
                  !canOrder && (
                    <p className="text-danger mt-3" role="alert">
                      {items.length > 30
                        ? "An order can contain up to 30 different items. Please review your bag."
                        : "An item is unavailable or its quantity is invalid or exceeds the available stock. Please review your bag."}
                    </p>
                  )}
              </div>
            </div>
          </>
        )}
      </main>
    </>
  );
}