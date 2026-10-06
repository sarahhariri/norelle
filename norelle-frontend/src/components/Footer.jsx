import { Link } from "react-router";

function Footer() {
  return (
    <footer className="norelle-footer pt-5 pb-3">
      <div className="container">
        <div className="row g-4 g-lg-5 pb-4">
          <div className="col-12 col-md-5">
            <p className="eyebrow mb-3">MADE FOR YOUR EVERYDAY</p>

            <Link
              to="/"
              className="footer-brand text-decoration-none"
            >
              NORELLE
            </Link>

            <p className="product-description mt-3 mb-0">
              Timeless style.
              <br />
              A little elegance, every day.
            </p>
          </div>

          <div className="col-6 col-md-3">
            <h2 className="eyebrow fs-6 mb-4">EXPLORE</h2>

            <nav aria-label="Footer navigation">
              <Link
                to="/#collections"
                className="footer-link d-block mb-3"
              >
                Collections
              </Link>

              <Link
                to="/#shop"
                className="footer-link d-block mb-3"
              >
                Shop
              </Link>

              <Link
                to="/#about"
                className="footer-link d-block"
              >
                About
              </Link>
            </nav>
          </div>

          <div className="col-6 col-md-4">
            <h2 className="eyebrow fs-6 mb-4">STAY CONNECTED</h2>

            <p className="footer-link mb-3">
              <i
                className="bi bi-telephone me-2"
                aria-hidden="true"
              ></i>
              <span className="text-nowrap">
                +961 XX XXX XXX
              </span>
            </p>

            <div className="d-flex gap-2 mb-3">
              <span
                className="footer-social rounded-circle d-inline-flex align-items-center justify-content-center"
                role="img"
                aria-label="Instagram — demo"
              >
                <i
                  className="bi bi-instagram"
                  aria-hidden="true"
                ></i>
              </span>

              <span
                className="footer-social rounded-circle d-inline-flex align-items-center justify-content-center"
                role="img"
                aria-label="Facebook — demo"
              >
                <i
                  className="bi bi-facebook"
                  aria-hidden="true"
                ></i>
              </span>

              <span
  className="footer-social rounded-circle d-inline-flex align-items-center justify-content-center"
  role="img"
  aria-label="WhatsApp — demo"
>
  <i
    className="bi bi-whatsapp"
    aria-hidden="true"
  ></i>
</span>
            </div>

            <small className="product-description">
              Demo contact details
            </small>
          </div>
        </div>

        <div className="footer-bottom pt-3">
          <div className="row g-2 align-items-center">
            <div className="col-12 col-md-6 text-center text-md-start">
              <small>
                © {new Date().getFullYear()} NORELLE
              </small>
            </div>

            <div className="col-12 col-md-6 text-center text-md-end">
              <small>Timeless style. Everyday elegance.</small>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
}

export default Footer;