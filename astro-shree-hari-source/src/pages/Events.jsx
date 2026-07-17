import { useEffect, useState } from 'react';
import { CalendarBlank, MapPin, Clock, User, Phone, ArrowRight } from '@phosphor-icons/react';

export function Events() {
  const [events, setEvents] = useState([]);
  const [tours, setTours] = useState([]);
  const [tab, setTab] = useState('upcoming');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setLoading(true);
    Promise.all([
      fetch('/backend/api/events.php?type=event').then(r => r.json()),
      fetch('/backend/api/events.php?type=tour').then(r => r.json()),
    ]).then(([ed, td]) => {
      if (ed.success) setEvents(ed.data);
      if (td.success) setTours(td.data);
    }).catch(() => {}).finally(() => setLoading(false));
  }, []);

  const items = tab === 'upcoming' ? events : tours;

  return (
    <section className="section page-section">
      <div className="container">
        <div className="section-heading">
          <span>कार्यक्रम तथा यात्रा</span>
          <h2>आगामी कार्यक्रम र धार्मिक भ्रमण</h2>
          <p>प्रवचन, अनुष्ठान तथा तीर्थयात्राको विस्तृत जानकारी</p>
        </div>

        <div className="events-tabs">
          <button className={`tab-btn ${tab === 'upcoming' ? 'active' : ''}`} onClick={() => setTab('upcoming')}>प्रवचन तथा कार्यक्रम</button>
          <button className={`tab-btn ${tab === 'tour' ? 'active' : ''}`} onClick={() => setTab('tour')}>धार्मिक यात्रा</button>
        </div>

        {loading && <p className="loading-text">लोड हुँदैछ…</p>}

        <div className="events-grid">
          {items.map(e => (
            <article className="event-card" key={e.id}>
              {e.cover_image && <div className="event-cover"><img src={e.cover_image} alt={e.title_ne} /></div>}
              <div className="event-body">
                <h3>{e.title_ne}</h3>
                {e.title_en && <em className="event-title-en">{e.title_en}</em>}
                <div className="event-details">
                  <span><CalendarBlank /> {e.date_from?.slice(0, 10)}{e.date_to ? ` — ${e.date_to.slice(0, 10)}` : ''}</span>
                  {e.time_from && <span><Clock /> {e.time_from?.slice(0, 5)}</span>}
                  <span><MapPin /> {e.location}</span>
                  {e.contact_person && <span><User /> {e.contact_person}</span>}
                  {e.contact_phone && <span><Phone /> {e.contact_phone}</span>}
                </div>
                {e.description_ne && <p>{e.description_ne}</p>}
                {e.registration_url && <a href={e.registration_url} target="_blank" rel="noreferrer" className="button button-maroon">दर्ता गर्नुहोस् <ArrowRight /></a>}
              </div>
            </article>
          ))}
        </div>
        {!loading && items.length === 0 && <p className="empty-text">हाल कुनै कार्यक्रम उपलब्ध छैन।</p>}
      </div>
    </section>
  );
}
