import { useCart } from "../state/CartContext";
import { kebab, formatPrice } from "../utils/format";
import { useMutation } from "@apollo/client";
import { M_CREATE_ORDER } from "../gql";

export default function CartOverlay() {
  const {
    lines,
    inc,
    dec,
    total,
    count,
    clear,
    isOverlayOpen,
    closeOverlay,
  } = useCart();

  const [createOrder, { loading }] = useMutation(M_CREATE_ORDER);

  if (!isOverlayOpen) return null;

  const place = async () => {
    if (count === 0) return;
    const items = lines.map(l => ({
      productId: l.productId,
      quantity: l.quantity,
      selectedAttributes: Object.entries(l.selected).map(([name, value]) => ({
        name,
        value,
      })),
    }));
    await createOrder({ variables: { input: { items } } });
    clear();
    closeOverlay();
  };

  return (
    <>
      <div className="backdrop" onClick={closeOverlay} />
      <div className="panel" data-testid="cart-overlay">
        <div className="panel-title">
          <b>My Bag</b>, {count === 1 ? "1 item" : `${count} items`}
        </div>

        {lines.map(l => {
          const priceObj = l.prices[0];
          const itemPrice = priceObj
            ? formatPrice(priceObj.amount, priceObj.currency.symbol)
            : "";

          return (
            <div key={l.key} className="item">
              {/* left column: name, price, attributes */}
              <div className="item-left">
                <div className="item-name">{l.name}</div>

                <div className="item-price">{itemPrice}</div>

                {l.attributes.map(a => {
                  const attrK = kebab(a.name);
                  const sel = l.selected[a.name];

                  return (
                    <div
                      key={a.name}
                      className="item-attr"
                      data-testid={`cart-item-attribute-${attrK}`}
                    >
                      <div className="item-attr-label">{a.name}:</div>
                      <div className="item-attr-options">
                        {a.items.map(it => {
                          const isSel = it.value === sel;
                          const itemK = kebab(it.value);
                          const base =
                            a.type === "swatch"
                              ? {
                                  width: 18,
                                  height: 18,
                                  border: "1px solid #ddd",
                                  background: it.value,
                                  display: "inline-block",
                                  marginRight: 6,
                                }
                              : {
                                  border: "1px solid #1D1F22",
                                  padding: "2px 6px",
                                  marginRight: 6,
                                  display: "inline-block",
                                  fontSize: 12,
                                };
                          return (
                            <span
                              key={it.id}
                              className={
                                isSel
                                  ? "selected"
                                  : "not-selected"
                              }
                              style={
                                isSel
                                  ? { ...base, outline: "2px solid #1D1F22" }
                                  : base
                              }
                              data-testid={`cart-item-attribute-${attrK}-${itemK}${
                                isSel ? "-selected" : ""
                              }`}
                              title={`${a.name}: ${it.displayValue}`}
                            >
                              {a.type === "swatch" ? "" : it.displayValue}
                            </span>
                          );
                        })}
                      </div>
                    </div>
                  );
                })}
              </div>

              {/* middle: qty controls */}
              <div className="qty">
                <button
                  className="btn"
                  data-testid="cart-item-amount-increase"
                  onClick={() => inc(l.key)}
                >
                  +
                </button>
                <div data-testid="cart-item-amount">{l.quantity}</div>
                <button
                  className="btn"
                  data-testid="cart-item-amount-decrease"
                  onClick={() => dec(l.key)}
                >
                  -
                </button>
              </div>

              {/* right: image */}
              <img
                src={l.image}
                alt={l.name}
                className="item-image"
              />
            </div>
          );
        })}

        <div className="summary">
          <span>Total</span>
          <span data-testid="cart-total">
            {formatPrice(total("$"), "$")}
          </span>
        </div>

        <button
          className="place"
          onClick={place}
          disabled={count === 0 || loading}
        >
          PLACE ORDER
        </button>
      </div>
    </>
  );
}
