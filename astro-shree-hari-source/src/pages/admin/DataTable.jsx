import { editors } from './config';

export function DataTable({ rows, section, onEdit, onMutate }) {
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
