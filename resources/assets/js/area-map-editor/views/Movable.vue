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
    :class="{'selected': selected, 'neighbor': neighbor}"
  >
    <slot></slot>
  </Moveable>
</template>

<script>
import Moveable from 'vue-moveable'
import {mapGetters} from "vuex";

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
  mounted(){
    console.log(this.neighborMode);
  },
    computed:{
        ...mapGetters(['neighborMode']),
      left() {
          return this.$store.state.boxes[this.id].x + "px";
      },
        top() {
            return this.$store.state.boxes[this.id].y + "px";
        },
        selected() {

          return this.$store.state.selected === this.id;
        },
        neighbor(){
          const neighbors = this.$store.getters.selectedBoxNeighbors;
          const found = neighbors.find((ele) =>{
              return ele === this.id;
            });
          if(typeof found !== "undefined") {
            return true;
          }
           return false
        }
    },
  methods: {
      handleDragEnd(e){
        if(this.neighborMode){
          if(this.id !== this.$store.state.selected){
            this.$store.commit('toggleNeighbor', this.id)
          }
          return;
        }
        this.$store.commit('selectBox', this.id)
      },
      handleDragStart(e){

      },
      clickme(){
      },
      renderme(){
      },


    handleDrag ({ target, left, top }) {
        if(this.neighborMode){
          return;
        }
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
    &.neighbor{
      border:3px orange solid;
    }
  }
  /*
   * should be a better way of doing this.
   */
  .moveable-control-box{
    display:none !important;
  }
</style>
