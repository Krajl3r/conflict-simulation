document.addEventListener('DOMContentLoaded', function() {
    // Event listener for scenario form submission
    const scenarioForm = document.querySelector('form[action="create_scenario_action.php"]');
    if (scenarioForm) {
        scenarioForm.addEventListener('submit', function(event) {
            // Simple form validation
            const name = document.getElementById('name').value.trim();
            const theater = document.getElementById('theater').value.trim();
            if (!name || !theater) {
                event.preventDefault();
                alert('Proszę wypełnić wszystkie wymagane pola.');
            }
        });
    }

    // Event listener for dynamic unit management
    const unitForm = document.querySelector('form[action="manage_units_action.php"]');
    if (unitForm) {
        unitForm.addEventListener('submit', function(event) {
            const unitName = document.getElementById('unit_name').value.trim();
            const unitType = document.getElementById('unit_type').value.trim();
            if (!unitName || !unitType) {
                event.preventDefault();
                alert('Proszę wypełnić wszystkie wymagane pola.');
            }
        });
    }

    // Function to dynamically add units to a scenario
    const addUnitButton = document.getElementById('add_unit');
    if (addUnitButton) {
        addUnitButton.addEventListener('click', function() {
            const unitList = document.getElementById('unit_list');
            const newUnit = document.createElement('li');
            newUnit.textContent = `Jednostka ${unitList.children.length + 1}`;
            unitList.appendChild(newUnit);
        });
    }

    // Real-time battle simulation progress
    const startSimulationButton = document.getElementById('start_simulation');
    if (startSimulationButton) {
        startSimulationButton.addEventListener('click', function() {
            simulateBattle();
        });
    }

    function simulateBattle() {
        const progressBar = document.getElementById('simulation_progress');
        let progress = 0;
        const simulationInterval = setInterval(function() {
            if (progress < 100) {
                progress += 10;
                progressBar.style.width = `${progress}%`;
                progressBar.textContent = `${progress}%`;
            } else {
                clearInterval(simulationInterval);
                alert('Symulacja zakończona.');
            }
        }, 1000);
    }

    // Analysis and report generation
    const generateReportButton = document.getElementById('generate_report');
    if (generateReportButton) {
        generateReportButton.addEventListener('click', function() {
            generateReport();
        });
    }

    function generateReport() {
        const reportContent = document.getElementById('report_content');
        reportContent.innerHTML = '<p>Generowanie raportu...</p>';
        setTimeout(function() {
            reportContent.innerHTML = '<p>Raport gotowy!</p>';
        }, 2000);
    }
    document.addEventListener('DOMContentLoaded', function() {
        // Event listener for add unit form submission
        const unitForm = document.querySelector('#unitForm');
        if (unitForm) {
            unitForm.addEventListener('submit', function(event) {
                // Simple form validation
                const unitName = document.getElementById('unit_name').value.trim();
                const unitType = document.getElementById('unit_type').value.trim();
                if (!unitName || !unitType) {
                    event.preventDefault();
                    alert('Proszę wypełnić wszystkie wymagane pola.');
                }
            });
        }
    
        // Event listener for edit unit form submission
        const editUnitForm = document.querySelector('#editUnitForm');
        if (editUnitForm) {
            editUnitForm.addEventListener('submit', function(event) {
                const unitName = document.getElementById('edit_unit_name').value.trim();
                const unitType = document.getElementById('edit_unit_type').value.trim();
                if (!unitName || !unitType) {
                    event.preventDefault();
                    alert('Proszę wypełnić wszystkie wymagane pola.');
                }
            });
        }
    
        // Event listener for delete unit form submission
        const deleteUnitForm = document.querySelector('#deleteUnitForm');
        if (deleteUnitForm) {
            deleteUnitForm.addEventListener('submit', function(event) {
                const unitId = document.getElementById('delete_unit_id').value.trim();
                if (!unitId) {
                    event.preventDefault();
                    alert('Proszę podać ID jednostki.');
                }
            });
        }
    });
});
