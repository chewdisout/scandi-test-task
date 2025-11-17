import { Link, useMatch, useResolvedPath } from "react-router-dom";

type CategoryLinkProps = {
  to: string;
  children: React.ReactNode;
};

export default function CategoryLink({ to, children }: CategoryLinkProps) {
  const resolved = useResolvedPath(to);
  const match = useMatch({ path: resolved.pathname, end: true });
  const isActive = !!match;

  return (
    <Link
      to={to}
      data-testid={isActive ? "active-category-link" : "category-link"}
    >
        <span className={isActive ? "nav-link nav-link--active" : "nav-link"}>{children}</span>
    </Link>
  );
}
