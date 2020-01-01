<template>
  <Moveable
    class="moveable"
    v-bind="moveable"
    @drag="handleDrag"
    @resize="handleResize"
    @scale="handleScale"
    @rotate="handleRotate"
    @warp="handleWarp"
    @click="clickme"
    @dragStart="handleDragStart"
    @render="renderme"
    @dragEnd="handleDragEnd"
    :style="{left: left, top: top}"
    :class="{'selected': selected}"
  >
    <slot></slot>
  </Moveable>
</template>

<script>
import Moveable from 'vue-moveable'

export default {
  name: 'Movable',
  components: {
    Moveable
  },
    props:[ 'id' ]
    ,
  data: () => ({
    moveable: {
      draggable: true,
      throttleDrag: 0,
      resizable: false,
      throttleResize: 1,
      keepRatio: true,
      scalable: false,
      throttleScale: 0,
      rotatable: false,
      throttleRotate: 3
    }
  }),
    computed:{
      left() {
          return this.$store.state.boxes[this.id].x + "px";
      },
        top() {
            return this.$store.state.boxes[this.id].y + "px";
        },
        selected() {
          return this.$store.state.selected === this.id;
        }
    },
  methods: {
      handleDragEnd(e){
        this.$store.commit('selectBox', this.id)
      },
      handleDragStart(e){

      },
      clickme(){
      },
      renderme(){
      },


    handleDrag ({ target, left, top }) {
        this.$store.commit('moveBox',{id: this.id, x: left, y: top});
      // target.style.left = `${left}px`
      // target.style.top = `${top}px`
    },
    handleResize ({ target, width, height, delta }) {
      delta[0] && (target.style.width = `${width}px`)
      delta[1] && (target.style.height = `${height}px`)
    },
    handleScale ({ target, transform, scale }) {
      target.style.transform = transform
    },
    handleRotate ({ target, dist, transform }) {
      target.style.transform = transform
    },
    handleWarp ({ target, transform }) {
      target.style.transform = transform
    }
  }
}
</script>

<style lang="scss">
  .moveable {
    font-family: "Roboto", sans-serif;
    position: absolute;
    width: 32px;
    height: 32px;
    text-align: center;
    font-size: 10px;
    margin: 0 auto;
    font-weight: 100;
    letter-spacing: 1px;
    background: #eeeeee;
    &.selected{
      border:3px yellow solid;
     }
  }
  /*
   * should be a better way of doing this.
   */
  .moveable-control-box{
    display:none !important;
  }
</style>
