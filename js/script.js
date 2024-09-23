const qrBtn = document.getElementById('qrBtn');
const qrAmount = document.getElementById('amount');
const promocodeBadge = document.getElementById('qrCodeText')

// Функция для установки значения кеша
function cacheCopiedStatus(copiedStatus) {
    localStorage.setItem('copiedStatus', copiedStatus);
}

function getCopiedStatus() {
    const cachedData = localStorage.getItem('copiedStatus');
    if (cachedData) { return cachedData; }
    return false;
}
qrBtn.onclick = (e) => {
    e.target.disabled = true;
    navigator.clipboard.writeText(promocodeBadge.innerText)
        .then(function () {
            setTimeout(() => { e.target.disabled = false; }, 3000);
            if (!getCopiedStatus()) {
                cacheCopiedStatus(true)
                updateCopyCount(true);
            } else { updateCopyCount(false); }
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
            if (data.success) {
                promocodeBadge.classList.add('acive');
                promocodeBadge.innerText = data.promocode;
                qrAmount.innerText = data.demonstration;
                copyAmount = data.copyCount;
                copyTotal = data.main_count;
            }
            else { console.error('Failed to load copy count:', data.message); }
        }).catch(error => { console.error('Error:', error); });
});

function updateCopyCount(userStat) {
    fetch('update_copy_count.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'copy',
            newUser: userStat,
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                qrAmount.innerText = data.demonstration;
                promocodeBadge.innerText = data.promocode;
                console.log(data);
            }
            else { console.error('Failed to update copy count:', data); }
        })
        .catch(error => { console.error('Error:', error); });
}



