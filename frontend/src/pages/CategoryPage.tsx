import { useParams } from "react-router-dom";
import { useQuery } from "@apollo/client";
import { Q_PRODUCTS } from "../gql";
import ProductCard from "../components/ProductCard";

export default function CategoryPage(){
  const { name } = useParams<{name:string}>();
  const { data, loading, error } = useQuery(Q_PRODUCTS, { variables:{ category: name }});
  if (loading) return <div className="grid">Loading…</div>;
  if (error) return <pre>{error.message}</pre>;
  return (
    <div className="grid">
      {data.products.map((p:any)=> <ProductCard key={p.id} product={p}/>)}
    </div>
  );
}
