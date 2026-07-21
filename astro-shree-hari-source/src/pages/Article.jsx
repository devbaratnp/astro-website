import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import { ArrowLeft, CalendarBlank, ShareNetwork } from '@phosphor-icons/react';
import { getArticle } from '../services/api';

function setMeta(name, content) {
  let el = document.querySelector(`meta[name="${name}"], meta[property="${name}"]`);
  if (!el) { el = document.createElement('meta'); el.setAttribute(name.startsWith('og:') ? 'property' : 'name', name); document.head.appendChild(el); }
  el.setAttribute('content', content);
}

function removeMeta(name) {
  document.querySelectorAll(`meta[name="${name}"], meta[property="${name}"]`).forEach(el => el.remove());
}

export function Article() {
  const { slug } = useParams();
  const [article, setArticle] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setLoading(true);
    getArticle(slug)
      .then(d => setArticle(d))
      .catch(() => setArticle(null))
      .finally(() => setLoading(false));
  }, [slug]);

  useEffect(() => {
    if (!article) return;
    const url = `https://www.astroshreehari.com/article/${article.slug}`;
    const desc = article.excerpt_ne || article.title_ne;
    document.title = `${article.title_ne} | Astro Shree Hari`;
    setMeta('description', desc);
    setMeta('og:title', `${article.title_ne} | Astro Shree Hari`);
    setMeta('og:description', desc);
    setMeta('og:url', url);
    setMeta('og:type', 'article');
    setMeta('og:site_name', 'Astro Shree Hari');
    setMeta('og:locale', 'ne_NP');
    if (article.cover_image) setMeta('og:image', article.cover_image);
    let ld = document.getElementById('article-ld');
    if (!ld) { ld = document.createElement('script'); ld.id = 'article-ld'; ld.type = 'application/ld+json'; document.head.appendChild(ld); }
    ld.textContent = JSON.stringify({
      '@context': 'https://schema.org',
      '@type': 'Article',
      headline: article.title_ne,
      description: article.excerpt_ne || article.title_ne,
      ...(article.cover_image && { image: article.cover_image }),
      datePublished: article.published_at,
      author: { '@type': 'Person', name: 'पं. ज्यो. सीताराम तिमल्सेना', url: 'https://www.astroshreehari.com/about' },
      publisher: { '@type': 'Organization', name: 'श्रीहरि ज्योतिष परामर्श केन्द्र', url: 'https://www.astroshreehari.com' },
      mainEntityOfPage: { '@type': 'WebPage', '@id': url },
    });
    return () => { removeMeta('og:title'); removeMeta('og:description'); removeMeta('og:url'); removeMeta('og:type'); removeMeta('og:site_name'); removeMeta('og:locale'); removeMeta('og:image'); };
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
        <div className="article-content" dangerouslySetInnerHTML={{ __html: article.content_ne }} />
        <div className="article-footer">
          <button className="button button-outline" onClick={() => { navigator.share?.({ title: article.title_ne, url: window.location.href }); }}><ShareNetwork /> सेयर गर्नुहोस्</button>
        </div>
      </div>
    </article>
  );
}
