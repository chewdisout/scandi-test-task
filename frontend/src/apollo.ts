// apollo.ts
import { ApolloClient, InMemoryCache, HttpLink } from '@apollo/client';

const uri = 'https://scandi-test-task-be.onrender.com/graphql';

export const client = new ApolloClient({
  link: new HttpLink({ uri }),
  cache: new InMemoryCache(),
});

