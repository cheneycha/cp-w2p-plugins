import * as THREE from './three.module.js';
import {OrbitControls} from './OrbitControls.js';
import {GLTFLoader} from './GLTFLoader.js';

class NBD3DPreview {
  constructor(){
    this.renderer;
    this.texture;
    this.texture_material;
    this.camera;
    this.scene;
    this.inputElement;
    this.controls;
    this.renderRequested = false;
    this.spotLights = [];
  }
  init(data) {
    const {canvas, inputElement, model, settings, callback} = data;
    this.renderer = new THREE.WebGLRenderer({canvas: canvas, antialias: true});
    const _this = this;
    const fov = 75;
    const aspect = 2;
    const near = 0.1;
    const far = 5;
    this.camera = new THREE.PerspectiveCamera(fov, aspect, near, far);
    this.camera.position.z = 2;
    this.inputElement = inputElement;

    this.controls = new OrbitControls(this.camera, this.inputElement);
    this.controls.enableDamping = true;
    this.controls.target.set(0, 0, 0);
    this.controls.update();

    this.scene = new THREE.Scene();
    this.scene.background = new THREE.Color('white');

    var ambient = new THREE.AmbientLight( 0xcccccc );
    this.scene.add( ambient );

    for(let i = 0; i < 4; i++){
      let spotLight = new THREE.SpotLight( 0xffffff, 1 );
      let x = ( i % 2 == 0 ) ? 1 : - 1;
      let z = ( i % 4 > 1 ) ? -1 : 1;
      spotLight.position.set( x, 2, z );
      spotLight.angle = 0.50;
      spotLight.penumbra = 0.75;
      spotLight.intensity = 1;
      spotLight.decay = 3;
      this.scene.add(spotLight);
      this.spotLights.push(spotLight);
    }

    function frameArea(sizeToFitOnScreen, boxSize, boxCenter, camera) {
      const halfSizeToFitOnScreen = sizeToFitOnScreen * 0.5;
      const halfFovY = THREE.MathUtils.degToRad(camera.fov * .5);
      const distance = halfSizeToFitOnScreen / Math.tan(halfFovY);
      const direction = (new THREE.Vector3())
          .subVectors(camera.position, boxCenter)
          .multiply(new THREE.Vector3(1, 0, 1))
          .normalize();

      camera.position.copy(direction.multiplyScalar(distance).add(boxCenter));

      camera.near = boxSize / 100;
      camera.far = boxSize * 100;

      camera.updateProjectionMatrix();

      camera.lookAt(boxCenter.x, boxCenter.y, boxCenter.z);

      _this.spotLights.map(spotLight => spotLight.position.multiplyScalar(sizeToFitOnScreen / 2));
      _this.spotLights.map(spotLight => spotLight.target.position.set(boxCenter.x, boxCenter.y, boxCenter.z));
      _this.spotLights.map(spotLight => spotLight.target.updateMatrixWorld());
    }

    const gltfLoader = new GLTFLoader();
    gltfLoader.load(model, (gltf) => {
      const root = gltf.scene;
      this.scene.add(root);

      root.traverse((obj) => {
        if ( obj.isMesh ) {
          if(obj.name == settings.meshName ){
            let textureObject = obj,
            old_material = textureObject.material;
            let new_material = new THREE.MeshPhongMaterial( {color:0xffffff, map:old_material.map, transparent:true} );
            this.texture_material = new_material;
            this.texture_material.needsUpdate = true;
            textureObject.material = new_material;
          }else{
            let baseObject = obj;
            let old_material = baseObject.material;
            var new_base_material = new THREE.MeshPhongMaterial( {color:old_material.color} );
            if(old_material.map){
              new_base_material.map = old_material.map;
            }
            baseObject.material = new_base_material;
          }
        }
      });

      root.updateMatrixWorld();

      const box = new THREE.Box3().setFromObject(root);
      const boxSize = box.getSize(new THREE.Vector3()).length();
      const boxCenter = box.getCenter(new THREE.Vector3());

      frameArea(boxSize * 2, boxSize, boxCenter, this.camera);

      this.controls.maxDistance = boxSize * 10;
      this.controls.target.copy(boxCenter);
      this.controls.update();

      callback( "loaded_3d_model" );
    });

    this.render();
    function _render(){
      _this.render();
    }

    function requestRenderIfNotRequested() {
      if (!_this.renderRequested) {
        _this.renderRequested = true;
        requestAnimationFrame(_render);
      }
    }

    this.controls.addEventListener('change', requestRenderIfNotRequested);
  }

  resizeRendererToDisplaySize(renderer) {
    const canvas = renderer.domElement;
    const width = this.inputElement.clientWidth;
    const height = this.inputElement.clientHeight;
    const needResize = canvas.width !== width || canvas.height !== height;
    if (needResize) {
      this.renderer.setSize(width, height, false);
    }
    return needResize;
  }

  render() {
    this.renderRequested = undefined;

    if (this.resizeRendererToDisplaySize(this.renderer)) {
      this.camera.aspect = this.inputElement.clientWidth / this.inputElement.clientHeight;
      this.camera.updateProjectionMatrix();
    }

    this.controls.update();
    this.renderer.render(this.scene,this.camera);
  }

  update_design(design, context){
    const _this = this;
    if( context == 'on_worker' ){
      let loader = new THREE.ImageBitmapLoader();
      loader.load(design, function(imageBitmap ) {
        _this.texture = new THREE.CanvasTexture( imageBitmap );
        _this.texture.flipY = false;
        _this.texture_material.map = _this.texture;
        _this.texture.needsUpdate = true;
        _this.render();
      });
    }else{
      const image = new Image();
      this.texture = new THREE.Texture(image);
      this.texture.flipY = false;
      image.onload = () => {
        _this.texture_material.map = _this.texture;
        _this.texture.needsUpdate = true;
        _this.render();
      };
      image.src = design;
    }
  }
}
export default NBD3DPreview;