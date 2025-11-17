import { useCart } from "../state/CartContext";
import { kebab, formatPrice } from "../utils/format";
import { useMutation } from "@apollo/client";
import { M_CREATE_ORDER } from "../gql";

export default function CartOverlay({ open, onClose }:{ open:boolean; onClose:()=>void }){
  const { lines, inc, dec, total, count, clear } = useCart();
  const [createOrder, { loading }] = useMutation(M_CREATE_ORDER);

  if(!open) return null;

  const place = async () => {
    if (count === 0) return;
    const items = lines.map(l => ({
      productId: l.productId,
      quantity: l.quantity,
      selectedAttributes: Object.entries(l.selected).map(([name,value])=>({ name, value }))
    }));
    await createOrder({ variables:{ input:{ items } }});
    clear();
    onClose();
  };

  return (
    <>
      <div className="backdrop" data-testid="cart-btn" onClick={onClose}/>
      <div className="panel" data-testid="cart-overlay">
        <div style={{fontWeight:600, marginBottom:8}}>My Bag, {count === 1 ? "1 Item" : `${count} Items`}</div>

        {lines.map(l => (
          <div key={l.key} className="item">
            <div style={{flex:1}}>
              <div style={{fontWeight:600}}>{l.name}</div>
              {/* attribute groups with all options, mark selected; non-clickable */}
              {l.attributes.map(a => {
                const attrK = kebab(a.name);
                const sel = l.selected[a.name];
                return (
                  <div key={a.name} data-testid={`cart-item-attribute-${attrK}`} style={{marginTop:6}}>
                    {a.items.map(it => {
                      const isSel = it.value === sel;
                      const itemK = kebab(it.value);
                      const base = a.type === 'swatch'
                        ? { width:18, height:18, border:'1px solid #ddd', background: it.value, display:'inline-block', marginRight:6 }
                        : { border:'1px solid #1D1F22', padding:'2px 6px', marginRight:6, display:'inline-block', fontSize:12 };
                      return (
                        <span
                          key={it.id}
                          style={isSel ? { ...base, outline:'2px solid #1D1F22' } : base}
                          data-testid={`cart-item-attribute-${attrK}-${itemK}${isSel?'-selected':''}`}
                          title={`${a.name}: ${it.displayValue}`}
                        >
                          {a.type==='swatch' ? '' : it.displayValue}
                        </span>
                      );
                    })}
                  </div>
                );
              })}
            </div>

            <div className="qty">
              <button className="btn" data-testid="cart-item-amount-increase" onClick={()=>inc(l.key)}>+</button>
              <div data-testid="cart-item-amount">{l.quantity}</div>
              <button className="btn" data-testid="cart-item-amount-decrease" onClick={()=>dec(l.key)}>-</button>
            </div>

            <img src={l.image} alt={l.name} style={{width:105, height:137, objectFit:'cover'}}/>
          </div>
        ))}

        <div className="summary">
          <span>Total</span>
          <span data-testid="cart-total">{formatPrice(total("$"), "$")}</span>
        </div>

        <button
          className="place"
          onClick={place}
          disabled={count===0 || loading}
        >
          PLACE ORDER
        </button>
      </div>
    </>
  );
}
