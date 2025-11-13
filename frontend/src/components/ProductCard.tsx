import React from "react";
import { Link } from "react-router-dom";
import { kebab, formatPrice } from "../utils/format";
import { useCart } from "../state/CartContext";

type P = {
  product: {
        id: string;
        name: string;
        inStock: boolean;
        brand?: string;
        gallery: string[];
        prices: { amount: number; currency: { symbol: string } }[];
        attributes: {
        name: string;
        type: string;
        items: { id: string; displayValue: string; value: string }[];
        }[];
    };
};

export default function ProductCard({ product }: P){
  const { add } = useCart();
  const img = product.gallery[0];
  const price = product.prices[0];

  const defaults = Object.fromEntries(product.attributes.map(a => [a.name, a.items[0]?.value ?? ""]));

  const quick = (e: React.MouseEvent) => {
    e.preventDefault();
    if (!product.inStock) return;
    add({
      productId: product.id,
      name: product.name,
      brand: product.brand,
      image: img,
      prices: product.prices,
      selected: defaults,
      attributes: product.attributes,
    }, 1);
  };

  return (
    <Link to={`/product/${product.id}`} className="card" data-testid={`product-${kebab(product.name)}`}>
      <div className="imgwrap">
        <img src={img} alt={product.name}/>
        {!product.inStock && <div className="oos">OUT OF STOCK</div>}
        {product.inStock && <button className="quick" onClick={quick}>🛒</button>}
      </div>
      <div className="title">{product.brand} {product.name}</div>
      {price && <div className="price">{formatPrice(price.amount, price.currency.symbol)}</div>}
    </Link>
  );
}
