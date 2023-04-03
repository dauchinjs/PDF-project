<template>
    <div class="pdf-page">
        <div class="pdf-header">
            <primary-button class="add-pdf-button" @click="openCreateModal">Add new document</primary-button>
        </div>
        <div class="pdf-gallery">
            <div v-for="(pdf, index) in PDFs" :key="index" class="pdf-item">
                <div class="pdf-image">
                    <img :src="pdf.thumbnail" alt="pdf.title" @click="openModal(pdf.id)"/>
                </div>
                <div class="pdf-title">
                    <p>{{ pdf.title }}</p>
                    <danger-button @click="deletePDF(pdf.id)">Delete</danger-button>
                </div>
            </div>
        </div>
        <PdfModal :pdf-url="pdfUrl" v-if="pdfUrl" @close="closeModal"/>
        <CreatePdfModal :visible="showCreateModal" @close="closeCreateModal" @document-added="fetchDocuments"/>
        <div class="pdf-footer">
            <div class="pagination">
                <primary-button @click="previousPage" :disabled="pagination.current_page <= 1">
                    Previous Page
                </primary-button>
                <primary-button @click="nextPage" :disabled="!pagination.next_page_url">
                    Next Page
                </primary-button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import PdfModal from '../Components/PdfModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import CreatePdfModal from '@/Components/CreatePdfModal.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";

export default {
    components: {
        PrimaryButton,
        DangerButton,
        PdfModal,
        CreatePdfModal,
    },
    data() {
        return {
            PDFs: [],
            pdfUrl: null,
            showCreateModal: false,
            pagination: {
                previous_page_url: null,
                next_page_url: null,
                per_page: null,
                current_page: 1,
            },
        };
    },
    created() {
        this.fetchDocuments();
    },
    methods: {
        openModal(id) {
            this.pdfUrl = `/api/documents/${id}`;
        },
        closeModal() {
            this.pdfUrl = null;
        },
        fetchDocuments() {
            axios.get(`/api/documents`, {
                params: {
                    page: this.pagination.current_page,
                },
            })
                .then((response) => {
                    console.log('response', response);
                    this.PDFs = response.data.data;
                    this.pagination.current_page = response.data.current_page;
                    this.pagination.previous_page_url = response.data.previous_page_url;
                    this.pagination.next_page_url = response.data.next_page_url;
                })
                .catch((error) => {
                    console.log('error', error);
                });
        },
        previousPage() {
            if (this.pagination.previous_page_url) {
                this.pagination.current_page--;
                this.fetchDocuments();
            } else if (this.pagination.current_page > 1) {
                this.pagination.current_page--;
                this.pagination.previous_page_url = `/api/documents?page=${this.pagination.current_page - 1}`;
                this.fetchDocuments();
            }
        },
        nextPage() {
            if (this.pagination.next_page_url) {
                this.pagination.current_page++;
                this.fetchDocuments();
            }
        },
        deletePDF(id) {
            axios
                .delete(`/api/documents/${id}`)
                .then((response) => {
                    console.log('response', response);
                    this.PDFs = this.PDFs.filter((pdf) => pdf.id !== id);
                })
                .catch((error) => {
                    console.log('error', error);
                });
        },
        openCreateModal() {
            this.showCreateModal = true;
        },
        closeCreateModal() {
            this.showCreateModal = false;
        },
    },
};
</script>

<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background-color: #f1f1f1;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.add-pdf-button {
    margin: 1rem auto;
    display: block;
}

.pdf-gallery {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin: 1rem auto;
    max-width: 1000px;
}

.pdf-item {
    margin: 16px;
    text-align: center;
    width: 20%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border: 1px solid black;
    padding: 2px;
}

.pdf-image {
    height: 100%;
    width: 100%;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: transform 0.3s ease-out;
    object-fit: cover;
}

.pdf-image:hover img {
    transform: scale(1.1);
}

.pdf-title {
    margin-top: 0.5rem;
    padding: 4px;
    font-weight: bold;
    font-size: 1.2rem;
    text-align: center;
    width: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    color: #fff;
}

.pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.pagination button {
    background-color: #f1f1f1;
    border: none;
    color: black;
    padding: 8px 16px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
}

.pagination button:hover {
    background-color: #ddd;
}

.pagination button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
