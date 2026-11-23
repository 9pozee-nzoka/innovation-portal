# Attachment Preview System

## Overview
A unified JavaScript-based attachment preview system that works across the entire Innovation Portal. Supports multiple file formats including images, PDFs, Office documents, text files, audio, and video.

## Features

### Supported File Formats

#### ✅ **Images**
- JPG, JPEG, PNG, GIF, BMP, WebP, SVG
- Full-size preview with zoom capability
- High-quality rendering

#### ✅ **PDF Documents**
- Native browser PDF viewer
- Scroll, zoom, and search functionality
- Full document navigation

#### ✅ **Microsoft Office Documents**
- Word (.doc, .docx)
- Excel (.xls, .xlsx)
- PowerPoint (.ppt, .pptx)
- Uses Microsoft Office Online Viewer for public URLs
- Fallback to download/open in new tab for local development

#### ✅ **Text Files**
- TXT, CSV, LOG, MD (Markdown)
- JSON, XML, HTML, CSS, JS
- Syntax-highlighted code display
- Read-only preview with copy capability

#### ✅ **Audio Files**
- MP3, WAV, OGG, M4A
- Inline audio player with controls
- Playback, pause, volume, and seek

#### ✅ **Video Files**
- MP4, WebM, OGG, MOV
- Inline video player with controls
- Full-screen capability

#### ⚠️ **Unsupported Formats**
- ZIP, RAR (Archives)
- EXE, DLL (Executables)
- For these, the system shows "Download" and "Open in New Tab" options

## Installation

### 1. Include the Script
The script is already included in the main app layout (`resources/views/layouts/app.blade.php`):

```html
<script src="{{ asset('js/attachment-preview.js') }}"></script>
```

### 2. Add Preview Button
To add a preview button for an attachment, use this HTML:

```html
<button 
    onclick="previewAttachment('{{ $attachment->url() }}', '{{ $attachment->original_name }}', '{{ $attachment->mime_type }}')" 
    class="px-2.5 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-700"
    title="Preview">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
    </svg>
</button>
```

## JavaScript API

### Main Function
```javascript
previewAttachment(url, filename, mimeType)
```

**Parameters:**
- `url` (string): Full or relative URL to the attachment
- `filename` (string): Original filename with extension
- `mimeType` (string): MIME type of the file

**Example:**
```javascript
previewAttachment(
    '/storage/attachments/document.pdf',
    'Innovation_Proposal.pdf',
    'application/pdf'
);
```

### Close Function
```javascript
closeAttachmentPreview()
```

Programmatically closes the preview modal.

## Usage Examples

### In Blade Templates

**Example 1: Simple Preview Button**
```blade
@foreach($attachments as $attachment)
    <button onclick="previewAttachment('{{ $attachment->url() }}', '{{ $attachment->original_name }}', '{{ $attachment->mime_type }}')">
        Preview
    </button>
@endforeach
```

**Example 2: With Icon Button**
```blade
<button 
    onclick="previewAttachment('{{ $att->url() }}', '{{ $att->original_name }}', '{{ $att->mime_type }}')" 
    class="inline-flex items-center px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded hover:bg-emerald-700">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
    </svg>
    View
</button>
```

**Example 3: With Download Button**
```blade
<div class="flex gap-2">
    <!-- Preview Button -->
    <button onclick="previewAttachment('{{ $att->url() }}', '{{ $att->original_name }}', '{{ $att->mime_type }}')" 
            class="px-2.5 py-1 bg-emerald-600 text-white rounded">
        Preview
    </button>
    
    <!-- Download Button -->
    <a href="{{ $att->url() }}" download="{{ $att->original_name }}" 
       class="px-2.5 py-1 bg-gray-600 text-white rounded">
        Download
    </a>
</div>
```

## Modal Features

### Keyboard Shortcuts
- **ESC** - Close the preview modal
- Works globally when modal is open

### Click Outside
- Clicking the dark overlay outside the modal closes it

### Download Button
- Always visible in the modal header
- Downloads the original file

### Close Button
- X button in the top-right corner
- Closes the modal

## Styling

### Color Scheme
The preview system uses the application's KSG color palette:
- **Primary (Green)**: `bg-emerald-600` for preview buttons
- **Download**: `bg-gray-600` for download buttons
- **Hover States**: Darker shades on hover

### Responsive Design
- Mobile-friendly modal sizing
- Touch-friendly buttons
- Responsive iframe sizing

## Browser Compatibility

### Fully Supported
- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)

### Partial Support
- ⚠️ Internet Explorer 11 (basic functionality only)

## Troubleshooting

### Office Documents Not Previewing

**Issue:** Word/Excel/PowerPoint documents show "Opening document..." fallback.

**Causes:**
1. Files are on localhost (127.0.0.1) - Microsoft Office Viewer requires public URLs
2. CORS restrictions
3. File is too large (>10MB)

**Solutions:**
1. For production: Ensure files are publicly accessible
2. For development: Use the "Download" or "Open in New Tab" fallback
3. Configure CORS headers if needed

### PDFs Not Displaying

**Issue:** PDF shows blank screen.

**Solution:**
1. Check if PDF is corrupted
2. Try opening in new tab
3. Ensure browser's PDF viewer is enabled

### Images Not Loading

**Issue:** Images don't display in preview.

**Solution:**
1. Check file URL is correct
2. Verify file permissions
3. Check browser console for errors

## Performance

### File Size Limits
- **Images**: Recommended <5MB
- **PDFs**: Recommended <10MB
- **Office Docs**: Recommended <10MB
- **Text Files**: Recommended <1MB

### Loading Times
- Images: Instant
- PDFs: 1-3 seconds
- Office Documents: 3-10 seconds (depends on Microsoft's viewer)
- Text Files: Instant

## Security

### XSS Protection
- All text content is HTML-escaped
- User-generated content is sanitized

### MIME Type Validation
- File type is determined by both MIME type and extension
- Multiple validation layers

## Future Enhancements

Planned features:
- [ ] Multi-file gallery mode
- [ ] Image zoom and pan
- [ ] PDF page thumbnails
- [ ] Search within documents
- [ ] Print directly from preview
- [ ] Share link generation

## Support

For issues or questions:
1. Check browser console for errors
2. Verify file format is supported
3. Test with different file types
4. Check network tab for failed requests

## Credits

Built for Kenya School of Government Innovation Portal
Uses Microsoft Office Online Viewer API for Office document preview
