<?php require_once __DIR__ . '/../partials/header.php'; ?>

<main class="dashboard-container">
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px;">
            <div>
                <h2 style="font-size: 1.5rem; color: #1e293b; font-weight: 700; margin: 0;">
                    <i class="fas fa-wallet" style="color: var(--accent); margin-right: 8px;"></i> MFS Payment Settings Setup
                </h2>
                <p style="font-size: 0.875rem; color: #64748b; margin: 4px 0 0 0;">
                    Configure bKash, Nagad, Rocket numbers and manage automated customer payments.
                </p>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $messageType ?>"
                 style="margin: 20px 0; padding: 12px 16px; border-radius: 8px; font-weight: 500; font-size: 0.95rem; display: flex; align-items: center; gap: 10px; background-color: <?= $messageType === 'success' ? '#d1fae5' : '#fee2e2' ?>; color: <?= $messageType === 'success' ? '#065f46' : '#991b1b' ?>; border: 1px solid <?= $messageType === 'success' ? '#a7f3d0' : '#fecaca' ?>;">
                <i class="fas <?= $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Beautiful Modern Tabs Navigation -->
        <div class="payment-tabs-nav" style="display: flex; gap: 8px; border-bottom: 2px solid #e2e8f0; margin-top: 20px; padding-bottom: 2px;">
            <button class="tab-btn active" onclick="switchTab('gateways')" id="tab-gateways-btn">
                <i class="fas fa-cogs"></i> Configure Gateways
            </button>
            <button class="tab-btn" onclick="switchTab('webhook')" id="tab-webhook-btn">
                <i class="fas fa-code"></i> Developer API & Webhook
            </button>
            <button class="tab-btn" onclick="switchTab('simulator')" id="tab-simulator-btn">
                <i class="fas fa-flask"></i> Payment Simulator <span class="badge-pulse">Sandbox</span>
            </button>
            <button class="tab-btn" onclick="switchTab('logs')" id="tab-logs-btn">
                <i class="fas fa-list-alt"></i> Auto-Payment Logs
            </button>
        </div>

        <div class="payment-tabs-content" style="margin-top: 24px;">
            <!-- TAB 1: CONFIGURE GATEWAYS -->
            <div id="tab-gateways" class="tab-pane active">
                <form action="<?= url('setup/payment-settings') ?>" method="POST" id="gatewaySettingsForm">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 30px;">
                        
                        <!-- bKash Card -->
                        <div class="gateway-card bkash-theme">
                            <div class="card-bg-glow"></div>
                            <div class="gateway-card-header">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span class="gateway-logo-wrapper">bK</span>
                                    <div>
                                        <h3 class="gateway-title">bKash Payment</h3>
                                        <span class="gateway-type">Mobile Financial Service</span>
                                    </div>
                                </div>
                                <label class="switch-toggle">
                                    <input type="checkbox" name="bkash_status" value="1" <?= ($settings['bkash']['status'] ?? 0) ? 'checked' : '' ?>>
                                    <span class="slider-round"></span>
                                </label>
                            </div>
                            <div class="gateway-card-body">
                                <div class="form-group">
                                    <label class="form-label"><i class="fas fa-phone-alt"></i> Receive bKash Number</label>
                                    <input type="text" name="bkash_number" class="form-input" placeholder="e.g. 017XXXXXXXX" value="<?= htmlspecialchars($settings['bkash']['receive_number'] ?? '') ?>" required>
                                </div>
                                <div class="gateway-info-box">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Supports Merchant / Personal Accounts. Matches client IDs or mobile sender.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Nagad Card -->
                        <div class="gateway-card nagad-theme">
                            <div class="card-bg-glow"></div>
                            <div class="gateway-card-header">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span class="gateway-logo-wrapper">Na</span>
                                    <div>
                                        <h3 class="gateway-title">Nagad Payment</h3>
                                        <span class="gateway-type">Mobile Financial Service</span>
                                    </div>
                                </div>
                                <label class="switch-toggle">
                                    <input type="checkbox" name="nagad_status" value="1" <?= ($settings['nagad']['status'] ?? 0) ? 'checked' : '' ?>>
                                    <span class="slider-round"></span>
                                </label>
                            </div>
                            <div class="gateway-card-body">
                                <div class="form-group">
                                    <label class="form-label"><i class="fas fa-phone-alt"></i> Receive Nagad Number</label>
                                    <input type="text" name="nagad_number" class="form-input" placeholder="e.g. 018XXXXXXXX" value="<?= htmlspecialchars($settings['nagad']['receive_number'] ?? '') ?>" required>
                                </div>
                                <div class="gateway-info-box">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Instant checkout verification support. Automatic bill collection on receipt.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Rocket Card -->
                        <div class="gateway-card rocket-theme">
                            <div class="card-bg-glow"></div>
                            <div class="gateway-card-header">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span class="gateway-logo-wrapper">Ro</span>
                                    <div>
                                        <h3 class="gateway-title">Rocket Payment</h3>
                                        <span class="gateway-type">DBBL MFS Gateway</span>
                                    </div>
                                </div>
                                <label class="switch-toggle">
                                    <input type="checkbox" name="rocket_status" value="1" <?= ($settings['rocket']['status'] ?? 0) ? 'checked' : '' ?>>
                                    <span class="slider-round"></span>
                                </label>
                            </div>
                            <div class="gateway-card-body">
                                <div class="form-group">
                                    <label class="form-label"><i class="fas fa-phone-alt"></i> Receive Rocket Number</label>
                                    <input type="text" name="rocket_number" class="form-input" placeholder="e.g. 019XXXXXXXX" value="<?= htmlspecialchars($settings['rocket']['receive_number'] ?? '') ?>" required>
                                </div>
                                <div class="gateway-info-box">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Configure receiving number. Ensures automatic matching against system database.</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div style="text-align: right; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                        <button type="submit" class="btn-primary" style="padding: 12px 30px; font-weight: 600; font-size: 1rem; border-radius: 8px;">
                            <i class="fas fa-save" style="margin-right: 8px;"></i> Save Payment Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- TAB 2: DEVELOPER API & WEBHOOK -->
            <div id="tab-webhook" class="tab-pane">
                <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; color: #334155;">
                    <h3 style="margin-top: 0; font-size: 1.2rem; font-weight: 700; color: #1e293b;">
                        <i class="fas fa-plug" style="color: #6366f1; margin-right: 8px;"></i> Automated Payment Integration
                    </h3>
                    <p style="margin-bottom: 20px; font-size: 0.95rem; line-height: 1.6;">
                        This system features a universal API webhook endpoint. You can integrate it with any SMS-to-API Android application, directly with bKash/Nagad/Rocket webhooks, or utilizing third-party transaction forwarders.
                    </p>

                    <div class="api-field-group">
                        <label class="api-label">AUTOMATIC WEBHOOK ENDPOINT (POST)</label>
                        <div class="api-endpoint-box">
                            <span class="api-method">POST</span>
                            <span class="api-url" id="webhook-url-text"><?= url('setup/mfs-callback') ?></span>
                            <button onclick="copyToClipboard('webhook-url-text')" class="api-copy-btn">
                                <i class="fas fa-copy"></i> Copy Link
                            </button>
                        </div>
                    </div>

                    <h4 style="margin: 24px 0 10px 0; font-size: 1rem; font-weight: 600; color: #1e293b;">Payload Parameter Schema</h4>
                    <p style="font-size: 0.9rem; color: #475569; margin-bottom: 12px;">Send the callback details as either standard Form POST parameters or a JSON-encoded request body:</p>
                    
                    <div style="overflow-x: auto; background: #0f172a; border-radius: 8px; padding: 18px; margin-bottom: 24px;">
                        <pre style="margin: 0; color: #38bdf8; font-family: 'Courier New', Courier, monospace; font-size: 0.9rem;">
{
  <span style="color: #a78bfa;">"gateway"</span>: <span style="color: #34d399;">"bkash"</span>,      <span style="color: #64748b;">// String: 'bkash', 'nagad', or 'rocket' (Required)</span>
  <span style="color: #a78bfa;">"sender"</span>: <span style="color: #34d399;">"01712345678"</span>, <span style="color: #64748b;">// String: Customer's mobile number making the payment</span>
  <span style="color: #a78bfa;">"receiver"</span>: <span style="color: #34d399;">"01700000000"</span>, <span style="color: #64748b;">// String: The receive number that received the payment</span>
  <span style="color: #a78bfa;">"amount"</span>: <span style="color: #34d399;">500.00</span>,        <span style="color: #64748b;">// Numeric: Payment amount (Required)</span>
  <span style="color: #a78bfa;">"trx_id"</span>: <span style="color: #34d399;">"BKSH9284729"</span>, <span style="color: #64748b;">// String: The unique gateway transaction ID (Required)</span>
  <span style="color: #a78bfa;">"reference"</span>: <span style="color: #34d399;">"HK_10"</span>      <span style="color: #64748b;">// String: User-submitted Reference (ID, PPPoE name, etc)</span>
}</pre>
                    </div>

                    <h4 style="margin: 0 0 10px 0; font-size: 1rem; font-weight: 600; color: #1e293b;"><i class="fas fa-tasks" style="color: #10b981;"></i> Automatic Action Rules:</h4>
                    <ul class="api-rules-list">
                        <li><strong>Match 1 (Priority):</strong> Looks up <code>reference</code> in the customer's <strong>Payment ID (Gateway ID)</strong> column. If matched, credits instantly.</li>
                        <li><strong>Match 2:</strong> Looks up <code>reference</code> in the customer's <strong>Customer ID</strong> (e.g. matching <code>HK_10</code> or raw <code>10</code>). If matched, credits instantly.</li>
                        <li><strong>Match 3:</strong> Looks up <code>reference</code> in the customer's <strong>PPPoE Username</strong>. If matched, credits instantly.</li>
                        <li><strong>Match 4:</strong> Matches the <code>sender</code> phone number (or <code>reference</code>) with the customer's primary <strong>Mobile No</strong> or <strong>Alt Mobile No</strong>. If matched, credits instantly.</li>
                        <li><strong>Billing Update:</strong> Subtracts the payment amount from customer's due balance. If paid amount ≥ Monthly Rent, extends their <code>expire_date</code> by corresponding billing cycles and reactivates their status.</li>
                    </ul>
                </div>
            </div>

            <!-- TAB 3: MFS PAYMENT SIMULATOR -->
            <div id="tab-simulator" class="tab-pane">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <!-- Simulator Form -->
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                        <h3 style="margin-top: 0; margin-bottom: 16px; font-size: 1.2rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-vial" style="color: var(--accent);"></i> Sandbox Transaction Simulator
                        </h3>
                        <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 24px;">
                            Simulate an incoming MFS gateway callback transaction to test the Heuristic Matching Engine and Billing extension rules.
                        </p>

                        <form id="simulatorForm">
                            <div class="form-group" style="margin-bottom: 16px;">
                                <label class="form-label">Select Simulated Gateway</label>
                                <div style="display: flex; gap: 12px; margin-top: 8px;">
                                    <label class="sim-gateway-opt bkash-opt">
                                        <input type="radio" name="sim_gateway" value="bkash" checked onclick="updateSimDetails('bkash')">
                                        <span>bKash</span>
                                    </label>
                                    <label class="sim-gateway-opt nagad-opt">
                                        <input type="radio" name="sim_gateway" value="nagad" onclick="updateSimDetails('nagad')">
                                        <span>Nagad</span>
                                    </label>
                                    <label class="sim-gateway-opt rocket-opt">
                                        <input type="radio" name="sim_gateway" value="rocket" onclick="updateSimDetails('rocket')">
                                        <span>Rocket</span>
                                    </label>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                                <div class="form-group">
                                    <label class="form-label">Receive Number (Target)</label>
                                    <input type="text" id="sim_receiver" class="form-input" value="<?= htmlspecialchars($settings['bkash']['receive_number'] ?? '') ?>" readonly style="background-color: #f1f5f9;">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Sender Mobile No (Customer)</label>
                                    <input type="text" id="sim_sender" class="form-input" placeholder="e.g. 01712345678" value="01712345678" required>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                                <div class="form-group">
                                    <label class="form-label">Amount (TK)</label>
                                    <input type="number" id="sim_amount" class="form-input" placeholder="e.g. 500.00" value="500.00" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Transaction ID (TrxID)</label>
                                    <input type="text" id="sim_trx" class="form-input" placeholder="Unique transaction ID" required>
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom: 24px;">
                                <label class="form-label">Reference (Customer ID, Payment ID, PPPoE, Mobile)</label>
                                <input type="text" id="sim_reference" class="form-input" placeholder="e.g. HK_10 or user123 or 017..." value="HK_1" required>
                                <span style="font-size: 0.75rem; color: #64748b; margin-top: 4px; display: block;">The value submitted as payment reference by customer.</span>
                            </div>

                            <button type="submit" class="btn-primary" id="btnRunSimulator" style="width: 100%; padding: 14px; font-weight: 600; font-size: 1rem; border-radius: 8px;">
                                <i class="fas fa-play" style="margin-right: 8px;"></i> Simulate Payment Callback
                            </button>
                        </form>
                    </div>

                    <!-- Simulator Console Output -->
                    <div style="display: flex; flex-direction: column;">
                        <div class="sim-console">
                            <div class="sim-console-header">
                                <div style="display: flex; gap: 6px;">
                                    <span class="dot red"></span>
                                    <span class="dot amber"></span>
                                    <span class="dot green"></span>
                                </div>
                                <span style="font-size: 0.8rem; color: #94a3b8; font-family: monospace; font-weight: bold;">SANDBOX WEBHOOK OUTPUT</span>
                            </div>
                            <div class="sim-console-body" id="sim-console-out">
                                <span class="console-line system">[System] Waiting for transaction simulation trigger...</span>
                            </div>
                        </div>

                        <!-- Real-time billing match preview card -->
                        <div class="simulation-result-card" id="sim-result-card" style="display: none;">
                            <div class="res-card-icon" id="res-card-icon-wrapper">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div style="flex: 1;">
                                <h4 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #1e293b;" id="res-card-title">Customer Matched!</h4>
                                <p style="margin: 4px 0 10px 0; font-size: 0.85rem; color: #475569;" id="res-card-desc">Billing update was successfully completed.</p>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; background: rgba(255, 255, 255, 0.5); padding: 10px; border-radius: 8px;">
                                    <div style="font-size: 0.8rem;"><span style="color: #64748b;">Customer:</span> <strong id="res-cust-name" style="color:#1e293b;">John Doe</strong></div>
                                    <div style="font-size: 0.8rem;"><span style="color: #64748b;">New Due:</span> <strong id="res-cust-due" style="color:#e11d48;">0.00 TK</strong></div>
                                    <div style="font-size: 0.8rem;"><span style="color: #64748b;">Monthly Rent:</span> <strong id="res-cust-rent" style="color:#1e293b;">500.00 TK</strong></div>
                                    <div style="font-size: 0.8rem;"><span style="color: #64748b;">New Expiry:</span> <strong id="res-cust-expiry" style="color:#10b981;">01/07/2026</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: AUTO-PAYMENT LOGS -->
            <div id="tab-logs" class="tab-pane">
                <div class="card" style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
                    <div class="card-header" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="color: #1e293b; font-size: 1.1rem; font-weight: 700; margin: 0;">Automated Transaction Audit Log</h3>
                        <div>
                            <input type="text" id="logSearch" placeholder="Search logs (TrxID, Ref)..."
                                   style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; width: 220px; font-size: 0.85rem;">
                        </div>
                    </div>
                    <div class="card-body" style="padding: 0; overflow-x: auto;">
                        <table class="table" id="logsTable" style="margin: 0; border-collapse: collapse; width: 100%;">
                            <thead style="background-color: #f1f5f9;">
                                <tr>
                                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem;">Time</th>
                                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem;">Gateway</th>
                                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem;">Sender -> Receiver</th>
                                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem;">Transaction ID</th>
                                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem;">Ref</th>
                                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem;">Amount</th>
                                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem;">Match Customer</th>
                                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($logs)): ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center; padding: 40px; color: #94a3b8; font-size: 0.95rem;">
                                            <i class="fas fa-history" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.5;"></i> No automated payments logged yet.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr class="log-tr" style="border-bottom: 1px solid #f1f5f9; transition: background-color 0.2s;">
                                            <td style="padding: 12px 16px; color: #475569; font-size: 0.85rem; font-family: monospace;"><?= htmlspecialchars($log['created_at']) ?></td>
                                            <td style="padding: 12px 16px; font-size: 0.85rem;">
                                                <span class="badge-gw badge-<?= $log['gateway'] ?>"><?= ucfirst($log['gateway']) ?></span>
                                            </td>
                                            <td style="padding: 12px 16px; color: #334155; font-size: 0.85rem;">
                                                <?= htmlspecialchars($log['sender_number']) ?> &rarr; <span style="color:#64748b; font-size: 0.8rem;"><?= htmlspecialchars($log['receiver_number']) ?></span>
                                            </td>
                                            <td style="padding: 12px 16px; font-weight: 600; color: #0f172a; font-family: monospace; font-size: 0.85rem;"><?= htmlspecialchars($log['trx_id']) ?></td>
                                            <td style="padding: 12px 16px; color: #475569; font-size: 0.85rem;"><code><?= htmlspecialchars($log['reference'] ?: 'N/A') ?></code></td>
                                            <td style="padding: 12px 16px; font-weight: 700; color: #059669; font-size: 0.85rem;"><?= number_format($log['amount'], 2) ?> TK</td>
                                            <td style="padding: 12px 16px; font-size: 0.85rem;">
                                                <?php if ($log['customer_id']): ?>
                                                    <a href="<?= url('customer') ?>/show/<?= $log['customer_id'] ?>" style="color: var(--accent); font-weight: 500; text-decoration: none;">
                                                        <i class="fas fa-user-circle"></i> <?= htmlspecialchars($log['customer_name'] ?? 'View Customer') ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span style="color: #94a3b8; font-style: italic;">Unresolved</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="padding: 12px 16px; font-size: 0.85rem;">
                                                <?php if ($log['status'] === 'success'): ?>
                                                    <span class="badge-stat success"><i class="fas fa-check"></i> Success</span>
                                                <?php elseif ($log['status'] === 'unmatched'): ?>
                                                    <span class="badge-stat unmatched" title="Could not resolve customer reference. Check payload."><i class="fas fa-question-circle"></i> Unmatched</span>
                                                <?php else: ?>
                                                    <span class="badge-stat error" title="<?= htmlspecialchars($log['error_message'] ?? '') ?>"><i class="fas fa-times-circle"></i> Error</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- PREMIUM CSS OVERLAYS AND INTERACTIVES -->
