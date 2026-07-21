import { useEffect, useState } from 'react';
import { SignOut, List, House, ChartBar } from '@phosphor-icons/react';
import { BASE_PATH } from '../config';
import { api } from './admin/api';
import { sections } from './admin/config';
import { Dashboard } from './admin/Dashboard';
import { ListSection } from './admin/ListSection';
import '../admin.css';

export function Admin() {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [section, setSection] = useState('dashboard');
  const [data, setData] = useState(null);
  const [error, setError] = useState('');
  const [refresh, setRefresh] = useState(0);
  const [editing, setEditing] = useState(null);
  const [credentials, setCredentials] = useState({ username: '', password: '' });
  const [sidebarOpen, setSidebarOpen] = useState(true);

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
          {sections.map(([key, label, NavIcon]) => (
            <button
              key={key}
              className={section === key ? 'active' : ''}
              onClick={() => setSection(key)}
              title={sidebarOpen ? '' : label}
            >
              <NavIcon />
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
