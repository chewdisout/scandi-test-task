import React from "react";
import { Route, Routes, Navigate } from "react-router-dom";
import { useQuery } from "@apollo/client";
import { Q_CATEGORIES } from "./gql";
import Header from "./components/Header";
import CategoryPage from "./pages/CategoryPage";
import ProductPage from "./pages/ProductPage";
import { CartProvider } from "./state/CartContext";

const App: React.FC = () => {
  const { data } = useQuery(Q_CATEGORIES);
  const firstCat = data?.categories?.[0]?.name;

  return (
    <CartProvider>
      <Header />
      <Routes>
        <Route path="/" element={firstCat ? <Navigate to={`/${firstCat}`} replace /> : <div className="p-6">Loading…</div>} />
        <Route path="/:name" element={<CategoryPage />} />
        <Route path="/product/:id" element={<ProductPage />} />
        <Route path="*" element={<div className="p-6">Not found</div>} />
      </Routes>
    </CartProvider>
  );
};

export default App;
