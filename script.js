let scene, camera, renderer;
let globeParticles = [], globeOriginalPositions = [], globeVelocities = [], globeAlphas = [], globeLifetimes = [];
let fallingParticles = [], fallingVelocities = [], fallingAlphas = [], fallingLifetimes = [];
let globeSystem, fallingSystem;
let mouse = new THREE.Vector2(), raycaster = new THREE.Raycaster();
const maxGlobeParticles = 3000;
const maxFallingParticles = 500;
let emittedGlobe = 0, emittedFalling = 0;

init();
animate();

function init() {
  scene = new THREE.Scene();
  
  camera = new THREE.PerspectiveCamera(75, window.innerWidth/window.innerHeight, 0.1, 1000);
  camera.position.z = 250;

  renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('bg'), antialias: true });
  renderer.setSize(window.innerWidth, window.innerHeight);

  const size = 64;
  const canvas = document.createElement('canvas');
  canvas.width = canvas.height = size;
  const ctx = canvas.getContext('2d');
  const gradient = ctx.createRadialGradient(size/2, size/2, 0, size/2, size/2, size/2);
  gradient.addColorStop(0.2, 'white');
  gradient.addColorStop(1, 'transparent');
  ctx.fillStyle = gradient;
  ctx.beginPath();
  ctx.arc(size/2, size/2, size/2, 0, Math.PI * 2);
  ctx.fill();
  const texture = new THREE.CanvasTexture(canvas);

  const material = new THREE.PointsMaterial({
    size: 2,
    map: texture,
    blending: THREE.AdditiveBlending,
    depthWrite: false,
    transparent: true,
    vertexColors: false,
    opacity: 1
  });

  material.onBeforeCompile = shader => {
    shader.vertexShader = shader.vertexShader.replace(
      '#include <common>',
      `#include <common>
       attribute float alpha;
       varying float vAlpha;`
    ).replace(
      '#include <begin_vertex>',
      `#include <begin_vertex>
       vAlpha = alpha;`
    );

    shader.fragmentShader = shader.fragmentShader.replace(
      '#include <common>',
      `#include <common>
       varying float vAlpha;`
    ).replace(
      'gl_FragColor = vec4( outgoingLight, diffuseColor.a );',
      `gl_FragColor = vec4(outgoingLight, vAlpha);`
    );
  };

  // ---------- Globe Particle System ----------
  const globeGeo = new THREE.BufferGeometry();
  const globePositions = new Float32Array(maxGlobeParticles * 3);
  const globeAlphasAttr = new Float32Array(maxGlobeParticles);

  for (let i = 0; i < maxGlobeParticles; i++) {
    globeLifetimes.push(0);
    const phi = Math.acos(2 * Math.random() - 1);
    const theta = 2 * Math.PI * Math.random();
    const r = 100 - Math.random() * 20;

    const x = r * Math.sin(phi) * Math.cos(theta);
    const y = r * Math.sin(phi) * Math.sin(theta);
    const z = r * Math.cos(phi);

    const original = new THREE.Vector3(x, y, z);
    const offset = original.clone().addScaledVector(original.clone().normalize(), -2);

    globeOriginalPositions.push(original);
    globeParticles.push(offset);
    globeVelocities.push(original.clone().normalize().multiplyScalar(Math.random() * 0.5));
    globeAlphas.push(0);

    globePositions[i * 3] = offset.x;
    globePositions[i * 3 + 1] = offset.y;
    globePositions[i * 3 + 2] = offset.z;
    globeAlphasAttr[i] = 0.0;
  }

  globeGeo.setAttribute('position', new THREE.BufferAttribute(globePositions, 3));
  globeGeo.setAttribute('alpha', new THREE.BufferAttribute(globeAlphasAttr, 1));
  globeSystem = new THREE.Points(globeGeo, material.clone());
  scene.add(globeSystem);

  // ---------- Falling Particle System ----------
  const fallGeo = new THREE.BufferGeometry();
  const fallPositions = new Float32Array(maxFallingParticles * 3);
  const fallAlphasAttr = new Float32Array(maxFallingParticles);

  for (let i = 0; i < maxFallingParticles; i++) {
    fallingLifetimes.push(0);
    const x = (Math.random() - 0.5) * 50;
    const y = 200 + Math.random() * 50;
    const z = (Math.random() - 0.5) * 50;

    const pos = new THREE.Vector3(x, y, z);
    fallingParticles.push(pos);
    fallingVelocities.push(new THREE.Vector3(0, -0.5 - Math.random(), 0));
    fallingAlphas.push(0);

    fallPositions[i * 3] = pos.x;
    fallPositions[i * 3 + 1] = pos.y;
    fallPositions[i * 3 + 2] = pos.z;
    fallAlphasAttr[i] = 0.0;
  }

  fallGeo.setAttribute('position', new THREE.BufferAttribute(fallPositions, 3));
  fallGeo.setAttribute('alpha', new THREE.BufferAttribute(fallAlphasAttr, 1));
  fallingSystem = new THREE.Points(fallGeo, material.clone());
  scene.add(fallingSystem);

  // Resize
  window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
  });
}

function animate() {
  requestAnimationFrame(animate);

  const globePos = globeSystem.geometry.attributes.position;
  const globeAlpha = globeSystem.geometry.attributes.alpha;

  const fallPos = fallingSystem.geometry.attributes.position;
  const fallAlpha = fallingSystem.geometry.attributes.alpha;

  // Emit logic
  if (emittedGlobe < maxGlobeParticles) {
    emittedGlobe += 30 / 500;
    if (emittedGlobe > maxGlobeParticles) emittedGlobe = maxGlobeParticles;
  }
  if (emittedFalling < maxFallingParticles) {
    emittedFalling += 10 / 500;
    if (emittedFalling > maxFallingParticles) emittedFalling = maxFallingParticles;
  }

  // Update globe particles
  for (let i = 0; i < emittedGlobe; i++) {
    let pos = globeParticles[i];
    let vel = globeVelocities[i];
    let orig = globeOriginalPositions[i];
    globeLifetimes[i] += 1;

    pos.add(vel);
    globePos.setXYZ(i, pos.x, pos.y, pos.z);

    let alpha;
    if (globeLifetimes[i] < 60) {
      const t = globeLifetimes[i] / 60;
      alpha = t * t * (3 - 2 * t);
    } else if (globeLifetimes[i] > 200) {
      const t = (globeLifetimes[i] - 200) / 100;
      alpha = Math.max(1 - t, 0);
    } else {
      alpha = 0.85 + 0.15 * Math.sin(performance.now() * 0.003 + i);
    }
    globeAlphas[i] = alpha;
    globeAlpha.setX(i, alpha);
  }

 

  globePos.needsUpdate = true;
  globeAlpha.needsUpdate = true;
  fallPos.needsUpdate = true;
  fallAlpha.needsUpdate = true;

  globeSystem.rotation.y += 0.001;
  renderer.render(scene, camera);
}

