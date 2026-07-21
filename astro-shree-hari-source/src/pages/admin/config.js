import { ChartBar, CalendarCheck, HandsPraying, CreditCard, Envelope, BookOpen, List } from '@phosphor-icons/react';

export const sections = [
  ['dashboard', 'Dashboard', ChartBar],
  ['appointments', 'Appointments', CalendarCheck],
  ['pooja', 'Pooja orders', HandsPraying],
  ['payments', 'Payments', CreditCard],
  ['services', 'Pooja services', List],
  ['articles', 'Articles', BookOpen],
  ['events', 'Events & Tours', CalendarCheck],
  ['gallery', 'Gallery', BookOpen],
  ['testimonials', 'Testimonials', ChartBar],
  ['panchang', 'Panchang', CalendarCheck],
];

export const editors = {
  services: [
    ['title_ne', 'Nepali title'],
    ['title_en', 'English title'],
    ['category', 'Category'],
    ['base_price', 'Price'],
    ['duration_minutes', 'Duration (min)'],
  ],
  articles: [
    ['title_ne', 'Nepali title'],
    ['slug', 'URL slug'],
    ['excerpt_ne', 'Excerpt'],
    ['content_ne', 'Content'],
  ],
  panchang: [
    ['date', 'Date'],
    ['tithi', 'Tithi'],
    ['nakshatra', 'Nakshatra'],
    ['sunrise', 'Sunrise'],
    ['sunset', 'Sunset'],
    ['special_events_ne', 'Special events'],
  ],
  testimonials: [
    ['name', 'Name'],
    ['title', 'Title'],
    ['content', 'Content'],
    ['rating', 'Rating (1–5)'],
    ['location', 'Location'],
    ['sort_order', 'Sort order'],
  ],
  events: [
    ['type', 'Type (event/tour)'],
    ['title_ne', 'Nepali title'],
    ['title_en', 'English title'],
    ['date_from', 'Date from'],
    ['location', 'Location'],
    ['contact_person', 'Contact person'],
    ['contact_phone', 'Contact phone'],
  ],
  gallery: [
    ['type', 'Type (image/video/audio)'],
    ['title_ne', 'Nepali title'],
    ['url', 'URL'],
    ['thumbnail', 'Thumbnail URL'],
    ['embed_url', 'Embed URL'],
    ['source', 'Source'],
  ],
};

export const imageFields = {
  articles: ['cover_image'],
  events: ['cover_image'],
  gallery: ['url', 'thumbnail'],
  testimonials: ['photo'],
};
