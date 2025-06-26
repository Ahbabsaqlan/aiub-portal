<?php
// Get all semesters for which the student has financial transactions
$semesters_stmt = $pdo->prepare("
    SELECT DISTINCT s.id, s.name FROM financial_transactions ft
    JOIN semesters s ON ft.semester_id = s.id
    WHERE ft.student_id = ? ORDER BY s.id DESC
");
$semesters_stmt->execute([$current_student['id']]);
$available_semesters = $semesters_stmt->fetchAll();

// Determine the selected semester
$selected_semester_id = $_GET['semester_id'] ?? ($available_semesters[2]['id'] ?? null);

$transactions = [];
$fee_breakdown = [];
$total_amount = 0;
$total_paid = 0;

if ($selected_semester_id) {
    // --- Fetch payment history for the selected semester ---
    $transactions_stmt = $pdo->prepare("
        SELECT * FROM financial_transactions 
        WHERE student_id = ? AND semester_id = ? ORDER BY payment_date DESC
    ");
    $transactions_stmt->execute([$current_student['id'], $selected_semester_id]);
    $transactions = $transactions_stmt->fetchAll();

    foreach($transactions as $trx) {
        if ($trx['status'] === 'Completed') {
            $total_paid += $trx['amount'];
        }
    }

    
    $per_credit_fee = 5500;
    $registration_fee = 10000;
    $other_fees = 15000; 

    // Get registered credits for the semester
    $credits_stmt = $pdo->prepare("
        SELECT IFNULL(SUM(c.credits), 0) as total_credits 
        FROM registrations rg
        JOIN sections s ON rg.section_id = s.id
        JOIN courses c ON s.course_id = c.id
        WHERE rg.student_id = ? AND s.semester_id = ?
    ");
    $credits_stmt->execute([$current_student['id'], $selected_semester_id]);
    $semester_credits = $credits_stmt->fetchColumn();
    
    $tuition_fee = $semester_credits * $per_credit_fee;

    $fee_breakdown = [
        ['type' => "Tuition Fee ({$semester_credits} credits)", 'amount' => $tuition_fee],
        ['type' => 'Registration Fee', 'amount' => $registration_fee],
        ['type' => 'Other Fees (Library, Activity, etc.)', 'amount' => $other_fees]
    ];
    
    if ($total_amount == 0) {
        $total_amount = $tuition_fee + $registration_fee + $other_fees;
    }
}
?>
<section id="financial-statements" class="page-content">
    <h2 class="page-title"><i class="fas fa-file-invoice-dollar"></i> Financial Statements</h2>
    
    <div class="semester-selector">
        <?php if (!empty($available_semesters)): ?>
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="financial-statements">
            <select name="semester_id" class="semester-select" onchange="this.form.submit()">
                <?php foreach ($available_semesters as $semester): ?>
                    <option value="<?php echo $semester['id']; ?>" <?php echo ($selected_semester_id == $semester['id'] ? 'selected' : ''); ?>>
                        <?php echo htmlspecialchars($semester['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <?php endif; ?>
    </div>

    <?php if ($selected_semester_id): ?>
        <div class="financial-card">
            <div class="financial-header">
                <div class="financial-title"><?php echo htmlspecialchars(array_column($available_semesters, 'name', 'id')[$selected_semester_id]); ?></div>
                <div class="financial-status <?php echo ($total_paid >= $total_amount) ? 'paid' : 'pending'; ?>">
                    <?php echo ($total_paid >= $total_amount) ? 'Paid' : 'Due'; ?>
                </div>
            </div>
            <div class="financial-details">
                <div class="financial-item"><span class="financial-label">Total Amount</span><span class="financial-value">৳ <?php echo number_format($total_amount, 2); ?></span></div>
                <div class="financial-item"><span class="financial-label">Amount Paid</span><span class="financial-value">৳ <?php echo number_format($total_paid, 2); ?></span></div>
                <div class="financial-item"><span class="financial-label">Amount Due</span><span class="financial-value">৳ <?php echo number_format($total_amount - $total_paid, 2); ?></span></div>
            </div>
            <div class="financial-actions">
                <button class="btn"><i class="fas fa-print"></i> Print Invoice</button>
                <button class="btn" id="view-payment-history-btn"><i class="fas fa-history"></i> Payment History</button>
            </div>
            
            <div class="financial-breakdown">
                <h4>Fee Breakdown</h4>
                <div class="table-container">
                    <table>
                        <thead><tr><th>Fee Type</th><th>Amount (৳)</th></tr></thead>
                        <tbody>
                            <?php foreach($fee_breakdown as $fee): ?>
                            <tr><td><?php echo htmlspecialchars($fee['type']); ?></td><td><?php echo number_format($fee['amount'], 2); ?></td></tr>
                            <?php endforeach; ?>
                            <tr style="font-weight: bold;"><td>Total</td><td><?php echo number_format($total_amount, 2); ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Payment History Section -->
        <div class="page-content" id="payment-history" style="display: none; margin-top: 2rem; padding:0; box-shadow: none; border: 1px solid #eee; border-radius: var(--border-radius);">
            <h3 class="page-title" style="padding: 1rem; margin-bottom:0; border-bottom: 1px solid #eee; background-color: #f8f9fa; border-radius: var(--border-radius) var(--border-radius) 0 0;"><i class="fas fa-history"></i> Payment History</h3>
            <div class="table-container" style="margin-top:0;">
                <table>
                    <thead><tr><th>Payment Date</th><th>Description</th><th>Amount</th><th>Method</th><th>Transaction ID</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if(empty($transactions)): ?>
                            <tr><td colspan="6" style="text-align: center;">No payment history found for this semester.</td></tr>
                        <?php else: ?>
                            <?php foreach($transactions as $trx): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($trx['payment_date']); ?></td>
                                <td><?php echo htmlspecialchars($trx['description']); ?></td>
                                <td>৳ <?php echo number_format($trx['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($trx['payment_method']); ?></td>
                                <td><?php echo htmlspecialchars($trx['transaction_id']); ?></td>
                                <td><span class="status-badge status-approved"><?php echo htmlspecialchars($trx['status']); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <p class="no-results">No financial records found for this student.</p>
    <?php endif; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const historyBtn = document.getElementById('view-payment-history-btn');
    const historySection = document.getElementById('payment-history');

    if (historyBtn && historySection) {
        historyBtn.addEventListener('click', function() {
            const isVisible = historySection.style.display === 'block';
            historySection.style.display = isVisible ? 'none' : 'block';
            if (!isVisible) {
                historySection.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
});
</script>