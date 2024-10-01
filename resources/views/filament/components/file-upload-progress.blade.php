<!-- resources/views/filament/components/file-upload-progress.blade.php -->
<div id="progress-bar-container" style="">
    <progress id="upload-progress-bar" value="0" max="100" style="width: 100%;"></progress>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileUploadElement = document.getElementById('file-upload');
        const progressBarContainer = document.getElementById('progress-bar-container');
        const progressBar = document.getElementById('upload-progress-bar');

        if (fileUploadElement) {
            fileUploadElement.addEventListener('change', function () {
                const file = fileUploadElement.files[0];
                const formData = new FormData();
                formData.append('file', file);

                // Configura a barra de progresso para começar
                progressBarContainer.style.display = 'block';
                progressBar.value = 0;

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/upload-endpoint'); // Rota onde o upload será processado
                xhr.upload.onprogress = function (event) {
                    if (event.lengthComputable) {
                        const percentComplete = (event.loaded / event.total) * 100;
                        progressBar.value = percentComplete;
                    }
                };

                xhr.onload = function () {
                    if (xhr.status === 200) {
                        alert('Upload completo');
                        progressBarContainer.style.display = 'none';
                    } else {
                        alert('Erro no upload');
                    }
                };

                xhr.send(formData);
            });
        }
    });
</script>
