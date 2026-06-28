// static/js/main.js

const uploadContainer    = document.getElementById('uploadContainer');
const inputFile          = document.getElementById('inputFile');
const previewSection     = document.getElementById('previewSection');
const mediaWrapper       = document.getElementById('mediaWrapper');
const loadingOverlay     = document.getElementById('loadingOverlay');
const alertContainer     = document.getElementById('alertContainer');
const detectionTimeline  = document.getElementById('detectionTimeline');
const detectionStats     = document.getElementById('detectionStats');
const detectionLog       = document.getElementById('detectionLog');
const confidenceSlider   = document.getElementById('confidenceSlider');
const confidenceValue    = document.getElementById('confidenceValue');
const processingModal    = new bootstrap.Modal(document.getElementById('processingModal'), { keyboard: false });
const processingProgress = document.getElementById('processingProgress');
const processingStatus   = document.getElementById('processingStatus');

let currentProcessId, pollingInterval, mediaType, mediaElement;

document.addEventListener('DOMContentLoaded', () => {
  confidenceSlider.addEventListener('input', function() {
    confidenceValue.textContent = `${this.value}%`;
  });
});

uploadContainer.addEventListener('click', () => inputFile.click());
['dragover','dragleave','drop'].forEach(evt =>
  uploadContainer.addEventListener(evt, e => {
    e.preventDefault(); e.stopPropagation();
    uploadContainer.classList.toggle('dragover', evt === 'dragover');
    if (evt === 'drop') handleFile(e.dataTransfer.files[0]);
  })
);
inputFile.addEventListener('change', e => handleFile(e.target.files[0]));

async function handleFile(file) {
  if (!file) return;
  if (file.type.match('image/*'))      mediaType = 'image';
  else if (file.type.match('video/*')) mediaType = 'video';
  else {
    showError('Invalid file type. Please upload an image or video.');
    return;
  }

  const ext = file.name.split('.').pop().toLowerCase();
  const valid = { image:['jpg','jpeg','png'], video:['mp4','avi','mov'] };
  if (!valid[mediaType].includes(ext)) {
    showError(`Invalid ${mediaType} format. Supported: ${valid[mediaType].join(', ')}`);
    return;
  }

  if (file.size > 16 * 1024 * 1024) {
    showError('File too large. Maximum size is 16MB.');
    return;
  }

  const form = new FormData();
  form.append(mediaType, file);
  form.append('confidence', confidenceSlider.value / 100);

  try {
    if (mediaType === 'video') await processVideo(form, file.name);
    else                       await processImage(form);
  } catch (err) {
    showError(err.message || 'An error occurred during processing');
  }
}

async function processVideo(form, filename) {
  showLoading();
  try {
    const res  = await fetch('/upload_video', { method:'POST', body: form });
    const data = await res.json();
    if (data.error) throw new Error(data.error);

    currentProcessId = data.process_id;
    processingStatus.textContent = `Processing ${filename}...`;
    processingProgress.style.width = '0%';
    processingModal.show();
    startPolling(currentProcessId);

  } catch (err) {
    processingModal.hide();
    hideLoading();
    showError(err.message || 'Failed to upload video');
  }
}

async function processImage(form) {
  showLoading();
  try {
    const res = await fetch('/upload_image', { method:'POST', body: form });
    if (!res.ok) {
      const text = await res.text();
      throw new Error(text || `Server error: ${res.status}`);
    }
    const data = await res.json();
    if (data.error) throw new Error(data.error);
    displayResults(data, 'image');
  } catch (err) {
    showError(err.message || 'Failed to process image');
  } finally {
    hideLoading();
  }
}

function startPolling(pid) {
  clearInterval(pollingInterval);
  pollingInterval = setInterval(async () => {
    try {
      const res  = await fetch(`/status/${pid}`);
      const data = await res.json();
      if (data.error) {
        if (data.error === 'Process not found') {
          clearInterval(pollingInterval);
          processingModal.hide();
          fetchAndDisplayResults(pid);
          return;
        }
        throw new Error(data.error);
      }
      processingProgress.style.width = `${data.progress}%`;
      processingStatus.textContent = `Processing... ${Math.round(data.progress)}%`;
      if (data.status === 'completed') {
        clearInterval(pollingInterval);
        processingModal.hide();
        fetchAndDisplayResults(pid);
      }
    } catch (err) {
      clearInterval(pollingInterval);
      processingModal.hide();
      hideLoading();
      showError(err.message || 'Processing error');
    }
  }, 1000);
}

