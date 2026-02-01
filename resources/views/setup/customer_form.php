<?php require_once __DIR__ . '/../partials/header.php'; ?>

<style>
    :root {
        --primary: #3b82f6;
        --primary-dark: #2563eb;
        --bg-color: #f8fafc;
        --card-bg: #ffffff;
        --text-main: #1e293b;
        --text-sub: #64748b;
        --border-color: #e2e8f0;
        --danger: #ef4444;
    }

    .form-builder-container {
        max-width: 1000px;
        margin: 20px auto;
        padding: 0 20px;
        font-family: 'Inter', sans-serif;
    }

    .builder-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        background: white;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .builder-header h2 {
        margin: 0;
        font-size: 1.5rem;
        color: var(--text-main);
    }

    .builder-header p {
        margin: 4px 0 0;
        color: var(--text-sub);
        font-size: 0.9rem;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        font-size: 0.9rem;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
    }

    .btn-outline {
        background: white;
        border: 1px solid var(--border-color);
        color: var(--text-main);
    }

    .btn-outline:hover {
        background: #f8fafc;
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    /* Sections */
    .sections-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .section-card {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .section-header {
        padding: 16px 20px;
        background: #f8fafc;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: grab;
    }

    .section-header:active {
        cursor: grabbing;
    }

    .section-title {
        flex: 1;
        font-weight: 700;
        color: var(--text-main);
        font-size: 1.1rem;
        background: transparent;
        border: none;
        padding: 4px 8px;
        border-radius: 4px;
    }

    .section-title:focus {
        background: white;
        outline: 1px solid var(--primary);
    }

    /* Fields */
    .fields-list {
        padding: 16px;
        min-height: 50px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 12px;
    }

    .field-item {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: grab;
        transition: all 0.2s;
    }

    .field-item:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .field-item.dragging {
        opacity: 0.5;
        border: 2px dashed var(--primary);
    }

    .field-info {
        flex: 1;
    }

    .field-label {
        font-weight: 600;
        font-size: 0.95rem;
        display: block;
    }

    .field-meta {
        font-size: 0.75rem;
        color: var(--text-sub);
    }

    .field-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Toggle Switch (Reuse from column_preview) */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 20px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 20px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: var(--primary);
    }

    input:checked+.slider:before {
        transform: translateX(20px);
    }

    /* Drag Handles */
    .drag-handle {
        color: #cbd5e1;
    }

    .section-header:hover .drag-handle,
    .field-item:hover .drag-handle {
        color: #94a3b8;
    }

    /* Modals */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        padding: 24px;
        border-radius: 12px;
        width: 100%;
        max-width: 450px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        margin-bottom: 20px;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.25rem;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 0.95rem;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
    }
</style>

<div class="form-builder-container">
    <div class="builder-header">
        <div>
            <h2>Customer Form Setup</h2>
            <p>Customize fields, sections, and order. Drag and drop to rearrange.</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <button class="btn btn-outline" onclick="addSection()">+ Add Section</button>
            <button class="btn btn-primary" onclick="saveConfiguration()">Save Changes</button>
        </div>
    </div>

    <div class="sections-container" id="sectionsList">
        <?php foreach ($sections as $section): ?>
            <div class="section-card" data-id="<?= $section['id'] ?>">
                <div class="section-header">
                    <div class="drag-handle"><i class="fas fa-grip-vertical"></i></div>
                    <input type="text" class="section-title" value="<?= htmlspecialchars($section['name']) ?>"
                        onchange="markDirty()">
                    <div style="display: flex; gap: 8px;">
                        <button class="btn btn-outline" style="padding: 6px 12px;"
                            onclick="addField(<?= $section['id'] ?>)">+ Add Field</button>
                        <button class="btn btn-outline" style="padding: 6px 12px; color: var(--danger);"
                            onclick="deleteSection(this)"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                <div class="fields-list" id="section-<?= $section['id'] ?>">
                    <?php foreach ($section['fields'] as $field): ?>
                        <div class="field-item" data-id="<?= $field['id'] ?>" data-key="<?= $field['field_key'] ?>"
                            data-standard="<?= $field['is_standard'] ?>"
                            data-placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>">
                            <div class="drag-handle"><i class="fas fa-grip-vertical"></i></div>
                            <div class="field-info">
                                <span class="field-label">
                                    <?= htmlspecialchars($field['label']) ?>
                                    <?= $field['required'] ? ' *' : '' ?>
                                </span>
                                <span class="field-meta">
                                    <?= strtoupper($field['type']) ?> (
                                    <?= $field['field_key'] ?>)
                                </span>
                            </div>
                            <div class="field-actions">
                                <label class="toggle-switch" title="Toggle Visibility">
                                    <input type="checkbox" class="visibility-toggle" <?= $field['is_visible'] ? 'checked' : '' ?>
                                        onchange="markDirty()">
                                    <span class="slider"></span>
                                </label>
                                <button class="btn btn-outline" style="padding: 6px 10px;" onclick="editField(this)"><i
                                        class="fas fa-edit"></i></button>
                                <?php if (!$field['is_standard']): ?>
                                    <button class="btn btn-outline" style="padding: 6px 10px; color: var(--danger);"
                                        onclick="deleteField(this)"><i class="fas fa-trash"></i></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add/Edit Field Modal -->
