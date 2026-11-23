/**
 * Attachment Preview System
 * Handles preview for all file types: Images, PDFs, Office Documents, etc.
 */

// Initialize preview modal
function initAttachmentPreview() {
    // Check if modal already exists
    if (document.getElementById('attachmentPreviewModal')) {
        return;
    }

    // Create modal HTML
    const modalHTML = `
        <div id="attachmentPreviewModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
            <div class="relative max-w-7xl w-full max-h-[95vh] bg-white rounded-lg shadow-2xl overflow-hidden flex flex-col">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50 shrink-0">
                    <h3 id="attachmentPreviewTitle" class="text-lg font-bold text-gray-900 truncate pr-4"></h3>
                    <div class="flex items-center space-x-2">
                        <a id="attachmentDownloadBtn" href="#" download class="inline-flex items-center px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded hover:bg-emerald-700 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download
                        </a>
                        <button onclick="closeAttachmentPreview()" class="text-gray-400 hover:text-gray-600 transition p-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Content -->
                <div id="attachmentPreviewContent" class="flex-1 overflow-auto bg-gray-100">
                    <!-- Content will be inserted here dynamically -->
                </div>

                <!-- Loading Spinner -->
                <div id="attachmentPreviewLoader" class="hidden absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-emerald-600 mx-auto mb-4"></div>
                        <p class="text-gray-600 font-medium">Loading preview...</p>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Append modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Add click outside to close
    document.getElementById('attachmentPreviewModal').addEventListener('click', function(e) {
        if (e.target.id === 'attachmentPreviewModal') {
            closeAttachmentPreview();
        }
    });

    // Add ESC key to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAttachmentPreview();
        }
    });
}

// Preview attachment function
function previewAttachment(url, filename, mimeType) {
    // Initialize modal if not exists
    initAttachmentPreview();

    const modal = document.getElementById('attachmentPreviewModal');
    const title = document.getElementById('attachmentPreviewTitle');
    const content = document.getElementById('attachmentPreviewContent');
    const loader = document.getElementById('attachmentPreviewLoader');
    const downloadBtn = document.getElementById('attachmentDownloadBtn');

    // Set title and download link
    title.textContent = filename;
    downloadBtn.href = url;
    downloadBtn.download = filename;

    // Show modal and loader
    modal.classList.remove('hidden');
    loader.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Clear previous content
    content.innerHTML = '';

    // Determine file type and render preview
    const extension = filename.split('.').pop().toLowerCase();
    
    setTimeout(() => {
        loader.classList.add('hidden');
        renderPreview(content, url, filename, mimeType, extension);
    }, 300);
}

// Render preview based on file type
function renderPreview(container, url, filename, mimeType, extension) {
    // Image files
    if (mimeType.startsWith('image/') || ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'].includes(extension)) {
        container.innerHTML = `
            <div class="p-8 flex items-center justify-center min-h-full">
                <img src="${url}" alt="${filename}" class="max-w-full max-h-[75vh] object-contain rounded-lg shadow-lg">
            </div>
        `;
        return;
    }

    // PDF files
    if (mimeType === 'application/pdf' || extension === 'pdf') {
        container.innerHTML = `
            <iframe src="${url}" class="w-full h-full min-h-[75vh]" frameborder="0"></iframe>
        `;
        return;
    }

    // Text files
    if (mimeType.startsWith('text/') || ['txt', 'csv', 'log', 'md', 'json', 'xml', 'html', 'css', 'js'].includes(extension)) {
        fetch(url)
            .then(response => response.text())
            .then(text => {
                container.innerHTML = `
                    <div class="p-8">
                        <pre class="bg-white p-6 rounded-lg border border-gray-300 overflow-auto text-sm font-mono whitespace-pre-wrap max-h-[70vh]">${escapeHtml(text)}</pre>
                    </div>
                `;
            })
            .catch(error => {
                showPreviewError(container, url, filename, 'Could not load text file');
            });
        return;
    }

    // Office documents (Word, Excel, PowerPoint)
    if (['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(extension)) {
        const fullUrl = url.startsWith('http') ? url : window.location.origin + url;
        const isLocalhost = window.location.hostname === 'localhost' || 
                           window.location.hostname === '127.0.0.1' || 
                           window.location.hostname === '';
        
        // For localhost, skip the viewer and show fallback immediately
        if (isLocalhost) {
            showOfficeDocumentFallback(container, url, filename, extension);
            return;
        }
        
        // For production URLs, try Microsoft Office Online Viewer
        container.innerHTML = `
            <div class="relative w-full h-full min-h-[75vh]">
                <iframe 
                    src="https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(fullUrl)}" 
                    class="w-full h-full"
                    frameborder="0"
                    onload="this.parentElement.querySelector('.office-loader')?.remove()"
                    onerror="handleOfficeViewerError(this, '${url}', '${filename}')">
                </iframe>
                <div class="office-loader absolute inset-0 flex items-center justify-center bg-white">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-4 border-emerald-600 mx-auto mb-3"></div>
                        <p class="text-gray-600 text-sm">Loading Office document...</p>
                    </div>
                </div>
            </div>
        `;
        
        // Fallback after 5 seconds if Office viewer doesn't load
        setTimeout(() => {
            const loader = container.querySelector('.office-loader');
            if (loader && loader.parentElement) {
                showOfficeDocumentFallback(container, url, filename, extension);
            }
        }, 5000);
        return;
    }

    // Video files
    if (mimeType.startsWith('video/') || ['mp4', 'webm', 'ogg', 'mov'].includes(extension)) {
        container.innerHTML = `
            <div class="p-8 flex items-center justify-center min-h-full">
                <video controls class="max-w-full max-h-[75vh] rounded-lg shadow-lg">
                    <source src="${url}" type="${mimeType || 'video/' + extension}">
                    Your browser does not support video playback.
                </video>
            </div>
        `;
        return;
    }

    // Audio files
    if (mimeType.startsWith('audio/') || ['mp3', 'wav', 'ogg', 'm4a'].includes(extension)) {
        container.innerHTML = `
            <div class="p-8 flex flex-col items-center justify-center min-h-full">
                <svg class="w-24 h-24 text-emerald-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                </svg>
                <audio controls class="w-full max-w-md">
                    <source src="${url}" type="${mimeType || 'audio/' + extension}">
                    Your browser does not support audio playback.
                </audio>
                <p class="text-gray-600 text-sm mt-4">${filename}</p>
            </div>
        `;
        return;
    }

    // Default: Show download/open options for unsupported types
    showUnsupportedPreview(container, url, filename, extension);
}

// Show Office document fallback
function showOfficeDocumentFallback(container, url, filename, extension) {
    const docTypeInfo = {
        doc: { icon: 'Word', color: 'blue', desc: 'Microsoft Word Document' },
        docx: { icon: 'Word', color: 'blue', desc: 'Microsoft Word Document' },
        xls: { icon: 'Excel', color: 'green', desc: 'Microsoft Excel Spreadsheet' },
        xlsx: { icon: 'Excel', color: 'green', desc: 'Microsoft Excel Spreadsheet' },
        ppt: { icon: 'PowerPoint', color: 'orange', desc: 'Microsoft PowerPoint Presentation' },
        pptx: { icon: 'PowerPoint', color: 'orange', desc: 'Microsoft PowerPoint Presentation' }
    };

    const info = docTypeInfo[extension] || { icon: 'Office', color: 'gray', desc: 'Office Document' };

    container.innerHTML = `
        <div class="p-8 flex flex-col items-center justify-center min-h-full text-center">
            <svg class="w-24 h-24 text-${info.color}-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-xl font-bold text-gray-900 mb-2">${info.icon} Document</h3>
            <p class="text-gray-600 mb-2 font-semibold">${filename}</p>
            <p class="text-sm text-gray-500 mb-6">${info.desc}</p>
            <p class="text-sm text-blue-600 mb-6 font-medium">
                📄 Document viewer will open in a new tab
            </p>
            <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="window.open('${url}', '_blank'); closeAttachmentPreview();" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-semibold">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    View Document
                </button>
                <a href="${url}" download="${filename}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-semibold">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download
                </a>
            </div>
            <p class="text-xs text-gray-400 mt-6">
                Office documents can be viewed in your browser or with desktop applications
            </p>
        </div>
    `;
    
    // Auto-open in new tab after 1 second
    setTimeout(() => {
        window.open(url, '_blank');
    }, 1000);
}

// Show unsupported file preview
function showUnsupportedPreview(container, url, filename, extension) {
    const extensionUpper = extension.toUpperCase();
    
    container.innerHTML = `
        <div class="p-8 flex flex-col items-center justify-center min-h-full text-center">
            <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center mb-6">
                <span class="text-2xl font-bold text-gray-600">${extensionUpper}</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Preview Not Available</h3>
            <p class="text-gray-600 mb-2">${filename}</p>
            <p class="text-sm text-gray-500 mb-6">This file type cannot be previewed in the browser</p>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="${url}" target="_blank" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-semibold">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Open in New Tab
                </a>
                <a href="${url}" download="${filename}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-semibold">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download
                </a>
            </div>
        </div>
    `;
}

// Show preview error
function showPreviewError(container, url, filename, errorMessage) {
    container.innerHTML = `
        <div class="p-8 flex flex-col items-center justify-center min-h-full text-center">
            <svg class="w-20 h-20 text-red-500 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Preview Error</h3>
            <p class="text-gray-600 mb-6">${errorMessage}</p>
            <a href="${url}" download="${filename}" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-semibold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download File
            </a>
        </div>
    `;
}

// Handle Office viewer error
function handleOfficeViewerError(iframe, url, filename) {
    const extension = filename.split('.').pop().toLowerCase();
    showOfficeDocumentFallback(iframe.parentElement.parentElement, url, filename, extension);
}

// Close preview modal
function closeAttachmentPreview() {
    const modal = document.getElementById('attachmentPreviewModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Utility function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Auto-initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAttachmentPreview);
} else {
    initAttachmentPreview();
}
