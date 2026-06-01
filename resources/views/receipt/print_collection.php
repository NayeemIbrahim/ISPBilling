<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Money Receipt - #<?= htmlspecialchars($col['transaction_id']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; background: #f0f0f0; }
        .page { width: 210mm; min-height: 297mm; padding: 10mm 15mm; margin: 10mm auto; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); box-sizing: border-box; overflow: hidden; }
        
        @media print {
            body { background: none; }
            .page { margin: 0; box-shadow: none; page-break-after: always; }
            .no-print { display: none; }
        }

        .invoice-bill { border-bottom: 1px dashed #bbb; padding-bottom: 15px; margin-bottom: 15px; font-size: 10.5px; color: #1e293b; position: relative; }
        .invoice-bill:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        
        .inv-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
        .inv-qr { width: 45px; height: 45px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 8px; background: #f8fafc; }
        .inv-title { font-weight: 700; font-size: 13px; text-align: center; flex: 1; padding: 0 10px; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; }
        .inv-company { text-align: right; width: 180px; line-height: 1.4; }
        .inv-company-name { font-weight: 700; font-size: 12px; }
        
        .inv-info-row { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 10px; }
        .inv-customer-name { font-weight: 700; font-size: 11.5px; margin: 5px 0 2px; color: #0f172a; }
        .inv-customer-address { font-size: 9.5px; color: #475569; line-height: 1.4; }
        
        .inv-body { display: grid; grid-template-columns: 1fr 200px; gap: 15px; margin-top: 10px; }
        .inv-totals table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .inv-totals td { padding: 3px 5px; }
        .inv-totals td:last-child { text-align: right; font-weight: 600; }
        .inv-totals .row-sep td { border-top: 1px solid #333; border-bottom: 1px solid #333; font-weight: 700; }
        
        .inv-box { border: 1px solid #334155; padding: 6px 10px; margin-top: 10px; font-weight: 500; font-size: 10px; line-height: 1.6; background: #f8fafc; }
        .inv-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 12px; }
        .inv-note { font-size: 10px; font-weight: 500; color: #475569; }
        .inv-signature { text-align: center; width: 130px; font-size: 9px; font-weight: 600; }
        .inv-signature img { max-height: 30px; display: block; margin: 0 auto 3px; }
        .inv-sig-line { border-top: 1px solid #334155; padding-top: 3px; }
        
        .payment-highlight {
            font-size: 14px;
            font-weight: 700;
            color: #059669;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            padding: 8px 12px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 8px;
        }
    </style>
</head>
<body <?= isset($_GET['preview']) ? '' : 'onload="window.print()"' ?>>

<?php
$layout = $settings['layout'] ?? '3';
$numCopies = ($layout === '2') ? 2 : (($layout === '3') ? 3 : 1);
$titles = ['Office Copy', 'Customer Receipt', 'Audit Copy'];
$signaturePath = (!empty($settings['signature_path'])) ? url($settings['signature_path']) : '';
$showHeader = (($settings['header_style'] ?? 'with_header') === 'with_header');

// Format dates
$collectionDate = date('d-M-y h:i A', strtotime($col['collection_date']));
$expireDate = $col['next_expire_date'] ? date('d/m/Y', strtotime($col['next_expire_date'])) : 'N/A';
?>

<div class="page">
    <?php for ($i = 0; $i < $numCopies; $i++): 
        $title = $titles[$i] ?? 'Money Receipt';
        $isLast = ($i === $numCopies - 1);
    ?>
        <div class="invoice-bill">
            <div class="inv-header">
                <div class="inv-qr">
                    <!-- Standard placeholder QR code -->
                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" style="width:30px; height:30px; opacity:0.3;">
                        <rect x="10" y="10" width="80" height="80" fill="none" stroke="black" stroke-width="5"/>
                        <rect x="25" y="25" width="50" height="50" fill="black"/>
                    </svg>
                </div>
                <div class="inv-title"><?= $title ?></div>
                <div class="inv-company" style="<?= $showHeader ? '' : 'visibility:hidden' ?>">
                    <div class="inv-company-name">Hello Khulna</div>
                    <div style="font-size:9px; color:#64748b;">Your trusted ISP partner</div>
                </div>
            </div>

            <div class="inv-info-row">
                <span>Date: <strong><?= $collectionDate ?></strong></span>
                <span>ID: <strong><?= htmlspecialchars(($col['prefix_code'] ?? '') . $col['customer_id']) ?></strong> &nbsp; Username: <strong><?= htmlspecialchars($col['pppoe_name'] ?: 'N/A') ?></strong></span>
            </div>

            <div class="inv-customer-name"><?= htmlspecialchars($col['full_name']) ?></div>
            <div class="inv-customer-address">
                <?= htmlspecialchars(implode(', ', array_filter([$col['house_no'], $col['area'], $col['thana'], $col['district'], $col['mobile_no']]))) ?>
            </div>
            
            <div style="font-size:9.5px; margin-top:3px; display:flex; justify-content:space-between;">
                <span>Connection Date: <?= $col['connection_date'] ? date('d-m-Y', strtotime($col['connection_date'])) : 'N/A' ?></span>
                <span>Receipt No: <strong>#<?= htmlspecialchars($col['transaction_id']) ?></strong></span>
            </div>

            <div class="inv-body">
                <div>
                    <div class="payment-highlight">
                        PAID AMOUNT: <?= number_format($col['amount'], 2) ?> TK
                    </div>
                    <div style="margin-top: 10px; font-size: 9.5px; color: #475569;">
                        <div>Payment Method: <strong><?= htmlspecialchars($col['payment_method']) ?></strong></div>
                        <?php if (!empty($col['invoice_no'])): ?>
                            <div>Transaction / Invoice ID: <strong><?= htmlspecialchars($col['invoice_no']) ?></strong></div>
                        <?php endif; ?>
                        <div>Collected By: <strong><?= htmlspecialchars($col['collected_by_name'] ?? 'System / Auto') ?></strong></div>
                    </div>
                </div>
                <div class="inv-totals">
                    <table>
                        <tr><td>Monthly Rent:</td><td><?= number_format($col['monthly_rent'], 2) ?> TK</td></tr>
                        <tr><td>Collected Amount:</td><td style="color:#059669;">- <?= number_format($col['amount'], 2) ?> TK</td></tr>
                        <tr class="row-sep"><td>Current Balance:</td><td><?= number_format($col['due_amount'], 2) ?> TK</td></tr>
                    </table>
                </div>
            </div>

            <div class="inv-box">
                <div style="display:flex; justify-content:space-between;">
                    <span>Next Expiration Date: <strong><?= $expireDate ?></strong></span>
                    <span>Status: <strong style="text-transform:uppercase; color:#059669;"><?= htmlspecialchars($col['status']) ?></strong></span>
                </div>
                <?php if (!empty($col['note'])): ?>
                    <div style="margin-top:4px; font-size:9px; color:#475569; font-style:italic;">Note: <?= htmlspecialchars($col['note']) ?></div>
                <?php endif; ?>
            </div>

            <div class="inv-footer">
                <div class="inv-note">Note: <?= htmlspecialchars($settings['receipt_text'] ?? 'Thank you for connecting with us.') ?></div>
                <div class="inv-signature">
                    <?php if ($signaturePath && $isLast): ?>
                        <img src="<?= $signaturePath ?>" alt="Sig">
                    <?php else: ?>
                        <div style="height:30px;"></div>
                    <?php endif; ?>
                    <div class="<?= $isLast ? 'inv-sig-line' : '' ?>"><?= $isLast ? 'Authorized Signature' : '' ?></div>
                </div>
            </div>
        </div>
    <?php endfor; ?>
</div>

</body>
</html>
