/**
 * Función genérica para exportar tablas HTML a Excel con diseño profesional usando ExcelJS
 * @param {string} idTabla - El ID de la tabla HTML a exportar
 * @param {string} nombreHoja - Nombre de la pestaña dentro del archivo Excel
 * @param {string} nombreArchivo - Nombre del archivo descargado (sin extensión)
 */
async function exportarTablaAExcel(idTabla, nombreHoja, nombreArchivo) {
    const tabla = document.getElementById(idTabla);
    
    if (!tabla) {
        alert('No se encontró la tabla en la vista.');
        return;
    }

    // 1. Crear el libro y la hoja de trabajo
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet(nombreHoja);

    // 2. Extraer dinámicamente las columnas de la tabla (ignorando la clase 'no-export' de acciones)
    const encabezadosRow = tabla.querySelectorAll('thead tr th');
    let columnasConfig = [];
    let indicesPermitidos = [];

    encabezadosRow.forEach((th, index) => {
        if (!th.classList.contains('no-export')) {
            columnasConfig.push({ header: th.innerText.trim(), key: 'col_' + index, width: 22 });
            indicesPermitidos.push(index);
        }
    });

    worksheet.columns = columnasConfig;

    // 3. Extraer los datos de las filas de la tabla
    const filas = tabla.querySelectorAll('tbody tr');

    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        if (celdas.length > 0 && !fila.querySelector('td[colspan]')) {
            let filaData = {};
            let contador = 0;
            
            indicesPermitidos.forEach(idxOriginal => {
                if (celdas[idxOriginal]) {
                    filaData['col_' + indicesPermitidos[contador]] = celdas[idxOriginal].innerText.trim();
                }
                contador++;
            });
            
            worksheet.addRow(filaData);
        }
    });

    // 4. Diseñar la fila de Encabezados (Fila 1)
    const headerRow = worksheet.getRow(1);
    headerRow.font = { name: 'Arial', size: 11, bold: true, color: { argb: 'FFFFFF' } };
    headerRow.fill = {
        type: 'pattern',
        pattern: 'solid',
        fgColor: { argb: '1F4E78' } // Azul corporativo
    };
    headerRow.alignment = { vertical: 'middle', horizontal: 'center' };
    headerRow.height = 25;

    // 5. Aplicar bordes, fuentes y efecto cebra a las celdas de datos
    worksheet.eachRow((row, rowNumber) => {
        row.eachCell(cell => {
            cell.border = {
                top: { style: 'thin', color: { argb: 'D9D9D9' } },
                left: { style: 'thin', color: { argb: 'D9D9D9' } },
                bottom: { style: 'thin', color: { argb: 'D9D9D9' } },
                right: { style: 'thin', color: { argb: 'D9D9D9' } }
            };
            if (rowNumber > 1) {
                cell.font = { name: 'Arial', size: 10 };
            }
        });

        // Efecto cebra en filas pares
        if (rowNumber > 1 && rowNumber % 2 === 0) {
            row.fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: { argb: 'F2F7FA' }
            };
        }
    });

    // 6. Generar archivo y forzar descarga
    const buffer = await workbook.xlsx.writeBuffer();
    const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    const fechaActual = new Date().toISOString().slice(0, 10);
    
    saveAs(blob, `${nombreArchivo}_${fechaActual}.xlsx`);
}