async function fetchAndDisplayResults(pid) {
  try {
    const ts  = Date.now();
    const res = await fetch(`/results/${pid}?t=${ts}`);
    if (!res.ok) {
      const errData = await res.json().catch(() => ({ error: `Server returned ${res.status}` }));
      throw new Error(errData.error || `Server returned ${res.status}`);
    }
    const data = await res.json();
    if (data.error) throw new Error(data.error);
    
    // Debug: log the response
    console.log("Results response:", data);
    
    displayResults(data, 'video');
  } catch (err) {
    console.error("Error fetching results:", err);
    
    if (err.message.includes('not found')) {
      // fallback: display video only
      const videoUrl = `/static/results/annot_${pid}.mp4`;
      console.log("Trying fallback URL:", videoUrl);
      displayResults({ video_url: videoUrl, detections: [] }, 'video');
    } else {
      showError(err.message || 'Failed to fetch results');
    }
  } finally {
    hideLoading();
  }
}

function displayResults(data, type) {
  previewSection.classList.remove('d-none');
  mediaWrapper.innerHTML = '';
  
  console.log("Displaying results:", type, data);

  // Get the URL
  let mediaUrl = type === 'video' ? data.video_url : data.image_url;
  
  // Debug: log the URL
  console.log("Media URL:", mediaUrl);
  
  if (!mediaUrl) {
    showError("Missing media URL in response");
    return;
  }
  
  let media;
  if (type === 'video') {
    media = document.createElement('video');
    media.controls = true;
    media.autoplay = false;
    media.muted = false;
    media.preload = "auto";
    media.playsinline = true;
    media.crossOrigin = "anonymous";
    
    // Set type attribute explicitly to help the browser
    const source = document.createElement('source');
    source.src = mediaUrl;
    source.type = "video/mp4";
    media.appendChild(source);
    
    media.id = 'mediaDisplay';
    media.className = 'w-100 rounded';
    
    // Add event listeners to help debug
    media.addEventListener('error', (e) => {
      console.error("Video error:", e);
      const errMsg = e.target.error ? e.target.error.message : "Unknown error";
      showError(`Video loading error: ${errMsg}`);
    });
    
    media.addEventListener('loadedmetadata', () => {
      console.log("Video metadata loaded, duration:", media.duration);
      renderTimeline(data.detections || [], media.duration);
      detectionTimeline.classList.remove('d-none');
    });
    
    media.addEventListener('timeupdate', () => {
      updateTimelineProgress(media.currentTime, media.duration);
      highlightCurrentDetections(data.detections || [], media.currentTime);
    });
    
    // Add play button for better UX
    const playButton = document.createElement('button');
    playButton.className = 'btn btn-primary play-button';
    playButton.innerHTML = '<i class="fas fa-play"></i> Play Video';
    playButton.addEventListener('click', () => {
      media.play();
      playButton.style.display = 'none';
    });
    
    // Wait a moment and try to trigger play
    setTimeout(() => {
      try {
        const playPromise = media.play();
        if (playPromise !== undefined) {
          playPromise.catch(error => {
            console.log("Auto-play prevented:", error);
            // Show play button if autoplay is prevented
            mediaWrapper.appendChild(playButton);
          });
        }
      } catch (e) {
        console.error("Error trying to play video:", e);
      }
    }, 1000);
  } else {
    media = document.createElement('img');
    media.src = mediaUrl;
    media.id = 'mediaDisplay';
    media.className = 'w-100 rounded';
    
    media.addEventListener('error', (e) => {
      console.error("Image error:", e);
      showError("Failed to load image");
    });
    
    detectionTimeline.classList.add('d-none');
  }

  mediaElement = media;
  mediaWrapper.appendChild(media);
  
  console.log("Media element added to DOM:", media);

  renderStats(data.detections || []);
  logDetections(data.detections || []);
  
  // Force a layout recalculation
  setTimeout(() => {
    window.dispatchEvent(new Event('resize'));
  }, 100);
}

