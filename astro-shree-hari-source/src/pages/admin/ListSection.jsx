import { Plus } from '@phosphor-icons/react';
import { editors, imageFields } from './config';
import { Editor } from './Editor';
import { DataTable } from './DataTable';

export function ListSection({ section, data, editing, setEditing, onSave, onMutate }) {
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
