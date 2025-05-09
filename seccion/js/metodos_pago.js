function mostrarInfoPago() {
    var metodo = document.getElementById('metodo_pago').value;
    document.getElementById('info_efectivo').style.display = (metodo == 'efectivo') ? 'block' : 'none';
    document.getElementById('info_transferencia').style.display = (metodo == 'transferencia') ? 'block' : 'none';
}
function disableSubmitButton() {
    var submitButton = document.querySelector('input[type="submit"]');
    submitButton.disabled = true;
    return true;
}
