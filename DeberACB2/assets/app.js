/**
 * app.js — Interacciones del cliente para GestorArchivos
 * - Previsualización de nombre al seleccionar archivo
 * - Drag & Drop en el área de subida
 * - Modal de confirmación de eliminación
 * - Auto-ocultar alertas
 */

'use strict';

/* ── DROP AREA ── */
const dropArea   = document.getElementById('dropArea');
const fileInput  = document.getElementById('archivo');
const dropNombre = document.getElementById('dropNombre');

if (fileInput) {
    fileInput.addEventListener('change', () => {
        const nombre = fileInput.files[0]?.name ?? '';
        dropNombre.textContent = nombre ? `✔ ${nombre}` : '';
    });
}

if (dropArea) {
    ['dragenter', 'dragover'].forEach(ev => {
        dropArea.addEventListener(ev, e => {
            e.preventDefault();
            dropArea.classList.add('dragover');
        });
    });

    ['dragleave', 'drop'].forEach(ev => {
        dropArea.addEventListener(ev, () => dropArea.classList.remove('dragover'));
    });

    dropArea.addEventListener('drop', e => {
        e.preventDefault();
        const archivo = e.dataTransfer?.files[0];
        if (archivo && fileInput) {
            const dt = new DataTransfer();
            dt.items.add(archivo);
            fileInput.files = dt.files;
            dropNombre.textContent = `✔ ${archivo.name}`;
        }
    });
}

/* ── MODAL ── */
const modal             = document.getElementById('modal');
const modalDesc         = document.getElementById('modal-desc');
const inputNombreElim   = document.getElementById('inputNombreEliminar');

function confirmarEliminar(nombreInterno, nombreOriginal) {
    if (!modal) return;
    modalDesc.textContent =
        `¿Estás seguro de que deseas eliminar "${nombreOriginal}"? Esta acción no se puede deshacer.`;
    inputNombreElim.value = nombreInterno;
    modal.hidden = false;
    modal.querySelector('.btn-peligro').focus();
}

function cerrarModal() {
    if (modal) modal.hidden = true;
}

// Cerrar con Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && modal && !modal.hidden) cerrarModal();
});

// Cerrar al clic en el overlay (fuera de la caja)
modal?.addEventListener('click', e => {
    if (e.target === modal) cerrarModal();
});

// Exponer para onclick inline
window.confirmarEliminar = confirmarEliminar;
window.cerrarModal       = cerrarModal;

/* ── AUTO-OCULTAR ALERTA ── */
const alerta = document.querySelector('.alerta');
if (alerta) {
    setTimeout(() => {
        alerta.style.transition = 'opacity .5s';
        alerta.style.opacity = '0';
        setTimeout(() => alerta.remove(), 500);
    }, 5000);
}
