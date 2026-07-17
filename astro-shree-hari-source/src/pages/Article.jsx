import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import { ArrowLeft, CalendarBlank, ShareNetwork } from '@phosphor-icons/react';

export function Article() {
  const { slug } = useParams();
  const [article, setArticle] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setLoading(true);
    fetch(`/backend/api/articles.php?slug=${slug}`)
      .then(r => r.json())
      .then(d => { if (d.success) setArticle(d.data); else setArticle(null); })
      .catch(() => setArticle(null))
      .finally(() => setLoading(false));
  }, [slug]);

  useEffect(() => {
    if (article) {
      document.title = `${article.title_ne} | Astro Shree Hari`;
      document.querySelector('meta[name="description"]')?.setAttribute('content', article.excerpt_ne || article.title_ne);
    }
  }, [article]);

  if (loading) return <section className="section page-section"><div className="container"><p className="loading-text">लेख लोड हुँदैछ…</p></div></section>;
  if (!article) return <section className="section page-section"><div className="container"><p className="empty-text">लेख फेला परेन।</p><Link to="/blog" className="button button-outline"><ArrowLeft /> पछाडि जानुहोस्</Link></div></section>;

  return (
    <article className="section page-section">
      <div className="container article-container">
        <Link to="/blog" className="back-link"><ArrowLeft /> लेखहरूमा फर्कनुहोस्</Link>
        <div className="article-header">
          <h1>{article.title_ne}</h1>
          <div className="article-meta">
            <span><CalendarBlank /> {article.published_at?.slice(0, 10)}</span>
          </div>
        </div>
        {article.cover_image && <img src={article.cover_image} alt={article.title_ne} className="article-cover" />}
        <div className="article-content">{article.content_ne}</div>
        <div className="article-footer">
          <button className="button button-outline" onClick={() => { navigator.share?.({ title: article.title_ne, url: window.location.href }); }}><ShareNetwork /> सेयर गर्नुहोस्</button>
        </div>
      </div>
    </article>
  );
}