<style>
    /* Premium Tabs Styles */
    .tab-btn {
        background: transparent;
        border: none;
        padding: 12px 20px;
        font-weight: 600;
        color: #64748b;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 2px solid transparent;
        position: relative;
    }
    
    .tab-btn:hover {
        color: #1e293b;
    }
    
    .tab-btn.active {
        color: var(--accent);
        border-bottom: 2px solid var(--accent);
    }

    .badge-pulse {
        background: #f43f5e;
        color: white;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 9999px;
        text-transform: uppercase;
        animation: pulseAnimation 2s infinite;
        margin-left: 4px;
    }

    @keyframes pulseAnimation {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 4px rgba(244, 63, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(244, 63, 94, 0); }
    }

    .tab-pane {
        display: none;
        animation: tabFadeIn 0.3s ease-out;
    }

    .tab-pane.active {
        display: block;
    }

    @keyframes tabFadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* MFS Gateway Cards Styling */
    .gateway-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.02), 0 4px 6px -2px rgba(0, 0, 0, 0.01);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 250px;
    }

    .gateway-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
    }

    .card-bg-glow {
        position: absolute;
        top: -150px;
        right: -150px;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        opacity: 0.05;
        transition: all 0.5s ease;
        pointer-events: none;
    }

    .gateway-card:hover .card-bg-glow {
        opacity: 0.1;
        transform: scale(1.1);
    }

    .gateway-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        position: relative;
        z-index: 2;
    }

    .gateway-logo-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.25rem;
        color: white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
    }

    .gateway-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
    }

    .gateway-type {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 500;
        display: block;
        margin-top: 1px;
    }

    .gateway-card-body {
        margin-top: 24px;
        position: relative;
        z-index: 2;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }

    .form-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        margin-bottom: 6px;
        display: block;
    }

    .form-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.95rem;
        color: #0f172a;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .gateway-info-box {
        background: #f8fafc;
        border-radius: 8px;
        padding: 10px;
        font-size: 0.75rem;
        color: #64748b;
        display: flex;
        gap: 8px;
        align-items: flex-start;
        margin-top: 14px;
        border: 1px dashed #e2e8f0;
    }

    .gateway-info-box i {
        margin-top: 2px;
        font-size: 0.85rem;
    }

    /* bKash Theme Colors */
    .bkash-theme .gateway-logo-wrapper { background: #e2136e; }
    .bkash-theme .card-bg-glow { background: #e2136e; }
    .bkash-theme:hover { border-color: rgba(226, 19, 110, 0.3); }

    /* Nagad Theme Colors */
    .nagad-theme .gateway-logo-wrapper { background: #f05a24; }
    .nagad-theme .card-bg-glow { background: #f05a24; }
    .nagad-theme:hover { border-color: rgba(240, 90, 36, 0.3); }

    /* Rocket Theme Colors */
    .rocket-theme .gateway-logo-wrapper { background: #8c388c; }
    .rocket-theme .card-bg-glow { background: #8c388c; }
    .rocket-theme:hover { border-color: rgba(140, 56, 140, 0.3); }

    /* Switch Toggles */
    .switch-toggle {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
    }

    .switch-toggle input { opacity: 0; width: 0; height: 0; }

    .slider-round {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .4s;
        border-radius: 34px;
    }

    .slider-round:before {
        position: absolute;
        content: "";
        height: 18px; width: 18px;
        left: 4px; bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    input:checked + .slider-round {
        background-color: #10b981;
    }

    input:checked + .slider-round:before {
        transform: translateX(22px);
    }

    /* Webhook UI Specs */
    .api-endpoint-box {
        display: flex;
        align-items: center;
        background: #0f172a;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #1e293b;
        margin-top: 8px;
    }

    .api-method {
        background: #10b981;
        color: white;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 12px 18px;
        letter-spacing: 0.5px;
    }

    .api-url {
        flex: 1;
        color: #f8fafc;
        font-family: monospace;
        font-size: 0.9rem;
        padding: 12px 16px;
        overflow-x: auto;
        white-space: nowrap;
    }

    .api-copy-btn {
        background: #1e293b;
        color: #cbd5e1;
        border: none;
        padding: 12px 18px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .api-copy-btn:hover {
        background: #334155;
        color: white;
    }

    .api-field-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 20px;
    }

    .api-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        letter-spacing: 0.5px;
    }

    .api-rules-list {
        padding-left: 20px;
        margin: 10px 0 0 0;
        font-size: 0.9rem;
        line-height: 1.6;
    }

    .api-rules-list li {
        margin-bottom: 8px;
    }

    /* Simulator Panel Styling */
    .sim-gateway-opt {
        flex: 1;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .sim-gateway-opt input { display: none; }

    .bkash-opt { color: #e2136e; }
    .bkash-opt input:checked + span, .bkash-opt:hover {
        border-color: #e2136e;
        background: rgba(226, 19, 110, 0.05);
    }

    .nagad-opt { color: #f05a24; }
    .nagad-opt input:checked + span, .nagad-opt:hover {
        border-color: #f05a24;
        background: rgba(240, 90, 36, 0.05);
    }

    .rocket-opt { color: #8c388c; }
    .rocket-opt input:checked + span, .rocket-opt:hover {
        border-color: #8c388c;
        background: rgba(140, 56, 140, 0.05);
    }

    /* Console styles */
    .sim-console {
        background: #090d16;
        border-radius: 12px;
        border: 1px solid #1e293b;
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        min-height: 250px;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.5);
    }

    .sim-console-header {
        background: #111827;
        border-bottom: 1px solid #1e293b;
        padding: 10px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .sim-console-header .dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    .sim-console-header .dot.red { background: #f43f5e; }
    .sim-console-header .dot.amber { background: #eab308; }
    .sim-console-header .dot.green { background: #10b981; }

    .sim-console-body {
        padding: 16px;
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.85rem;
        line-height: 1.6;
        flex: 1;
        overflow-y: auto;
        color: #38bdf8;
    }

    .console-line { display: block; margin-bottom: 6px; white-space: pre-wrap; }
    .console-line.system { color: #94a3b8; }
    .console-line.input { color: #a78bfa; }
    .console-line.success { color: #34d399; }
    .console-line.unmatched { color: #fbbf24; }
    .console-line.error { color: #f87171; }

    .simulation-result-card {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 12px;
        padding: 16px;
        margin-top: 16px;
        display: flex;
        gap: 16px;
        animation: resultFadeIn 0.3s ease-out;
    }

    @keyframes resultFadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    .res-card-icon {
        width: 44px; height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .simulation-result-card.success { background: #f0fdf4; border-color: #bbf7d0; }
    .simulation-result-card.success .res-card-icon { background: #dcfce7; color: #166534; }
    
    .simulation-result-card.unmatched { background: #fffbeb; border-color: #fef3c7; }
    .simulation-result-card.unmatched .res-card-icon { background: #fef9c3; color: #854d0e; }
    .simulation-result-card.unmatched .res-card-icon i::before { content: "\f128"; }

    .simulation-result-card.error { background: #fef2f2; border-color: #fee2e2; }
    .simulation-result-card.error .res-card-icon { background: #fee2e2; color: #991b1b; }
    .simulation-result-card.error .res-card-icon i::before { content: "\f06a"; }

    /* Audit Logs Badges */
    .badge-gw {
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.75rem;
        display: inline-block;
        color: white;
    }

    .badge-bkash { background-color: #e2136e; }
    .badge-nagad { background-color: #f05a24; }
    .badge-rocket { background-color: #8c388c; }

    .badge-stat {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-stat.success { background-color: #dcfce7; color: #15803d; }
    .badge-stat.unmatched { background-color: #fef9c3; color: #a16207; }
    .badge-stat.error { background-color: #fee2e2; color: #b91c1c; }

    .log-tr:hover {
        background-color: #f8fafc !important;
    }
</style>

<!-- TAB SWITCH & SIMULATOR LOGIC -->
<script>
    // Tab switching
    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
        
        document.getElementById(`tab-${tabId}-btn`).classList.add('active');
        document.getElementById(`tab-${tabId}`).classList.add('active');
    }

    // Copy to clipboard helper
    function copyToClipboard(elementId) {
        const text = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert("Webhook endpoint copied to clipboard!");
        });
    }

    // Simulated Gateway configs
    const gatewayNumbers = {
        bkash: "<?= htmlspecialchars($settings['bkash']['receive_number'] ?? '01700000000') ?>",
        nagad: "<?= htmlspecialchars($settings['nagad']['receive_number'] ?? '01800000000') ?>",
        rocket: "<?= htmlspecialchars($settings['rocket']['receive_number'] ?? '01900000000') ?>"
    };

    function generateRandomTrx(gateway) {
        const prefix = gateway.substring(0, 3).toUpperCase();
        const randNum = Math.floor(100000 + Math.random() * 900000);
        return `${prefix}${randNum}M${Math.floor(10 + Math.random() * 89)}`;
    }

    // Set a default TRX ID on load
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('sim_trx').value = generateRandomTrx('bkash');
    });

    function updateSimDetails(gateway) {
        document.getElementById('sim_receiver').value = gatewayNumbers[gateway] || '';
        document.getElementById('sim_trx').value = generateRandomTrx(gateway);
        
        if (gateway === 'bkash') {
            document.getElementById('sim_reference').value = "HK_1";
            document.getElementById('sim_sender').value = "01712345678";
        } else if (gateway === 'nagad') {
            document.getElementById('sim_reference').value = "PAY-983";
            document.getElementById('sim_sender').value = "01822334455";
        } else {
            document.getElementById('sim_reference').value = "user_router";
            document.getElementById('sim_sender').value = "01988776655";
        }
    }

    // Run simulator post request
    document.getElementById('simulatorForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btnRunSimulator');
        btn.disabled = true;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Triggering Callback...`;
        
        const consoleOut = document.getElementById('sim-console-out');
        const resCard = document.getElementById('sim-result-card');
        
        resCard.style.display = "none";
        
        const gateway = document.querySelector('input[name="sim_gateway"]:checked').value;
        const sender = document.getElementById('sim_sender').value;
        const receiver = document.getElementById('sim_receiver').value;
        const amount = document.getElementById('sim_amount').value;
        const trx_id = document.getElementById('sim_trx').value;
        const reference = document.getElementById('sim_reference').value;
        
        // Log to simulated console
        consoleOut.innerHTML = `
<span class="console-line system">[System] Connection established to gateway callback simulator.</span>
<span class="console-line system">[System] Sending POST request payload...</span>
<span class="console-line input">POST Payload: {
  "gateway": "${gateway}",
  "sender": "${sender}",
  "receiver": "${receiver}",
  "amount": ${amount},
  "trx_id": "${trx_id}",
  "reference": "${reference}"
}</span>
        `;
        
        // Generate FormData to match standard POST request or JSON
        const payload = { gateway, sender, receiver, amount, trx_id, reference };
        
        fetch("<?= url('setup/mfs-callback') ?>", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = `<i class="fas fa-play" style="margin-right: 8px;"></i> Simulate Payment Callback`;
            
            // Console response logging
            consoleOut.innerHTML += `
<span class="console-line system">[System] Callback processed. HTTP 200 OK received.</span>
<span class="console-line success">Response JSON: ${JSON.stringify(data, null, 2)}</span>
            `;
            
            // Auto scroll console
            consoleOut.scrollTop = consoleOut.scrollHeight;
            
            // Render Result Card
            resCard.className = `simulation-result-card ${data.status}`;
            const iconWrapper = document.getElementById('res-card-icon-wrapper');
            const title = document.getElementById('res-card-title');
            const desc = document.getElementById('res-card-desc');
            
            if (data.status === 'success') {
                resCard.style.display = "flex";
                title.innerText = "Payment Processed successfully!";
                desc.innerText = data.message;
                
                document.getElementById('res-cust-name').innerText = data.customer_name || 'N/A';
                document.getElementById('res-cust-due').innerText = `${parseFloat(data.new_due).toFixed(2)} TK`;
                document.getElementById('res-cust-rent').innerText = `${parseFloat(data.monthly_rent).toFixed(2)} TK`;
                document.getElementById('res-cust-expiry').innerText = data.new_expiry ? data.new_expiry.split('-').reverse().join('/') : 'N/A';
            } else if (data.status === 'unmatched') {
                resCard.style.display = "flex";
                title.innerText = "Transaction Unmatched";
                desc.innerText = "The transaction was saved in the logs but no customer matched this reference or sender mobile.";
                
                document.getElementById('res-cust-name').innerText = 'Unresolved';
                document.getElementById('res-cust-due').innerText = 'N/A';
                document.getElementById('res-cust-rent').innerText = 'N/A';
                document.getElementById('res-cust-expiry').innerText = 'N/A';
            } else {
                resCard.style.display = "flex";
                title.innerText = "Simulation Error";
                desc.innerText = data.message || "An unexpected error occurred during processing.";
                
                document.getElementById('res-cust-name').innerText = 'N/A';
                document.getElementById('res-cust-due').innerText = 'N/A';
                document.getElementById('res-cust-rent').innerText = 'N/A';
                document.getElementById('res-cust-expiry').innerText = 'N/A';
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = `<i class="fas fa-play" style="margin-right: 8px;"></i> Simulate Payment Callback`;
            
            consoleOut.innerHTML += `<span class="console-line error">[Fatal Error] Request failed: ${err.message}</span>`;
            consoleOut.scrollTop = consoleOut.scrollHeight;
        });
    });

    // Simple search in logs table
    document.getElementById('logSearch').addEventListener('keyup', function() {
        const filter = this.value.toUpperCase();
        const rows = document.querySelectorAll("#logsTable tbody tr");
        
        rows.forEach(row => {
            const text = row.textContent.toUpperCase();
            if (text.indexOf(filter) > -1) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
