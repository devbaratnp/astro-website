import { useEffect, useState, useRef } from 'react';
import {
  ChartBar, CalendarCheck, HandsPraying, CreditCard,
  Envelope, BookOpen, SignOut, List, Upload, X, Plus, House
} from '@phosphor-icons/react';
import { API_BASE, BASE_PATH } from '../config';
import '../admin.css';

const API = API_BASE;

const sections = [
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

const editors = {
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

const imageFields = {
  articles: ['cover_image'],
  events: ['cover_image'],
  gallery: ['url', 'thumbnail'],
  testimonials: ['photo'],
};

async function api(path, options = {}) {
  const response = await fetch(`${API}/${path}`, {
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json' },
    ...options,
  });
  const body = await response.json().catch(() => ({
    success: false,
    message: `Server error (${response.status})`,
  }));
  if (!body.success) throw new Error(body.message || 'Request failed');
  return body.data;
}

export function Admin() {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [section, setSection] = useState('dashboard');
  const [data, setData] = useState(null);
  const [error, setError] = useState('');
  const [refresh, setRefresh] = useState(0);
  const [editing, setEditing] = useState(null);
  const [credentials, setCredentials] = useState({ username: '', password: '' });

  useEffect(() => {
    api('auth.php')
      .then(x => setUser(x.user))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  useEffect(() => {
    if (!user) return;
    setData(null);
    setError('');
    setEditing(null);
    api(`admin.php?resource=${section}`)
      .then(setData)
      .catch(e => setError(e.message));
  }, [user, section, refresh]);

  async function login(e) {
    e.preventDefault();
    setError('');
    try {
      const x = await api('auth.php', {
        method: 'POST',
        body: JSON.stringify(credentials),
      });
      setUser(x.user);
    } catch (e) {
      setError(e.message);
    }
  }

  async function logout() {
    await api('auth.php?logout=1');
    setUser(null);
  }

  async function mutate(method, payload) {
    try {
      await api(`admin.php?resource=${section}`, {
        method,
        body: JSON.stringify(payload),
      });
      setRefresh(x => x + 1);
    } catch (e) {
      setError(e.message);
    }
  }

  async function save(e) {
    e.preventDefault();
    const payload = Object.fromEntries(new FormData(e.currentTarget));
    if (editing?.id) payload.id = editing.id;
    await mutate(editing?.id ? 'PUT' : 'POST', payload);
  }

  if (loading) return <div className="admin-loading">Loading...</div>;

  if (!user) {
    return (
      <div className="admin-login">
        <form onSubmit={login}>
          <div className="admin-symbol">ॐ</div>
          <h1>Admin login</h1>
          <p>Shreehari management system</p>
          {error && <div className="admin-error">{error}</div>}
          <label>
            Username
            <input
              value={credentials.username}
              onChange={e =>
                setCredentials({ ...credentials, username: e.target.value })
              }
              required
            />
          </label>
          <label>
            Password
            <input
              type="password"
              value={credentials.password}
              onChange={e =>
                setCredentials({ ...credentials, password: e.target.value })
              }
              required
            />
          </label>
          <button>Log in</button>
          <a href={`${BASE_PATH || '/'}`}>← Back to website</a>
        </form>
      </div>
    );
  }

  const current = sections.find(s => s[0] === section);
  const Icon = current?.[2] || ChartBar;
  const [sidebarOpen, setSidebarOpen] = useState(true);

  return (
    <div className={`admin-shell${sidebarOpen ? '' : ' sidebar-collapsed'}`}>
      <aside>
        <div className="admin-brand">
          <button className="sidebar-toggle" onClick={() => setSidebarOpen(o => !o)} aria-label="Toggle sidebar">
            <List weight="bold" />
          </button>
          <span>
            Shreehari Admin
            <small>Management system</small>
          </span>
        </div>
        <nav>
          {sections.map(([key, label, Icon]) => (
            <button
              key={key}
              className={section === key ? 'active' : ''}
              onClick={() => setSection(key)}
              title={sidebarOpen ? '' : label}
            >
              <Icon />
              <span className="nav-label">{label}</span>
            </button>
          ))}
        </nav>
        <div className="admin-user">
          <button onClick={logout}>
            <SignOut /> <span className="nav-label">Logout</span>
          </button>
        </div>
      </aside>

      <main>
        <div className="admin-topbar">
          {!sidebarOpen && (
            <button className="sidebar-toggle mobile-toggle" onClick={() => setSidebarOpen(true)} aria-label="Open sidebar">
              <List weight="bold" />
            </button>
          )}
          <h2>
            <Icon style={{ marginRight: 8, verticalAlign: -2 }} />
            {current?.[1] || 'Dashboard'}
          </h2>
          <a href={`${BASE_PATH || '/'}`} style={{ color: 'var(--muted)', fontSize: 12 }}>
            <House style={{ verticalAlign: -1 }} /> View site
          </a>
        </div>

        {error && <div className="admin-error">{error}</div>}

        {section === 'dashboard' ? (
          <Dashboard data={data} />
        ) : (
          <ListSection
            section={section}
            data={data}
            editing={editing}
            setEditing={setEditing}
            onSave={save}
            onMutate={mutate}
          />
        )}
      </main>
    </div>
  );
}

/* ── Dashboard ── */
function Dashboard({ data }) {
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

/* ── List Section ── */
function ListSection({ section, data, editing, setEditing, onSave, onMutate }) {
  const rows = Array.isArray(data) ? data : [];
  const hasEditor = !!editors[section];

  return (
    <>
      {hasEditor && (
        <div className="admin-toolbar">
          {!editing && (
            <button
              className="button button-gold"
              onClick={() => setEditing({})}
            >
              <Plus weight="bold" /> Add {section.replaceAll('_', ' ')}
            </button>
          )}
        </div>
      )}

      {editing && (
        <Editor
          fields={editors[section]}
          value={editing}
          section={section}
          imageFields={imageFields[section]}
          slugFrom={section === 'articles' ? 'title_ne' : undefined}
          selectFields={{ events: { type: [['event', 'Event'], ['tour', 'Tour']] } }}
          onSubmit={onSave}
          onCancel={() => setEditing(null)}
        />
      )}

      <DataTable
        rows={rows}
        section={section}
        onEdit={setEditing}
        onMutate={onMutate}
      />
    </>
  );
}

/* ── File Upload ── */
function FileUpload({ value, onChange, accept = 'image/*' }) {
  const [drag, setDrag] = useState(false);
  const [preview, setPreview] = useState(value || '');
  const inputRef = useRef(null);

  async function upload(file) {
    const fd = new FormData();
    fd.append('file', file);
    fd.append('type', 'general');
    try {
      const res = await fetch(`${API}/upload.php`, {
        method: 'POST',
        credentials: 'same-origin',
        body: fd,
      });
      const d = await res.json();
      if (d.success) {
        setPreview(d.data.url);
        onChange(d.data.url);
      }
    } catch (e) {}
  }

  function handleDrop(e) {
    e.preventDefault();
    setDrag(false);
    const file = e.dataTransfer.files[0];
    if (file) upload(file);
  }

  function handleFile(e) {
    const file = e.target.files[0];
    if (file) upload(file);
  }

  return (
    <div className="file-upload-wrap">
      <div
        className={`file-zone${drag ? ' drag-over' : ''}`}
        onDragOver={e => {
          e.preventDefault();
          setDrag(true);
        }}
        onDragLeave={() => setDrag(false)}
        onDrop={handleDrop}
        onClick={() => inputRef.current?.click()}
      >
        {preview ? (
          <img src={preview} alt="preview" className="file-preview" />
        ) : (
          <span>
            <Upload weight="duotone" />
            Drop image here or click to upload
          </span>
        )}
      </div>
      <input
        ref={inputRef}
        type="file"
        accept={accept}
        style={{ display: 'none' }}
        onChange={handleFile}
      />
      {preview && (
        <button
          type="button"
          className="file-clear"
          onClick={() => {
            setPreview('');
            onChange('');
          }}
        >
          <X />
        </button>
      )}
    </div>
  );
}

/* ── Editor Form ── */
function Editor({ fields, value, section, imageFields, slugFrom, selectFields, onSubmit, onCancel }) {
  const [uploads, setUploads] = useState({});
  const slugRef = useRef(null);

  function handleTitleChange(e) {
    if (!slugRef.current || !slugFrom) return;
    const title = e.target.value;
    slugRef.current.value = title
      .toLowerCase()
      .replace(/[^\w\s\-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '') || 'post-' + Date.now();
  }

  function handleChange(name, val) {
    setUploads(p => ({ ...p, [name]: val }));
  }

  function slugLabel(name) {
    return slugFrom && name === slugFrom;
  }

  const sectionSelects = selectFields?.[section] || {};

  return (
    <form className="admin-editor" onSubmit={onSubmit}>
      <h3>{value?.id ? 'Edit record' : 'Add record'}</h3>
      <div>
        {fields.map(([name, label]) => {
          const opts = sectionSelects[name];
          return (
          <label
            key={name}
            className={imageFields?.includes(name) ? 'image-field' : ''}
          >
            {label}
            {imageFields?.includes(name) ? (
              <FileUpload
                value={value?.[name] || ''}
                onChange={v => handleChange(name, v)}
              />
            ) : name === 'slug' ? (
              <input
                ref={slugRef}
                name="slug"
                defaultValue={value?.slug ?? ''}
                placeholder="auto-generated"
              />
            ) : opts ? (
              <select name={name} defaultValue={value?.[name] ?? ''}>
                <option value="">— Select —</option>
                {opts.map(([val, lbl]) => (
                  <option key={val} value={val}>{lbl}</option>
                ))}
              </select>
            ) : (
              <input
                name={name}
                defaultValue={value?.[name] ?? ''}
                required={!imageFields?.includes(name)}
                onChange={slugLabel(name) ? handleTitleChange : undefined}
              />
            )}
          </label>
          );
        })}
      </div>
      {Object.entries(uploads).map(([k, v]) => (
        <input key={k} type="hidden" name={k} value={v} />
      ))}
      <button>{value?.id ? 'Update' : 'Save'}</button>
      {value?.id && (
        <button type="button" className="secondary" onClick={onCancel}>
          Cancel
        </button>
      )}
    </form>
  );
}

/* ── Data Table ── */
function DataTable({ rows, section, onEdit, onMutate }) {
  if (!rows?.length) {
    return <div className="admin-empty">No records yet.</div>;
  }

  const hidden = [
    'admin_notes', 'message', 'content_ne', 'content_en',
    'description_ne', 'description_en', 'password_hash',
  ];
  const keys = Object.keys(rows[0])
    .filter(k => !hidden.includes(k))
    .slice(0, 6);

  const statuses = {
    appointments: ['pending', 'confirmed', 'completed', 'cancelled'],
    pooja: ['pending', 'confirmed', 'completed', 'cancelled'],
    payments: ['pending', 'approved', 'rejected'],
  };

  return (
    <div className="admin-table-wrap">
      <table>
        <thead>
          <tr>
            {keys.map(k => (
              <th key={k}>{k.replaceAll('_', ' ')}</th>
            ))}
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {rows.map((r, i) => (
            <tr key={r.id || i}>
              {keys.map(k => (
                <td key={k}>
                  {typeof r[k] === 'string' && r[k].startsWith('http') ? (
                    <img src={r[k]} alt="" className="cell-thumb" />
                  ) : (
                    String(r[k] ?? '—')
                  )}
                </td>
              ))}
              <td className="admin-actions">
                {statuses[section] && (
                  <select
                    value={r.status}
                    onChange={e =>
                      onMutate('PATCH', { id: r.id, status: e.target.value })
                    }
                  >
                    {statuses[section].map(s => (
                      <option key={s}>{s}</option>
                    ))}
                  </select>
                )}

                {editors[section] && (
                  <>
                    <button onClick={() => onEdit(r)}>Edit</button>
                    <button
                      className="danger"
                      onClick={() =>
                        confirm('Delete this record?') &&
                        onMutate('DELETE', { id: r.id })
                      }
                    >
                      Delete
                    </button>
                  </>
                )}
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
