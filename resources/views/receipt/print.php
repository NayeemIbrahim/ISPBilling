<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Receipts</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; background: #f0f0f0; }
        .page { width: 210mm; min-height: 297mm; padding: 10mm 15mm; margin: 10mm auto; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); box-sizing: border-box; overflow: hidden; }
        
        @media print {
            body { background: none; }
            .page { margin: 0; box-shadow: none; page-break-after: always; }
        }

        .invoice-bill { border-bottom: 1px dashed #bbb; padding-bottom: 15px; margin-bottom: 15px; font-size: 10.5px; color: #1e293b; position: relative; }
        .invoice-bill:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        
        .inv-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
        .inv-qr { width: 45px; height: 45px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 8px; background: #f8fafc; }
        .inv-title { font-weight: 700; font-size: 13px; text-align: center; flex: 1; padding: 0 10px; }
        .inv-company { text-align: right; width: 180px; line-height: 1.4; }
        .inv-company-name { font-weight: 700; font-size: 12px; }
        
        .inv-info-row { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 10px; }
        .inv-customer-name { font-weight: 700; font-size: 11.5px; margin: 5px 0 2px; }
        .inv-customer-address { font-size: 9.5px; color: #475569; line-height: 1.4; }
        
        .inv-body { display: grid; grid-template-columns: 1fr 200px; gap: 15px; margin-top: 10px; }
        .inv-totals table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .inv-totals td { padding: 2px 5px; }
        .inv-totals td:last-child { text-align: right; font-weight: 600; }
        .inv-totals .row-sep td { border-top: 1px solid #333; border-bottom: 1px solid #333; font-weight: 700; }
        
        .inv-box { border: 1px solid #334155; padding: 6px 10px; margin-top: 10px; font-weight: 500; font-size: 10px; line-height: 1.6; }
        .inv-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 12px; }
        .inv-note { font-size: 10px; font-weight: 500; }
        .inv-signature { text-align: center; width: 130px; font-size: 9px; font-weight: 600; }
        .inv-signature img { max-height: 30px; display: block; margin: 0 auto 3px; }
        .inv-sig-line { border-top: 1px solid #334155; padding-top: 3px; }
    </style>
</head>
<body <?= isset($_GET['preview']) ? '' : 'onload="window.print()"' ?>>

<?php
$layout = $settings['layout'];
$numCopies = ($layout === '2') ? 2 : (($layout === '3') ? 3 : 1);
$titles = ['Office Document', 'Money Receipt', 'Invoice'];
$signaturePath = $settings['signature_path'] ? url($settings['signature_path']) : '';
$showHeader = ($settings['header_style'] === 'with_header');

// Format the billing month from YYYY-MM to M YYYY (e.g., 2024-05 to May 2024)
$monthTs = isset($billingMonth) ? strtotime($billingMonth . '-01') : time();
$formattedMonth = date('M Y', $monthTs);

foreach ($customers as $cust):
?>
    <div class="page">
        <?php for ($i = 0; $i < $numCopies; $i++): 
            $title = $titles[$i] ?? 'Invoice';
            $isLast = ($i === $numCopies - 1);
        ?>
            <div class="invoice-bill">
                <div class="inv-header">
                    <div class="inv-qr">
                        <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" style="width:30px; height:30px; opacity:0.3;">
                            <rect x="10" y="10" width="80" height="80" fill="none" stroke="black" stroke-width="5"/>
                        </svg>
                    </div>
                    <div class="inv-title"><?= $title ?></div>
                    <div class="inv-company" style="<?= $showHeader ? '' : 'visibility:hidden' ?>">
                        <div class="inv-company-name">Hello Khulna</div>
                        <div style="font-size:9px; color:#64748b;">Your trusted ISP partner</div>
                    </div>
                </div>

                <div class="inv-info-row">
                    <span><?= date('d-M-y') ?></span>
                    <span>ID : <strong><?= htmlspecialchars(($cust['prefix_code'] ?? '') . $cust['id']) ?></strong> &nbsp; Username: <strong><?= htmlspecialchars($cust['pppoe_name']) ?></strong></span>
                </div>

                <div class="inv-customer-name"><?= htmlspecialchars($cust['full_name']) ?></div>
                <div class="inv-customer-address">
                    <?= htmlspecialchars(implode(', ', array_filter([$cust['house_no'], $cust['area'], $cust['thana'], $cust['district'], $cust['mobile_no']]))) ?>
                </div>
                <div style="font-size:9.5px; margin-top:3px;">Connection Date : <?= $cust['connection_date'] ? date('d-m-Y', strtotime($cust['connection_date'])) : 'N/A' ?></div>

                <div class="inv-body">
                    <div style="font-size: 10px; line-height: 1.6; color: #475569;">
                        <?php if (isset($cust['received_amount']) && $cust['received_amount'] > 0): ?>
                            <div style="padding: 8px 12px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px;">
                                <div style="font-size: 11px; font-weight: 700; color: #065f46; text-transform: uppercase; letter-spacing: 0.5px;">Payment Received</div>
                                <div style="font-size: 14px; font-weight: 800; color: #047857; margin-top: 2px;">
                                    <?= number_format($cust['received_amount'], 2) ?> TK
                                </div>
                                <div style="font-size: 9px; margin-top: 4px; color: #065f46; border-top: 1px dashed rgba(6, 95, 70, 0.2); padding-top: 4px; line-height: 1.4;">
                                    Method: <strong><?= htmlspecialchars($cust['payment_method']) ?></strong>
                                    <?php if (!empty($cust['payment_invoice_no'])): ?>
                                        <br>TrxID: <strong><?= htmlspecialchars($cust['payment_invoice_no']) ?></strong>
                                    <?php endif; ?>
                                    <?php if (!empty($cust['payment_date'])): ?>
                                        <br>Date: <strong><?= date('d-M-Y h:i A', strtotime($cust['payment_date'])) ?></strong>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="padding: 8px 12px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 6px;">
                                <div style="font-size: 10px; font-weight: 700; color: #b45309; text-transform: uppercase;">No Payment Received</div>
                                <div style="font-size: 8.5px; margin-top: 2px; color: #b45309;">
                                    No payment logged for the selected period. Showing current outstanding.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="inv-totals">
                        <table>
                            <tr><td>Monthly Rent :</td><td><?= number_format($cust['monthly_rent'], 2) ?></td></tr>
                            <tr><td>Additional :</td><td><?= number_format($cust['additional_charge'] ?? 0, 2) ?></td></tr>
                            <tr><td>Discount :</td><td><?= number_format($cust['discount'] ?? 0, 2) ?></td></tr>
                            <tr class="row-sep"><td>SUM :</td><td><?= number_format($cust['monthly_rent'] + ($cust['additional_charge'] ?? 0) - ($cust['discount'] ?? 0), 2) ?></td></tr>
                            <tr><td>Vat (0%) :</td><td>0.00</td></tr>
                            
                            <?php 
                            $receivedAmount = floatval($cust['received_amount'] ?? 0);
                            $beforePayDue = floatval($cust['due_amount']) + $receivedAmount;
                            ?>
                            <tr><td>Previous Outstanding :</td><td><?= number_format($beforePayDue, 2) ?></td></tr>
                            <tr class="row-sep"><td>Total Outstanding :</td><td><?= number_format($beforePayDue, 2) ?></td></tr>
                            <tr><td style="color:#059669; font-weight:700;">Amount Paid :</td><td style="color:#059669; font-weight:700;">- <?= number_format($receivedAmount, 2) ?></td></tr>
                            <tr class="row-sep"><td>Current Remaining Due :</td><td><?= number_format($cust['due_amount'], 2) ?></td></tr>
                        </table>
                    </div>
                </div>

                <div class="inv-box">
                    <div>Billing Month : <?= $formattedMonth ?></div>
                    <div>Due Month's List :</div>
                </div>

                <div class="inv-footer">
                    <div class="inv-note">Note : <?= htmlspecialchars($settings['receipt_text']) ?></div>
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
<?php endforeach; ?>

</body>
</html>
