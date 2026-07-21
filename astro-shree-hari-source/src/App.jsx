import { Routes, Route } from 'react-router-dom';
import { Layout } from './components/Layout';
import { Home } from './pages/Home';
import { About } from './pages/About';
import { Services } from './pages/Services';
import { Appointment } from './pages/Appointment';
import { Contact } from './pages/Contact';
import { Admin } from './pages/Admin';
import { Kundali } from './pages/Kundali';
import { Pooja } from './pages/Pooja';
import { Panchang } from './pages/Panchang';
import { Payment } from './pages/Payment';
import { Blog } from './pages/Blog';
import { Article } from './pages/Article';
import { Events } from './pages/Events';
import { Gallery } from './pages/Gallery';
import { Muhurta } from './pages/Muhurta';
import { Store } from './pages/Store';
import './styles.css';

export function App() {
  return (
    <Routes>
      <Route path="/admin/*" element={<Admin />} />
      <Route path="*" element={
        <Layout>
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/about" element={<About />} />
            <Route path="/services" element={<Services />} />
            <Route path="/appointment" element={<Appointment />} />
            <Route path="/contact" element={<Contact />} />
            <Route path="/kundali" element={<Kundali />} />
            <Route path="/pooja" element={<Pooja />} />
            <Route path="/panchang" element={<Panchang />} />
            <Route path="/payment" element={<Payment />} />
            <Route path="/blog" element={<Blog />} />
            <Route path="/article/:slug" element={<Article />} />
            <Route path="/events" element={<Events />} />
            <Route path="/gallery" element={<Gallery />} />
            <Route path="/muhurta" element={<Muhurta />} />
            <Route path="/store" element={<Store />} />
          </Routes>
        </Layout>
      } />
    </Routes>
  );
}
