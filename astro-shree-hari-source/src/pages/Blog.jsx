import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { Article, CalendarBlank, ArrowRight } from '@phosphor-icons/react';
import { getArticles } from '../services/api';

export function Blog() {
  const [articles, setArticles] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getArticles()
      .then(d => setArticles(d.articles))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  return (
    <section className="section page-section">
      <div className="container">
        <div className="section-heading">
          <span>साहित्य तथा सिर्जना</span>
          <h1>लेख तथा रचनाहरू</h1>
          <p>शास्त्रीय ज्ञान, आध्यात्मिक चिन्तन र सनातन संस्कृतिका विविध आयाम</p>
        </div>
        {loading && <p className="loading-text">लेखहरू लोड हुँदैछन्…</p>}
        <div className="blog-grid">
          {articles.map(a => (
            <Link to={`/article/${a.slug}`} className="blog-card" key={a.id}>
              {a.cover_image && <div className="blog-cover"><img src={a.cover_image} alt={a.title_ne} loading="lazy" /></div>}
              <div className="blog-body">
                <h2>{a.title_ne}</h2>
                {a.excerpt_ne && <p>{a.excerpt_ne}</p>}
                <span className="blog-meta"><CalendarBlank /> {a.published_at?.slice(0, 10)}</span>
                <strong className="blog-read">पूरा पढ्नुहोस् <ArrowRight /></strong>
              </div>
            </Link>
          ))}
        </div>
        {!loading && articles.length === 0 && <p className="empty-text">हाल कुनै लेख उपलब्ध छैनन्।</p>}
      </div>
    </section>
  );
}
