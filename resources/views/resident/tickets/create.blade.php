<x-app-layout>
<div style="max-width: 700px; margin: 0 auto;">

    <div style="margin-bottom: 32px;">
        <a href="{{ route('tickets.index') }}" style="color: #7B746B; text-decoration: none;">← Back to Tickets</a>
        <h1 style="font-size: 28px; font-weight: 600; color: #2F2A27; margin-top: 16px;">Create Maintenance Ticket</h1>
        <p style="color: #7B746B;">Describe the issue. Photos and videos help us understand better. (Max 2MB for images, 10MB for videos)</p>
    </div>

    <div style="background: white; border-radius: 24px; padding: 32px; border: 1px solid #F0F0F0;">
        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" id="ticketForm">
            @csrf

            {{-- Title --}}
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2F2A27;">Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" 
                       style="width: 100%; padding: 14px; border: 1px solid #E5E0D8; border-radius: 16px;"
                       placeholder="Brief description of the issue">
                @error('title') <p style="color: #E74C3C; font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2F2A27;">Description *</label>
                <textarea name="description" rows="6" 
                          style="width: 100%; padding: 14px; border: 1px solid #E5E0D8; border-radius: 16px; font-family: inherit; resize: vertical;"
                          placeholder="Please provide detailed information about the issue...">{{ old('description') }}</textarea>
                @error('description') <p style="color: #E74C3C; font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
            </div>

            {{-- CATEGORY FIELD (NEW) --}}
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2F2A27;">Category *</label>
                <select name="category" id="category" required
                        style="width: 100%; padding: 14px; border: 1px solid #E5E0D8; border-radius: 16px; background: white; cursor: pointer;">
                    <option value="">Select a category</option>
                    <option value="plumbing" {{ old('category') == 'plumbing' ? 'selected' : '' }}>🚰 Plumbing (Leaks, pipes, faucets)</option>
                    <option value="electrical" {{ old('category') == 'electrical' ? 'selected' : '' }}>⚡ Electrical (Lights, outlets, wiring)</option>
                    <option value="furniture" {{ old('category') == 'furniture' ? 'selected' : '' }}>🪑 Furniture (Chairs, tables, beds, desks)</option>
                    <option value="hvac" {{ old('category') == 'hvac' ? 'selected' : '' }}>❄️ HVAC (AC, heater, ventilation)</option>
                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>📦 Other</option>
                </select>
                @error('category') <p style="color: #E74C3C; font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
            </div>

            {{-- Priority --}}
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 12px; color: #2F2A27;">Priority *</label>
                <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                    <label class="priority-pill" data-priority="low"
                           style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; border: 1px solid #E5E0D8; border-radius: 40px; cursor: pointer; transition: all 0.2s; background: {{ old('priority', 'medium') == 'low' ? '#EEF8F0' : 'transparent' }}; border-color: {{ old('priority', 'medium') == 'low' ? '#4D8C5B' : '#E5E0D8' }};">
                        <input type="radio" name="priority" value="low" {{ old('priority', 'medium') == 'low' ? 'checked' : '' }} style="display: none;">
                        <span style="font-size: 14px;">🟢 Low</span>
                    </label>
                    
                    <label class="priority-pill" data-priority="medium"
                           style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; border: 1px solid #E5E0D8; border-radius: 40px; cursor: pointer; transition: all 0.2s; background: {{ old('priority', 'medium') == 'medium' ? '#FEF8EE' : 'transparent' }}; border-color: {{ old('priority', 'medium') == 'medium' ? '#B8842F' : '#E5E0D8' }};">
                        <input type="radio" name="priority" value="medium" {{ old('priority', 'medium') == 'medium' ? 'checked' : '' }} style="display: none;">
                        <span style="font-size: 14px;">🟠 Medium</span>
                    </label>
                    
                    <label class="priority-pill" data-priority="high"
                           style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; border: 1px solid #E5E0D8; border-radius: 40px; cursor: pointer; transition: all 0.2s; background: {{ old('priority') == 'high' ? '#FFF1EE' : 'transparent' }}; border-color: {{ old('priority') == 'high' ? '#B96A5D' : '#E5E0D8' }};">
                        <input type="radio" name="priority" value="high" {{ old('priority') == 'high' ? 'checked' : '' }} style="display: none;">
                        <span style="font-size: 14px;">🔴 High</span>
                    </label>
                    
                    <label class="priority-pill" data-priority="urgent"
                           style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; border: 1px solid #E5E0D8; border-radius: 40px; cursor: pointer; transition: all 0.2s; background: {{ old('priority') == 'urgent' ? '#FFF1EE' : 'transparent' }}; border-color: {{ old('priority') == 'urgent' ? '#E74C3C' : '#E5E0D8' }};">
                        <input type="radio" name="priority" value="urgent" {{ old('priority') == 'urgent' ? 'checked' : '' }} style="display: none;">
                        <span style="font-size: 14px;">⚠️ Urgent</span>
                    </label>
                </div>
                @error('priority') <p style="color: #E74C3C; font-size: 12px; margin-top: 8px;">{{ $message }}</p> @enderror
            </div>

            {{-- Attachment Area with Paperclip Icon --}}
            <div style="margin-bottom: 24px; padding-top: 8px; border-top: 1px solid #F0F0F0;">
                <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                    <label style="cursor: pointer; display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; background: #F8F4EC; border-radius: 40px; transition: all 0.2s;"
                           onmouseover="this.style.background='#F0E8DC'"
                           onmouseout="this.style.background='#F8F4EC'">
                        <span style="font-size: 18px;">📎</span>
                        <span style="font-size: 13px; color: #5C5348;">Attach Photo/Video</span>
                        <input type="file" name="attachment" id="attachment" style="display: none;" accept="image/*,video/*">
                    </label>
                    
                    <div id="selectedFile" style="font-size: 12px; color: #7B746B; display: none;">
                        <span id="fileName"></span>
                        <button type="button" onclick="clearAttachment()" style="background: none; border: none; cursor: pointer; color: #E74C3C; margin-left: 8px;">✕</button>
                    </div>
                    
                    <input type="file" name="image" id="imageInput" style="display: none;" accept="image/*">
                    <input type="file" name="video" id="videoInput" style="display: none;" accept="video/*">
                    
                    <p style="font-size: 11px; color: #B39A78; margin-left: auto;">
                        Images (Max 2MB) | Videos (Max 10MB)
                    </p>
                </div>
            </div>

            {{-- Preview Area for Image/Video --}}
            <div id="previewArea" style="margin-bottom: 24px; display: none;">
                <div id="imagePreview" style="display: none;">
                    <img id="previewImage" style="max-width: 100%; max-height: 200px; border-radius: 12px;">
                </div>
                <div id="videoPreview" style="display: none;">
                    <video id="previewVideo" controls style="max-width: 100%; max-height: 200px; border-radius: 12px;"></video>
                </div>
            </div>

            {{-- Submit Button with Loading State --}}
            <button type="submit" id="submitBtn"
                    style="background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%); color: white; padding: 14px 28px; border-radius: 40px; font-weight: 600; border: none; cursor: pointer; width: 100%; transition: all 0.2s;">
                Submit Ticket
            </button>

            <p style="text-align: center; font-size: 12px; color: #B39A78; margin-top: 16px;">
                Your ticket will be reviewed by maintenance staff.
            </p>
        </form>
    </div>

