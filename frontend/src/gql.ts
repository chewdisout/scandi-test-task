import { gql } from "@apollo/client";

export const Q_CATEGORIES = gql`
  { categories { id name } }
`;

export const Q_PRODUCTS = gql`
  query Products($category: String) {
    products(category: $category) {
      id
      name
      inStock
      brand
      gallery
      prices { amount currency { symbol } }
      attributes {
        id
        name
        type
        items { id displayValue value }
      }
    }
  }
`;

export const Q_PRODUCT = gql`
  query Product($id: ID!) {
    product(id: $id) {
      id
      name
      inStock
      brand
      description
      gallery
      prices { amount currency { symbol label } }
      attributes {
        id
        name
        type
        items { id displayValue value }
      }
    }
  }
`;

export const M_CREATE_ORDER = gql`
  mutation CreateOrder($input: CreateOrderInput!) {
    createOrder(input: $input) {
      id
      createdAt
      items {
        id
        quantity
        selectedAttributes { name value }
        product { id name }
      }
    }
  }
`;
