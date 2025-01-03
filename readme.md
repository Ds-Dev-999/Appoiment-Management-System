# Change gamil username and password in .env file 

# Change Add Appoiment and Edit Appoiment Message in add_appoiment.php and edit_appoiment.php
    Code line 30



/* General Styles */
body {
    background-color: #121212;
    font-family: Arial, sans-serif;
    color: #ffffff;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Header Styles */
.header {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #000000;
    color: #ffffff;
    padding: 1rem;
    border-radius: 5px;
    width: 100%;
    margin-bottom: 1.5rem;
}

.header h2 {
    text-align: center;
    font-size: 24px;
    margin: 0;
}

/* Button Styles */
.header .btn {
    margin-left: 0.5rem;
    background-color: #ff4c4c;
    color: #ffffff;
    font-weight: bold;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.header .btn:hover {
    background-color: #cc3c3c;
}

/* Filter Section */
.filter-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    margin-top: 1.5rem;
    padding: 1rem;
    background-color: #333333;
    border-radius: 5px;
    width: 100%;
}

.filter-container label {
    font-weight: bold;
    margin-right: 0.5rem;
    color: #ff4c4c;
}

.filter-container input[type="date"],
.filter-container input[type="text"],
.filter-container select {
    margin-bottom: 1rem;
    background-color: #555555;
    color: #ffffff;
    border: none;
    padding: 0.5rem;
    border-radius: 5px;
}

/* Table Styles */
.table-container {
    width: 100%;
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    background-color: #2c2c2c;
    color: #ffffff;
    margin-top: 1.5rem;
}

.table thead th {
    text-align: center;
    background-color: #000000;
    color: #ff4c4c;
    padding: 1rem;
    font-weight: bold;
}

.table tbody td {
    text-align: center;
    vertical-align: middle;
    padding: 0.75rem;
    color: #ffffff;
    border-top: 1px solid #444444;
}

/* Action Buttons */
.table tbody tr td .action-buttons {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
}

.table .btn-warning {
    background-color: #007bff;
    color: #ffffff;
    font-weight: bold;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.table .btn-danger {
    background-color: #b30000;
    color: #ffffff;
    font-weight: bold;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.table .btn-warning:hover {
    background-color: #0056b3;
}

.table .btn-danger:hover {
    background-color: #8b0000;
}

/* Chart Wrapper */
.chart-wrapper {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 1rem;
    width: 100%;
    margin-top: 1.5rem;
}

.chart-container {
    flex: 1;
    padding: 1rem;
    background-color: #222222;
    border: 1px solid #444444;
    border-radius: 5px;
    min-width: 300px;
}

/* Progress Bar */
.progress {
    height: 20px;
    background-color: #333333;
    border-radius: 5px;
}

.progress-bar {
    background-color: #ff4c4c;
    font-weight: bold;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .filter-container, .chart-wrapper, .table-container, .header {
        width: 100%;
        padding: 0;
    }

    .filter-container, .table-container {
        overflow-x: auto;
    }

    .table tbody td {
        font-size: 0.9rem;
        padding: 0.5rem;
    }

    .action-buttons {
        flex-direction: row;
    }
}
