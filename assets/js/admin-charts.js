// Chart initialization functions
function initializeUserActivityCharts() {
    const ctx = document.getElementById('mainChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: reportData.map(row => row.date),
            datasets: [{
                label: 'New Users',
                data: reportData.map(row => row.new_users),
                borderColor: '#4F46E5',
                tension: 0.4
            }, {
                label: 'Active Users',
                data: reportData.map(row => row.active_users),
                borderColor: '#10B981',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'User Activity Overview'
                }
            }
        }
    });
}

function initializeMatchSuccessCharts() {
    const ctx = document.getElementById('mainChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: reportData.map(row => row.date),
            datasets: [{
                label: 'Successful Matches',
                data: reportData.map(row => row.successful_matches),
                backgroundColor: '#4F46E5'
            }, {
                label: 'Total Attempts',
                data: reportData.map(row => row.total_attempts),
                backgroundColor: '#10B981'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Match Success Rate'
                }
            }
        }
    });
}

function initializeDemographicsCharts() {
    const ctx = document.getElementById('mainChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: reportData.map(row => row.category),
            datasets: [{
                data: reportData.map(row => row.count),
                backgroundColor: [
                    '#4F46E5',
                    '#10B981',
                    '#F59E0B',
                    '#EF4444',
                    '#8B5CF6'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'User Demographics'
                }
            }
        }
    });
}

function initializeEngagementCharts() {
    const ctx = document.getElementById('mainChart').getContext('2d');
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Profile Completion', 'Daily Logins', 'Messages Sent', 'Matches Made', 'Profile Views'],
            datasets: [{
                label: 'User Engagement Metrics',
                data: reportData.map(row => row.value),
                backgroundColor: 'rgba(79, 70, 229, 0.2)',
                borderColor: '#4F46E5',
                pointBackgroundColor: '#4F46E5'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'User Engagement Overview'
                }
            }
        }
    });
} 