<?php

$stmt = $pdo->query("SELECT * FROM academic_calendar ORDER BY event_date ASC");
$all_events = $stmt->fetchAll();

// Group events by Month
$calendar_by_month = [];
foreach ($all_events as $event) {
    $month_name = date('F', strtotime($event['event_date']));
    if (!isset($calendar_by_month[$month_name])) {
        $calendar_by_month[$month_name] = [];
    }
    $calendar_by_month[$month_name][] = $event;
}

// Helper function to format date ranges
function format_event_date($event) {
    return date('d (D)', strtotime($event['event_date']));
}

?>
<!-- Custom CSS for the academic calendar table -->
<style>
    .academic-calendar-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 2rem;
        font-size: 0.95rem;
    }
    .academic-calendar-table th, .academic-calendar-table td {
        border: 1px solid #ddd;
        padding: 12px 15px;
        text-align: left;
        vertical-align: top;
    }
    .academic-calendar-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: var(--primary);
    }
    .academic-calendar-table .month-cell {
        font-weight: bold;
        width: 10%;
        background-color: #f8f9fa;
    }
    .academic-calendar-table .date-cell {
        width: 20%;
    }
    .academic-calendar-table .day-cell {
        width: 20%;
    }
    .academic-calendar-table .description-cell {
        width: 50%;
    }
    .page-content .calendar-title {
        text-align: center;
        font-size: 2rem;
        font-weight: bold;
        color: #000;
        margin-bottom: 0.5rem;
    }
    .page-content .calendar-subtitle {
        text-align: center;
        font-size: 1.5rem;
        font-weight: normal;
        margin-bottom: 2rem;
        color: #333;
    }
</style>

<section id="academic-calendar-page" class="page-content">
    <h2 class="calendar-title">ACADEMIC CALENDAR</h2>
    <h3 class="calendar-subtitle">Spring 2024</h3>
    
    <table class="academic-calendar-table">
        <thead>
            <tr>
                <th colspan="2">2024</th>
                <th>Day</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($calendar_by_month as $month => $events): ?>
                <?php foreach ($events as $index => $event): ?>
                    <tr>
                        <?php if ($index === 0): ?>
                            <td class="month-cell" rowspan="<?php echo count($events); ?>"><?php echo $month; ?></td>
                        <?php endif; ?>
                        
                        <td class="date-cell"><?php echo format_event_date($event); ?></td>
                        <td class="day-cell"><?php /* Day is already in date cell, can be left blank or used differently */ ?></td>
                        <td class="description-cell"><?php echo htmlspecialchars($event['title']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>