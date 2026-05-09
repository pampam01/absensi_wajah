import * as faceapi from '@vladmandic/face-api';

document.addEventListener('DOMContentLoaded', async () => {
    const video = document.getElementById('webcam');
    const canvas = document.getElementById('overlay');
    const captureBtn = document.getElementById('capture-btn');
    const loadingOverlay = document.getElementById('loading-overlay');
    const loadingText = document.getElementById('loading-text');
    const previewImage = document.getElementById('preview-image');
    const previewPlaceholder = document.getElementById('preview-placeholder');
    const form = document.getElementById('registration-form');
    const alertContainer = document.getElementById('alert-container');
    
    let stream = null;

    // Load models in parallel with logging
    try {
        console.time('FaceAPI_Load');
        loadingText.innerText = 'Menginisialisasi AI...';
        console.log('Mulai memuat model face-api...');
        
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('/models').then(() => console.log('TinyFaceDetector loaded')),
            faceapi.nets.faceLandmark68Net.loadFromUri('/models').then(() => console.log('FaceLandmark68 loaded')),
            faceapi.nets.faceRecognitionNet.loadFromUri('/models').then(() => console.log('FaceRecognition loaded'))
        ]);
        
        console.timeEnd('FaceAPI_Load');
        console.log('Semua model berhasil dimuat.');
        loadingOverlay.classList.add('hidden');
        startVideo();
    } catch (e) {
        console.error(e);
        loadingText.innerText = 'Gagal memuat model. Pastikan folder public/models tersedia.';
        loadingText.classList.add('text-red-500');
    }

    function startVideo() {
        navigator.mediaDevices.getUserMedia({ video: { width: 720, height: 560 } })
            .then(s => {
                stream = s;
                video.srcObject = stream;
            })
            .catch(err => {
                console.error("Error accessing webcam:", err);
                showAlert('Gagal mengakses kamera. Pastikan izin diberikan.', 'error');
            });
    }

    video.addEventListener('play', () => {
        const displaySize = { width: video.videoWidth, height: video.videoHeight };
        faceapi.matchDimensions(canvas, displaySize);
        captureBtn.disabled = false;

        // Draw landmarks periodically just to show it's working
        setInterval(async () => {
            if (video.paused || video.ended) return;
            const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks();
            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            faceapi.draw.drawDetections(canvas, resizedDetections);
            faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);
        }, 100);
    });

    captureBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        
        const employeeId = document.getElementById('employee_id').value;
        if (!employeeId) {
            showAlert('Pilih karyawan terlebih dahulu.', 'error');
            return;
        }

        captureBtn.disabled = true;
        captureBtn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span> Memproses...';

        try {
            const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptors();

            if (detections.length === 0) {
                showAlert('Tidak ada wajah terdeteksi. Posisikan wajah dengan benar.', 'error');
                resetBtn();
                return;
            }

            if (detections.length > 1) {
                showAlert('Terdeteksi lebih dari satu wajah. Pastikan hanya ada satu wajah di frame.', 'error');
                resetBtn();
                return;
            }

            const descriptor = Array.from(detections[0].descriptor);
            
            // Capture image
            const imageCanvas = document.createElement('canvas');
            imageCanvas.width = video.videoWidth;
            imageCanvas.height = video.videoHeight;
            imageCanvas.getContext('2d').drawImage(video, 0, 0);
            const base64Image = imageCanvas.toDataURL('image/jpeg', 0.9);

            // Show preview
            previewImage.src = base64Image;
            previewImage.classList.remove('hidden');
            previewPlaceholder.classList.add('hidden');

            // Send to backend
            const response = await fetch(window.routes.faceRegistrationStore, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.routes.csrfToken
                },
                body: JSON.stringify({
                    employee_id: employeeId,
                    descriptor: descriptor,
                    photo: base64Image
                })
            });

            const data = await response.json();

            if (response.ok) {
                showAlert('Wajah berhasil didaftarkan!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showAlert(data.message || 'Terjadi kesalahan.', 'error');
                resetBtn();
            }

        } catch (error) {
            console.error(error);
            showAlert('Terjadi kesalahan saat memproses wajah.', 'error');
            resetBtn();
        }
    });

    function resetBtn() {
        captureBtn.disabled = false;
        captureBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> Ambil Foto & Daftarkan Wajah';
    }

    function showAlert(message, type) {
        alertContainer.classList.remove('hidden', 'bg-red-100', 'text-red-800', 'bg-emerald-100', 'text-emerald-800');
        if (type === 'error') {
            alertContainer.classList.add('bg-red-100', 'text-red-800');
        } else {
            alertContainer.classList.add('bg-emerald-100', 'text-emerald-800');
        }
        alertContainer.innerText = message;
    }
});
