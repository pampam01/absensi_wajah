import * as faceapi from '@vladmandic/face-api';

document.addEventListener('DOMContentLoaded', async () => {
    const video = document.getElementById('webcam');
    const canvas = document.getElementById('overlay');
    const scanBtn = document.getElementById('scan-btn');
    const autoScanBtn = document.getElementById('auto-scan-btn');
    const autoScanText = document.getElementById('auto-scan-text');
    const loadingOverlay = document.getElementById('loading-overlay');
    const loadingText = document.getElementById('loading-text');
    
    // Status elements
    const statusBox = document.getElementById('status-box');
    const statusIcon = document.getElementById('status-icon');
    const statusTitle = document.getElementById('status-title');
    const statusDesc = document.getElementById('status-desc');
    const matchDetails = document.getElementById('match-details');
    const matchDistanceEl = document.getElementById('match-distance');
    
    let stream = null;
    let employees = [];
    let faceMatcher = null;
    let isAutoScanning = false;
    let autoScanInterval = null;
    let isProcessing = false;
    let cooldownTimer = null;

    const MATCH_THRESHOLD = 0.55;

    // Load models and data in parallel with logging
    try {
        console.time('Attendance_Init');
        loadingText.innerText = 'Menginisialisasi AI & Data...';
        console.log('Mulai memuat data dan model...');
        
        await Promise.all([
            fetchEmployees().then(() => console.log('Employees data loaded')),
            faceapi.nets.tinyFaceDetector.loadFromUri('/models').then(() => console.log('TinyFaceDetector loaded')),
            faceapi.nets.faceLandmark68Net.loadFromUri('/models').then(() => console.log('FaceLandmark68 loaded')),
            faceapi.nets.faceRecognitionNet.loadFromUri('/models').then(() => console.log('FaceRecognition loaded'))
        ]);
        
        console.timeEnd('Attendance_Init');
        console.log('Semua inisialisasi berhasil.');
        loadingOverlay.classList.add('hidden');
        startVideo();
    } catch (e) {
        console.error(e);
        loadingText.innerText = 'Gagal memuat sistem. Muat ulang halaman.';
        loadingText.classList.add('text-red-500');
    }

    async function fetchEmployees() {
        const res = await fetch(window.routes.attendanceEmployees);
        const data = await res.json();
        
        employees = data;
        
        if(employees.length > 0) {
            const labeledDescriptors = employees.map(emp => {
                // Konversi string JSON yang berisi array angka menjadi Float32Array
                let descriptorArray = emp.face_descriptor;
                if (typeof descriptorArray === 'string') {
                    descriptorArray = JSON.parse(descriptorArray);
                }
                const descriptor = new Float32Array(descriptorArray);
                return new faceapi.LabeledFaceDescriptors(emp.id.toString(), [descriptor]);
            });
            faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, MATCH_THRESHOLD);
        }
    }

    function startVideo() {
        navigator.mediaDevices.getUserMedia({ video: { width: 720, height: 560 } })
            .then(s => {
                stream = s;
                video.srcObject = stream;
            })
            .catch(err => {
                console.error("Error accessing webcam:", err);
                setStatus('error', 'Kamera Error', 'Gagal mengakses kamera web.');
            });
    }

    video.addEventListener('play', () => {
        const displaySize = { width: video.videoWidth, height: video.videoHeight };
        faceapi.matchDimensions(canvas, displaySize);
        scanBtn.disabled = false;
        autoScanBtn.disabled = false;

        // Draw bounding boxes periodically
        setInterval(async () => {
            if (video.paused || video.ended || isProcessing) return;
            const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions());
            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            faceapi.draw.drawDetections(canvas, resizedDetections);
        }, 100);
    });

    scanBtn.addEventListener('click', () => {
        if (!isProcessing) scanFace();
    });

    autoScanBtn.addEventListener('click', () => {
        isAutoScanning = !isAutoScanning;
        
        if (isAutoScanning) {
            autoScanBtn.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-200');
            autoScanBtn.classList.add('bg-indigo-600', 'text-white');
            autoScanText.innerText = 'Stop Auto Scan';
            autoScanInterval = setInterval(() => {
                if (!isProcessing) scanFace();
            }, 3000);
            setStatus('info', 'Auto Scan Aktif', 'Mencari wajah...');
        } else {
            autoScanBtn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-200');
            autoScanBtn.classList.remove('bg-indigo-600', 'text-white');
            autoScanText.innerText = 'Mulai Auto Scan';
            clearInterval(autoScanInterval);
            setStatus('idle', 'Menunggu Scan', 'Arahkan wajah ke kamera.');
        }
    });

    async function scanFace() {
        if (!faceMatcher) {
            setStatus('error', 'Sistem Belum Siap', 'Data wajah karyawan kosong.');
            return;
        }

        isProcessing = true;
        scanBtn.disabled = true;

        try {
            const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptors();

            if (detections.length === 0) {
                setStatus('error', 'Wajah Tidak Ditemukan', 'Mohon lihat ke arah kamera.');
                finishScan();
                return;
            }

            if (detections.length > 1) {
                setStatus('error', 'Lebih Dari Satu Wajah', 'Hanya satu wajah yang diperbolehkan.');
                finishScan();
                return;
            }

            const bestMatch = faceMatcher.findBestMatch(detections[0].descriptor);

            matchDetails.classList.remove('hidden');
            matchDistanceEl.innerText = bestMatch.distance.toFixed(4);

            if (bestMatch.label === 'unknown') {
                setStatus('error', 'Wajah Tidak Dikenali', 'Jarak terlalu jauh ( > ' + MATCH_THRESHOLD + ').');
                finishScan();
                return;
            }

            const employeeId = bestMatch.label;
            const emp = employees.find(e => e.id.toString() === employeeId);
            
            setStatus('success', emp.name, 'Kecocokan: ' + ((1 - bestMatch.distance) * 100).toFixed(0) + '%');
            
            // Send to server
            await submitAttendance(employeeId, bestMatch.distance);
            
        } catch (error) {
            console.error(error);
            setStatus('error', 'Terjadi Kesalahan', 'Gagal memproses gambar.');
            finishScan();
        }
    }

    async function submitAttendance(employeeId, distance) {
        try {
            const response = await fetch(window.routes.attendanceStore, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.routes.csrfToken
                },
                body: JSON.stringify({
                    employee_id: employeeId,
                    match_distance: distance
                })
            });

            const data = await response.json();

            if (response.ok) {
                setStatus('success', 'Berhasil', data.message);
                
                // Cooldown to prevent double submit
                if (isAutoScanning) {
                    clearInterval(autoScanInterval);
                    setTimeout(() => {
                        if (isAutoScanning) {
                            autoScanInterval = setInterval(() => {
                                if (!isProcessing) scanFace();
                            }, 3000);
                            setStatus('info', 'Auto Scan Aktif', 'Melanjutkan pencarian...');
                        }
                    }, 10000);
                }
            } else {
                setStatus('error', 'Gagal Absen', data.message);
            }
        } catch (error) {
            setStatus('error', 'Kesalahan Jaringan', 'Gagal terhubung ke server.');
        } finally {
            finishScan();
        }
    }

    function finishScan() {
        setTimeout(() => {
            isProcessing = false;
            if (!isAutoScanning) {
                scanBtn.disabled = false;
            }
        }, 1500); // Small cooldown
    }

    function setStatus(type, title, desc) {
        statusBox.className = 'rounded-xl p-4 text-center border transition duration-300 ';
        statusTitle.innerText = title;
        statusDesc.innerText = desc;

        let iconSvg = '';
        if (type === 'success') {
            statusBox.classList.add('bg-emerald-50', 'dark:bg-emerald-900/30', 'border-emerald-200', 'dark:border-emerald-800');
            statusTitle.classList.add('text-emerald-700', 'dark:text-emerald-400');
            iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
            statusIcon.classList.remove('text-gray-400', 'text-red-400', 'text-blue-400');
            statusIcon.classList.add('text-emerald-500');
        } else if (type === 'error') {
            statusBox.classList.add('bg-red-50', 'dark:bg-red-900/30', 'border-red-200', 'dark:border-red-800');
            statusTitle.classList.add('text-red-700', 'dark:text-red-400');
            iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
            statusIcon.classList.remove('text-gray-400', 'text-emerald-500', 'text-blue-400');
            statusIcon.classList.add('text-red-500');
        } else if (type === 'info') {
            statusBox.classList.add('bg-blue-50', 'dark:bg-blue-900/30', 'border-blue-200', 'dark:border-blue-800');
            statusTitle.classList.add('text-blue-700', 'dark:text-blue-400');
            iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
            statusIcon.classList.remove('text-gray-400', 'text-emerald-500', 'text-red-500');
            statusIcon.classList.add('text-blue-500');
        } else {
            statusBox.classList.add('bg-gray-50', 'dark:bg-gray-700/50', 'border-gray-200', 'dark:border-gray-600');
            statusTitle.className = 'mt-2 text-lg font-bold text-gray-900 dark:text-white';
            iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
            statusIcon.className = 'w-12 h-12 mx-auto text-gray-400';
        }

        statusIcon.innerHTML = iconSvg;
        
        if (type === 'idle') {
            matchDetails.classList.add('hidden');
        }
    }
});