function renderTimeline(dets, duration) {
  if (!duration || duration <= 0) duration = 1;
  detectionTimeline.innerHTML = '';
  const prog = document.createElement('div');
  prog.className = 'timeline-progress';
  detectionTimeline.append(prog);

  dets.forEach(d => {
    const m = document.createElement('div');
    m.className = 'detection-marker';
    const pos = ((d.time || 0) / duration) * 100;
    m.style.left = `${pos}%`;
    m.title = `${d.class} (${Math.round(d.confidence * 100)}%)`;
    m.dataset.time = d.time || 0;
    m.addEventListener('click', () => {
      if (mediaElement && mediaElement.tagName === 'VIDEO') {
        mediaElement.currentTime = d.time || 0;
      }
    });
    detectionTimeline.append(m);
  });

  detectionTimeline.addEventListener('click', e => {
    if (e.target === detectionTimeline && mediaElement && mediaElement.tagName === 'VIDEO') {
      const rect = detectionTimeline.getBoundingClientRect();
      const pct  = (e.clientX - rect.left) / rect.width;
      mediaElement.currentTime = pct * mediaElement.duration;
    }
  });
}

function updateTimelineProgress(currentTime, duration) {
  const prog = detectionTimeline.querySelector('.timeline-progress');
  if (prog) prog.style.width = `${(currentTime / duration) * 100}%`;
}

function highlightCurrentDetections(dets, currentTime) {
  if (!mediaElement || !dets.length) return;
  
  detectionTimeline.querySelectorAll('.detection-marker').forEach(m => m.classList.remove('active'));
  const current = dets.filter(d => Math.abs((d.time || 0) - currentTime) < 0.5);
  current.forEach(d => {
    const pos = ((d.time || 0) / mediaElement.duration) * 100;
    const marker = detectionTimeline.querySelector(`.detection-marker[style*="left: ${pos}%"]`);
    if (marker) marker.classList.add('active');
  });
  
  detectionLog.querySelectorAll('.log-entry').forEach(e => e.classList.remove('bg-light'));
  current.forEach(d => {
    detectionLog.querySelectorAll('.log-entry').forEach(e => {
      if (e.dataset.time && Math.abs(parseFloat(e.dataset.time) - currentTime) < 0.5) {
        e.classList.add('bg-light');
      }
    });
  });
}

function renderStats(dets) {
  if (!Array.isArray(dets) || dets.length === 0) {
    detectionStats.innerHTML = '<li class="list-group-item">No detections found</li>';
    return;
  }
  const counts = dets.reduce((a, d) => { a[d.class] = (a[d.class] || 0) + 1; return a; }, {});
  const sorted = Object.entries(counts).sort((a, b) => b[1] - a[1]);
  detectionStats.innerHTML = sorted.map(([cls, cnt]) =>
    `<li class="list-group-item d-flex justify-content-between align-items-center">
       ${cls}<span class="badge bg-primary rounded-pill">${cnt}</span>
     </li>`
  ).join('');
  const total = Object.values(counts).reduce((a, b) => a + b, 0);
  detectionStats.innerHTML +=
    `<li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
       Total<span class="badge bg-success rounded-pill">${total}</span>
     </li>`;
}

function logDetections(dets) {
  if (!Array.isArray(dets) || dets.length === 0) {
    detectionLog.innerHTML = '<div class="log-entry text-muted">No detections found</div>';
    return;
  }
  const sorted = [...dets].sort((a, b) => {
    if (a.time !== undefined && b.time !== undefined) return a.time - b.time;
    return b.confidence - a.confidence;
  });
  detectionLog.innerHTML = sorted.map(d => {
    const ts   = d.time !== undefined
      ? `${Math.floor(d.time / 60)}:${String(Math.floor(d.time % 60)).padStart(2, '0')}`
      : '';
    const info = ts ? `[${ts}] ` : '';
    const pct  = Math.round(d.confidence * 100);
    const badge = pct > 75 ? 'bg-success' : pct > 50 ? 'bg-info' : 'bg-warning';
    return `<div class="log-entry" data-time="${d.time || 0}">
              ${info}<strong>${d.class}</strong>
              <span class="float-end badge ${badge}">${pct}%</span>
            </div>`;
  }).join('');
  detectionLog.scrollTop = detectionLog.scrollHeight;
}

function showLoading() { loadingOverlay.classList.remove('d-none'); }
function hideLoading() { loadingOverlay.classList.add('d-none'); }

function showError(message) {
  console.error("Error:", message);
  const tpl = document.getElementById('errorTemplate');
  const alert = tpl.content.cloneNode(true);
  alert.querySelector('.error-message').textContent = message;
  const el = alert.firstElementChild;
  alertContainer.appendChild(el);
  setTimeout(() => {
    el.classList.remove('show');
    setTimeout(() => el.remove(), 500);
  }, 5000);
}