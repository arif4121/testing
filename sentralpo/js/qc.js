function checkExaminerInput(barcode) {
    const nameInput = document.getElementById('examiner_' + barcode);
    const receiveButton = document.getElementById('receive_' + barcode);
    const rejectButton = document.getElementById('reject_' + barcode);

    if (nameInput.value.trim() !== "") {
        receiveButton.disabled = false;
        rejectButton.disabled = false;
    } else {
        receiveButton.disabled = true;
        rejectButton.disabled = true;
    }
}