<template>
    <div class="pdf-modal">
        <div class="pdf-modal-header">
            <span class="pdf-modal-close" @click="$emit('close')">✕</span>
        </div>
        <div class="pdf-modal-content">
            <iframe :src="pdfUrl"></iframe>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        pdfUrl: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            pdfFile: null,
        };
    },
    created() {
        axios.get(this.pdfUrl, {responseType: 'blob'})
            .then((response) => {
                const blob = new Blob([response.data], {type: 'application/pdf'});
                this.pdfFile = URL.createObjectURL(blob);
            })
            .catch((error) => {
                console.log('error', error);
            });
    },
};
</script>

<style scoped>
.pdf-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.pdf-modal-header {
    width: 90%;
    display: flex;
    justify-content: flex-end;
    padding: 10px;
}

.pdf-modal-close {
    font-size: 1.5rem;
    color: #fff;
    cursor: pointer;
}

.pdf-modal-content {
    width: 90%;
    height: 90%;
    margin: 0 auto;
}

iframe {
    width: 100%;
    height: 100%;
}
</style>
