import React, { createContext, useContext, useEffect, useMemo, useState } from "react";

export type SelectedMap = Record<string, string>; // { Size: "M", Color: "#fff" }

export type CartLine = {
  key: string;
  productId: string;
  name: string;
  brand?: string;
  image?: string;
  prices: { amount: number; currency: { symbol: string } }[];
  selected: Record<string, string>;
  attributes: {
    name: string;
    type: string;
    items: { id: string; displayValue: string; value: string }[];
  }[];
  quantity: number;
};


type CartCtx = {
  lines: CartLine[];
  add: (line: Omit<CartLine, "key" | "quantity">, quantity?: number) => void;
  inc: (key: string) => void;
  dec: (key: string) => void;
  clear: () => void;
  count: number;
  total: (symbol: string) => number;
};

const Ctx = createContext<CartCtx>(null as any);

const keyOf = (pId: string, selected: SelectedMap) =>
  `${pId}|${Object.entries(selected).sort().map(([k, v]) => `${k}:${v}`).join(",")}`;

const LS_KEY = "cart_v1";

export const CartProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [lines, setLines] = useState<CartLine[]>(() => {
    const raw = localStorage.getItem(LS_KEY);
    return raw ? JSON.parse(raw) : [];
  });

  useEffect(() => {
    localStorage.setItem(LS_KEY, JSON.stringify(lines));
  }, [lines]);

  const add: CartCtx["add"] = (l, q = 1) => {
    const key = keyOf(l.productId, l.selected);
    setLines(prev => {
      const idx = prev.findIndex(x => x.key === key);
      if (idx >= 0) {
        const copy = [...prev];
        copy[idx] = { ...copy[idx], quantity: copy[idx].quantity + q };
        return copy;
      }
      return [...prev, { ...l, key, quantity: q }];
    });
  };

  const inc = (key: string) =>
    setLines(prev => prev.map(x => (x.key === key ? { ...x, quantity: x.quantity + 1 } : x)));

  const dec = (key: string) =>
    setLines(prev =>
      prev
        .map(x => (x.key === key ? { ...x, quantity: x.quantity - 1 } : x))
        .filter(x => x.quantity > 0)
    );

  const clear = () => setLines([]);

  const count = useMemo(() => lines.reduce((a, b) => a + b.quantity, 0), [lines]);

  const total: CartCtx["total"] = () =>
    lines.reduce((sum, l) => {
      const price = l.prices[0]?.amount ?? 0;
      return sum + price * l.quantity;
    }, 0);

  return (
    <Ctx.Provider value={{ lines, add, inc, dec, clear, count, total }}>
      {children}
    </Ctx.Provider>
  );
};

export const useCart = () => useContext(Ctx);
