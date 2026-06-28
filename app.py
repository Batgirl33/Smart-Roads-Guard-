from flask import Flask, render_template, request, jsonify, send_from_directory, url_for
import cv2
from ultralytics import YOLO
import os
import json
from datetime import datetime, timedelta
import threading
import logging
from pathlib import Path
import re
import urllib.parse
import subprocess
import shutil

# -------------------- App & Config --------------------
app = Flask(__name__)
BASE_DIR = Path(__file__).parent

app.config['UPLOAD_FOLDER']    = BASE_DIR / 'uploads'
app.config['RESULTS_FOLDER']   = BASE_DIR / 'static' / 'results'
app.config['METADATA_FOLDER']  = BASE_DIR / 'metadata'
app.config['MAX_CONTENT_LENGTH'] = 16 * 1024 * 1024  # 16 MB
app.config['CLEANUP_THRESHOLD']  = timedelta(hours=24)
app.config['MAX_PROCESSES']     = 10

MODEL_PATH = os.environ.get('MODEL_PATH', BASE_DIR / 'best.pt')
DEBUG_MODE = os.environ.get('DEBUG_MODE', 'False').lower() == 'true'
PORT       = int(os.environ.get('PORT', 5000))

# Ensure directories
os.makedirs(app.config['UPLOAD_FOLDER'],   exist_ok=True)
os.makedirs(app.config['RESULTS_FOLDER'],  exist_ok=True)
os.makedirs(app.config['METADATA_FOLDER'], exist_ok=True)

# Logging
logging.basicConfig(
    level=logging.DEBUG if DEBUG_MODE else logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler(BASE_DIR / 'app.log'),
        logging.StreamHandler()
    ]
)
logger = logging.getLogger(__name__)

# Load the YOLO model
try:
    model = YOLO(str(MODEL_PATH))
    logger.info(f"Model loaded from {MODEL_PATH}")
except Exception as e:
    logger.error(f"Failed to load model: {e}")
    model = None

# In-memory process tracking
active_processes = {}
process_lock     = threading.Lock()

