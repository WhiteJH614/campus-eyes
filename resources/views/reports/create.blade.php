<!-- Author: Tan Jun Yan -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Submit Report - Campus Eye</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { margin: 0; overflow: hidden; font-family: 'Segoe UI', sans-serif; background-color: #dcecfb; }
        
        #canvas-container {
            width: 100vw;
            height: 100vh;
            display: block;
        }

        #ui-panel {
            position: absolute;
            top: 20px; left: 20px;
            width: 360px;
            max-height: 90vh;
            overflow-y: auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            z-index: 100;
        }

        #ui-panel:hover { transform: translateY(-2px); }

        .header h3 { margin: 0 0 5px 0; color: #2c3e50; font-size: 1.4rem; }
        .header p { margin: 0 0 20px 0; font-size: 0.9rem; color: #7f8c8d; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.75rem; font-weight: bold; color: #34495e; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px; }
        .form-group .required { color: #e74c3c; }
        
        input, textarea, select {
            width: 100%; padding: 10px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            background: #f9f9f9;
            font-size: 0.9rem;
            box-sizing: border-box; 
            transition: border-color 0.2s;
        }
        
        input:focus, textarea:focus, select:focus { outline: none; border-color: #3498db; background: #fff; }
        input[readonly] { background-color: #e8f6f3; color: #16a085; font-weight: bold; cursor: not-allowed; }
        
        select { cursor: pointer; }
        select:disabled { background-color: #ecf0f1; cursor: not-allowed; }

        .submit-btn {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none; border-radius: 8px;
            font-weight: bold; cursor: pointer;
            font-size: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4); }
        .submit-btn:disabled { background: #bdc3c7; cursor: not-allowed; transform: none; box-shadow: none; }

        .back-link { 
            display: inline-block; 
            margin-bottom: 15px; 
            color: #3498db; 
            text-decoration: none; 
            font-size: 0.9rem;
        }
        .back-link:hover { text-decoration: underline; }

        #status { margin-top: 10px; font-size: 0.8rem; text-align: center; color: #7f8c8d; }

        .error-text { color: #e74c3c; font-size: 0.8rem; margin-top: 4px; }
        
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        .file-input-wrapper input[type=file] {
            position: absolute;
            left: 0; top: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%; height: 100%;
        }
        .file-input-label {
            display: block;
            padding: 10px;
            background: #f9f9f9;
            border: 2px dashed #bdc3c7;
            border-radius: 8px;
            text-align: center;
            color: #7f8c8d;
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .file-input-label:hover { border-color: #3498db; }
        .file-input-label.has-file { border-color: #27ae60; background: #e8f6f3; color: #27ae60; }

        .urgency-options { display: flex; gap: 8px; }
        .urgency-option {
            flex: 1;
            padding: 10px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: #f9f9f9;
        }
        .urgency-option:hover { border-color: #3498db; }
        .urgency-option.selected { border-color: #3498db; background: #ebf5fb; }
        .urgency-option.low.selected { border-color: #27ae60; background: #e8f6f3; }
        .urgency-option.medium.selected { border-color: #f39c12; background: #fef9e7; }
        .urgency-option.high.selected { border-color: #e74c3c; background: #fdedec; }
        .urgency-option input { display: none; }
    </style>

    <script type="importmap">
        {
            "imports": {
                "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
                "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
            }
        }
    </script>
</head>
<body>

    <div id="ui-panel">
        <a href="{{ route('reports.index') }}" class="back-link">← Back to My Reports</a>
        
        <div class="header">
            <h3>📍 Submit Report</h3>
            <p>Click a building on the map, then fill in the details</p>
        </div>

        <form id="reportForm" action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Selected Block <span class="required">*</span></label>
                <select id="blockSelect" name="block_id" required>
                    <option value="">Click a building on the map...</option>
                    @foreach($blocks as $block)
                        <option value="{{ $block->id }}" {{ old('block_id') == $block->id ? 'selected' : '' }}>
                            {{ $block->block_name }}
                        </option>
                    @endforeach
                </select>
                @error('block_id')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Room <span class="required">*</span></label>
                <select id="roomSelect" name="room_id" required disabled>
                    <option value="">Select a block first...</option>
                </select>
                @error('room_id')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Category <span class="required">*</span></label>
                <select name="category_id" required>
                    <option value="">Select issue type...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Urgency Level <span class="required">*</span></label>
                <div class="urgency-options">
                    <label class="urgency-option low {{ old('urgency', 'Low') === 'Low' ? 'selected' : '' }}">
                        <input type="radio" name="urgency" value="Low" {{ old('urgency', 'Low') === 'Low' ? 'checked' : '' }}>
                        Low
                    </label>
                    <label class="urgency-option medium {{ old('urgency') === 'Medium' ? 'selected' : '' }}">
                        <input type="radio" name="urgency" value="Medium" {{ old('urgency') === 'Medium' ? 'checked' : '' }}>
                        Medium
                    </label>
                    <label class="urgency-option high {{ old('urgency') === 'High' ? 'selected' : '' }}">
                        <input type="radio" name="urgency" value="High" {{ old('urgency') === 'High' ? 'checked' : '' }}>
                        High
                    </label>
                </div>
                @error('urgency')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Description <span class="required">*</span></label>
                <textarea name="description" rows="3" placeholder="e.g. Aircond leaking in Room 201..." required>{{ old('description') }}</textarea>
                @error('description')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Proof Image (optional)</label>
                <div class="file-input-wrapper">
                    <label class="file-input-label" id="fileLabel">
                        📷 Click to upload image (JPG, PNG - max 5MB)
                    </label>
                    <input type="file" name="attachment" id="attachmentInput" accept="image/jpeg,image/png">
                </div>
                @error('attachment')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="submit-btn">
                Submit Report 🚀
            </button>
        </form>
        
        <div id="status">Loading 3D Map...</div>
    </div>

    <div id="canvas-container"></div>

    <script type="module">
        import * as THREE from 'three';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
        import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';

        // Block name mapping (3D model name -> block_id)
        const blockMapping = {
            'Admin': 'Admin',
            'BlockA': 'Block A',
            'BlockB': 'Block B',
            'BlockM': 'Block M',
            'Canteen': 'Canteen',
            'DKBuilding1': 'DK Building 1',
            'DKBuilding2': 'DK Building 2',
            'Hall': 'Hall',
            'IDK': 'IDK',
            'Library': 'Library',
            'Multipurpose1': 'Multipurpose 1',
            'Multipurpose2': 'Multipurpose 2'
        };

        // --- 1. SETUP SCENE ---
        const scene = new THREE.Scene();
        scene.background = new THREE.Color(0x87CEEB);
        scene.fog = new THREE.Fog(0x87CEEB, 200, 500);

        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        camera.position.set(0, 150, -180);

        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.shadowMap.enabled = true;
        document.getElementById('canvas-container').appendChild(renderer.domElement);

        // --- 2. LIGHTING ---
        const hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444, 0.6);
        hemiLight.position.set(0, 200, 0);
        scene.add(hemiLight);

        const sunLight = new THREE.DirectionalLight(0xffdfba, 1);
        sunLight.position.set(50, 100, 50);
        sunLight.castShadow = true;
        sunLight.shadow.mapSize.width = 2048;
        sunLight.shadow.mapSize.height = 2048;
        scene.add(sunLight);

        // --- GROUND PLANE ---
        const groundGeometry = new THREE.PlaneGeometry(350, 300);
        const groundMaterial = new THREE.MeshStandardMaterial({ color: 0x4caf50, roughness: 0.8, metalness: 0.2 });
        const ground = new THREE.Mesh(groundGeometry, groundMaterial);
        ground.rotation.x = -Math.PI / 2;
        ground.receiveShadow = true;
        ground.position.y = -0.1;
        scene.add(ground);

        // --- 3. LOAD THE CAMPUS MODEL ---
        const loader = new GLTFLoader();
        const raycaster = new THREE.Raycaster();
        const mouse = new THREE.Vector2();

        const targetNames = Object.keys(blockMapping);
        let clickableObjects = [];
        let hoveredObject = null;

        loader.load('{{ asset("campus_TanJunYan.glb") }}', (gltf) => {
            const model = gltf.scene;
            scene.add(model);

            model.traverse((child) => {
                if (child.isMesh) {
                    child.castShadow = true;
                    child.receiveShadow = true;

                    if (targetNames.includes(child.name)) {
                        child.material = child.material.clone();
                        child.userData.originalColor = child.material.color.getHex();
                        clickableObjects.push(child);
                    }
                }
            });
            
            document.getElementById('status').innerText = `Ready: ${clickableObjects.length} Buildings Found`;
        }, undefined, (error) => {
            console.error(error);
            document.getElementById('status').innerText = 'Error loading 3D map';
        });

        // --- 4. INTERACTION LOGIC ---
        window.addEventListener('mousemove', (event) => {
            mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
            mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

            raycaster.setFromCamera(mouse, camera);
            const intersects = raycaster.intersectObjects(clickableObjects);

            if (intersects.length > 0) {
                const hitObj = intersects[0].object;
                if (hoveredObject !== hitObj) {
                    if (hoveredObject) {
                        hoveredObject.material.color.setHex(hoveredObject.userData.originalColor);
                    }
                    hoveredObject = hitObj;
                    hoveredObject.material.color.set(0xffd700);
                    document.body.style.cursor = 'pointer';
                }
            } else {
                if (hoveredObject) {
                    hoveredObject.material.color.setHex(hoveredObject.userData.originalColor);
                    hoveredObject = null;
                }
                document.body.style.cursor = 'default';
            }
        });

        window.addEventListener('click', (event) => {
            if (event.target.closest('#ui-panel')) return;
            
            if (hoveredObject) {
                const blockName = blockMapping[hoveredObject.name] || hoveredObject.name;
                const blockSelect = document.getElementById('blockSelect');
                
                for (let option of blockSelect.options) {
                    if (option.text.trim() === blockName) {
                        blockSelect.value = option.value;
                        blockSelect.dispatchEvent(new Event('change'));
                        blockSelect.style.backgroundColor = "#fff3cd";
                        setTimeout(() => blockSelect.style.backgroundColor = "", 300);
                        break;
                    }
                }
            }
        });

        // --- 5. CONTROLS ---
        const controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.minPolarAngle = 0.1;
        controls.maxPolarAngle = Math.PI / 2 - 0.05;
        controls.minDistance = 20;
        controls.maxDistance = 400;

        // --- 6. CAMPUS GATE/FENCE ---
        function createGate() {
            const gateMaterial = new THREE.MeshStandardMaterial({ color: 0x2c3e50, roughness: 0.6, metalness: 0.8 });
            const pillarMaterial = new THREE.MeshStandardMaterial({ color: 0x34495e, roughness: 0.5, metalness: 0.9 });
            const gateGroup = new THREE.Group();

            const gateWidth = 350, gateDepth = 300, postHeight = 12, postRadius = 0.8, railHeight = 0.4, railWidth = 0.3, postSpacing = 8;

            function createFenceSection(startX, startZ, endX, endZ) {
                const dx = endX - startX, dz = endZ - startZ;
                const length = Math.sqrt(dx * dx + dz * dz);
                const angle = Math.atan2(dz, dx);
                const numPosts = Math.floor(length / postSpacing);

                for (let i = 0; i <= numPosts; i++) {
                    const t = i / numPosts;
                    const x = startX + dx * t, z = startZ + dz * t;

                    const postGeo = new THREE.CylinderGeometry(postRadius, postRadius, postHeight, 8);
                    const post = new THREE.Mesh(postGeo, pillarMaterial);
                    post.position.set(x, postHeight / 2, z);
                    post.castShadow = true;
                    gateGroup.add(post);

                    const sphereGeo = new THREE.SphereGeometry(postRadius * 1.3, 8, 8);
                    const sphere = new THREE.Mesh(sphereGeo, pillarMaterial);
                    sphere.position.set(x, postHeight + postRadius, z);
                    gateGroup.add(sphere);
                }

                [2, 5, 8].forEach(h => {
                    const railGeo = new THREE.BoxGeometry(length, railHeight, railWidth);
                    const rail = new THREE.Mesh(railGeo, gateMaterial);
                    rail.position.set((startX + endX) / 2, h, (startZ + endZ) / 2);
                    rail.rotation.y = -angle;
                    rail.castShadow = true;
                    gateGroup.add(rail);
                });
            }

            const corners = [[-gateWidth/2, -gateDepth/2], [gateWidth/2, -gateDepth/2], [gateWidth/2, gateDepth/2], [-gateWidth/2, gateDepth/2]];
            corners.forEach(([x, z]) => {
                const pillarGeo = new THREE.BoxGeometry(3, postHeight + 4, 3);
                const pillar = new THREE.Mesh(pillarGeo, pillarMaterial);
                pillar.position.set(x, (postHeight + 4) / 2, z);
                pillar.castShadow = true;
                gateGroup.add(pillar);

                const pyramidGeo = new THREE.ConeGeometry(2.5, 4, 4);
                const pyramid = new THREE.Mesh(pyramidGeo, gateMaterial);
                pyramid.position.set(x, postHeight + 6, z);
                pyramid.rotation.y = Math.PI / 4;
                gateGroup.add(pyramid);
            });

            const entranceWidth = 25;
            createFenceSection(-gateWidth/2, -gateDepth/2, -entranceWidth/2, -gateDepth/2);
            createFenceSection(entranceWidth/2, -gateDepth/2, gateWidth/2, -gateDepth/2);
            createFenceSection(gateWidth/2, -gateDepth/2, gateWidth/2, gateDepth/2);
            createFenceSection(gateWidth/2, gateDepth/2, -gateWidth/2, gateDepth/2);
            createFenceSection(-gateWidth/2, gateDepth/2, -gateWidth/2, -gateDepth/2);

            const entrancePillarHeight = 18;
            [-entranceWidth/2, entranceWidth/2].forEach(x => {
                const pillarGeo = new THREE.BoxGeometry(4, entrancePillarHeight, 4);
                const pillar = new THREE.Mesh(pillarGeo, pillarMaterial);
                pillar.position.set(x, entrancePillarHeight/2, -gateDepth/2);
                pillar.castShadow = true;
                gateGroup.add(pillar);

                const topGeo = new THREE.BoxGeometry(5, 2, 5);
                const top = new THREE.Mesh(topGeo, gateMaterial);
                top.position.set(x, entrancePillarHeight + 1, -gateDepth/2);
                gateGroup.add(top);

                const sphereGeo = new THREE.SphereGeometry(2, 12, 12);
                const sphere = new THREE.Mesh(sphereGeo, pillarMaterial);
                sphere.position.set(x, entrancePillarHeight + 4, -gateDepth/2);
                gateGroup.add(sphere);
            });

            const archGeo = new THREE.BoxGeometry(entranceWidth, 2, 3);
            const arch = new THREE.Mesh(archGeo, gateMaterial);
            arch.position.set(0, entrancePillarHeight, -gateDepth/2);
            gateGroup.add(arch);

            scene.add(gateGroup);
        }
        createGate();

        // --- BUILDING EXCLUSION ZONES ---
        const exclusionZones = [
            { x: -100, z: -91, radiusX: 45, radiusZ: 20 },
            { x: -29, z: -106, radiusX: 43, radiusZ: 34 },
            { x: 42, z: -91, radiusX: 37, radiusZ: 22 },
            { x: 137, z: -77, radiusX: 20, radiusZ: 32 },
            { x: 133, z: -24, radiusX: 28, radiusZ: 32 },
            { x: 121, z: 18, radiusX: 29, radiusZ: 19 },
            { x: 17, z: 64, radiusX: 33, radiusZ: 20 },
            { x: -37, z: 84, radiusX: 18, radiusZ: 31 },
            { x: -97, z: 93, radiusX: 25, radiusZ: 39 },
            { x: -81, z: -14, radiusX: 24, radiusZ: 38 },
            { x: -29, z: -31, radiusX: 26, radiusZ: 33 },
            { x: 34, z: -51, radiusX: 29, radiusZ: 21 },
            { x: 0, z: -150, radiusX: 15, radiusZ: 15 },
            { x: 80, z: -50, radiusX: 20, radiusZ: 25 },
        ];

        function isInsideExclusionZone(x, z) {
            return exclusionZones.some(zone => Math.abs(x - zone.x) < zone.radiusX && Math.abs(z - zone.z) < zone.radiusZ);
        }

        // --- TREE HELPERS ---
        function createPineTree(x, z) {
            const trunkGeo = new THREE.CylinderGeometry(1.5, 2, 8, 8);
            const trunkMat = new THREE.MeshStandardMaterial({ color: 0x8B4513 });
            const trunk = new THREE.Mesh(trunkGeo, trunkMat);
            trunk.position.y = 4;
            trunk.castShadow = true;

            const tree = new THREE.Group();
            tree.add(trunk);

            const greenColors = [0x228B22, 0x2E8B57, 0x3CB371];
            for (let i = 0; i < 3; i++) {
                const size = 8 - i * 2;
                const coneGeo = new THREE.ConeGeometry(size, 8, 8);
                const coneMat = new THREE.MeshStandardMaterial({ color: greenColors[i % 3] });
                const cone = new THREE.Mesh(coneGeo, coneMat);
                cone.position.y = 10 + i * 5;
                cone.castShadow = true;
                tree.add(cone);
            }

            tree.rotation.y = Math.random() * Math.PI;
            const scale = 0.6 + Math.random() * 0.4;
            tree.scale.set(scale, scale, scale);
            tree.position.set(x, 0, z);
            scene.add(tree);
        }

        function createRoundTree(x, z) {
            const trunkGeo = new THREE.CylinderGeometry(1.5, 2, 10, 8);
            const trunkMat = new THREE.MeshStandardMaterial({ color: 0x654321 });
            const trunk = new THREE.Mesh(trunkGeo, trunkMat);
            trunk.position.y = 5;
            trunk.castShadow = true;

            const leavesGeo = new THREE.SphereGeometry(7, 12, 12);
            const leavesMat = new THREE.MeshStandardMaterial({ color: 0x32CD32 });
            const leaves = new THREE.Mesh(leavesGeo, leavesMat);
            leaves.position.y = 14;
            leaves.castShadow = true;

            const tree = new THREE.Group();
            tree.add(trunk);
            tree.add(leaves);

            const scale = 0.7 + Math.random() * 0.5;
            tree.scale.set(scale, scale, scale);
            tree.position.set(x, 0, z);
            scene.add(tree);
        }

        function createPalmTree(x, z) {
            const trunkGeo = new THREE.CylinderGeometry(1, 1.5, 15, 8);
            const trunkMat = new THREE.MeshStandardMaterial({ color: 0xCD853F });
            const trunk = new THREE.Mesh(trunkGeo, trunkMat);
            trunk.position.y = 7.5;
            trunk.rotation.z = Math.random() * 0.1 - 0.05;
            trunk.castShadow = true;

            const tree = new THREE.Group();
            tree.add(trunk);

            for (let i = 0; i < 8; i++) {
                const leafGeo = new THREE.ConeGeometry(1.5, 10, 4);
                const leafMat = new THREE.MeshStandardMaterial({ color: 0x228B22 });
                const leaf = new THREE.Mesh(leafGeo, leafMat);
                leaf.position.y = 15;
                leaf.rotation.z = Math.PI / 3;
                leaf.rotation.y = (i / 8) * Math.PI * 2;
                tree.add(leaf);
            }

            const scale = 0.8 + Math.random() * 0.3;
            tree.scale.set(scale, scale, scale);
            tree.position.set(x, 0, z);
            scene.add(tree);
        }

        // --- LAMP POST ---
        function createLampPost(x, z) {
            const postGeo = new THREE.CylinderGeometry(0.3, 0.4, 10, 8);
            const postMat = new THREE.MeshStandardMaterial({ color: 0x1a1a1a, metalness: 0.9 });
            const post = new THREE.Mesh(postGeo, postMat);
            post.position.set(x, 5, z);
            post.castShadow = true;
            scene.add(post);

            const lampGeo = new THREE.SphereGeometry(1.2, 12, 12);
            const lampMat = new THREE.MeshStandardMaterial({ color: 0xFFFACD, emissive: 0xFFD700, emissiveIntensity: 0.5 });
            const lamp = new THREE.Mesh(lampGeo, lampMat);
            lamp.position.set(x, 10.5, z);
            scene.add(lamp);

            const light = new THREE.PointLight(0xFFD700, 0.5, 30);
            light.position.set(x, 10.5, z);
            scene.add(light);
        }

        // --- CLOUDS ---
        const clouds = [];
        function createCloud(x, y, z) {
            const cloudGroup = new THREE.Group();
            const cloudMat = new THREE.MeshStandardMaterial({ color: 0xFFFFFF, transparent: true, opacity: 0.9 });

            const numPuffs = 5 + Math.floor(Math.random() * 4);
            for (let i = 0; i < numPuffs; i++) {
                const size = 8 + Math.random() * 10;
                const puffGeo = new THREE.SphereGeometry(size, 8, 8);
                const puff = new THREE.Mesh(puffGeo, cloudMat);
                puff.position.set((Math.random() - 0.5) * 30, (Math.random() - 0.5) * 8, (Math.random() - 0.5) * 15);
                cloudGroup.add(puff);
            }

            cloudGroup.position.set(x, y, z);
            cloudGroup.userData.speed = 0.02 + Math.random() * 0.03;
            scene.add(cloudGroup);
            clouds.push(cloudGroup);
        }

        // --- BIRDS ---
        const birds = [];
        function createBird(x, y, z) {
            const birdGroup = new THREE.Group();

            const bodyGeo = new THREE.SphereGeometry(0.5, 8, 8);
            bodyGeo.scale(1.5, 1, 1);
            const bodyMat = new THREE.MeshStandardMaterial({ color: 0x333333 });
            const body = new THREE.Mesh(bodyGeo, bodyMat);
            birdGroup.add(body);

            const wingGeo = new THREE.PlaneGeometry(2, 0.5);
            const wingMat = new THREE.MeshStandardMaterial({ color: 0x222222, side: THREE.DoubleSide });

            const leftWing = new THREE.Mesh(wingGeo, wingMat);
            leftWing.position.set(-1, 0, 0);
            leftWing.name = 'leftWing';
            birdGroup.add(leftWing);

            const rightWing = new THREE.Mesh(wingGeo, wingMat);
            rightWing.position.set(1, 0, 0);
            rightWing.name = 'rightWing';
            birdGroup.add(rightWing);

            birdGroup.position.set(x, y, z);
            birdGroup.userData.speed = 0.3 + Math.random() * 0.2;
            birdGroup.userData.wingPhase = Math.random() * Math.PI * 2;
            birdGroup.userData.circleRadius = 50 + Math.random() * 100;
            birdGroup.userData.angle = Math.random() * Math.PI * 2;
            scene.add(birdGroup);
            birds.push(birdGroup);
        }

        // --- PLANT TREES ---
        let planted = 0;
        while (planted < 40) {
            const x = (Math.random() - 0.5) * 320;
            const z = (Math.random() - 0.5) * 270;
            if (!isInsideExclusionZone(x, z)) { createPineTree(x, z); planted++; }
        }

        planted = 0;
        while (planted < 25) {
            const x = (Math.random() - 0.5) * 320;
            const z = (Math.random() - 0.5) * 270;
            if (!isInsideExclusionZone(x, z)) { createRoundTree(x, z); planted++; }
        }

        for (let i = 0; i < 10; i++) {
            const side = i % 2 === 0 ? -1 : 1;
            createPalmTree(side * (20 + i * 3), -120 + i * 8);
        }

        // --- DECORATIVE ELEMENTS ---
        for (let i = -2; i <= 2; i++) { if (i !== 0) createLampPost(i * 30, -140); }
        createLampPost(-55, -60); createLampPost(75, -60); createLampPost(-55, 35);
        createLampPost(55, 40); createLampPost(100, -25); createLampPost(-120, 40);
        createLampPost(110, 50); createLampPost(-10, 110);

        for (let i = 0; i < 12; i++) {
            createCloud((Math.random() - 0.5) * 600, 100 + Math.random() * 50, (Math.random() - 0.5) * 400);
        }

        for (let i = 0; i < 8; i++) {
            createBird((Math.random() - 0.5) * 200, 40 + Math.random() * 30, (Math.random() - 0.5) * 200);
        }

        // --- ANIMATION LOOP ---
        let time = 0;
        function animate() {
            requestAnimationFrame(animate);
            time += 0.016;

            clouds.forEach(cloud => {
                cloud.position.x += cloud.userData.speed;
                if (cloud.position.x > 350) cloud.position.x = -350;
            });

            birds.forEach(bird => {
                bird.userData.angle += 0.01 * bird.userData.speed;
                const radius = bird.userData.circleRadius;
                bird.position.x = Math.cos(bird.userData.angle) * radius;
                bird.position.z = Math.sin(bird.userData.angle) * radius;
                bird.rotation.y = -bird.userData.angle + Math.PI / 2;

                const wingAngle = Math.sin(time * 10 + bird.userData.wingPhase) * 0.5;
                bird.children.forEach(child => {
                    if (child.name === 'leftWing') child.rotation.z = wingAngle;
                    if (child.name === 'rightWing') child.rotation.z = -wingAngle;
                });
            });

            controls.update();
            renderer.render(scene, camera);
        }
        animate();

        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>

    <script>
        // AJAX: Load rooms when block changes
        document.getElementById('blockSelect').addEventListener('change', async function() {
            const blockId = this.value;
            const roomSelect = document.getElementById('roomSelect');
            
            if (!blockId) {
                roomSelect.innerHTML = '<option value="">Select a block first...</option>';
                roomSelect.disabled = true;
                return;
            }

            roomSelect.innerHTML = '<option value="">Loading rooms...</option>';
            roomSelect.disabled = true;

            try {
                const response = await fetch(`{{ url('/reports/rooms') }}/${blockId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                
                const rooms = await response.json();
                
                roomSelect.innerHTML = '<option value="">Select a room...</option>';
                rooms.forEach(room => {
                    const option = document.createElement('option');
                    option.value = room.id;
                    option.textContent = `Floor ${room.floor_number} - ${room.room_name}`;
                    roomSelect.appendChild(option);
                });
                roomSelect.disabled = false;
            } catch (error) {
                console.error('Error loading rooms:', error);
                roomSelect.innerHTML = '<option value="">Error loading rooms</option>';
            }
        });

        // Urgency radio button visual feedback
        document.querySelectorAll('.urgency-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.urgency-option').forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
            });
        });

        // File input label update
        document.getElementById('attachmentInput').addEventListener('change', function() {
            const label = document.getElementById('fileLabel');
            if (this.files.length > 0) {
                label.textContent = '✓ ' + this.files[0].name;
                label.classList.add('has-file');
            } else {
                label.textContent = '📷 Click to upload image (JPG, PNG - max 5MB)';
                label.classList.remove('has-file');
            }
        });
    </script>
</body>
</html>
