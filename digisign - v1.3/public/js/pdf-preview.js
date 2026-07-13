const pdfPreview = {
    pdf: null,
    currentPage: 1,
    totalPages: 0,
    zoom: 1,
    baseZoom: 1,
    canvas: null,
    ctx: null,
    markerPosition: null,
    isClickable: false,
    markerRadius: 15,

    init() {
        this.canvas = document.getElementById('pdf-canvas');
        this.ctx = this.canvas.getContext('2d');
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    },

    loadPDF(arrayBuffer) {
        this.init();
        pdfjsLib.getDocument(arrayBuffer).promise.then((pdf) => {
            this.pdf = pdf;
            this.totalPages = pdf.numPages;
            this.currentPage = 1;
            this.zoom = this.baseZoom;
            
            document.getElementById('pdf-container').style.display = 'block';
            document.getElementById('pdf-total-pages').textContent = this.totalPages;
            this.renderPage();
            this.attachCanvasClickListener();
        }).catch(err => {
            console.error('Error loading PDF:', err);
            alert('Gagal memuat PDF. Pastikan file valid.');
        });
    },

    renderPage() {
        if (!this.pdf) return;

        this.pdf.getPage(this.currentPage).then((page) => {
            const scale = this.zoom;
            const viewport = page.getViewport({ scale });

            this.canvas.width = viewport.width;
            this.canvas.height = viewport.height;

            const renderContext = {
                canvasContext: this.ctx,
                viewport: viewport
            };

            page.render(renderContext).promise.then(() => {
                this.drawMarker();
                document.getElementById('pdf-current-page').textContent = this.currentPage;
            });
        });
    },

    drawMarker() {
        if (!this.markerPosition || this.markerPosition.page !== this.currentPage) {
            return;
        }

        const { x, y } = this.markerPosition;
        
        this.ctx.strokeStyle = 'var(--ds-violet-600)';
        this.ctx.fillStyle = 'rgba(109, 40, 217, 0.1)';
        this.ctx.lineWidth = 2;
        
        this.ctx.beginPath();
        this.ctx.arc(x, y, this.markerRadius, 0, 2 * Math.PI);
        this.ctx.fill();
        this.ctx.stroke();

        this.ctx.fillStyle = 'var(--ds-violet-600)';
        this.ctx.font = 'bold 12px Arial';
        this.ctx.textAlign = 'center';
        this.ctx.textBaseline = 'middle';
        this.ctx.fillText('✓', x, y);
    },

    attachCanvasClickListener() {
        this.canvas.addEventListener('click', (e) => {
            if (!this.isClickable) return;

            const rect = this.canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            this.markerPosition = {
                page: this.currentPage,
                x: x,
                y: y
            };

            document.getElementById('position-info').style.display = 'block';
            document.getElementById('pos-page').textContent = this.currentPage;
            document.getElementById('pos-x').textContent = Math.round(x);
            document.getElementById('pos-y').textContent = Math.round(y);
            document.getElementById('add-signer-btn').style.display = 'block';

            this.renderPage();
        });

        this.canvas.style.cursor = this.isClickable ? 'crosshair' : 'default';
    },

    setClickable(clickable) {
        this.isClickable = clickable;
        if (this.canvas) {
            this.canvas.style.cursor = clickable ? 'crosshair' : 'default';
        }
    },

    clearMarker() {
        this.markerPosition = null;
        if (this.pdf) {
            this.renderPage();
        }
    },

    nextPage() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
            this.markerPosition = null;
            this.renderPage();
        }
    },

    previousPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.markerPosition = null;
            this.renderPage();
        }
    },

    zoomIn() {
        if (this.zoom < 3) {
            this.zoom += 0.25;
            document.getElementById('pdf-zoom-level').textContent = Math.round(this.zoom * 100);
            this.renderPage();
        }
    },

    zoomOut() {
        if (this.zoom > 0.5) {
            this.zoom -= 0.25;
            document.getElementById('pdf-zoom-level').textContent = Math.round(this.zoom * 100);
            this.renderPage();
        }
    },

    destroy() {
        this.pdf = null;
        this.currentPage = 1;
        this.totalPages = 0;
        this.zoom = this.baseZoom;
        this.markerPosition = null;
        this.isClickable = false;
        
        if (this.canvas) {
            this.canvas.width = 0;
            this.canvas.height = 0;
        }
        
        document.getElementById('pdf-container').style.display = 'none';
        document.getElementById('pdf-current-page').textContent = '0';
        document.getElementById('pdf-total-pages').textContent = '0';
        document.getElementById('pdf-zoom-level').textContent = '100';
    }
};

document.addEventListener('DOMContentLoaded', function() {
    pdfPreview.init();
});