# -------------------- VideoProcessor --------------------
class VideoProcessor:
    def __init__(self, path, result_path, conf=0.5):
        self.video_path    = path
        self.result_path   = result_path
        self.confidence    = conf
        self.status        = 'processing'
        self.progress      = 0
        self.detections    = []
        self.frame_count   = 0
        self.created_at    = datetime.now()
        self.last_updated  = datetime.now()
        self.error_message = None
        self.metadata_path = get_metadata_path(str(result_path))

    def process(self):
        try:
            if model is None:
                raise ValueError("Model not loaded properly")

            cap = cv2.VideoCapture(str(self.video_path))
            if not cap.isOpened():
                raise ValueError(f"Cannot open video: {self.video_path}")

            self.frame_count = int(cap.get(cv2.CAP_PROP_FRAME_COUNT))
            fps = cap.get(cv2.CAP_PROP_FPS) or 30
            w   = int(cap.get(cv2.CAP_PROP_FRAME_WIDTH))
            h   = int(cap.get(cv2.CAP_PROP_FRAME_HEIGHT))

            # Change codec from mp4v to avc1 for better browser compatibility
            fourcc = cv2.VideoWriter_fourcc(*'avc1')  # H.264 encoding

            # Fallback to mp4v if avc1 is not available
            try:
                writer = cv2.VideoWriter(str(self.result_path), fourcc, fps, (w, h))
                if not writer.isOpened():
                    # Try with mp4v as fallback
                    logger.warning("Failed to open video writer with avc1 codec, trying mp4v")
                    fourcc = cv2.VideoWriter_fourcc(*'mp4v')
                    writer = cv2.VideoWriter(str(self.result_path), fourcc, fps, (w, h))
            except Exception as e:
                logger.warning(f"Error with avc1 codec: {e}, falling back to mp4v")
                fourcc = cv2.VideoWriter_fourcc(*'mp4v')
                writer = cv2.VideoWriter(str(self.result_path), fourcc, fps, (w, h))

            if not writer.isOpened():
                raise RuntimeError("Could not initialize video writer")

            frame_no = 0
            sent_first_obstacle = False
            while True:
                ret, frame = cap.read()
                if not ret:
                    break

                res = model(frame)[0]
                annotated = res.plot()

                max_conf = 0
                best_box = None
                best_name = None

                for box in res.boxes:
                    cls   = int(box.cls)
                    conf  = float(box.conf)
                    coords = box.xyxy[0].tolist()
                    name  = res.names[cls]

                    self.detections.append({
                        'frame': frame_no,
                        'class': name,
                        'confidence': conf,
                        'box': coords,
                        'time': frame_no / fps
                    })

                    # فقط نخزن بيانات التصنيف accident_class1 الأعلى
                    if name == 'accident_class1' and conf >= 0.50 and conf > max_conf:
                        max_conf = conf
                        best_box = box
                        best_name = name

                # بعد انتهاء حلقة for نرسل الصورة إذا كان هناك box مناسب
                if best_box is not None and not sent_first_obstacle:
                    snapshot_dir = BASE_DIR / "static" / "snapshots"
                    os.makedirs(snapshot_dir, exist_ok=True)

                    snapshot_filename = f"{datetime.now().strftime('%Y%m%d_%H%M%S_%f')}_frame{frame_no}.jpg"
                    snapshot_path = snapshot_dir / snapshot_filename
                    cv2.imwrite(str(snapshot_path), frame)

                    php_image_dir = Path("C:/xampp/htdocs/GP_V3/img")
                    os.makedirs(php_image_dir, exist_ok=True)
                    shutil.copy(snapshot_path, php_image_dir / snapshot_filename)

                    data = {
                        'image_name': snapshot_filename,
                        'class_name': best_name,
                        'timestamp': datetime.now().strftime('%Y-%m-%d %H:%M:%S')
                    }

                    try:
                        import requests
                        php_url = 'http://localhost/GP_V3/includes/AddObstacle.php'
                        response = requests.post(php_url, data=data)
                        if response.status_code == 200:
                            logger.info(f"تم إرسال أفضل عائق إلى PHP: {snapshot_filename}")
                        else:
                            logger.warning(f"فشل إرسال أفضل عائق إلى PHP. الكود: {response.status_code}")
                    except Exception as e:
                        logger.error(f"خطأ أثناء إرسال أفضل عائق إلى PHP: {e}")

                    sent_first_obstacle = True





                writer.write(annotated)
                frame_no += 1
                self.progress      = min(100, frame_no / self.frame_count * 100)
                self.last_updated  = datetime.now()

                if frame_no % max(1, self.frame_count // 20) == 0:
                    self.save_metadata()

            cap.release()
            writer.release()

            # If using mp4v, we need to convert it to a web-compatible format
            if fourcc == cv2.VideoWriter_fourcc(*'mp4v'):
                logger.info("Converting mp4v to web-compatible format")
                try:
                    # Create a temporary file path
                    temp_path = str(self.result_path) + ".temp.mp4"

                    # Use FFmpeg if available (much more reliable for video conversion)
                    if shutil.which('ffmpeg'):
                        cmd = [
                            'ffmpeg', '-i', str(self.result_path),
                            '-vcodec', 'libx264', '-acodec', 'aac',
                            '-pix_fmt', 'yuv420p',  # Important for compatibility
                            '-movflags', '+faststart',  # Important for web streaming
                            '-y',  # Overwrite output files
                            temp_path
                        ]
                        subprocess.run(cmd, check=True)

                        # Replace the original file with the converted one
                        os.replace(temp_path, str(self.result_path))
                        logger.info("Successfully converted video with ffmpeg")
                    else:
                        logger.warning("FFmpeg not found, converted video may not play in all browsers")
                except Exception as e:
                    logger.error(f"Error converting video: {e}")
                    # Continue anyway, the original file might still work

            self.status = 'completed'
            self.save_metadata()
            logger.info(f"Video done: {self.result_path}")

        except Exception as e:
            logger.error(f"Video processing error: {e}")
            self.status        = 'error'
            self.error_message = str(e)
            self.save_metadata()
            # Clean up partial file
            if Path(self.result_path).exists():
                try:
                    Path(self.result_path).unlink()
                except:
                    pass

    def save_metadata(self):
        try:
            metadata = {
                'status': self.status,
                'progress': self.progress,
                'detections': self.detections,
                'created_at': self.created_at.isoformat(),
                'last_updated': datetime.now().isoformat(),
                'error_message': self.error_message,
                'result_path': str(self.result_path),
                'video_path': str(self.video_path)
            }
            with open(self.metadata_path, 'w') as f:
                json.dump(metadata, f)
        except Exception as e:
            logger.error(f"Failed to save metadata: {e}")

# -------------------- Utilities --------------------
def get_metadata_path(result_path):
    fname = os.path.basename(result_path)
    return os.path.join(app.config['METADATA_FOLDER'], f"{fname}.json")

def load_metadata(pid):
    try:
        direct = os.path.join(app.config['METADATA_FOLDER'], f"annot_{pid}.mp4.json")
        if os.path.exists(direct):
            return json.load(open(direct))
        for fn in os.listdir(app.config['METADATA_FOLDER']):
            if pid in fn:
                return json.load(open(os.path.join(app.config['METADATA_FOLDER'], fn)))
    except Exception as e:
        logger.error(f"Error loading metadata for {pid}: {e}")
    return None

def find_result_file(pid):
    direct = app.config['RESULTS_FOLDER'] / f"annot_{pid}.mp4"
    if direct.exists():
        return direct
    pattern = re.compile(re.escape(pid))
    for fp in Path(app.config['RESULTS_FOLDER']).iterdir():
        if pattern.search(fp.name):
            return fp
    return None

def cleanup_old_files():
    now       = datetime.now()
    threshold = now - app.config['CLEANUP_THRESHOLD']
    for folder in (app.config['UPLOAD_FOLDER'], app.config['RESULTS_FOLDER']):
        for fp in Path(folder).iterdir():
            if fp.is_file() and datetime.fromtimestamp(fp.stat().st_mtime) < threshold:
                fp.unlink()
                meta = get_metadata_path(str(fp))
                if os.path.exists(meta):
                    os.unlink(meta)
    with process_lock:
        stale = [pid for pid,p in active_processes.items() if p.last_updated < threshold]
        for pid in stale:
            del active_processes[pid]
    logger.info(f"Cleanup done, removed {len(stale)} processes")

def safe_path(folder, fname):
    try:
        # Handle URL-encoded filenames
        fname = urllib.parse.unquote(fname)
        base   = Path(folder).resolve()
        target = (base / fname).resolve()
        if base in target.parents or base == target:
            return target
    except:
        pass
    return None

# -------------------- Routes --------------------
@app.route('/')
def index():
    return render_template('index.html')

@app.route('/upload_video', methods=['POST'])
def upload_video():
    if 'video' not in request.files:
        return jsonify(error='No video provided'), 400
    vid = request.files['video']
    if not vid.filename.lower().endswith(('.mp4', '.avi', '.mov')):
        return jsonify(error='Invalid video format. Supported: MP4, AVI, MOV'), 400

    with process_lock:
        if len(active_processes) >= app.config['MAX_PROCESSES']:
            return jsonify(error='Server busy. Try again later.'), 503

    try:
        # Strip original extension to avoid double .mp4
        base, ext = os.path.splitext(vid.filename)  # ext == ".mp4"
        # Replace spaces and parentheses to avoid URL encoding issues
        base = base.replace(' ', '_').replace('(', '').replace(')', '')
        stem = f"{datetime.now().strftime('%Y%m%d_%H%M%S')}_{base}"
        in_path   = app.config['UPLOAD_FOLDER']  / f"{stem}{ext}"
        out_path  = app.config['RESULTS_FOLDER'] / f"annot_{stem}.mp4"

        vid.save(in_path)
        cleanup_old_files()

        conf    = float(request.form.get('confidence', 0.5))
        vid_proc = VideoProcessor(in_path, out_path, conf=conf)
        pid = stem
        with process_lock:
            active_processes[pid] = vid_proc
        threading.Thread(target=vid_proc.process, daemon=True).start()

        logger.info(f"Video upload: {vid.filename} -> {in_path}, process ID: {pid}")
        return jsonify(process_id=pid)
    except Exception as e:
        logger.error(f"Error in upload_video: {e}")
        return jsonify(error=f"Server error: {e}"), 500

@app.route('/status/<pid>')
def status(pid):
    with process_lock:
        p = active_processes.get(pid)
    if not p:
        meta = load_metadata(pid)
        if meta:
            return jsonify(status=meta['status'], progress=meta['progress'], error=meta.get('error_message'))
        rf = find_result_file(pid)
        if rf:
            return jsonify(status='completed', progress=100)
        return jsonify(error='Process not found'), 404

    resp = {'status': p.status, 'progress': p.progress}
    if p.status == 'error' and p.error_message:
        resp['error'] = p.error_message
    return jsonify(resp)

@app.route('/results/<pid>')
def results(pid):
    with process_lock:
        p = active_processes.get(pid)
    if not p:
        metadata = load_metadata(pid)
        if metadata and metadata['status'] == 'completed':
            rp = metadata['result_path']
            if os.path.exists(rp):
                result_filename = os.path.basename(rp)
                vid_url = url_for('static', filename=f'results/{result_filename}', _external=True)
                logger.info(f"Serving video from metadata: {result_filename}, URL: {vid_url}")
                return jsonify(video_url=vid_url, detections=metadata.get('detections', []))

        rf = find_result_file(pid)
        if rf:
            result_filename = rf.name
            vid_url = url_for('static', filename=f'results/{result_filename}', _external=True)
            logger.info(f"Serving video from found file: {result_filename}, URL: {vid_url}")
            return jsonify(video_url=vid_url, detections=[])
        return jsonify(error='Process or result not found'), 404

    if p.status != 'completed':
        return jsonify(error='Processing not complete', status=p.status, progress=p.progress), 400

    result_filename = os.path.basename(str(p.result_path))
    vid_url = url_for('static', filename=f'results/{result_filename}', _external=True)
    logger.info(f"Serving video from active process: {result_filename}, URL: {vid_url}")
    return jsonify(video_url=vid_url, detections=p.detections)

@app.route('/upload_image', methods=['POST'])
def upload_image():
    if model is None:
        return jsonify(error='Model not loaded properly'), 500
    if 'image' not in request.files:
        return jsonify(error='No image provided'), 400
    img = request.files['image']
    if not img.filename.lower().endswith(('.jpg', '.jpeg', '.png')):
        return jsonify(error='Invalid image format. Supported: JPG, PNG'), 400

    try:
        # Replace spaces and special characters to avoid URL encoding issues
        safe_filename = img.filename.replace(' ', '_').replace('(', '').replace(')', '')
        fname = f"{datetime.now().strftime('%Y%m%d_%H%M%S')}_{safe_filename}"
        in_path = app.config['UPLOAD_FOLDER'] / fname
        out_path = app.config['RESULTS_FOLDER'] / f"annot_{fname}"
        img.save(in_path)
        cleanup_old_files()

        conf, res = float(request.form.get('confidence', 0.5)), None
        res = model.predict(str(in_path), conf=conf)[0]
        out = res.plot()
        cv2.imwrite(str(out_path), out)

        result_filename = os.path.basename(str(out_path))
        img_url = url_for('static', filename=f'results/{result_filename}', _external=True)
        detections = [{
            'class': res.names[int(b.cls)],
            'confidence': float(b.conf),
            'box': b.xyxy[0].tolist()
        } for b in res.boxes]



        try:
            import requests
            php_image_dir = Path("C:/xampp/htdocs/GP_V3/img")
            os.makedirs(php_image_dir, exist_ok=True)
            shutil.copy(out_path, php_image_dir / result_filename)

            timestamp_now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            for det in detections:
                data = {
                    'image_name': result_filename,
                    'class_name': det['class'],
                    'timestamp': timestamp_now
                }
                php_url = 'http://localhost/GP_V3/includes/AddObstacle.php'
                response = requests.post(php_url, data=data)
                if response.status_code == 200:
                    logger.info(f"تم إرسال العائق {det['class']} إلى PHP")
                else:
                    logger.warning(f"فشل إرسال العائق {det['class']} إلى PHP. الكود: {response.status_code}")
        except Exception as e:
            logger.error(f"خطأ أثناء إرسال البيانات إلى PHP: {e}")










        logger.info(f"Image processed: {result_filename}, URL: {img_url}")


        return jsonify(image_url=img_url, detections=detections)
    except Exception as e:
        logger.error(f"Error in upload_image: {e}")
        return jsonify(error=f"Server error: {e}"), 500

# Note: Removing the route for /static/results/<path:fname>
# because Flask already serves static files from /static
# and we're now using url_for('static', ...) instead

@app.route('/cleanup', methods=['POST'])
def manual_cleanup():
    if request.headers.get('X-API-Key') != os.environ.get('API_KEY'):
        return jsonify(error='Unauthorized'), 401
    cleanup_old_files()
    return jsonify(message='Cleanup completed')

@app.errorhandler(404)
def not_found(e):
    return render_template('error.html', error_code=404, error_message="Page not found"), 404

@app.errorhandler(500)
def server_error(e):
    return render_template('error.html', error_code=500, error_message="Internal server error"), 500

@app.errorhandler(413)
def request_too_large(e):
    return jsonify(error="File too large. Maximum size is 16MB"), 413

def start_cleanup_scheduler():
    def task():
        while True:
            threading.Event().wait(3600)
            cleanup_old_files()
    threading.Thread(target=task, daemon=True).start()

# Enable CORS headers to ensure video can be loaded
@app.after_request
def add_cors_headers(response):
    response.headers['Access-Control-Allow-Origin'] = '*'
    response.headers['Access-Control-Allow-Methods'] = 'GET, POST, OPTIONS'
    response.headers['Access-Control-Allow-Headers'] = 'Origin, Accept, Content-Type, X-Requested-With, X-CSRF-Token'

    # Add caching headers for video files
    if request.path.startswith('/static/results/') and request.path.endswith('.mp4'):
        response.headers['Cache-Control'] = 'public, max-age=3600'

    return response

start_cleanup_scheduler()

if __name__ == '__main__':
    app.run(debug=DEBUG_MODE, host='0.0.0.0', port=PORT)
