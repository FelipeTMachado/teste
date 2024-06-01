// Função para criar e mostrar o toast
function showToast(message, type) {
    const toastContainer = document.getElementById("toast-container");
    const toast = document.createElement("div");
    toast.className = `toast show-toast ${type}`;
    toast.textContent = message;
    toastContainer.appendChild(toast);

    // Após 3 segundos, ocultar o toast
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

// Verifica se há uma mensagem de erro definida
if (typeof erroMsg !== 'undefined') {
    showToast(erroMsg, "toast-error");
    erroMsg = 'undefined';
}

// Verifica se há uma mensagem de erro definida
if (typeof infoMsg !== 'undefined') {
    showToast(infoMsg, "toast-info");
    infoMsg = 'undefined';
}

// Verifica se há uma mensagem de erro definida
if (typeof warningMsg !== 'undefined') {
    showToast(warningMsg, "toast-warning");
    warningMsg = 'undefined';
}

// Verifica se há uma mensagem de erro definida
if (typeof successMsg !== 'undefined') {
    showToast(successMsg, "toast-success");
    successMsg = 'undefined';
}