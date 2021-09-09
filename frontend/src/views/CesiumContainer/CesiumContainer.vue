<template>
  <div class="home viewer">
    <vc-viewer @ready="onViewerReady" :sceneMode="sceneMode" :showCredit="false">
      <!-- mapbox矢量底图 -->
      <vc-layer-imagery
        :alpha="alpha"
        :brightness="brightness"
        :contrast="contrast"
        :sortOrder="1"
      >
        <vc-provider-imagery-mapbox
          ref="provider"
          url="https://api.mapbox.com/styles/v1"
          username="planee"
          styleId="cksjuob7t9z9618rhpnzh60hd"
          accessToken="pk.eyJ1IjoicGxhbmVlIiwiYSI6ImNrc2p1bGU0dTBnajMyd3BjbTkxcTN1ZjQifQ.ZvAcVs6HPASbNNnguG41LQ"
        ></vc-provider-imagery-mapbox>
      </vc-layer-imagery>
    </vc-viewer>
  </div>
</template>

<script>
export default {
  name: 'Home',
  data() {
    return {
      point: {
        pixelSize: 28,
        color: 'red'
      },
      label: {
        text: 'Hello VueCesium',
        pixelOffset: [0, 80],
        fillColor: 'red'
      },
      billboard: {
        image: 'https://zouyaoji.top/vue-cesium/favicon.png',
        scale: 0.5
      },
      sceneMode: 2
    }
  },
  methods: {
    onViewerReady() {
      console.log('onViewerReady')
    },
    onEntityEvt(e) {
      console.log(e)
      if (e.type === 'onmouseover') {
        this.billboard = {
          image: 'https://zouyaoji.top/vue-cesium/favicon.png',
          scale: 0.6
        }
      } else if (e.type === 'onmouseout') {
        this.billboard = {
          image: 'https://zouyaoji.top/vue-cesium/favicon.png',
          scale: 0.5
        }
      }
    }
  }
}
</script>
<style>
.viewer {
  height: 100%;
}
</style>
