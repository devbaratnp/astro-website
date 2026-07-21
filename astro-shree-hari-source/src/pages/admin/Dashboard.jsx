export function Dashboard({ data }) {
  if (!data) return <div className="admin-loading">Loading...</div>;
  return (
    <div className="admin-stats">
      {Object.entries(data).map(([k, v]) => (
        <article key={k}>
          <strong>{v}</strong>
          <span>{k.replaceAll('_', ' ')}</span>
        </article>
      ))}
    </div>
  );
}
