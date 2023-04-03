<template>
    <div>
        <div class="modal-background" v-if="visible">
            <div class="modal-content">
                <span class="modal-close" @click="$emit('close')">✕</span>
                <div>
                    <label for="pdf-upload" class="file-upload" @dragover.prevent @drop.prevent="handleFileUpload">
                        <span v-if="!pdfFile">Drag and drop PDF here, or click to select file</span>
                        <span v-else>File Selected: {{ pdfFile.name }}</span>
                    </label>
                    <div class="warning-message">Only PDF files smaller than 100kB are accepted.</div>
                    <input ref="fileInput" id="pdf-upload" type="file" @change="handleFileUpload" style="display:none">
                    <input type="text" v-model="title" placeholder="Title">
                    <secondary-button @click="submitForm" :disabled="!pdfFile">Submit</secondary-button>
                    <danger-button v-if="pdfFile" class="remove-file-button" @click="clearFile">Remove File
                    </danger-button>
                    <div v-if="formSubmitted">
                        <p v-if="success" class="success-message">PDF uploaded successfully!</p>
                        <p v-else class="error-message">Error uploading PDF. Please try again.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

export default {
    components: {DangerButton, SecondaryButton},
    props: {
        visible: {
            type: Boolean,
            required: true,
        },
    },
    data() {
        return {
            pdfFile: null,
            title: "",
            formSubmitted: false,
            success: false
        };
    },
    methods: {
        handleFileUpload(event) {
            event.preventDefault();
            if (event.dataTransfer) {
                this.pdfFile = event.dataTransfer.files[0];
            } else {
                this.pdfFile = event.target.files[0];
            }
        },
        clearFile() {
            this.pdfFile = null;
            this.$refs.fileInput.value = "";
        },
        submitForm() {
            const formData = new FormData();
            formData.append("pdfFile", this.pdfFile);
            formData.append("title", this.title);

            axios.post("/api/documents/" + this.title, formData)
                .then(response => {
                    console.log(response);
                    this.pdfFile = null;
                    this.title = "";
                    this.success = true;
                    this.formSubmitted = true;
                    this.$emit('document-added');
                }).catch(error => {
                console.log(error);
                this.success = false;
                this.formSubmitted = true;
            });
        }
    }
};
</script>

<style scoped>
.modal-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.modal-content {
    background-color: #fff;
    padding: 2rem;
    border-radius: 0.5rem;
    box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1);
}

label.file-upload {
    display: block;
    border: 2px dashed #ccc;
    padding: 20px;
    text-align: center;
    cursor: pointer;
}

input[type="text"] {
    display: block;
    margin: 10px 0;
    padding: 10px;
    width: 100%;
    border: 1px solid #ccc;
    border-radius: 3px;
}

button {
    display: block;
    margin: 10px auto;
    padding: 10px 20px;
    cursor: pointer;
}

.modal-close {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    font-size: 1.5rem;
    color: black;
    cursor: pointer;
}

.error-message {
    color: red;
    text-align: center;
}

.success-message {
    color: green;
    text-align: center;
}

.warning-message {
    color: red;
    text-align: center;
    opacity: 50%;
    font-size: small;
    margin: 4px;
}
</style>