</div>

<script>
    // Priority pill styling
    function updatePriorityStyles() {
        const selectedPriority = document.querySelector('input[name="priority"]:checked');
        const allPills = document.querySelectorAll('.priority-pill');
        
        const priorityStyles = {
            'low': { bg: '#EEF8F0', border: '#4D8C5B' },
            'medium': { bg: '#FEF8EE', border: '#B8842F' },
            'high': { bg: '#FFF1EE', border: '#B96A5D' },
            'urgent': { bg: '#FFF1EE', border: '#E74C3C' }
        };
        
        allPills.forEach(pill => {
            const radio = pill.querySelector('input[type="radio"]');
            if (radio && radio.checked) {
                const style = priorityStyles[radio.value];
                pill.style.background = style.bg;
                pill.style.borderColor = style.border;
            } else {
                pill.style.background = 'transparent';
                pill.style.borderColor = '#E5E0D8';
            }
        });
    }
    
    // Handle priority pill clicks
    document.querySelectorAll('.priority-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                updatePriorityStyles();
            }
        });
    });
    
    // Initialize styles
    updatePriorityStyles();
    
    // Handle attachment (paperclip)
    const attachmentInput = document.getElementById('attachment');
    const imageInput = document.getElementById('imageInput');
    const videoInput = document.getElementById('videoInput');
    const selectedFileDiv = document.getElementById('selectedFile');
    const fileNameSpan = document.getElementById('fileName');
    const previewArea = document.getElementById('previewArea');
    const imagePreview = document.getElementById('imagePreview');
    const videoPreview = document.getElementById('videoPreview');
    const previewImage = document.getElementById('previewImage');
    const previewVideo = document.getElementById('previewVideo');
    
    attachmentInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        // Check file size
        if (file.type.startsWith('image/') && file.size > 2 * 1024 * 1024) {
            alert('Image must be less than 2MB');
            attachmentInput.value = '';
            return;
        }
        if (file.type.startsWith('video/') && file.size > 10 * 1024 * 1024) {
            alert('Video must be less than 10MB');
            attachmentInput.value = '';
            return;
        }
        
        imageInput.value = '';
        videoInput.value = '';
        imagePreview.style.display = 'none';
        videoPreview.style.display = 'none';
        
        fileNameSpan.textContent = file.name;
        selectedFileDiv.style.display = 'block';
        previewArea.style.display = 'block';
        
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                imagePreview.style.display = 'block';
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                imageInput.files = dataTransfer.files;
            };
            reader.readAsDataURL(file);
        } else if (file.type.startsWith('video/')) {
            const url = URL.createObjectURL(file);
            previewVideo.src = url;
            videoPreview.style.display = 'block';
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            videoInput.files = dataTransfer.files;
        }
    });
    
    function clearAttachment() {
        attachmentInput.value = '';
        imageInput.value = '';
        videoInput.value = '';
        selectedFileDiv.style.display = 'none';
        previewArea.style.display = 'none';
        imagePreview.style.display = 'none';
        videoPreview.style.display = 'none';
        previewImage.src = '';
        previewVideo.src = '';
    }
    
    // Form validation with loading state
    document.getElementById('ticketForm').addEventListener('submit', function(e) {
        const title = document.querySelector('input[name="title"]').value.trim();
        const description = document.querySelector('textarea[name="description"]').value.trim();
        const category = document.getElementById('category').value;
        const submitBtn = document.getElementById('submitBtn');
        
        if (!title) {
            e.preventDefault();
            alert('Please enter a title for your ticket.');
        } else if (!description) {
            e.preventDefault();
            alert('Please describe the issue.');
        } else if (!category) {
            e.preventDefault();
            alert('Please select a category for your issue.');
        } else {
            // Show loading state
            submitBtn.innerHTML = '⏳ Submitting...';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
        }
    });
</script>
</x-app-layout>