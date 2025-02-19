// Simpan data history di localStorage
const saveDataToLocalStorage = (data) => {
    let history = JSON.parse(localStorage.getItem('history')) || [];
    history.push(data);
    localStorage.setItem('history', JSON.stringify(history));
};

// Ambil data history dari localStorage
const getDataFromLocalStorage = () => {
    return JSON.parse(localStorage.getItem('history')) || [];
};

// Handle submit form
document.getElementById('fileForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    saveDataToLocalStorage(data);
    alert('Data berhasil disimpan!');
    e.target.reset();
});

// Tampilkan data history di halaman history.html
const historyTable = document.getElementById('historyTable');
if (historyTable) {
    const history = getDataFromLocalStorage();
    history.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="py-2">${item.noFile}</td>
            <td class="py-2">${item.namaFile}</td>
            <td class="py-2">${item.dari}</td>
            <td class="py-2">${item.tujuan}</td>
            <td class="py-2">${item.tanggal}</td>
            <td class="py-2">${item.keterangan}</td>
        `;
        historyTable.appendChild(row);
    });
}