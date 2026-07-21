import { useEffect, useState } from 'react';
import { Image, Video, Play, CalendarBlank } from '@phosphor-icons/react';
import { getGallery } from '../services/api';

export function Gallery() {
  const [items, setItems] = useState([]);
  const [type, setType] = useState('all');
  const [loading, setLoading] = useState(true);
  const [lightbox, setLightbox] = useState(null);

  useEffect(() => {
    setLoading(true);
    getGallery(type !== 'all' ? type : '')
      .then(d => setItems(d))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, [type]);

  const hasImage = items.some(i => i.type === 'image');
  const hasVideo = items.some(i => i.type === 'video');

  return (
    <section className="section page-section">
      <div className="container">
        <div className="section-heading">
          <span>मिडिया ग्यालेरी</span>
          <h2>भिडियो तथा फोटो ग्यालेरी</h2>
          <p>प्रवचन, भजन तथा कार्यक्रमका झलकहरू</p>
        </div>

        {(hasImage || hasVideo) && <div className="gallery-tabs">
          <button className={`tab-btn ${type === 'all' ? 'active' : ''}`} onClick={() => setType('all')}>सबै</button>
          {hasVideo && <button className={`tab-btn ${type === 'video' ? 'active' : ''}`} onClick={() => setType('video')}><Video /> भिडियो</button>}
          {hasImage && <button className={`tab-btn ${type === 'image' ? 'active' : ''}`} onClick={() => setType('image')}><Image /> फोटो</button>}
        </div>}

        {loading && <p className="loading-text">लोड हुँदैछ…</p>}

        <div className="gallery-grid">
          {items.map(item => (
            <div className="gallery-item" key={item.id}>
              {item.type === 'video' ? (
                <a href={item.embed_url || item.url} target="_blank" rel="noreferrer" className="gallery-video">
                  <div className="gallery-thumb" style={{ backgroundImage: item.thumbnail ? `url(${item.thumbnail})` : 'none' }}>
                    <span className="play-btn"><Play weight="fill" /></span>
                  </div>
                  <div className="gallery-info">
                    <strong>{item.title_ne}</strong>
                    {item.source && <small>{item.source}</small>}
                  </div>
                </a>
              ) : (
                <div className="gallery-photo" onClick={() => setLightbox(item)}>
                  <img src={item.url} alt={item.title_ne} loading="lazy" />
                  <div className="gallery-info"><strong>{item.title_ne}</strong></div>
                </div>
              )}
            </div>
          ))}
        </div>
        {!loading && items.length === 0 && <p className="empty-text">हाल कुनै मिडिया उपलब्ध छैन।</p>}
      </div>

      {lightbox && (
        <div className="lightbox" onClick={() => setLightbox(null)}>
          <img src={lightbox.url} alt={lightbox.title_ne} />
          <button className="lightbox-close" onClick={() => setLightbox(null)}>✕</button>
        </div>
      )}
    </section>
  );
}
