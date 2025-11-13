export const kebab = (s: string) =>
  s.toLowerCase().replace(/\s+/g, "-").replace(/[^a-z0-9-]/g, "");

export const formatPrice = (amount: number, symbol: string) =>
  `${symbol}${amount.toFixed(2)}`;
