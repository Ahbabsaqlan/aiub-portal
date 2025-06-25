<?php
// pages/financial-statements.php
?>
<section id="financial-statements" class="page-content" >
    <h2 class="page-title"><i class="fas fa-file-invoice-dollar"></i> Financial Statements</h2>
    <div class="semester-selector"><select class="semester-select" id="finance-semester-filter">
        <option value="spring2024">Spring 2023-2024</option>
        <option value="fall2023">Fall 2022-2023</option>
        </select>
    </div>
    <div id="financial-statements-content-area">
        <div class="semester-content" id="spring2024-finance"> <div class="financial-card"><div class="financial-header"><div class="financial-title">Spring 2023-2024</div><div class="financial-status paid">Paid</div></div><div class="financial-details">
            <div class="financial-item"><span class="financial-label">Total Amount</span><span class="financial-value">৳ 125,000</span></div><div class="financial-item"><span class="financial-label">Amount Paid</span><span class="financial-value">৳ 125,000</span></div>
            <div class="financial-item"><span class="financial-label">Due Date</span><span class="financial-value">February 15, 2024</span></div><div class="financial-item"><span class="financial-label">Payment Date</span><span class="financial-value">February 10, 2024</span></div>
        </div><div class="financial-actions"><button class="btn"><i class="fas fa-print"></i> Print Invoice</button><button class="btn"><i class="fas fa-download"></i> Download Receipt</button><button class="btn" id="view-payment-history"><i class="fas fa-history"></i> Payment History</button></div>
        <div class="financial-breakdown"><h4>Fee Breakdown</h4><div class="table-container"><table><thead><tr><th>Fee Type</th><th>Amount (৳)</th></tr></thead><tbody>
            <tr><td>Tuition Fee (15 credits)</td><td>75,000</td></tr><tr><td>Registration Fee</td><td>10,000</td></tr><tr><td>Lab Fee</td><td>15,000</td></tr>
            <tr><td>Library Fee</td><td>5,000</td></tr><tr><td>Student Activity Fee</td><td>2,000</td></tr><tr><td>Technology Fee</td><td>3,000</td></tr>
            <tr><td>Health Service Fee</td><td>1,500</td></tr><tr><td>Development Fee</td><td>13,500</td></tr><tr style="font-weight: bold;"><td>Total</td><td>125,000</td></tr>
        </tbody></table></div></div></div>
        <div class="financial-summary"><h3>Total University Spending</h3>
            <div class="summary-item"><span>Tuition Fees:</span><span>৳ 420,000</span></div><div class="summary-item"><span>Registration Fees:</span><span>৳ 40,000</span></div>
            <div class="summary-item"><span>Lab Fees:</span><span>৳ 60,000</span></div><div class="summary-item"><span>Library Fees:</span><span>৳ 20,000</span></div>
            <div class="summary-item"><span>Other Fees:</span><span>৳ 45,000</span></div><div class="summary-item"><span>Total:</span><span>৳ 585,000</span></div>
        </div>
        <div class="page-content" id="payment-history" style="display: none; margin-top: 2rem; padding:0; box-shadow: none; border: 1px solid #eee; border-radius: var(--border-radius);">
            <h3 class="page-title" style="padding: 1rem; margin-bottom:0; border-bottom: 1px solid #eee; background-color: #f8f9fa; border-top-left-radius: var(--border-radius); border-top-right-radius: var(--border-radius); "><i class="fas fa-history"></i> Payment History</h3>
            <div class="table-container" style="margin-top:0;"><table><thead><tr><th>Payment Date</th><th>Semester</th><th>Amount</th><th>Method</th><th>Transaction ID</th><th>Status</th></tr></thead><tbody>
                <tr><td>Feb 10, 2024</td><td>Spring 2023-2024</td><td>৳ 125,000</td><td>Online Banking</td><td>TXN-2024-12548</td><td><span class="status-badge status-approved">Completed</span></td></tr>
            </tbody></table></div></div></div>
        <div class="semester-content" id="fall2023-finance" style="display:none;"> <p class="no-results">Financial details for Fall 2022-2023 are not shown in this mock.</p> </div>
    </div>
</section>