<div class="modal" id="fieldModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add Field</h3>
        </div>
        <form id="fieldForm">
            <input type="hidden" id="editFieldId">
            <input type="hidden" id="currentSectionId">
            <div class="form-group">
                <label>Field Label</label>
                <input type="text" id="fieldLabel" placeholder="e.g. Passport Number" required>
            </div>
            <div class="form-group">
                <label>Field Type</label>
                <select id="fieldType">
                    <option value="text">Text Input</option>
                    <option value="textarea">Text Area</option>
                    <option value="number">Number</option>
                    <option value="date">Date Picker</option>
                    <option value="dropdown">Dropdown</option>
                    <option value="email">Email</option>
                    <option value="tel">Phone</option>
                </select>
            </div>
            <div class="form-group">
                <label>Placeholder Text</label>
                <input type="text" id="fieldPlaceholder" placeholder="e.g. 1.1.1.1 or 2000">
            </div>
            <div class="form-group" id="optionsGroup" style="display: none;">
                <label>Options (Comma separated)</label>
                <input type="text" id="fieldOptions" placeholder="Option 1, Option 2, Option 3">
            </div>
            <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="fieldRequired" style="width: auto;">
                <label style="margin: 0;">Required Field</label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Apply Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Load SortableJS for drag and drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    let isDirty = false;
    const deletedSections = [];
    const deletedFields = [];

    function markDirty() { isDirty = true; }

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Section Sorting
        const sectionsList = document.getElementById('sectionsList');
        new Sortable(sectionsList, {
            animation: 150,
            handle: '.section-header',
            onEnd: markDirty
        });

        // Initialize Field Sorting for each section
        document.querySelectorAll('.fields-list').forEach(list => {
            initFieldSortable(list);
        });
    });

    function initFieldSortable(el) {
        new Sortable(el, {
            group: 'fields',
            animation: 150,
            handle: '.drag-handle',
            onEnd: markDirty
        });
    }

    function addSection() {
        const name = prompt("Enter section name:");
        if (!name) return;

        const container = document.getElementById('sectionsList');
        const id = 'new_' + Date.now();
        const html = `
            <div class="section-card" data-id="${id}">
                <div class="section-header">
                    <div class="drag-handle"><i class="fas fa-grip-vertical"></i></div>
                    <input type="text" class="section-title" value="${name}" onchange="markDirty()">
                    <div style="display: flex; gap: 8px;">
                        <button class="btn btn-outline" style="padding: 6px 12px;" onclick="addField('${id}')">+ Add Field</button>
                        <button class="btn btn-outline" style="padding: 6px 12px; color: var(--danger);" onclick="deleteSection(this)"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                <div class="fields-list" id="section-${id}"></div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        initFieldSortable(document.getElementById(`section-${id}`));
        markDirty();
    }

    function deleteSection(btn) {
        if (!confirm("Are you sure? All fields in this section will also be deleted.")) return;
        const card = btn.closest('.section-card');
        const id = card.dataset.id;
        if (!id.startsWith('new_')) deletedSections.push(id);
        card.remove();
        markDirty();
    }

    // Modal logic
    const modal = document.getElementById('fieldModal');
    const fieldTypeSelect = document.getElementById('fieldType');
    fieldTypeSelect.onchange = () => {
        document.getElementById('optionsGroup').style.display = (fieldTypeSelect.value === 'dropdown') ? 'block' : 'none';
    };

    function addField(sectionId) {
        document.getElementById('editFieldId').value = '';
        document.getElementById('currentSectionId').value = sectionId;
        document.getElementById('fieldForm').reset();
        document.getElementById('optionsGroup').style.display = 'none';
        document.getElementById('modalTitle').innerText = 'Add Field';
        modal.style.display = 'flex';
    }

    function editField(btn) {
        const item = btn.closest('.field-item');
        const id = item.dataset.id;
        const label = item.querySelector('.field-label').innerText.replace(' *', '');
        const meta = item.querySelector('.field-meta').innerText;
        const type = meta.split(' (')[0].toLowerCase();
        const required = item.querySelector('.field-label').innerText.includes(' *');

        document.getElementById('editFieldId').value = id;
        document.getElementById('fieldLabel').value = label;
        document.getElementById('fieldType').value = (type === 'select') ? 'dropdown' : type;
        document.getElementById('fieldPlaceholder').value = item.dataset.placeholder || '';
        document.getElementById('fieldRequired').checked = required;

        // Options handling (if it was dropdown)
        // For simplicity, we could fetch full details via AJAX or store in data-attrs

        document.getElementById('optionsGroup').style.display = (type === 'select' || type === 'dropdown') ? 'block' : 'none';
        document.getElementById('modalTitle').innerText = 'Edit Field';
        modal.style.display = 'flex';
    }

    function closeModal() { modal.style.display = 'none'; }

    document.getElementById('fieldForm').onsubmit = (e) => {
        e.preventDefault();
        const id = document.getElementById('editFieldId').value;
        const sectionId = document.getElementById('currentSectionId').value;
        const label = document.getElementById('fieldLabel').value;
        const type = document.getElementById('fieldType').value;
        const placeholder = document.getElementById('fieldPlaceholder').value;
        const required = document.getElementById('fieldRequired').checked;
        const options = document.getElementById('fieldOptions').value;

        if (id) {
            // Update existing
            const item = document.querySelector(`.field-item[data-id="${id}"]`);
            item.querySelector('.field-label').innerHTML = label + (required ? ' *' : '');
            item.querySelector('.field-meta').innerText = `${type.toUpperCase()} (${item.dataset.key})`;
            item.dataset.placeholder = placeholder;
        } else {
            // New field
            const list = document.getElementById(`section-${sectionId}`);
            const newId = 'new_f_' + Date.now();
            const html = `
                <div class="field-item" data-id="${newId}" data-key="" data-standard="0" data-placeholder="${placeholder}">
                    <div class="drag-handle"><i class="fas fa-grip-vertical"></i></div>
                    <div class="field-info">
                        <span class="field-label">${label}${required ? ' *' : ''}</span>
                        <span class="field-meta">${type.toUpperCase()} (new_field)</span>
                    </div>
                    <div class="field-actions">
                        <label class="toggle-switch">
                            <input type="checkbox" class="visibility-toggle" checked onchange="markDirty()">
                            <span class="slider"></span>
                        </label>
                        <button class="btn btn-outline" style="padding: 6px 10px;" onclick="editField(this)"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-outline" style="padding: 6px 10px; color: var(--danger);" onclick="deleteField(this)"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;
            list.insertAdjacentHTML('beforeend', html);
        }
        closeModal();
        markDirty();
    };

    function deleteField(btn) {
        if (!confirm("Are you sure?")) return;
        const item = btn.closest('.field-item');
        const id = item.dataset.id;
        if (!id.startsWith('new_')) deletedFields.push(id);
        item.remove();
        markDirty();
    }

    async function saveConfiguration() {
        const sections = [];
        document.querySelectorAll('.section-card').forEach((card, sIndex) => {
            const fields = [];
            card.querySelectorAll('.field-item').forEach((item, fIndex) => {
                const label = item.querySelector('.field-label').innerText.replace(' *', '');
                const required = item.querySelector('.field-label').innerText.includes(' *');
                const meta = item.querySelector('.field-meta').innerText;
                const type = meta.split(' (')[0].toLowerCase();

                fields.push({
                    id: item.dataset.id.startsWith('new_') ? 0 : item.dataset.id,
                    field_key: item.dataset.key,
                    label: label,
                    placeholder: item.dataset.placeholder || '',
                    type: (type === 'dropdown') ? 'select' : type,
                    required: required,
                    is_visible: item.querySelector('.visibility-toggle').checked,
                    order_index: fIndex
                });
            });

            sections.push({
                id: card.dataset.id.startsWith('new_') ? 0 : card.dataset.id,
                name: card.querySelector('.section-title').value,
                order_index: sIndex,
                fields: fields
            });
        });

        const data = {
            sections: sections,
            deleted_sections: deletedSections,
            deleted_fields: deletedFields
        };

        try {
            const response = await fetch('<?= url("setup/saveCustomerForm") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.status === 'success') {
                alert('Configuration saved successfully!');
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (err) {
            console.error(err);
            alert('Failed to save configuration');
        }
    }
</script>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>