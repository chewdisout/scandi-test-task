import React from "react";
import { useParams } from "react-router-dom";
import { useQuery } from "@apollo/client";
import { Q_PRODUCT } from "../gql";
import { kebab, formatPrice } from "../utils/format";
import { useCart } from "../state/CartContext";

function formatTestIdValue(attrK: string, value: string) {
  if (attrK === "color") return value;
  if (attrK === "capacity") return value.toUpperCase();
  return kebab(value);
}

export default function ProductPage(){
  const { id } = useParams<{id:string}>();
  const { data, loading, error } = useQuery(Q_PRODUCT, { variables:{ id } });
  const [active, setActive] = React.useState(0);
  const [sel, setSel] = React.useState<Record<string,string>>({});
  const { add } = useCart();

  if (loading) return <div className="grid">Loading…</div>;
  if (error) return <pre>{error.message}</pre>;

  const p = data.product;
  const price = p.prices[0];
  const canAdd = p.attributes.every((a:any) => sel[a.name]);

  const onAdd = () => {
    if (!canAdd) return;
    add({
      productId: p.id,
      name: p.name,
      brand: p.brand,
      image: p.gallery[0],
      prices: p.prices,
      selected: sel,
      attributes: p.attributes,
    }, 1);
  };

  return (
    <div className="pdp">
      <div className="thumbs">
        {p.gallery.map((g:string,i:number)=>(
          <img key={g} src={g} onClick={()=>setActive(i)} alt="" />
        ))}
      </div>

      <div className="mainimg" data-testid="product-gallery">
        <img src={p.gallery[active]} alt={p.name}/>
      </div>

      <div>
        <h2 style={{margin:'0 0 6px 0'}}>{p.name}</h2>

        {p.attributes.map((a:any) => {
          const attrK = kebab(a.name);
          const current = sel[a.name];
          return (
            <div key={a.id} className="attr" data-testid={`product-attribute-${attrK}`}>
              <div className="label">{a.name}</div>
              <div className="opts">
                {a.items.map((it:any) => {
                  const pressed = current === it.value;
                  const style = a.type === 'swatch' ? { background: it.value } : undefined;
                  const cls = a.type === 'swatch' ? 'swatch' : 'opt';
                  return (
                    <button
                      key={it.id}
                      onClick={()=>setSel(s=>({ ...s, [a.name]: it.value }))}
                      aria-pressed={pressed}
                      className={cls}
                      style={style}
                      data-testid={`product-attribute-${attrK}-${formatTestIdValue(attrK, it.value)}`}
                      aria-label={`${a.name}: ${it.displayValue}`}
                    >
                      {a.type === 'swatch' ? '' : it.displayValue}
                    </button>
                  );
                })}
              </div>
            </div>
          );
        })}

        <div className="attr">
          <div className="label">PRICE:</div>
          <div style={{fontWeight:600, fontSize:18}}>
            {price ? formatPrice(price.amount, price.currency.symbol) : ""}
          </div>
        </div>

        <button
          className="add"
          data-testid="add-to-cart"
          disabled={!canAdd}
          onClick={onAdd}
        >
          ADD TO CART
        </button>

        <div className="desc" data-testid="product-description">
          <p>{p.description}</p>
        </div>
      </div>
    </div>
  );
}
