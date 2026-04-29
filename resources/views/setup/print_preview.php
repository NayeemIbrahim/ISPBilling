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
    }

    .dashboard-container {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: var(--text-main);
    }

    /* Two-column layout: left controls (fixed) + right preview (fluid) */
    .layout-container {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 24px;
        align-items: start;
        margin-top: 24px;
    }

    @media(max-width: 1100px) {
        .layout-container { grid-template-columns: 1fr; }
    }

    /* ---- Left Panel ---- */
    .sidebar-panel {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        border: 1px solid var(--border-color);
        position: sticky;
        top: 20px;
    }

    .panel-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0 0 20px 0;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .panel-title i { color: var(--primary); }

    .section-label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        margin: 18px 0 10px 0;
    }

    .radio-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .radio-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
        border: 1px solid var(--border-color);
        transition: all 0.15s;
        font-size: 0.9rem;
        color: var(--text-sub);
    }

    .radio-option:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .radio-option input[type="radio"] { accent-color: var(--primary); width: 15px; height: 15px; cursor: pointer; }
    .radio-option.selected { background: #eff6ff; border-color: var(--primary); color: var(--primary); font-weight: 600; }

    .divider { border: none; border-top: 1px solid var(--border-color); margin: 20px 0; }

    .form-label { font-size: 0.875rem; font-weight: 600; color: var(--text-main); margin-bottom: 6px; display: block; }
    .form-control { width: 100%; border: 1px solid var(--border-color); border-radius: 8px; padding: 9px 12px; font-size: 0.9rem; color: var(--text-main); resize: none; font-family: inherit; transition: border-color 0.2s; box-sizing: border-box; }
    .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
    .char-hint { font-size: 0.75rem; color: #94a3b8; margin-top: 4px; }

    .btn-primary { background: var(--primary); color: white; border: none; padding: 9px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.9rem; transition: background 0.2s; box-shadow: 0 4px 6px -1px rgba(59,130,246,0.3); }
    .btn-primary:hover { background: var(--primary-dark); }
    .btn-info { background: #06b6d4; color: white; border: none; padding: 9px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.9rem; transition: background 0.2s; }
    .btn-info:hover { background: #0891b2; }
    .btn-row { display: flex; gap: 10px; margin-top: 16px; flex-wrap: wrap; }

    /* File upload row */
    .file-upload-row { display: flex; align-items: center; gap: 10px; }
    .file-upload-row input[type="file"] { flex: 1; font-size: 0.85rem; border: 1px solid var(--border-color); border-radius: 8px; padding: 7px 10px; }

    /* Alert */
    .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; font-size: 0.9rem; font-weight: 500; }
    .alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .alert-error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

    /* ---- Right Panel (Preview) ---- */
    .card-main {
        background: var(--card-bg);
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }

    .preview-header {
        padding: 20px 28px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .preview-header h2 { margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--text-main); }
    .preview-header p { margin: 4px 0 0 0; font-size: 0.85rem; color: var(--text-sub); }

    .preview-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        background: #eff6ff;
        color: var(--primary);
        border: 1px solid #bfdbfe;
    }

    .preview-canvas {
        background: #eef2f6;
        padding: 28px;
        min-height: 500px;
        overflow-y: auto;
        max-height: calc(100vh - 220px);
    }

    /* Paper look */
    #paper-preview {
        background: white;
        width: 210mm;
        min-height: 200mm;
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        padding: 20mm 18mm;
        margin: 0 auto;
        font-family: 'Inter', Arial, sans-serif;
        border-radius: 2px;
    }

    /* Invoice Template Styles */
    .invoice-bill { border-bottom: 1px dashed #bbb; padding-bottom: 20px; margin-bottom: 20px; font-size: 11px; color: #1e293b; }
    .invoice-bill:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }

    .inv-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
    .inv-qr { width: 56px; height: 56px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: #f8fafc; }
    .inv-qr svg { width: 40px; height: 40px; opacity: 0.4; }
    .inv-title { font-weight: 700; font-size: 13px; text-align: center; flex: 1; padding: 0 12px; line-height: 1.3; color: #1e293b; }
    .inv-company { text-align: right; line-height: 1.5; min-width: 160px; }
    .inv-company-name { font-weight: 700; font-size: 13px; color: #1e293b; }
    .inv-company-sub { font-size: 10px; color: #64748b; }

    .inv-info-row { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 10.5px; }
    .inv-customer-name { font-weight: 700; font-size: 12px; margin: 6px 0 3px; }
    .inv-customer-address { font-size: 10px; color: #475569; line-height: 1.5; }

    .inv-body { display: grid; grid-template-columns: 1fr 200px; gap: 16px; margin: 12px 0; }

    .inv-totals table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
    .inv-totals td { padding: 2.5px 5px; }
    .inv-totals td:last-child { text-align: right; font-weight: 600; }
    .inv-totals .row-sep td { border-top: 1px solid #333; border-bottom: 1px solid #333; font-weight: 700; }

    .inv-box { border: 1px solid #334155; padding: 8px 12px; font-size: 10.5px; line-height: 1.8; font-weight: 500; margin-top: 12px; }
    .inv-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 14px; }
    .inv-note { font-size: 10px; color: #475569; }
    .inv-signature { text-align: center; min-width: 120px; font-size: 10px; font-weight: 600; }
    .inv-signature img { max-height: 32px; display: block; margin: 0 auto 4px; }
    .inv-signature .sig-line { border-top: 1px solid #334155; padding-top: 4px; }
</style>

<main class="dashboard-container">

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="layout-container">

        <!-- ===== LEFT: Control Panel ===== -->
        <div class="sidebar-panel">
            <h3 class="panel-title"><i class="fas fa-print"></i> Printing Page Setup</h3>

            <form method="POST" action="<?= url('setup/print-preview') ?>">
                <input type="hidden" name="action" value="save_settings">
                <input type="hidden" name="header_style" id="actual_header_style" value="<?= $settings['header_style'] ?? 'with_header' ?>">
                <input type="hidden" name="layout" id="actual_layout" value="<?= $settings['layout'] ?? '1' ?>">

                <!-- With Header -->
                <div class="section-label">With Header</div>
                <div class="radio-group">
                    <?php foreach (['1'=>'1 Bill per Page','2'=>'2 Bill per Page','3'=>'3 Bill per Page','pad'=>'Pad'] as $val=>$lbl): ?>
                        <label class="radio-option <?= ($settings['header_style']=='with_header' && $settings['layout']==$val) ? 'selected' : '' ?>">
                            <input type="radio" name="layout_with_header" value="<?= $val ?>"
                                <?= ($settings['header_style']=='with_header' && $settings['layout']==$val) ? 'checked' : '' ?>
                                onchange="updatePreview('with_header','<?= $val ?>')">
                            <?= $lbl ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <!-- Without Header -->
                <div class="section-label">Without Header</div>
                <div class="radio-group">
                    <?php foreach (['1'=>'1 Bill per Page','2'=>'2 Bill per Page','3'=>'3 Bill per Page','pad'=>'Pad'] as $val=>$lbl): ?>
                        <label class="radio-option <?= ($settings['header_style']=='without_header' && $settings['layout']==$val) ? 'selected' : '' ?>">
                            <input type="radio" name="layout_without_header" value="<?= $val ?>"
                                <?= ($settings['header_style']=='without_header' && $settings['layout']==$val) ? 'checked' : '' ?>
                                onchange="updatePreview('without_header','<?= $val ?>')">
                            <?= $lbl ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <hr class="divider">

                <!-- Custom Text -->
                <label class="form-label" for="receipt_text">Customise text for money receipt</label>
                <textarea id="receipt_text" name="receipt_text" class="form-control"
                    maxlength="50" rows="2"
                    oninput="updateCustomText(this.value)"><?= htmlspecialchars($settings['receipt_text'] ?? '') ?></textarea>
                <div class="char-hint">Max 50 characters allowed</div>

                <div class="btn-row">
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Settings</button>
                    <button type="button" class="btn-info"><i class="fas fa-file-invoice"></i> Invoice Setup</button>
                </div>
            </form>

            <hr class="divider">

            <!-- Signature Upload -->
            <h3 class="panel-title" style="margin-bottom:14px;"><i class="fas fa-signature"></i> Signature</h3>
            <form method="POST" action="<?= url('setup/print-preview') ?>" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_signature">
                <div class="file-upload-row">
                    <input type="file" name="signature" accept="image/*" required>
                    <button type="submit" class="btn-primary">Upload</button>
                </div>
                <?php if (!empty($settings['signature_path'])): ?>
                    <div style="margin-top:10px; font-size:0.8rem; color:#15803d;">
                        <i class="fas fa-check-circle"></i> Signature uploaded
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- ===== RIGHT: Live Preview ===== -->
        <div class="card-main">
            <div class="preview-header">
                <div>
                    <h2>Invoice Preview</h2>
                    <p>Updates live as you change settings</p>
                </div>
                <span class="preview-badge" id="layout-badge">1 Bill / With Header</span>
            </div>
            <div class="preview-canvas">
                <div id="paper-preview">
                    <div id="preview-content"></div>
                </div>
            </div>
        </div>

    </div><!-- .layout-container -->
</main>

<script>
const signaturePath = "<?= isset($settings['signature_path']) && $settings['signature_path'] ? url($settings['signature_path']) : '' ?>";

// Real customer data from DB, or fallback demo
const cust = <?= json_encode($previewCustomer ?: [
    'id'              => '1',
    'prefix_id'       => 'HK',
    'full_name'       => 'Demo Customer',
    'username'        => 'demo_user',
    'mobile_no'       => '01XXXXXXXXX',
    'area'            => 'Demo Area',
    'thana'           => 'Demo Thana',
    'district'        => 'Demo District',
    'house_no'        => '',
    'connection_date' => date('Y-m-d'),
    'monthly_rent'    => 1000,
    'due_amount'      => 0,
]) ?>;

// ---------- helpers ----------
function formatDate(d) {
    if (!d) return 'N/A';
    const dt = new Date(d);
    return dt.toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'});
}
const today       = formatDate(new Date());
const billingMonth = new Date().toLocaleDateString('en-GB', {month:'short', year:'numeric'});

const custId      = (cust.prefix_id || '') + cust.id;
const username    = cust.username    || 'N/A';
const fullName    = cust.full_name   || 'N/A';
const connDate    = formatDate(cust.connection_date);

const parts = [cust.house_no, cust.area, cust.thana, cust.district, cust.mobile_no].filter(Boolean);
const address = parts.join(', ') || 'N/A';

const rent    = parseFloat(cust.monthly_rent || 0).toFixed(2);
const prevDue = parseFloat(cust.due_amount   || 0).toFixed(2);
const total   = (parseFloat(rent) + parseFloat(prevDue)).toFixed(2);

// QR placeholder SVG
const qrSvg = `<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
  <rect x="10" y="10" width="30" height="30" rx="2" fill="none" stroke="#94a3b8" stroke-width="6"/>
  <rect x="60" y="10" width="30" height="30" rx="2" fill="none" stroke="#94a3b8" stroke-width="6"/>
  <rect x="10" y="60" width="30" height="30" rx="2" fill="none" stroke="#94a3b8" stroke-width="6"/>
  <rect x="22" y="22" width="6" height="6" fill="#94a3b8"/>
  <rect x="72" y="22" width="6" height="6" fill="#94a3b8"/>
  <rect x="22" y="72" width="6" height="6" fill="#94a3b8"/>
  <rect x="55" y="55" width="6" height="6" fill="#94a3b8"/>
  <rect x="65" y="55" width="6" height="6" fill="#94a3b8"/>
  <rect x="75" y="55" width="6" height="6" fill="#94a3b8"/>
  <rect x="55" y="65" width="6" height="6" fill="#94a3b8"/>
  <rect x="75" y="65" width="6" height="6" fill="#94a3b8"/>
  <rect x="55" y="75" width="6" height="6" fill="#94a3b8"/>
  <rect x="65" y="75" width="6" height="6" fill="#94a3b8"/>
  <rect x="75" y="75" width="6" height="6" fill="#94a3b8"/>
</svg>`;

// ---------- build one bill ----------
function buildBill(title, showHeader, customText, isLast) {
    const companyHtml = showHeader
        ? `<div class="inv-company">
                <div class="inv-company-name">Hello Khulna</div>
                <div class="inv-company-sub">Your trusted ISP partner</div>
           </div>`
        : `<div class="inv-company" style="visibility:hidden;">
                <div class="inv-company-name">Hello Khulna</div>
                <div class="inv-company-sub">ISP partner</div>
           </div>`;

    const sigBlock = isLast
        ? (signaturePath
            ? `<img src="${signaturePath}" alt="sig"><div class="sig-line">Authorized Signature</div>`
            : `<div style="height:28px;"></div><div class="sig-line">Authorized Signature</div>`)
        : '';

    return `
    <div class="invoice-bill">
        <div class="inv-header">
            <div class="inv-qr">${qrSvg}</div>
            <div class="inv-title">${title}</div>
            ${companyHtml}
        </div>

        <div class="inv-info-row">
            <span>${today}</span>
            <span>ID : <strong>${custId}</strong> &nbsp; Username: <strong>${username}</strong></span>
        </div>

        <div class="inv-customer-name">${fullName}</div>
        <div class="inv-customer-address">${address}</div>
        <div style="font-size:10px; margin-top:3px;">Connection Date : ${connDate}</div>

        <div class="inv-body">
            <div><!-- left spacer --></div>
            <div class="inv-totals">
                <table>
                    <tr><td>Monthly Rent :</td><td>${rent}</td></tr>
                    <tr><td>Additional :</td><td>0.00</td></tr>
                    <tr><td>Discount :</td><td>0.00</td></tr>
                    <tr><td>Advance :</td><td>0.00</td></tr>
                    <tr class="row-sep"><td>SUM :</td><td>${rent}</td></tr>
                    <tr><td>Vat (0%) :</td><td>0.00</td></tr>
                    <tr class="row-sep"><td>SUM with vat :</td><td>${rent}</td></tr>
                    <tr><td>Previous DUE :</td><td>${prevDue}</td></tr>
                    <tr class="row-sep"><td>Total :</td><td>${total}</td></tr>
                </table>
            </div>
        </div>

        <div class="inv-box">
            <div>Billing Month : ${billingMonth}</div>
            <div>Due Month's List :</div>
        </div>

        <div class="inv-footer">
            <div class="inv-note">Note : <span class="inv-custom-text-content">${customText}</span></div>
            <div class="inv-signature">${sigBlock}</div>
        </div>
    </div>`;
}

// ---------- render preview ----------
function renderInvoice(headerStyle, layout) {
    const customText = document.getElementById('receipt_text').value;
    const showHeader = (headerStyle === 'with_header');
    const numBills   = layout === '2' ? 2 : layout === '3' ? 3 : 1;
    const titles     = ['Office Document', 'Money Receipt', 'Invoice'];
    let html = '';
    for (let i = 0; i < numBills; i++) {
        html += buildBill(titles[i] || 'Invoice', showHeader, customText, i === numBills - 1);
    }
    document.getElementById('preview-content').innerHTML = html;

    // Update badge
    const layoutLabel = layout === 'pad' ? 'Pad' : layout + ' Bill';
    const headerLabel = showHeader ? 'With Header' : 'Without Header';
    document.getElementById('layout-badge').textContent = layoutLabel + ' / ' + headerLabel;
}

// ---------- radio change ----------
function updatePreview(headerStyle, layout) {
    document.getElementById('actual_header_style').value = headerStyle;
    document.getElementById('actual_layout').value = layout;

    // Style active radio options
    document.querySelectorAll('.radio-option').forEach(el => el.classList.remove('selected'));
    const changed = event.target.closest('.radio-option');
    if (changed) changed.classList.add('selected');

    // Uncheck opposite group
    const opposite = headerStyle === 'with_header' ? 'layout_without_header' : 'layout_with_header';
    document.querySelectorAll(`input[name="${opposite}"]`).forEach(r => {
        r.checked = false;
        r.closest('.radio-option')?.classList.remove('selected');
    });

    renderInvoice(headerStyle, layout);
}

function updateCustomText(text) {
    document.querySelectorAll('.inv-custom-text-content').forEach(el => el.textContent = text);
}

// ---------- initial render ----------
document.addEventListener('DOMContentLoaded', () => {
    renderInvoice(
        '<?= $settings['header_style'] ?? 'with_header' ?>',
        '<?= $settings['layout'] ?? '1' ?>'
    );
    // Seed custom text
    updateCustomText(document.getElementById('receipt_text').value);
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
