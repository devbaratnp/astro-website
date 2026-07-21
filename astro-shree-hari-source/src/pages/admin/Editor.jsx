import { useState, useRef } from 'react';
import { FileUpload } from './FileUpload';

export function Editor({ fields, value, section, imageFields, slugFrom, selectFields, onSubmit, onCancel }) {
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
