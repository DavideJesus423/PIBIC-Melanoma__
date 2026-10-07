document.addEventListener('DOMContentLoaded', function () {
    const input      = document.getElementById('imageInput');
    const btnAnalyze = document.querySelector('.btn-analyze');
    const uploadArea = document.getElementById('uploadArea');

    if (!input || !btnAnalyze) return;

    // Habilita o botão quando uma imagem é selecionada
    input.addEventListener('change', function () {
        if (input.files && input.files.length > 0) {
            btnAnalyze.disabled = false;
            btnAnalyze.style.opacity = '1';
            btnAnalyze.style.cursor  = 'pointer';

            // Mostra o nome do arquivo na área de upload
            const fileName = input.files[0].name;
            const subtitle = uploadArea.querySelector('.upload-subtitle');
            if (subtitle) {
                subtitle.textContent = `Arquivo selecionado: ${fileName}`;
                subtitle.style.color = '#198754';
                subtitle.style.fontWeight = '600';
            }
        } else {
            btnAnalyze.disabled = true;
            btnAnalyze.style.opacity = '0.45';
            btnAnalyze.style.cursor  = 'not-allowed';
        }
    });

    // Permite clicar na área de upload para abrir o seletor de arquivo
    if (uploadArea) {
        uploadArea.addEventListener('click', function (e) {
            if (e.target.tagName !== 'LABEL' && e.target.tagName !== 'INPUT') {
                input.click();
            }
        });
    }
});