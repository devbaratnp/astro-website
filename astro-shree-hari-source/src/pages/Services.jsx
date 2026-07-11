import { Link } from 'react-router-dom';
import { ArrowRight } from '@phosphor-icons/react';
import { services } from '../constants';

export function Services() {
  return (
    <div className="section page-section">
      <div className="container" style={{ paddingTop: '40px' }}>
        <div className="section-heading">
          <span>हाम्रा प्रमुख सेवाहरू</span>
          <h2>जीवनका हरेक पक्षका लागि वैदिक समाधान</h2>
          <p>शास्त्रसम्मत विधि, अनुभव र गोपनीयतामा आधारित व्यक्तिगत सेवा</p>
        </div>
        <div className="service-grid">
          {services.map(({ icon: Icon, title, text }) => (
            <article className="service-card" key={title}>
              <Icon weight="thin" />
              <h3>{title}</h3>
              <p>{text}</p>
              <Link to="/appointment">परामर्श लिनुहोस् <ArrowRight /></Link>
            </article>
          ))}
        </div>
      </div>
    </div>
  );
}
