<!-- SheetJS for Excel/CSV -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<!-- jsPDF for PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<script>
    /**
     * Export HTML Table to various formats
     * @param {string} tableId - ID of the table to export
     * @param {string} format - 'excel', 'csv', or 'pdf'
     * @param {string} filename - Base filename for the download
     * @param {Array} columnsToIgnore - Array of column indices (0-based) to ignore
     */
    function exportTable(tableId, format, filename = 'export', columnsToIgnore = []) {
        const table = document.getElementById(tableId);
        if (!table) {
            alert('Table not found!');
            return;
        }

        const dateStr = new Date().toISOString().split('T')[0];
        const fullFilename = `${filename}_${dateStr}`;

        if (format === 'excel' || format === 'csv') {
            // Clone table to modify for export (remove hidden/ignored columns)
            const wb = XLSX.utils.table_to_book(table, {
                sheet: "Sheet1",
                display: true, // adhere to CSS display:none
                raw: true // keep numbers as numbers
            });

            if (format === 'excel') {
                XLSX.writeFile(wb, `${fullFilename}.xlsx`);
            } else {
                XLSX.writeFile(wb, `${fullFilename}.csv`);
            }
        } else if (format === 'pdf') {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4'); // Landscape, millimeters, A4

            // Filter columns for autoTable
            // We need to parse headers and body manually to exclude ignored columns if needed
            // But usually just hiding them via CSS 'no-print' or 'd-none' works for table_to_book
            // For autoTable, it respects the visible state if useCss: true?
            // Let's use simple html parsing

            doc.autoTable({
                html: '#' + tableId,
                theme: 'striped',
                headStyles: { fillColor: [37, 99, 235] }, // --primary-color #2563eb
                styles: { fontSize: 8 },
                excludeHiddenColumns: true,
                columns: columnsToIgnore.length > 0 ? (index) => !columnsToIgnore.includes(index) : undefined,
                didParseCell: function (data) {
                    // Remove "Action" column content if any
                    // Check if cell is in a column that should be hidden
                    if (data.column.index === table.rows[0].cells.length - 1) {
                        // Often the last column is actions
                    }
                }
            });

            doc.save(`${fullFilename}.pdf`);
        }
    }

    /**
     * Import Excel file and return JSON data
     * @param {HTMLInputElement} inputElement 
     * @param {Function} callback - Function(data, error)
     */
    function importExcel(inputElement, callback) {
        if (!inputElement.files || inputElement.files.length === 0) {
            return;
        }

        const file = inputElement.files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });

                // Assuming first sheet
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];

                // Convert to JSON
                const jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: "" }); // defval ensures empty cells are empty strings
                callback(jsonData, null);
            } catch (err) {
                callback(null, err);
            }
        };

        reader.readAsArrayBuffer(file);
    }
</script>