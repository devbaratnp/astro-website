import { useState, useRef } from 'react';
import { Upload, X } from '@phosphor-icons/react';
import { API_BASE } from '../../config';

const API = API_BASE;

export function FileUpload({ value, onChange, accept = 'image/*' }) {
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
