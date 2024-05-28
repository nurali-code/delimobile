const qrBtn = document.getElementById('qrBtn');
const qrAmount = document.getElementById('amount');

document.getElementById('qrCodeText').innerText = qrCode;

qrBtn.onclick = (e) => {
    e.target.disabled = true;
    navigator.clipboard.writeText(qrCode)
        .then(function () {
            setTimeout(() => { e.target.disabled = false; }, 3000);
            updateCopyCount();
        })
        .catch(function (err) {
            console.error('Failed to copy text:', err);
            e.target.disabled = false;
        });
};

document.addEventListener('DOMContentLoaded', (event) => {
    fetch('get_copy_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) { qrAmount.innerText = data.copyCount; }
            else { console.error('Failed to load copy count:', data.message); }
        }).catch(error => { console.error('Error:', error); });
});

function updateCopyCount() {
    fetch('update_copy_count.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'copy' })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) { qrAmount.innerText = data.copyCount; }
            else { console.error('Failed to update copy count:', data.message); }
        })
        .catch(error => { console.error('Error:', error); });
}